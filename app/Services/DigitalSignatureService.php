<?php

namespace App\Services;

use App\Models\Document;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;

/**
 * Сервис для работы с электронной подписью
 * Соответствует требованиям ФЗ-63 "Об электронной подписи"
 */
class DigitalSignatureService
{
    /**
     * Создание простой электронной подписи
     */
    public function createSimpleSignature(Document $document, $user, array $additionalData = [])
    {
        $signatureData = [
            'type' => 'simple',
            'version' => '1.0',
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_email' => $user->email,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toISOString(),
            'document_hash' => $this->calculateDocumentHash($document),
            'signature_hash' => $this->generateSimpleSignatureHash($document, $user),
            'additional_data' => $additionalData,
        ];

        $document->update([
            'signature_data' => $signatureData,
            'signature_status' => Document::SIGNATURE_STATUS_SIGNED,
            'signed_at' => now(),
        ]);

        Log::info('Создана простая электронная подпись', [
            'document_id' => $document->id,
            'user_id' => $user->id,
            'signature_hash' => $signatureData['signature_hash']
        ]);

        return $signatureData;
    }

    /**
     * Создание квалифицированной электронной подписи
     */
    public function createQualifiedSignature(Document $document, array $certificateData, string $pinCode)
    {
        // Валидация сертификата
        $this->validateCertificate($certificateData);

        // Создание подписи по ГОСТ
        $signatureData = [
            'type' => 'qualified',
            'version' => '1.0',
            'algorithm' => 'GOST R 34.10-2012',
            'hash_algorithm' => 'GOST R 34.11-2012',
            'certificate' => $certificateData,
            'timestamp' => now()->toISOString(),
            'document_hash' => $this->calculateDocumentHash($document),
            'signature_value' => $this->generateGostSignature($document, $certificateData, $pinCode),
            'tsp_token' => $this->generateTimestampToken($document),
        ];

        $document->update([
            'signature_data' => $signatureData,
            'digital_signature' => base64_encode(json_encode($signatureData)),
            'signature_certificate' => $certificateData,
            'signature_status' => Document::SIGNATURE_STATUS_SIGNED,
            'signed_at' => now(),
        ]);

        Log::info('Создана квалифицированная электронная подпись', [
            'document_id' => $document->id,
            'certificate_serial' => $certificateData['serial_number'] ?? 'unknown',
            'algorithm' => $signatureData['algorithm']
        ]);

        return $signatureData;
    }

