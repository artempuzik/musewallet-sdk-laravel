# Changelog

All notable changes to `musewallet-sdk` will be documented in this file.

## [1.0.0] - 2024-10-23

### Added
- Initial release of MuseWallet SDK for Laravel
- Full integration with MusePay Card API
- Support for all card operations (create, activate, query, top-up)
- Card holder management endpoints
- KYC verification support (upload documents and generate KYC links)
- Balance checking functionality
- Event-driven webhook handling with 10 event types
- Comprehensive error codes with descriptions and suggestions
- Automatic retry logic with exponential backoff
- Response caching support
- Detailed logging capabilities
- RSA SHA1 signature generation
- Webhook signature verification
- Comprehensive test suite (Unit and Feature tests)
- Full PHPDoc annotations and type hints
- Laravel auto-discovery support
- Configuration publishing
- Facade support for easy usage

### Events
- CardCreatedEvent - Dispatched when a card is created
- CardActivatedEvent - Dispatched when a card is activated
- CardBlockedEvent - Dispatched when a card is blocked
- TransactionCompletedEvent - Dispatched when a transaction completes
- TransactionFailedEvent - Dispatched when a transaction fails
- TopUpCompletedEvent - Dispatched when a top-up completes
- KycApprovedEvent - Dispatched when KYC is approved
- KycRejectedEvent - Dispatched when KYC is rejected
- ApplicationApprovedEvent - Dispatched when application is approved
- ApplicationRejectedEvent - Dispatched when application is rejected

### Enums
- ApplyStatus - Card application statuses
- CardStatus - Card operational statuses
- CardLevel - Card tier levels (1-5)
- KycStatus - KYC verification statuses
- Currency - Supported cryptocurrencies
- DocumentType - KYC document types

### Services
- MuseWalletService - Main service class
- SignatureParameters - Signature field extraction
- MuseWalletErrorCodes - Error code definitions
- MuseWalletResponseFormatter - Response formatting utilities

### Configuration
- Comprehensive configuration file with all options
- Environment variable support
- Testing mode configuration
- Cache configuration
- Logging configuration
- Webhook configuration
- Event configuration

### Testing
- 100+ test cases
- Unit tests for all components
- Feature tests for service integration
- Event dispatching tests
- Mock HTTP responses for testing

### Documentation
- Comprehensive README with usage examples
- Code-level documentation with PHPDoc
- Configuration documentation
- Event handling examples
- Error handling guide

## Future Enhancements

### Planned for v1.1.0
- Card limit management endpoints
- Card replacement functionality
- Transaction verification endpoints
- Additional webhook events
- Enhanced caching strategies
- Rate limiting support
- Batch operations support

### Planned for v2.0.0
- Support for multiple MusePay accounts
- Advanced transaction filtering
- Card statement generation
- Dispute management
- Enhanced analytics
- GraphQL support
- Real-time notifications via WebSockets

## Support

For issues, questions, or feature requests, please use the GitHub issue tracker.

