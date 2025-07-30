<!-- Модальное окно для подписи -->
<div class="modal fade" id="signatureModal" tabindex="-1" aria-labelledby="signatureModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="signatureModalLabel">Подписание документа</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="signatureForm">
                    <input type="hidden" id="documentId" name="document_id">
                    
                    <div class="mb-3">
                        <label for="signatureType" class="form-label">Тип подписи</label>
                        <select class="form-select" id="signatureType" name="signature_type" required>
                            <option value="">Выберите тип подписи</option>
                            <option value="simple">Простая электронная подпись</option>
                            <option value="qualified">Квалифицированная электронная подпись</option>
                        </select>
                    </div>

                    <div id="qualifiedSignatureFields" style="display: none;">
                        <div class="mb-3">
                            <label for="certificateFile" class="form-label">Файл сертификата</label>
                            <input type="file" class="form-control" id="certificateFile" accept=".p12,.pfx">
                            <div class="form-text">Загрузите файл сертификата (.p12 или .pfx)</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="pinCode" class="form-label">PIN-код</label>
                            <input type="password" class="form-control" id="pinCode" name="pin_code">
                            <div class="form-text">Введите PIN-код для доступа к закрытому ключу</div>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <h6>Информация о типах электронной подписи:</h6>
                        <ul class="mb-0">
                            <li><strong>Простая ЭП:</strong> Подтверждает авторство и целостность документа. Создается на основе данных пользователя.</li>
                            <li><strong>Квалифицированная ЭП:</strong> Соответствует требованиям 63-ФЗ "Об электронной подписи", имеет юридическую силу в РФ. Создается с использованием сертификата ключа проверки электронной подписи.</li>
                        </ul>
                    </div>

                    <div class="alert alert-warning">
                        <h6>Требования к квалифицированной ЭП в РФ:</h6>
                        <ul class="mb-0">
                            <li>Использование алгоритмов ГОСТ Р 34.10-2012 и ГОСТ Р 34.11-2012</li>
                            <li>Сертификат должен быть выдан аккредитованным удостоверяющим центром</li>
                            <li>Средства электронной подписи должны иметь сертификат соответствия ФСБ России</li>
                            <li>Подпись должна быть создана с использованием лицензированного СКЗИ</li>
                        </ul>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-primary" id="signDocument">Подписать</button>
            </div>
        </div>
    </div>
</div>
