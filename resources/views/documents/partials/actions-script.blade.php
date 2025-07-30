// Обработка смены типа подписи
const signatureType = document.getElementById('signatureType');
const qualifiedFields = document.getElementById('qualifiedSignatureFields');

if (signatureType) {
    signatureType.addEventListener('change', function() {
        if (this.value === 'qualified') {
            qualifiedFields.style.display = 'block';
        } else {
            qualifiedFields.style.display = 'none';
        }
    });
}

// Обработка кнопок подписи
document.querySelectorAll('.btn-sign').forEach(button => {
    button.addEventListener('click', function() {
        const documentId = this.dataset.documentId;
        document.getElementById('documentId').value = documentId;
        
        const modal = new bootstrap.Modal(document.getElementById('signatureModal'));
        modal.show();
    });
});

// Обработка подписания документа
const signDocumentBtn = document.getElementById('signDocument');
if (signDocumentBtn) {
    signDocumentBtn.addEventListener('click', function() {
        const form = document.getElementById('signatureForm');
        const formData = new FormData(form);
        
        // Обработка файла сертификата
        const certificateFile = document.getElementById('certificateFile').files[0];
        if (formData.get('signature_type') === 'qualified' && certificateFile) {
            const reader = new FileReader();
            reader.onload = function(e) {
                formData.append('certificate_data', JSON.stringify({
                    file_content: e.target.result,
                    file_name: certificateFile.name
                }));
                submitSignature(formData);
            };
            reader.readAsDataURL(certificateFile);
        } else {
            submitSignature(formData);
        }
    });
}

function submitSignature(formData) {
    const documentId = formData.get('document_id');
    
    fetch(`/documents/${documentId}/sign`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', data.message);
            location.reload();
        } else {
            showAlert('error', data.message || 'Произошла ошибка при подписании');
        }
        
        const modal = bootstrap.Modal.getInstance(document.getElementById('signatureModal'));
        modal.hide();
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('error', 'Произошла ошибка при подписании документа');
    });
}

// Обработка отправки документов
document.querySelectorAll('.btn-send').forEach(button => {
    button.addEventListener('click', function() {
        const documentId = this.dataset.documentId;
        
        if (confirm('Вы уверены, что хотите отправить этот документ?')) {
            fetch(`/documents/${documentId}/send`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', data.message);
                    location.reload();
                } else {
                    showAlert('error', data.message || 'Произошла ошибка при отправке');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('error', 'Произошла ошибка при отправке документа');
            });
        }
    });
});

// Обработка удаления документов
document.querySelectorAll('.btn-delete').forEach(button => {
    button.addEventListener('click', function() {
        const documentId = this.dataset.documentId;
        
        if (confirm('Вы уверены, что хотите удалить этот документ? Это действие нельзя отменить.')) {
            fetch(`/documents/${documentId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', data.message);
                    location.reload();
                } else {
                    showAlert('error', data.message || 'Произошла ошибка при удалении');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('error', 'Произошла ошибка при удалении документа');
            });
        }
    });
});

// Функция показа уведомлений
function showAlert(type, message) {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const alert = document.createElement('div');
    alert.className = `alert ${alertClass} alert-dismissible fade show`;
    alert.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.querySelector('.container-fluid').insertBefore(alert, document.querySelector('.row'));
    
    setTimeout(() => {
        alert.remove();
    }, 5000);
}
