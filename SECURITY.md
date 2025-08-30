# Security Policy

## Supported Versions

We actively support the following versions with security updates:

| Version | Supported          |
| ------- | ------------------ |
| 1.x     | :white_check_mark: |

## Reporting a Vulnerability

We take security seriously. If you discover a security vulnerability, please follow these steps:

1. **Do not open an issue** - Security vulnerabilities should be reported privately
2. **Email us directly** at security@litepie.com
3. **Include detailed information**:
   - Description of the vulnerability
   - Steps to reproduce the issue
   - Potential impact
   - Any suggested fixes

## What to expect

- **Acknowledgment**: We'll acknowledge receipt of your report within 24 hours
- **Assessment**: We'll assess the vulnerability within 48 hours
- **Updates**: We'll keep you updated on our progress
- **Resolution**: We aim to resolve critical vulnerabilities within 7 days
- **Credit**: We'll credit you in our security advisory (unless you prefer to remain anonymous)

## Security Best Practices

When using this package:

1. **Keep dependencies updated** - Regularly update to the latest version
2. **Secure your signing secret** - Use a strong, unique secret for OTP signing
3. **Configure rate limiting** - Set appropriate rate limits to prevent abuse
4. **Monitor for suspicious activity** - Log and monitor OTP generation and verification attempts
5. **Use HTTPS** - Always transmit OTPs over secure connections
6. **Set appropriate expiration times** - Don't make OTPs valid for too long
7. **Limit attempts** - Configure maximum verification attempts

## Responsible Disclosure

We follow responsible disclosure practices and ask that you:

- Give us reasonable time to address the issue before public disclosure
- Don't access, modify, or delete data belonging to others
- Don't perform actions that could harm our users or systems
- Don't violate any laws or regulations

Thank you for helping keep our package and users secure!
