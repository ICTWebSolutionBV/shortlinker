# Changelog

All notable changes to Shortlinker are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## Versioning policy

- **Major (`X.0.0`)** — Breaking changes to routes, database schema, or configuration that require manual intervention.
- **Minor (`1.X.0`)** — New user-facing features, backwards-compatible.
- **Patch (`1.0.X`)** — Bug fixes, performance improvements, copy/UI tweaks.

Every push that ships production-visible changes should bump the appropriate segment and add a dated entry below.

---

## [Unreleased]

_Nothing yet._

## [1.0.1] — 2026-05-12

### Fixed
- Short URL was not copied to clipboard after creating a link from the backend — `new_short_url` flash value was not included in Inertia's shared props.
- Create form showed a random alias preview that did not match the alias actually saved. The form now pre-fills with a random alias that is submitted as-is, so the preview and the created link are always the same. Toggling "Use a custom alias" off regenerates a new random alias; toggling it on allows a manual override.

## [1.0.0] — 2026-05-12

First release of Shortlinker — a self-hosted URL shortener built with Laravel and Vue.js.

### Added

#### URL shortening
- Public homepage — shorten any URL without an account.
- Custom aliases (min. 5 characters, alphanumeric, hyphens, underscores).
- Unambiguous auto-generated aliases that exclude confusing characters (`0`, `O`, `1`, `I`, `i`, `l`, `L`, `o`, `f`).
- Expiry presets: Never, 1 Hour, 2 Hours, 4 Hours, 6 Hours, 12 Hours, 1 Day, 2 Days, 3 Days, 5 Days, 7 Days, 14 Days, 30 Days. Default is **14 days**.
- Custom expiry — pick an exact date and time.
- Burn-after-read — link self-destructs after the first click; no click record is stored.
- QR code download — high-res PNG for any short link.
- Auto-copy — short URL copied to clipboard immediately on creation.

#### Click analytics
- Per-link analytics: daily and hourly charts, totals, browser, device, OS, country, and referrer breakdowns.
- IP-based geolocation via ip-api.com (cached 24 h).
- No-tracking option — disable click logging per link.
- Global platform stats for super admins: network-wide totals, 30-day trend, sortable links table with owner info.

#### Link management (authenticated)
- Dashboard with stat cards, 30-day trend chart, top 5 links, and recently created links.
- Full CRUD — create, edit, delete, bulk-delete.
- Search & filter by status (active / inactive / expired), sort by newest, oldest, or most clicks.
- Status badges — Active, Inactive, Expired.
- Link activation toggle — disable without deleting.

#### Authentication & 2FA
- Mandatory two-factor authentication (TOTP, email OTP, WebAuthn passkeys).
- Invite-only registration — no public sign-up.
- Password reset via email plus admin-initiated reset links.
- Admin-initiated 2FA reset forcing re-enrollment on next sign-in.
- `TWO_FACTOR_ENABLED` flag to skip 2FA enforcement locally.

#### Roles & administration
- Three-tier role model: `user`, `admin`, `super_admin`.
- Admins: manage users, send invites, reset passwords and 2FA.
- Super admins: all admin capabilities plus platform-wide link stats dashboard.
- `php artisan user:promote <email> <role>` for CLI role management.

#### Profile & preferences
- Light / dark / auto theme.
- Timezone and date format configurable per user.
- Passkey registration and revocation from the profile page.

#### Infrastructure
- Inertia.js v3 + Vue 3 + Tailwind CSS 4 frontend.
- Supports MySQL, PostgreSQL, and SQLite.
- In-app feedback widget (sends to `FEEDBACK_EMAIL`).
- In-app "What's new" changelog modal.

[Unreleased]: https://github.com/ICTWebSolutionBV/shortlinker/compare/v1.0.1...HEAD
[1.0.1]: https://github.com/ICTWebSolutionBV/shortlinker/compare/v1.0.0...v1.0.1
[1.0.0]: https://github.com/ICTWebSolutionBV/shortlinker/releases/tag/v1.0.0
