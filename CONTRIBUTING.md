# Contributing to MuseWallet SDK

Thank you for considering contributing to the MuseWallet SDK! This document outlines the process for contributing to this project.

## Code of Conduct

Please be respectful and constructive in all interactions with the community.

## How Can I Contribute?

### Reporting Bugs

Before creating bug reports, please check existing issues. When creating a bug report, include:

- A clear and descriptive title
- Steps to reproduce the issue
- Expected behavior vs actual behavior
- MusePay API version
- SDK version
- Laravel version
- PHP version
- Any error messages or logs

### Suggesting Enhancements

Enhancement suggestions are welcome! Please provide:

- A clear and descriptive title
- Detailed description of the suggested enhancement
- Use case and benefits
- Possible implementation approach

### Pull Requests

1. **Fork the repository** and create your branch from `main`
2. **Follow PSR-12** coding standards
3. **Write or update tests** for your changes
4. **Update documentation** if needed
5. **Ensure tests pass**: `composer test`
6. **Follow commit message conventions**:
   - `feat:` for new features
   - `fix:` for bug fixes
   - `docs:` for documentation
   - `test:` for tests
   - `refactor:` for code refactoring

## Development Setup

```bash
# Clone your fork
git clone https://github.com/YOUR_USERNAME/musewallet-sdk.git
cd musewallet-sdk

# Install dependencies
composer install

# Run tests
composer test

# Run static analysis
composer analyse
```

## Testing Guidelines

- Write tests for all new features
- Ensure all tests pass before submitting PR
- Maintain or improve code coverage (currently 97%)
- Follow existing test structure:
  - Unit tests in `tests/Unit/`
  - Feature tests in `tests/Feature/`

## Documentation

- Update README.md for user-facing changes
- Add PHPDoc blocks for all public methods
- Update CHANGELOG.md following [Keep a Changelog](https://keepachangelog.com/)
- Include examples for new features

## Coding Standards

- Follow PSR-12 coding standard
- Use type hints for all parameters and return types
- Write descriptive variable and method names
- Keep methods focused and single-purpose
- Add comments for complex logic

## Questions?

Feel free to open an issue for any questions about contributing!

## License

By contributing, you agree that your contributions will be licensed under the MIT License.

