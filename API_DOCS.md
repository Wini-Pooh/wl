# Документация по API для системы электронных документов

## Общие сведения
API для работы с электронными документами и запросами на подписи.

## Эндпоинты

### GET /api/documents/incoming
Получение входящих запросов на подпись для авторизованного пользователя.

**Параметры запроса:**
- `page` - номер страницы для пагинации (по умолчанию: 1)

**Ответ:**
```json
{
  "incomingRequests": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "document_id": 123,
        "sender_id": 456,
        "recipient_id": 789,
        "status": "pending",
        "message": "Требуется ваша подпись",
        "expires_at": "2025-07-29T14:00:00.000000Z",
        "created_at": "2025-07-22T10:00:00.000000Z",
        "updated_at": "2025-07-22T10:00:00.000000Z",
        "document": {
          "id": 123,
          "name": "Договор.pdf",
          "size": 1024000
        },
        "sender": {
          "id": 456,
          "name": "Иван Петров"
        }
      }
    ],
    "total": 10,
    "per_page": 10,
    "last_page": 1
  }
}
```

### GET /api/documents/outgoing
Получение исходящих запросов на подпись для авторизованного пользователя.

**Параметры запроса:**
- `page` - номер страницы для пагинации (по умолчанию: 1)

**Ответ:**
```json
{
  "outgoingRequests": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "document_id": 123,
        "sender_id": 789,
        "recipient_id": 456,
        "status": "pending",
        "message": "Требуется ваша подпись",
        "expires_at": "2025-07-29T14:00:00.000000Z",
        "created_at": "2025-07-22T10:00:00.000000Z",
        "updated_at": "2025-07-22T10:00:00.000000Z",
        "document": {
          "id": 123,
          "name": "Договор.pdf",
          "size": 1024000
        },
        "recipient": {
          "id": 456,
          "name": "Иван Петров"
        }
      }
    ],
    "total": 5,
    "per_page": 10,
    "last_page": 1
  }
}
```

### GET /api/documents/signed
Получение подписанных документов для авторизованного пользователя.

**Параметры запроса:**
- `page` - номер страницы для пагинации (по умолчанию: 1)

**Ответ:**
```json
{
  "signedDocuments": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "user_id": 789,
        "document_id": 123,
        "signature_request_id": 1,
        "signature_data": "...",
        "signature_date": "2025-07-22T11:00:00.000000Z",
        "created_at": "2025-07-22T11:00:00.000000Z",
        "updated_at": "2025-07-22T11:00:00.000000Z",
        "document": {
          "id": 123,
          "name": "Договор.pdf",
          "size": 1024000
        },
        "signatureRequest": {
          "id": 1,
          "sender_id": 456,
          "status": "signed"
        }
      }
    ],
    "total": 3,
    "per_page": 10,
    "last_page": 1
  }
}
```

### GET /api/documents/projects
Получение списка проектов для выбора при создании документа.

**Ответ:**
```json
{
  "projects": [
    {
      "id": 1,
      "name": "Ремонт квартиры",
      "client_id": 123,
      "partner_id": 456
    }
  ]
}
```
