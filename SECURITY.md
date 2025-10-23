# Security Policy

## Supported Versions

| Version | Supported          |
| ------- | ------------------ |
| 1.0.x   | :white_check_mark: |

## Reporting a Vulnerability

**Please do not report security vulnerabilities through public GitHub issues.**

Instead, please report security vulnerabilities by email to: artempuzik@example.com

You should receive a response within 48 hours. If for some reason you do not, please follow up via email to ensure we received your original message.

Please include the following information:

- Type of issue (e.g. buffer overflow, SQL injection, cross-site scripting, etc.)
- Full paths of source file(s) related to the issue
- Location of the affected source code (tag/branch/commit or direct URL)
- Any special configuration required to reproduce the issue
- Step-by-step instructions to reproduce the issue
- Proof-of-concept or exploit code (if possible)
- Impact of the issue, including how an attacker might exploit it

## Security Best Practices

When using this SDK:

1. **Protect API Keys**: Never commit private keys or API credentials to version control
2. **Use HTTPS**: Always use HTTPS endpoints in production
3. **Webhook Security**: Verify webhook signatures (enabled by default)
4. **Environment Variables**: Store sensitive data in `.env` file
5. **Rate Limiting**: Implement rate limiting on your API endpoints
6. **Logging**: Be careful not to log sensitive data (card numbers, CVV, etc.)

## What to Expect

After you submit a report:

1. We will acknowledge receipt of your vulnerability report
2. We will investigate and determine the severity
3. We will develop and test a fix
4. We will release a security patch
5. We will publicly disclose the vulnerability (with credit to reporter if desired)

Thank you for helping keep MuseWallet SDK and its users safe!

