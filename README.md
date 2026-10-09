# Laravel Jalali Schedule

[![Latest Version on Packagist](https://img.shields.io/packagist/v/erfanvahabpour/laravel-jalali-schedule.svg?style=flat-square)](https://packagist.org/packages/erfanvahabpour/laravel-jalali-schedule)
[![Total Downloads](https://img.shields.io/packagist/dt/erfanvahabpour/laravel-jalali-schedule.svg?style=flat-square)](https://packagist.org/packages/erfanvahabpour/laravel-jalali-schedule)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![PHP Version](https://img.shields.io/badge/PHP-8.2%20--%208.4-777bb4.svg?style=flat-square&logo=php)](https://php.net)
[![Laravel Version](https://img.shields.io/badge/Laravel-10%20%7C%2011%20%7C%2012-ff2d20.svg?style=flat-square&logo=laravel)](https://laravel.com)

Fluent Jalali (Solar Hijri / هجری شمسی) scheduling macros for Laravel tasks and console commands.

> 🇮🇷 **[برای مشاهده مستندات به زبان فارسی کلیک کنید (Persian Documentation)](README.fa.md)**

---

## The Problem

Standard Cron expressions only understand Gregorian calendar fields (`minute hour day-of-month month day-of-week`). As a result, native Laravel scheduler methods like `monthly()`, `monthlyOn()`, `quarterly()`, and `yearly()` only trigger on Gregorian dates.

Before this package, scheduling tasks for Iranian business cycles (such as payroll on the 1st of each Shamsi month or VAT reports every season) required verbose, repetitive closures:

```php
// ❌ The cumbersome way (Before)
Schedule::command('payroll:process')
    ->dailyAt('00:15')
    ->when(fn () => verta()->day === 1)
    ->withoutOverlapping()
    ->onOneServer();
```

With **Laravel Jalali Schedule**, it becomes expressive, clean, and fluent:

```php
// ✅ The clean way (After)
Schedule::command('payroll:process')
    ->jalaliMonthlyOn(1, '00:15')
    ->withoutOverlapping()
    ->onOneServer();
```

---

## Features

- 📅 **Fluent Monthly Scheduling:** Run tasks on the 1st, 15th, specific days, or dynamically on the last day of each Jalali month (handling 29, 30, and 31-day months seamlessly).
- 🍂 **Iranian Seasons & Quarters:** Easily schedule quarterly reports on the 1st of Farvardin (Bahar), Tir (Tabestan), Mehr (Paeez), and Dey (Zemestan).
- 🌱 **Nowruz & Annual Tasks:** Run jobs on Farvardin 1st or custom Jalali month and day.
- 🏢 **Iranian Work Week Helpers:** Built-in macros for Saturday through Wednesday (`iranWeekdays`), Thursday and Friday (`iranWeekends`), and individual days (`shanbeh`, `jomeh`).
- ⚡ **Zero Configuration:** Automatically registered via Laravel Package Discovery.
- 🧪 **Time Travel & Test Friendly:** Uses `Date::now($tz)` so `Carbon::setTestNow()` and `$this->travelTo()` work seamlessly in tests.
- 💡 **PhpStorm Autocomplete:** Includes IDE helper stubs so you get zero "Method not found" warnings and full parameter hinting.

---

## Installation

Install the package via Composer:

```bash
composer require erfanvahabpour/laravel-jalali-schedule
```

The service provider is automatically registered via Laravel's package discovery.

### Optional: PhpStorm Autocomplete Support

To enable autocompletion and prevent PhpStorm from reporting *"Method not found in Event"*, publish the IDE helper stub:

```bash
php artisan vendor:publish --tag=jalali-schedule-ide-helper
```

This publishes `_ide_helper_jalali_schedule.php` to your application's root directory, which PhpStorm indexes immediately.

---

## Usage & API Reference

### 1. Monthly Scheduling

```php
use Illuminate\Support\Facades\Schedule;

// Run on the 1st day of every Jalali month at 00:00 (or custom time)
Schedule::command('reports:monthly')->jalaliMonthly('01:00');

// Run on a specific day of every Jalali month at a given time
Schedule::command('attendance:reconcile')->jalaliMonthlyOn(1, '00:15');

// Run twice a month (defaults to 1st and 16th, or specify custom days)
Schedule::command('payroll:advances')->jalaliTwiceMonthly(1, 15, '03:00');

// Run on the last day of the Jalali month (dynamically handles 31, 30, or 29/30 leap Esfand)
Schedule::command('finance:close-books')->jalaliLastDayOfMonth('23:50');

// Run on specific days of the month (e.g. 5th, 15th, and 25th)
Schedule::command('reminders:send')->jalaliDaysOfMonth([5, 15, 25], '09:00');
```

---

### 2. Quarterly & Yearly Scheduling

In the Jalali calendar, quarters represent the four Iranian seasons:
- **Q1 (Bahar):** Months 1, 2, 3 (Farvardin, Ordibehesht, Khordad)
- **Q2 (Tabestan):** Months 4, 5, 6 (Tir, Mordad, Shahrivar)
- **Q3 (Paeez):** Months 7, 8, 9 (Mehr, Aban, Azar)
- **Q4 (Zemestan):** Months 10, 11, 12 (Dey, Bahman, Esfand)

```php
// Run on the 1st of each Jalali quarter (Farvardin 1, Tir 1, Mehr 1, Dey 1)
Schedule::command('tax:seasonal-vat')->jalaliQuarterly('02:00');

// Run on a specific day of the first month of each quarter (e.g. Day 15)
Schedule::command('audit:quarterly')->jalaliQuarterlyOn(15, '04:00');

// Run annually on Nowruz (Farvardin 1st)
Schedule::command('greeting:nowruz')->jalaliYearly('08:00');

// Run annually on a specific Jalali month and day (e.g. Esfand 29th)
Schedule::command('fiscal:year-end-closing')->jalaliYearlyOn(12, 29, '23:00');
```

---

### 3. Condition-Only Filters (For Custom Timing)

If you have a task with a non-daily frequency (such as `hourly()` or `twiceDaily()`) and want to restrict it to specific Jalali dates:

```php
// Run hourly, but only on the 1st day of the Jalali month
Schedule::command('sync:final-attendance')
    ->hourly()
    ->whenJalaliDay(1);

// Run every 2 hours, but only on the last day of the Jalali month
Schedule::command('monitor:closing-activity')
    ->everyTwoHours()
    ->whenJalaliLastDayOfMonth();

// Run every minute during the month of Esfand (Month 12)
Schedule::command('countdown:nowruz')
    ->everyMinute()
    ->whenJalaliMonth(12);
```

---

### 4. Iranian Working Week & Weekends

Standard Laravel methods `weekdays()` (Mon–Fri) and `weekends()` (Sat–Sun) correspond to Western business calendars. This package provides Iranian equivalents:

```php
// Saturday through Wednesday (Shanbeh to Chaharshanbeh)
Schedule::command('office:morning-sync')->iranWeekdays('07:30');

// Thursday and Friday (Panjshanbeh and Jomeh)
Schedule::command('backup:weekend-database')->iranWeekends('12:00');

// Specific Iranian day of the week
Schedule::command('digest:weekly')->shanbeh('08:00'); // Saturday
Schedule::command('cleanup:weekly')->jomeh('23:00');   // Friday
```

---

## Method Reference Summary

| Method | Parameters | Description |
| :--- | :--- | :--- |
| `jalaliMonthly` | `string $time = '0:0'` | 1st of every Jalali month |
| `jalaliMonthlyOn` | `int $day = 1, string $time = '0:0'` | Specific day of every Jalali month |
| `jalaliTwiceMonthly` | `int $first = 1, int $second = 16, string $time = '0:0'` | Twice per Jalali month |
| `jalaliLastDayOfMonth`| `string $time = '0:0'` | Last day of the Jalali month (dynamic 29/30/31) |
| `jalaliDaysOfMonth` | `array\|int ...$days, string $time = '0:0'` | Specific days of the Jalali month |
| `jalaliQuarterly` | `string $time = '0:0'` | 1st day of each Jalali quarter (Farvardin 1, Tir 1, Mehr 1, Dey 1) |
| `jalaliQuarterlyOn` | `int $day = 1, string $time = '0:0'` | Specific day of the 1st month of each quarter |
| `jalaliYearly` | `string $time = '0:0'` | Annually on Nowruz (Farvardin 1st) |
| `jalaliYearlyOn` | `int $month = 1, int $day = 1, string $time = '0:0'` | Annually on specific Jalali month & day |
| `whenJalaliDay` | `int\|array ...$days` | Filter closure for custom intervals |
| `whenJalaliLastDayOfMonth` | - | Filter closure for the last day of the month |
| `whenJalaliMonth` | `int\|array ...$months` | Filter closure for specific month(s) |
| `iranWeekdays` | `string $time = '0:0'` | Saturday through Wednesday (`6,0,1,2,3`) |
| `iranWeekends` | `string $time = '0:0'` | Thursday and Friday (`4,5`) |
| `shanbeh` | `string $time = '0:0'` | Saturdays |
| `jomeh` | `string $time = '0:0'` | Fridays |

---

## Testing

Run tests with PHPUnit:

```bash
composer test
```

---

## Changelog

Please see [CHANGELOG.md](CHANGELOG.md) for details on recent updates.

---

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

---

## Author

Developed by **[Erfan Vahabpour](https://github.com/ErfanVahabpour)**  
- Email: erfanvahabpour@yahoo.com  
- Website: [erfanvahabpour.ir](https://erfanvahabpour.ir/)