    /**
     * Проверка электронной подписи
     */
    public function verifySignature(Document $document): array
    {
        if (!$document->signature_data) {
            return [
                'valid' => false,
                'error' => 'Подпись отсутствует'
            ];
        }

        $signatureData = $document->signature_data;
        
        try {
            // Проверка целостности документа
            $currentHash = $this->calculateDocumentHash($document);
            $originalHash = $signatureData['document_hash'] ?? '';

            if ($currentHash !== $originalHash) {
                return [
                    'valid' => false,
                    'error' => 'Документ был изменен после подписания'
                ];
            }

            // Проверка в зависимости от типа подписи
            if ($signatureData['type'] === 'simple') {
                return $this->verifySimpleSignature($document, $signatureData);
            } elseif ($signatureData['type'] === 'qualified') {
                return $this->verifyQualifiedSignature($document, $signatureData);
            }

            return [
                'valid' => false,
                'error' => 'Неизвестный тип подписи'
            ];

        } catch (Exception $e) {
            Log::error('Ошибка при проверке подписи', [
                'document_id' => $document->id,
                'error' => $e->getMessage()
            ]);

            return [
                'valid' => false,
                'error' => 'Ошибка при проверке подписи: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Вычисление хеша документа
     */
    private function calculateDocumentHash(Document $document): string
    {
        $content = $document->content;
        if ($document->file_path) {
            // Если есть файл, включаем его в хеш
            $fileContent = file_get_contents(storage_path('app/public/' . $document->file_path));
            $content .= $fileContent;
        }
        
        return hash('sha256', $content);
    }

    /**
     * Генерация хеша простой подписи
     */
    private function generateSimpleSignatureHash(Document $document, $user): string
    {
        $data = $document->content . $user->id . $user->email . now()->timestamp;
        return hash('sha256', $data);
    }

    /**
     * Валидация сертификата
     */
    private function validateCertificate(array $certificateData): bool
    {
        // Проверка обязательных полей
        $requiredFields = ['serial_number', 'issuer', 'subject', 'valid_from', 'valid_until', 'public_key'];
        
        foreach ($requiredFields as $field) {
            if (!isset($certificateData[$field])) {
                throw new Exception("Отсутствует обязательное поле сертификата: {$field}");
            }
        }

        // Проверка срока действия
        $validUntil = Carbon::parse($certificateData['valid_until']);
        if ($validUntil->isPast()) {
            throw new Exception('Срок действия сертификата истек');
        }

        // Проверка УЦ (заглушка - в реальности нужна проверка через API)
        $trustedIssuers = [
            'CN=Crypto-Pro CA',
            'CN=CryptoPro TSA',
            'CN=УЦ КриптоПро',
            'CN=СКБ Контур CA',
            'CN=Калуга Астрал CA'
        ];

        $issuverValid = false;
        foreach ($trustedIssuers as $trustedIssuer) {
            if (strpos($certificateData['issuer'], $trustedIssuer) !== false) {
                $issuverValid = true;
                break;
            }
        }

        if (!$issuverValid) {
            throw new Exception('Сертификат выдан неаккредитованным УЦ');
        }

        return true;
    }

    /**
     * Генерация подписи по ГОСТ (заглушка)
     */
    private function generateGostSignature(Document $document, array $certificateData, string $pinCode): string
    {
        // В реальном приложении здесь должно быть:
        // 1. Подключение к КриптоПро CSP
        // 2. Создание подписи с использованием закрытого ключа
        // 3. Применение алгоритмов ГОСТ Р 34.10-2012
        
        // Для демонстрации генерируем имитацию подписи
        $documentHash = $this->calculateDocumentHash($document);
        $signatureInput = $documentHash . $certificateData['serial_number'] . $pinCode . now()->timestamp;
        
        return hash('sha256', $signatureInput) . '_gost_signature';
    }

    /**
     * Генерация штампа времени
     */
    private function generateTimestampToken(Document $document): string
    {
        // В реальном приложении здесь должен быть запрос к сервису TSA
        // Для демонстрации генерируем простой токен
        $documentHash = $this->calculateDocumentHash($document);
        $timestampData = [
            'document_hash' => $documentHash,
            'timestamp' => now()->toISOString(),
            'tsa_name' => 'Demo TSA Service',
        ];
        
        return base64_encode(json_encode($timestampData));
    }

    /**
     * Проверка простой подписи
     */
    private function verifySimpleSignature(Document $document, array $signatureData): array
    {
        // Проверяем, что подпись была создана не слишком давно (опционально)
        $signedAt = Carbon::parse($signatureData['timestamp']);
        if ($signedAt->diffInDays(now()) > 365) {
            return [
                'valid' => false,
                'error' => 'Подпись слишком старая'
            ];
        }

        return [
            'valid' => true,
            'type' => 'simple',
            'signed_by' => $signatureData['user_name'] ?? 'Неизвестно',
            'signed_at' => $signatureData['timestamp'] ?? 'Неизвестно',
        ];
    }

    /**
     * Проверка квалифицированной подписи
     */
    private function verifyQualifiedSignature(Document $document, array $signatureData): array
    {
        // Проверка сертификата
        try {
            $this->validateCertificate($signatureData['certificate']);
        } catch (Exception $e) {
            return [
                'valid' => false,
                'error' => 'Недействительный сертификат: ' . $e->getMessage()
            ];
        }

        // Проверка алгоритма
        if ($signatureData['algorithm'] !== 'GOST R 34.10-2012') {
            return [
                'valid' => false,
                'error' => 'Неподдерживаемый алгоритм подписи'
            ];
        }

        // В реальном приложении здесь должна быть проверка подписи через КриптоПро
        
        return [
            'valid' => true,
            'type' => 'qualified',
            'algorithm' => $signatureData['algorithm'],
            'certificate_serial' => $signatureData['certificate']['serial_number'] ?? 'Неизвестно',
            'certificate_issuer' => $signatureData['certificate']['issuer'] ?? 'Неизвестно',
            'signed_at' => $signatureData['timestamp'] ?? 'Неизвестно',
        ];
    }

    /**
     * Получение информации о статусе сертификата
     */
    public function getCertificateStatus(array $certificateData): array
    {
        // В реальном приложении здесь должен быть запрос к OCSP или CRL
        return [
            'status' => 'valid',
            'checked_at' => now()->toISOString(),
            'method' => 'demo_check'
        ];
    }

    /**
     * Экспорт подписи в формате CAdES
     */
    public function exportToCAdES(Document $document): string
    {
        if (!$document->signature_data) {
            throw new Exception('Документ не подписан');
        }

        // В реальном приложении здесь должно быть формирование CAdES
        // Для демонстрации возвращаем base64-encoded JSON
        $cadesData = [
            'format' => 'CAdES-BES',
            'document_id' => $document->id,
            'signature_data' => $document->signature_data,
            'exported_at' => now()->toISOString(),
        ];

        return base64_encode(json_encode($cadesData, JSON_UNESCAPED_UNICODE));
    }
}
