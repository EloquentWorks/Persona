# 🔐 Security Policy

## ✅ Supported Versions

Security updates are provided for the latest stable release of Laravel Persona.

| Version | Supported |
| --- | --- |
| 1.x | ✅ |
| < 1.0 | ❌ |

## 🚨 Reporting a Vulnerability

Please do not open public GitHub issues for security vulnerabilities.

Report security issues privately to the maintainers.

Include:

- A clear description of the issue
- Reproduction steps
- Impact assessment
- Affected versions
- Suggested fix, if known

## 🧾 Security Notes

Laravel Persona handles user-generated profile data.

Applications should:

- Authorize profile edits.
- Validate profile slugs.
- Validate public URLs.
- Escape rendered profile content.
- Avoid exposing private profiles.
- Avoid exposing unpublished profiles when publishing is required.
- Protect profile media uploads.
- Moderate comments when public interaction is enabled.

## 🔗 URL Safety

If your application allows website, social, or custom links, validate protocols and avoid rendering untrusted `javascript:` or unsafe URLs.

## 💬 Comment Safety

If comments are enabled, treat all comment bodies and author names as untrusted user-generated content.

Escape rendered content and add application-level moderation when needed.
