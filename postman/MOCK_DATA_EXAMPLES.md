# Mock Data Examples for MuseWallet SDK

Ready-to-use valid data for copying and using in Postman or tests.

## 👤 User 1: John Doe (USA)

### Create Card Holder (Full)
```json
{
    "user_xid": "user_john_doe_001",
    "email": "john.doe@example.com",
    "user_name": "johndoe",
    "individual": {
        "first_name": "John",
        "last_name": "Doe",
        "date_of_birth": "1990-05-15",
        "occupation": "Software Engineer",
        "annual_income": "100000"
    },
    "address": {
        "country": "US",
        "city": "New York",
        "post_code": "10001",
        "details": "123 Main Street, Apt 4B"
    },
    "document": {
        "type": "2",
        "number": "P12345678",
        "country": "US",
        "expiry_date": "2030-12-31",
        "front": "data:image/jpeg;base64,/9j/4AAQSkZJRg...",
        "back": "data:image/jpeg;base64,/9j/4AAQSkZJRg...",
        "face": "data:image/jpeg;base64,/9j/4AAQSkZJRg..."
    }
}
```

### Apply for Card
```json
{
    "user_id": "8000123",
    "request_id": "req_john_001",
    "card_product_id": "prod_visa_virtual_01",
    "card_level": "1",
    "phone_number": "5551234567",
    "phone_area_code": "1",
    "embossed_name": "JOHN DOE"
}
```

## 👤 User 2: Jane Smith (UK)

### Create Card Holder (Minimal)
```json
{
    "user_xid": "user_jane_smith_002",
    "email": "jane.smith@example.com",
    "individual": {
        "first_name": "Jane",
        "last_name": "Smith",
        "date_of_birth": "1985-08-22"
    },
    "address": {
        "country": "GB",
        "city": "London",
        "post_code": "SW1A 1AA",
        "details": "10 Downing Street"
    }
}
```

### Upload KYC
```json
{
    "user_xid": "user_jane_smith_002",
    "individual": {
        "first_name": "Jane",
        "last_name": "Smith",
        "date_of_birth": "1985-08-22",
        "occupation": "Business Owner",
        "annual_income": "150000"
    },
    "document": {
        "type": "1",
        "number": "ID987654321",
        "country": "GB",
        "expiry_date": "2028-06-30",
        "front": "data:image/jpeg;base64,/9j/4AAQSkZJRg...",
        "back": "data:image/jpeg;base64,/9j/4AAQSkZJRg...",
        "face": "data:image/jpeg;base64,/9j/4AAQSkZJRg..."
    },
    "address": {
        "country": "GB",
        "city": "London",
        "post_code": "SW1A 1AA",
        "details": "10 Downing Street"
    }
}
```

## 👤 User 3: Maria Garcia (Spain)

### Create Card Holder
```json
{
    "user_xid": "user_maria_garcia_003",
    "email": "maria.garcia@example.es",
    "user_name": "mariagarcia",
    "individual": {
        "first_name": "Maria",
        "last_name": "Garcia",
        "date_of_birth": "1992-03-10",
        "occupation": "Marketing Manager",
        "annual_income": "75000"
    },
    "address": {
        "country": "ES",
        "city": "Barcelona",
        "post_code": "08001",
        "details": "Carrer de la Pau, 25"
    }
}
```

## 👤 User 4: Wei Chen (Singapore)

### Create Card Holder
```json
{
    "user_xid": "user_wei_chen_004",
    "email": "wei.chen@example.sg",
    "individual": {
        "first_name": "Wei",
        "last_name": "Chen",
        "date_of_birth": "1988-11-25",
        "occupation": "Financial Analyst",
        "annual_income": "120000"
    },
    "address": {
        "country": "SG",
        "city": "Singapore",
        "post_code": "018956",
        "details": "1 Raffles Place, #50-01"
    },
    "document": {
        "type": "1",
        "number": "S1234567A",
        "country": "SG",
        "expiry_date": "2029-11-25",
        "front": "data:image/jpeg;base64,/9j/4AAQSkZJRg...",
        "face": "data:image/jpeg;base64,/9j/4AAQSkZJRg..."
    }
}
```

## 💳 Card Operations Examples

### Top Up Card
```json
{
    "request_id": "topup_001",
    "card_id": "card_123456789",
    "user_id": "8000123",
    "amount": "100.00",
    "currency": "USDT"
}
```

### Activate Card
```json
{
    "user_id": "8000123",
    "card_id": "card_123456789",
    "request_id": "activate_001"
}
```

### Query Apply Result
```json
{
    "request_id": "req_john_001",
    "user_id": "8000123",
    "apply_id": "apply_123456"
}
```

