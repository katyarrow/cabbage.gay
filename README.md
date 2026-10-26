# Cabbage.gay

## Setup
### Requirements
- MariaDB or MySQL
- Nginx or Apache (ideally running PHP FPM)
- Node to build assets.
- Supervisor (set to run reverb)
- The usual set of laravel extensions for php (see [laravel docs](https://laravel.com/framework/docs/deployment#server-requirements))
- sodium php extension
- Cron jobs (see [laravel docs](https://laravel.com/framework/docs/12.x/scheduling#running-the-scheduler))
- Supervisor (set to run reverb, see next point)
- Reverb production set up (see [laravel docs](https://laravel.com/framework/docs/12.x/reverb#production))

Ideally a dockerized version of this setup will be created in future but for now a manual set up is required.

## Releases

### v2.0.1
26/10/2026 Minor bug fixes and changes for previous release.
- Fixed an issue with padding.
- Removed locally built assets from git.
- Added delete routine for expired rotas.
- Allowed touch events on rotas.

### v2.0.0
24/10/2026 Major update for 2026.
- Updated backend and frontend for security.
- Added rota system (still in beta)
- Added site wide messages.
- Added instruction page.
- Updated error pages.

### v1.2.0
28/03/2026 security update, donation prompt.
- Updated backend and frontend for security.
- Added a donation prompt when creating a meeting as well as a button in the header.

### v1.1.1
20/02/2026 Minor security update.
- Updated backend and frontend for security.

### v1.1.0
17/07/2025 Fixing bugs, adding minor features, etc.
- Updated backend and frontend for security.
- Added admin error notifications.
- Customised the error pages.
- Created an automated end-end test suite using laravel dusk.
- Clear selected day when switching to adding availability (this was causing border issues)
- Fixed data being cleared when submission fails due to network issues.
- Automatically refresh CSRF tokens on submission if needed.
- Added disco animation.
- Allow swiping on optional period meeting when not in add availability mode.
- Added releases page.
- Fixed issue when increasing size of viewport from on last page of a meeting.
- Added donation page.
- Made various code improvements.

### v1.0.0
16/03/2025 The initial release of the system.
- Creating encrypted optional period and entire period meetings.
- Added ability of adding availability to a poll.
- Added ability to remove availability from a poll
- Added destruction dates.
- Added FAQ page.