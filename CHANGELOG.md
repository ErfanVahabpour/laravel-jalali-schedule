# Changelog

All notable changes to `erfanvahabpour/laravel-jalali-schedule` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2026-10-09

### Added
- Initial release of Laravel Jalali Schedule.
- Fluent monthly scheduling macros: `jalaliMonthly`, `jalaliMonthlyOn`, `jalaliTwiceMonthly`, `jalaliLastDayOfMonth`, and `jalaliDaysOfMonth`.
- Seasonal and annual scheduling macros: `jalaliQuarterly`, `jalaliQuarterlyOn`, `jalaliYearly`, and `jalaliYearlyOn`.
- Conditional execution filters for custom intervals: `whenJalaliDay`, `whenJalaliLastDayOfMonth`, and `whenJalaliMonth`.
- Iranian working week and weekend scheduling helpers: `iranWeekdays`, `iranWeekends`, `shanbeh`, and `jomeh`.
- Laravel Auto-Discovery support via `JalaliScheduleServiceProvider`.
- Full IDE autocompletion stub generator and publishable `_ide_helper_jalali_schedule.php`.
- Complete test suite with 100% assertions coverage.