### Get Card Info
```json
{
    "card_id": "card_123456789",
    "user_id": "8000123"
}
```

## 📝 Document Types

| Type | Description | Countries |
|------|-------------|-----------|
| `1` | National ID | Most countries |
| `2` | Passport | International |

## 🎚️ Card Levels

| Level | Name | Description |
|-------|------|-------------|
| `1` | Basic | Entry level card |
| `2` | Silver | Mid-tier card |
| `3` | Gold | Premium card |
| `4` | Platinum | High-tier card |
| `5` | Black | Elite card |

## 💰 Supported Currencies

- `USDT` - Tether USD
- `USDC` - USD Coin
- `EUR` - Euro
- `GBP` - British Pound

## 🌍 Country Codes (ISO 3166-1 alpha-2)

| Code | Country |
|------|---------|
| `US` | United States |
| `GB` | United Kingdom |
| `ES` | Spain |
| `FR` | France |
| `DE` | Germany |
| `IT` | Italy |
| `BR` | Brazil |
| `SG` | Singapore |
| `JP` | Japan |
| `KR` | South Korea |
| `AU` | Australia |
| `CA` | Canada |

## 📞 Phone Area Codes

| Code | Country/Region |
|------|----------------|
| `1` | USA/Canada |
| `44` | UK |
| `34` | Spain |
| `33` | France |
| `49` | Germany |
| `55` | Brazil |
| `65` | Singapore |
| `81` | Japan |
| `82` | South Korea |
| `61` | Australia |

## 🔐 Base64 Image Prefixes

Always add the correct MIME type:

```
data:image/jpeg;base64,{YOUR_BASE64_STRING}
data:image/png;base64,{YOUR_BASE64_STRING}
```

### Generate Base64 from File

**Linux/Mac:**
```bash
base64 -i passport_front.jpg | tr -d '\n' > front_base64.txt
echo "data:image/jpeg;base64,$(cat front_base64.txt)"
```

**Windows (PowerShell):**
```powershell
$bytes = [System.IO.File]::ReadAllBytes("passport_front.jpg")
$base64 = [System.Convert]::ToBase64String($bytes)
"data:image/jpeg;base64,$base64"
```

**PHP:**
```php
$imageData = file_get_contents('passport_front.jpg');
$base64 = base64_encode($imageData);
$dataUri = "data:image/jpeg;base64," . $base64;
```

**JavaScript/Node.js:**
```javascript
const fs = require('fs');
const imageBuffer = fs.readFileSync('passport_front.jpg');
const base64 = imageBuffer.toString('base64');
const dataUri = `data:image/jpeg;base64,${base64}`;
```

## 🧪 Test Scenarios

### Scenario 1: Complete Card Creation Flow

1. **Get Partner Balance** → Get current balance
2. **Create Card Holder** → Create user (John Doe)
3. **Get Card Products** → Select card product
4. **Apply for Card** → Submit application
5. **Query Apply Result** → Check status
6. **Activate Card** → Activate card
7. **Top Up Card** → Add balance

### Scenario 2: KYC Verification

1. **Create Card Holder** (Minimal) → Create user without documents (Jane Smith)
2. **Generate KYC Link** → Get KYC link
3. **Upload KYC Documents** → Upload documents

### Scenario 3: Webhook Testing

1. Send test webhook **Card Created**
2. Send test webhook **Transaction Completed**
3. Send test webhook **Top Up Completed**

## 📊 Expected API Responses

### Success Response
```json
{
    "code": "200",
    "message": "Success",
    "data": {
        // response data
    }
}
```

### Error Response
```json
{
    "code": "400",
    "message": "Invalid request",
    "errors": {
        "field_name": ["error message"]
    }
}
```

## ⚠️ Important Notes

1. **Birth dates** must be in the past
2. **Document expiry dates** must be in the future
3. **Email** must be valid
4. **Country codes** only in ISO 3166-1 alpha-2 format (2 characters)
5. **Base64 images** must include MIME type prefix
6. **Amounts** must be > 0
7. **Card level** must be between 1 and 5

## 🔄 Automated Tests

You can use this data in automated tests:

```php
// PHPUnit test example
public function test_create_card_holder()
{
    $response = $this->postJson('/api/v1/musewallet/card-holders', [
        'user_xid' => 'test_user_001',
        'email' => 'test@example.com',
        'individual' => [
            'first_name' => 'Test',
            'last_name' => 'User',
            'date_of_birth' => '1990-01-01'
        ],
        'address' => [
            'country' => 'US',
            'city' => 'New York',
            'post_code' => '10001',
            'details' => '123 Test St'
        ]
    ]);

    $response->assertStatus(200);
}
```
