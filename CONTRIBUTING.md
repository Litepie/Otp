# Contributing to Laravel OTP Package

Thank you for considering contributing to the Laravel OTP package! We welcome contributions from everyone.

## Code of Conduct

By participating in this project, you agree to abide by our code of conduct:

- Be respectful and inclusive
- Welcome newcomers and help them learn
- Focus on constructive feedback
- Respect differing opinions and experiences

## How to Contribute

### Reporting Bugs

1. **Check existing issues** first to avoid duplicates
2. **Use the bug report template** when creating a new issue
3. **Include detailed information**:
   - Laravel version
   - PHP version
   - Package version
   - Steps to reproduce
   - Expected vs actual behavior
   - Error messages/stack traces

### Suggesting Features

1. **Check existing feature requests** first
2. **Open a discussion** before implementing large features
3. **Provide clear use cases** and benefits
4. **Consider backward compatibility**

### Pull Requests

1. **Fork the repository** and create a feature branch
2. **Follow PSR-12 coding standards**
3. **Write tests** for new functionality
4. **Update documentation** as needed
5. **Ensure all tests pass**
6. **Keep PRs focused** - one feature/fix per PR

## Development Setup

```bash
# Clone your fork
git clone https://github.com/your-username/otp.git
cd otp

# Install dependencies
composer install

# Run tests
composer test

# Check code style
composer format

# Run static analysis
composer analyse
```

## Coding Standards

- Follow **PSR-12** coding style
- Use **type declarations** where possible
- Write **meaningful commit messages**
- Add **PHPDoc blocks** for public methods
- Use **descriptive variable names**

## Testing

- Write tests for all new features
- Ensure existing tests still pass
- Aim for high test coverage
- Use meaningful test names
- Test both success and failure scenarios

### Running Tests

```bash
# Run all tests
composer test

# Run tests with coverage
composer test-coverage

# Run specific test file
vendor/bin/phpunit tests/Unit/OtpTest.php
```

## Documentation

- Update README.md for new features
- Add examples to EXAMPLES.md
- Update configuration documentation
- Keep docblocks up to date

## Review Process

1. **Automated checks** must pass (tests, style, analysis)
2. **Code review** by maintainers
3. **Discussion** and feedback
4. **Approval** and merge

## Release Process

1. Update CHANGELOG.md
2. Update version in relevant files
3. Create release tag
4. Publish to Packagist

## Getting Help

- **Discussions**: Use GitHub Discussions for questions
- **Issues**: Use GitHub Issues for bugs and feature requests
- **Email**: Contact maintainers at support@litepie.com

## Recognition

Contributors will be:
- Listed in the Contributors section
- Credited in release notes
- Acknowledged in the community

Thank you for contributing! 🎉
