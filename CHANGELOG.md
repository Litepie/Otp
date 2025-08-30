# Changelog

All notable changes to the Laravel OTP package will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- Initial package setup
- OTP generation with customizable length and format
- Digital signing of OTP codes for enhanced security
- Multiple delivery channels (Email, SMS, Database)
- Configurable expiration times
- Rate limiting to prevent abuse
- Support for different OTP types (Login, Email Verification, Password Reset, etc.)
- Extensible channel system
- Queue support for sending OTPs
- Event system for OTP lifecycle
- Automatic cleanup of expired OTPs
- Comprehensive test suite
- Documentation and examples

### Security
- Digital signing of OTP codes
- Rate limiting per identifier
- Secure random code generation
- Protection against timing attacks
