# MuseWallet Routes Mapping

Complete routes mapping between 1go.exchange application and Postman collection.

## 📍 All Routes (from routes.php)

### Public Endpoints (no authentication required)

| Route | Method | Controller Method | Postman Request |
|-------|--------|-------------------|-----------------|
| `/api/v1/musewallet/webhook` | POST | `webhook()` | ✅ Webhook - * |
| `/api/v1/musewallet/card-products` | GET | `getCardProducts()` | ✅ Get Card Products |

### Authenticated Endpoints

| Route | Method | Controller Method | MusePay API | Postman Request |
|-------|--------|-------------------|-------------|-----------------|
| `/api/v1/musewallet/balance/partner` | POST | `getPartnerBalance()` | `POST /v1/balance/partner` | ✅ Get Partner Balance |
| `/api/v1/musewallet/carduser/create` | POST | `createCardHolder()` | `POST /v1/carduser/create` | ✅ Create Card Holder |
| `/api/v1/musewallet/carduser/upload-kyc` | POST | `uploadKyc()` | `POST /v1/carduser/upload-kyc` | ✅ Upload KYC Documents |
| `/api/v1/musewallet/carduser/kyc-link` | POST | `generateKycLink()` | `POST /v1/carduser/kyc-link` | ✅ Generate KYC Link |
| `/api/v1/musewallet/card/apply` | POST | `applyCard()` | `POST /v1/card/apply` | ✅ Apply for Card |
| `/api/v1/musewallet/card/apply-result` | POST | `queryApplyResult()` | `POST /v1/card/apply-result` | ✅ Query Apply Result |
| `/api/v1/musewallet/card/query` | POST | `getCardInfo()` | `POST /v1/card/query` | ✅ Get Card Info |
| `/api/v1/musewallet/card/activate` | POST | `activateCard()` | `POST /v1/card/activate` | ✅ Activate Card |
| `/api/v1/musewallet/cardaccount/topup` | POST | `topUpCard()` | `POST /v1/cardaccount/topup` | ✅ Top Up Card |

## 🔄 Collection Changes

### Corrected Endpoints (Match MusePay API v1)

1. **Get Partner Balance**
   - ❌ Was: `GET /api/v1/musewallet/balance` (query params)
   - ✅ Now: `POST /api/v1/musewallet/balance/partner` (body)
   - 📚 MusePay API: `POST /v1/balance/partner`

2. **Create Card Holder**
   - ❌ Was: `POST /api/v1/musewallet/card-holders`
   - ✅ Now: `POST /api/v1/musewallet/carduser/create`
   - 📚 MusePay API: `POST /v1/carduser/create`

3. **Top Up Card**
   - ❌ Was: `POST /api/v1/musewallet/card/topup`
   - ✅ Now: `POST /api/v1/musewallet/cardaccount/topup`
   - 📚 MusePay API: `POST /v1/cardaccount/topup`

4. **Query Apply Result**
   - ❌ Was: `GET /api/v1/musewallet/card/apply-result` (query params)
   - ✅ Now: `POST /api/v1/musewallet/card/apply-result` (body)
   - 📚 MusePay API: `POST /v1/card/apply-result`

5. **Get Card Info**
   - ❌ Was: `GET /api/v1/musewallet/card/{cardId}` (path param)
   - ✅ Now: `POST /api/v1/musewallet/card/query` (body)
   - 📚 MusePay API: `POST /v1/card/query`

### Corrected Data Fields

