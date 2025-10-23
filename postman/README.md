# MuseWallet SDK Postman Collection

Complete API request collection for testing MuseWallet SDK with valid mock data.

## 📦 Contents

- **MuseWallet-SDK-Collection.json** - Main collection with 13 requests
- **MuseWallet-Environment.json** - Environment file with variables
- **README.md** - This documentation
- **MOCK_DATA_EXAMPLES.md** - Ready-to-use mock data examples
- **ROUTES_MAPPING.md** - Complete routes mapping

## 🚀 Quick Start

### 1. Import to Postman

1. Open Postman
2. Click **Import** in the top left corner
3. Select file `MuseWallet-SDK-Collection.json`
4. Import file `MuseWallet-Environment.json`
5. Select **MuseWallet SDK Environment** in the top right corner

### 2. Configure Variables

Update the following variables in the environment:

| Variable | Description | Example |
|----------|-------------|---------|
| `base_url` | Your API URL | `http://localhost:8000` |
| `currency` | Currency for operations | `USDT` |
| `user_id` | MuseWallet user ID | `8000123` |
| `user_xid` | Your external user ID | `user_ext_123456` |
| `card_product_id` | Card product ID | `prod_visa_virtual_01` |
| `card_id` | Card ID | `card_123456789` |

### 3. Testing Sequence

Recommended order of request execution:

1. **Get Partner Balance** - Check partner balance
2. **Get Card Products** - Get list of available products
3. **Create Card Holder** - Create card holder
   - Save `user_id` from response
4. **Apply for Card** - Submit card application
   - Save `request_id` and `apply_id` from response
5. **Query Apply Result** - Check application status
6. **Get Card Info** - Get card information
   - Save `card_id` from response
7. **Activate Card** - Activate card
8. **Top Up Card** - Top up card balance
9. **Upload KYC Documents** - Upload KYC documents
10. **Generate KYC Link** - Generate KYC verification link

## 📋 Collection Structure

### Partner Operations

- **Get Partner Balance** - `POST /api/v1/musewallet/balance/partner`
- **Get Card Products** - `GET /api/v1/musewallet/card-products`

### Card Holder Management

- **Create Card Holder (Full)** - `POST /api/v1/musewallet/carduser/create`
  - Full version (with documents)
- **Create Card Holder (Minimal)** - `POST /api/v1/musewallet/carduser/create`
  - Minimal version (without documents)

### Card Operations

- **Apply for Card** - `POST /api/v1/musewallet/card/apply`
- **Query Apply Result** - `POST /api/v1/musewallet/card/apply-result`
- **Get Card Info** - `POST /api/v1/musewallet/card/query`
- **Activate Card** - `POST /api/v1/musewallet/card/activate`
- **Top Up Card** - `POST /api/v1/musewallet/cardaccount/topup`

### KYC Operations

- **Upload KYC Documents** - `POST /api/v1/musewallet/carduser/upload-kyc`
- **Generate KYC Link** - `POST /api/v1/musewallet/carduser/kyc-link`

### Webhooks

- **Webhook - Card Created** - `POST /api/v1/musewallet/webhook`
- **Webhook - Application Approved** - `POST /api/v1/musewallet/webhook`
- **Webhook - Transaction Completed** - `POST /api/v1/musewallet/webhook`
- **Webhook - Top Up Completed** - `POST /api/v1/musewallet/webhook`
- **Webhook - KYC Approved** - `POST /api/v1/musewallet/webhook`

## 🔑 Important Fields (According to MusePay API v1)

### Individual (Personal Information)

```json
{
  "individual": {
    "first_name": "John",           // required
    "last_name": "Doe",              // required
    "date_of_birth": "1990-05-15",   // required (YYYY-MM-DD)
    "occupation": "Engineer",         // optional
    "annual_income": "100000"         // optional
  }
}
```

### Document (Identity Documents)

```json
{
  "document": {
    "type": "1",                      // required (1=National ID, 2=Passport)
    "number": "ID123456",             // required
    "country": "US",                  // required (ISO 3166-1 alpha-2)
    "expiry_date": "2030-12-31",     // required (YYYY-MM-DD)
    "front": "data:image/jpeg;...",   // required (base64)
    "back": "data:image/jpeg;...",    // optional (base64)
    "face": "data:image/jpeg;..."     // optional (base64, selfie)
  }
}
```

### Address (Address Information)

```json
{
  "address": {
    "country": "US",                  // required (ISO 3166-1 alpha-2)
    "city": "New York",               // required
    "post_code": "10001",             // required
    "details": "123 Main Street"      // required
  }
}
```

## 🎯 Mock Data

All requests in the collection contain valid mock data:

- **Email**: `john.doe@example.com`, `jane.smith@example.com`
- **Phone**: `5551234567` (area code: `1`)
- **Addresses**: USA, United Kingdom
- **Document Types**:
  - `1` - National ID
  - `2` - Passport
- **Card Levels**: `1` (Basic), `2` (Silver), `3` (Gold), `4` (Platinum), `5` (Black)
- **Currencies**: `USDT`, `USDC`, `EUR`, `GBP`

## 🔒 Base64 Images

Requests use placeholder Base64 strings. For real testing, replace them with actual images:

```bash
# Convert image to Base64
base64 -i document_front.jpg | tr -d '\n' > front_base64.txt
```

Then add prefix: `data:image/jpeg;base64,{BASE64_STRING}`

## 🧪 Automatic Data Generation

The collection uses built-in Postman variables:

- `{{$randomUUID}}` - Generates UUID for `user_xid` and `request_id`
- `{{$timestamp}}` - Current timestamp
- `{{user_id}}`, `{{card_id}}` - Environment variables

## 📚 Additional Information

- **API Version**: v1
- **SDK Version**: 1.0.0
- **Documentation**: https://docs-card.musepay.io
- **Base URL**: Configured in environment variables

## 🐛 Troubleshooting

### Error 422 (Validation Error)

Check:
- Date format is correct (`YYYY-MM-DD`)
- Country codes are in ISO 3166-1 alpha-2 format (2 letters)
- Base64 strings start with `data:image/jpeg;base64,`
- All required fields are filled

### Error 401 (Unauthorized)

- Ensure API keys are configured in application `.env`
- Check request signature

### Error 404

- Check `base_url` variable
- Ensure routes are registered in the application

## 📝 Field Corrections (vs MusePay API Documentation)

All field names have been corrected to match MusePay Card API v1 specification:

### Individual Fields
- ✅ `individual.date_of_birth` (not `birthday`)
- ✅ `individual.occupation` (added, optional)
- ✅ `individual.annual_income` (added, optional)

### Document Fields
- ✅ `document.front` (not `front_image`)
- ✅ `document.back` (not `back_image`)
- ✅ `document.face` (not `selfie_image`)
- ✅ `document.country` (added, required)
- ✅ `document.expiry_date` (added, required)

### Address Fields
- ✅ `address.post_code` (not `postal_code`)
- ✅ `address.details` (not `address_line1` or `address_line2`)

## 📞 Support

For questions and issues:
- GitHub: https://github.com/artempuzik/musewallet-sdk
- Documentation: See `musewallet-sdk/README.md`
- Field Mapping: See `postman/ROUTES_MAPPING.md`
- Mock Data: See `postman/MOCK_DATA_EXAMPLES.md`
