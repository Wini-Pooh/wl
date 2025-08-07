<?php

namespace App\Services;

use App\Models\Document;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DigitalSignatureService
{
    /**
     * Создание простой электронной подписи
     */
    public function createSimpleSignature(Document $document, User $user, array $additionalData = [])
    {
        $signatureData = [
            'type' => 'simple',
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_email' => $user->email,
            'timestamp' => now()->toISOString(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'document_hash' => hash('sha256', $document->content),
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
            'timestamp' => now()->toISOString(),
            'algorithm' => 'GOST R 34.10-2012',
            'hash_algorithm' => 'GOST R 34.11-2012',
            'document_hash' => hash('gost', $document->content), // Используем ГОСТ хеш
            'certificate' => $certificateData,
            'signature' => $this->generateQualifiedSignature($document, $certificateData, $pinCode),
        ];

        $document->update([
            'signature_data' => $signatureData,
            'signature_status' => Document::SIGNATURE_STATUS_SIGNED,
            'signed_at' => now(),
            'digital_signature' => base64_encode(json_encode($signatureData)),
        ]);

        Log::info('Создана квалифицированная электронная подпись', [
            'document_id' => $document->id,
            'certificate_serial' => $certificateData['serial_number'] ?? 'unknown'
        ]);

        return $signatureData;
    }

    /**
     * Проверка подписи документа
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

        if ($signatureData['type'] === 'simple') {
            return $this->verifySimpleSignature($document, $signatureData);
        } else {
            return $this->verifyQualifiedSignature($document, $signatureData);
        }
    }

    /**
     * Экспорт подписи в формате CAdES
     */
    public function exportToCAdES(Document $document): string
    {
        if (!$document->digital_signature) {
            throw new \Exception('Документ не имеет цифровой подписи');
        }

        // Создание CAdES структуры
        $signatureData = json_decode(base64_decode($document->digital_signature), true);
        
        // Это упрощенная реализация, в реальности нужна библиотека для работы с CAdES
        $cadesStructure = [
            'version' => '1.0',
            'algorithm' => $signatureData['algorithm'] ?? 'GOST R 34.10-2012',
            'hash_algorithm' => $signatureData['hash_algorithm'] ?? 'GOST R 34.11-2012',
            'signature_timestamp' => $signatureData['timestamp'],
            'document_hash' => $signatureData['document_hash'],
            'signature_value' => $signatureData['signature'] ?? '',
            'certificate' => $signatureData['certificate'] ?? null,
        ];

        return base64_encode(json_encode($cadesStructure));
    }

    /**
     * Генерация хеша простой подписи
     */
    private function generateSimpleSignatureHash(Document $document, User $user): string
    {
        $data = [
            'document_id' => $document->id,
            'document_content' => $document->content,
            'user_id' => $user->id,
            'user_email' => $user->email,
            'timestamp' => now()->timestamp,
        ];

        return hash('sha256', serialize($data));
    }

    /**
     * Генерация квалифицированной подписи
     */
    private function generateQualifiedSignature(Document $document, array $certificateData, string $pinCode): string
    {
        // Это заглушка для реальной реализации ГОСТ подписи
        // В реальности здесь должна быть интеграция с КриптоПро или другими СКЗИ
        
        $documentHash = hash('sha256', $document->content);
        $privateKeyData = $this->extractPrivateKey($certificateData, $pinCode);
        
        // Симуляция создания подписи
        $signatureData = [
            'document_hash' => $documentHash,
            'certificate_thumbprint' => $certificateData['thumbprint'] ?? '',
            'timestamp' => now()->timestamp,
        ];

        return hash('sha256', serialize($signatureData) . $privateKeyData);
    }

    /**
     * Валидация сертификата
     */
    private function validateCertificate(array $certificateData): void
    {
        if (!isset($certificateData['file_content'])) {
            throw new \Exception('Файл сертификата не предоставлен');
        }

        // Здесь должна быть реальная валидация сертификата
        // Проверка формата, срока действия, цепочки доверия и т.д.
        
        Log::info('Валидация сертификата выполнена', [
            'file_name' => $certificateData['file_name'] ?? 'unknown'
        ]);
    }

    /**
     * Извлечение закрытого ключа из сертификата
     */
    private function extractPrivateKey(array $certificateData, string $pinCode): string
    {
        // Это заглушка для извлечения закрытого ключа
        // В реальности здесь должна быть работа с PKCS#12 контейнерами
        
        return hash('sha256', $certificateData['file_content'] . $pinCode);
    }

    /**
     * Проверка простой подписи
     */
    private function verifySimpleSignature(Document $document, array $signatureData): array
    {
        // Проверяем хеш документа
        $currentHash = hash('sha256', $document->content);
        if ($currentHash !== $signatureData['document_hash']) {
            return [
                'valid' => false,
                'error' => 'Документ был изменен после подписания'
            ];
        }

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

        // Проверка хеша документа
        $currentHash = hash('gost', $document->content);
        if ($currentHash !== $signatureData['document_hash']) {
            return [
                'valid' => false,
                'error' => 'Документ был изменен после подписания'
            ];
        }

        // Проверка цифровой подписи
        // Здесь должна быть реальная проверка ЭЦП

        return [
            'valid' => true,
            'type' => 'qualified',
            'signed_at' => $signatureData['timestamp'] ?? 'Неизвестно',
            'certificate_serial' => $signatureData['certificate']['serial_number'] ?? 'Неизвестно',
            'certificate_issuer' => $signatureData['certificate']['issuer'] ?? 'Неизвестно',
        ];
    }
}