1. **Individual (Personal Information)**
   - ❌ `individual.birthday` → ✅ `individual.date_of_birth`
   - ❌ `individual.nationality` → ✅ removed (doesn't exist in API)
   - ➕ Added: `individual.occupation` (optional)
   - ➕ Added: `individual.annual_income` (optional)

2. **Document (Identity Documents)**
   - ❌ `document.front_image` → ✅ `document.front`
   - ❌ `document.back_image` → ✅ `document.back`
   - ❌ `document.selfie_image` → ✅ `document.face`
   - ➕ Added: `document.country` (required)
   - ➕ Added: `document.expiry_date` (required)

3. **Address (Address Information)**
   - ❌ `address.postal_code` → ✅ `address.post_code`
   - ❌ `address.address_line1` → ✅ `address.details`
   - ❌ `address.state` → ✅ removed (doesn't exist in API)
   - ❌ `address.address_line2` → ✅ removed (doesn't exist in API)

## 📋 Request Structure

### 1. Get Partner Balance
```http
POST /api/v1/musewallet/balance/partner
Content-Type: application/json

{
    "currency": "USDT"
}
```

### 2. Get Card Products
```http
GET /api/v1/musewallet/card-products
```

### 3. Create Card Holder
```http
POST /api/v1/musewallet/carduser/create
Content-Type: application/json

{
    "user_xid": "string",
    "email": "string",
    "user_name": "string",
    "individual": {
        "first_name": "string",
        "last_name": "string",
        "date_of_birth": "YYYY-MM-DD",
        "occupation": "string",
        "annual_income": "string"
    },
    "address": {
        "country": "XX",
        "city": "string",
        "post_code": "string",
        "details": "string"
    },
    "document": {
        "type": "1|2",
        "number": "string",
        "country": "XX",
        "expiry_date": "YYYY-MM-DD",
        "front": "data:image/jpeg;base64,...",
        "back": "data:image/jpeg;base64,...",
        "face": "data:image/jpeg;base64,..."
    }
}
```

### 4. Apply for Card
```http
POST /api/v1/musewallet/card/apply
Content-Type: application/json

{
    "user_id": "string",
    "request_id": "string",
    "card_product_id": "string",
    "card_level": "1-5",
    "phone_number": "string",
    "phone_area_code": "string",
    "embossed_name": "string"
}
```

### 5. Query Apply Result
```http
POST /api/v1/musewallet/card/apply-result
Content-Type: application/json

{
    "request_id": "string",
    "user_id": "string",
    "apply_id": "string"
}
```

### 6. Get Card Info
```http
POST /api/v1/musewallet/card/query
Content-Type: application/json

{
    "card_id": "string",
    "user_id": "string"
}
```

### 7. Activate Card
```http
POST /api/v1/musewallet/card/activate
Content-Type: application/json

{
    "user_id": "string",
    "card_id": "string",
    "request_id": "string"
}
```

### 8. Top Up Card
```http
POST /api/v1/musewallet/cardaccount/topup
Content-Type: application/json

{
    "request_id": "string",
    "card_id": "string",
    "user_id": "string",
    "amount": "string",
    "currency": "string"
}
```

### 9. Upload KYC Documents
```http
POST /api/v1/musewallet/carduser/upload-kyc
Content-Type: application/json

{
    "user_xid": "string",
    "individual": { ... },
    "document": { ... },
    "address": { ... }
}
```

### 10. Generate KYC Link
```http
POST /api/v1/musewallet/carduser/kyc-link
Content-Type: application/json

{
    "user_xid": "string"
}
```

### 11. Webhook
```http
POST /api/v1/musewallet/webhook
Content-Type: application/json
X-Musewallet-Signature: signature_here

{
    "type": "CARD_CREATED|APPLY_AUDIT|CARD_BILL_TRANSACTION|CARD_TOP_UP|...",
    "timestamp": 1698765432,
    "data": { ... },
    "sign": "base64_signature"
}
```

## 🎯 Quick Start

### 1. Import to Postman
```bash
1. Open Postman
2. Import → File → MuseWallet-SDK-Collection.json
3. Import → File → MuseWallet-Environment.json
4. Select "MuseWallet SDK Environment" environment
```

### 2. Configure Environment Variables
```
base_url = http://localhost:8000
currency = USDT
user_id = (get from create card holder response)
user_xid = your_external_user_id
card_product_id = (get from get card products response)
card_id = (get from apply card response)
```

### 3. Testing Sequence
```
1. Get Card Products → get card_product_id
2. Create Card Holder → get user_id
3. Apply for Card → get apply_id, card_id
4. Query Apply Result → check status
5. Activate Card → activate
6. Top Up Card → add balance
7. Get Card Info → check information
```

## 📝 Notes

### Authentication
In routes.php specified `middleware => []`, which means:
- Authentication will be added in the future
- Currently endpoints are accessible without token
- Laravel Sanctum or Passport planned to be added

### Webhook Security
- Webhook does NOT require authentication
- Authenticity verification via `sign` field
- SDK automatically verifies RSA signature

### Error Handling
All endpoints return standard structure:
```json
{
    "code": "200",
    "message": "Success|Error message",
    "data": { ... }
}
```

## 🔗 Related Files

- **Application Routes**: `/routes/api/v1/routes.php`
- **Controller**: `/app/Http/Controllers/Api/v1/MuseWalletController.php`
- **SDK Controller**: `musewallet-sdk/src/Http/Controllers/MuseWalletController.php`
- **Requests**: `musewallet-sdk/src/Http/Requests/*.php`
- **Postman Collection**: `musewallet-sdk/postman/MuseWallet-SDK-Collection.json`

