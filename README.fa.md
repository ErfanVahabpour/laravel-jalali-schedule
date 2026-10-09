# زمان‌بندی دستورات لاراول بر پایه تقویم هجری شمسی (جلالی)

[![Latest Version on Packagist](https://img.shields.io/packagist/v/erfanvahabpour/laravel-jalali-schedule.svg?style=flat-square)](https://packagist.org/packages/erfanvahabpour/laravel-jalali-schedule)
[![Total Downloads](https://img.shields.io/packagist/dt/erfanvahabpour/laravel-jalali-schedule.svg?style=flat-square)](https://packagist.org/packages/erfanvahabpour/laravel-jalali-schedule)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![PHP Version](https://img.shields.io/badge/PHP-8.2%20--%208.4-777bb4.svg?style=flat-square&logo=php)](https://php.net)
[![Laravel Version](https://img.shields.io/badge/Laravel-10%20%7C%2011%20%7C%2012-ff2d20.svg?style=flat-square&logo=laravel)](https://laravel.com)

مجموعه‌ای از ماکروهای روان، خوانا و استاندارد برای زمان‌بندی کارها (Task Scheduling) در لاراول، متناسب با تقویم هجری شمسی، فصل‌ها و روزهای کاری ایران.

> 🇺🇸 **[View English Documentation](README.md)**

---

## چرا این پکیج؟

ساختار استاندارد عبارات Cron در سیستم‌های یونیکس و فریم‌ورک لاراول، صرفاً فیلدهای گاه‌شماری میلادی (`دقیقه ساعت روز_ماه ماه روز_هفته`) را پردازش می‌کند. بنابراین متدهای پیش‌فرض لاراول مانند `monthly()`، `monthlyOn()`، `quarterly()` و `yearly()` منحصراً بر اساس ماه‌ها و سال‌های میلادی کار می‌کنند.

در نرم‌افزارهای سازمانی ایرانی (مانند سامانه‌های حقوق و دستمزد، حضور و غیاب، حسابداری و گزارش‌های فصلی مالیات)، توسعه‌دهندگان معمولاً مجبور به نوشتن شرط‌های دستی و تکراری در فایل `console.php` بودند:

```php
// ❌ روش قدیمی و دشوار (قبل از پکیج)
Schedule::command('payroll:process')
    ->dailyAt('00:15')
    ->when(fn () => verta()->day === 1)
    ->withoutOverlapping()
    ->onOneServer();
```

با استفاده از این پکیج، کد به صورت کاملاً روان، رسا و هم‌گام با سینتکس اصلی لاراول نوشته می‌شود:

```php
// ✅ روش استاندارد و تمیز (با این پکیج)
Schedule::command('payroll:process')
    ->jalaliMonthlyOn(1, '00:15')
    ->withoutOverlapping()
    ->onOneServer();
```

---

## ویژگی‌ها

- 📅 **زمان‌بندی ماهانه کامل:** اجرای کارها در روز اول، روزهای خاص یا به صورت پویا در **آخرین روز ماه شمسی** (مدیریت خودکار ماه‌های ۳۱ روزه نیمه اول سال، ۳۰ روزه پاییز و زمستان، و ۲۹ یا ۳۰ روزه اسفند در سال‌های کبیسه).
- 🍂 **فصل‌های چهارگانه شمسی:** زمان‌بندی خودکار در اول فصل‌های بهار (۱ فروردین)، تابستان (۱ تیر)، پاییز (۱ مهر) و زمستان (۱ دی) جهت تسویه و گزارش‌های فصلی ارزش افزوده.
- 🌱 **تبریک نوروز و کارهای سالانه:** اجرای خودکار در روز اول فروردین یا هر ماه و روز شمسی مشخص.
- 🏢 **روزهای کاری و تعطیلات ایران:** ماکروهای آماده برای شنبه تا چهارشنبه (`iranWeekdays`)، پنج‌شنبه و جمعه (`iranWeekends`) و روزهای هفته (`shanbeh`، `jomeh`).
- ⚡ **شناسایی خودکار (Auto-Discovery):** بدون نیاز به رجیستر کردن دستی ServiceProvider در لاراول.
- 🧪 **سازگاری کامل با تست‌ها و سفر در زمان:** استفاده از `Date::now($tz)` که به شما امکان می‌دهد در تست‌های خود با `Carbon::setTestNow()` یا `$this->travelTo()` زمان را شبیه‌سازی کنید.
- 💡 **پشتیبانی کامل از PhpStorm:** همراه با فایل استاب برای رفع خطای ظاهری IDE و ارائه Autocomplete دقیق برای تمام متدها و آرگومان‌ها.

---

## نصب

نصب سریع از طریق کامپوزر:

```bash
composer require erfanvahabpour/laravel-jalali-schedule
```

به لطف قابلیت Package Discovery لاراول، پکیج بلافاصله آماده استفاده است.

### حل خطای Method Not Found در PhpStorm

برای اینکه PhpStorm این متدها را به صورت کامل شناسایی کند و هشدار ظاهری ندهد، کافی است فایل کمکی IDE را با دستور زیر پابلیش کنید:

```bash
php artisan vendor:publish --tag=jalali-schedule-ide-helper
```

این دستور فایل `_ide_helper_jalali_schedule.php` را در ریشه پروژه قرار می‌دهد و محیط توسعه شما بلافاصله پیشنهاد کلمات و راهنمای پارامترها را نمایش خواهد داد.

---

## راهنمای استفاده و متدها

### ۱. زمان‌بندی ماهانه

```php
use Illuminate\Support\Facades\Schedule;

// اجرا در اول هر ماه شمسی در ساعت ۰۱:۰۰ بامداد
Schedule::command('reports:monthly')->jalaliMonthly('01:00');

// اجرا در روز مشخصی از هر ماه شمسی (مثلاً اول ماه ساعت ۰۰:۱۵)
Schedule::command('attendance:reconcile')->jalaliMonthlyOn(1, '00:15');

// اجرا ۲ بار در ماه (به طور پیش‌فرض اول و شانزدهم، یا روزهای دلخواه)
Schedule::command('payroll:advances')->jalaliTwiceMonthly(1, 15, '03:00');

// اجرا در آخرین روز ماه شمسی (محاسبه دقیق ۲۹، ۳۰ یا ۳۱ روزه بودن ماه جاری)
Schedule::command('finance:close-books')->jalaliLastDayOfMonth('23:50');

// اجرا در روزهای خاصی از ماه شمسی (مثلاً روزهای ۵، ۱۵ و ۲۵ هر ماه)
Schedule::command('reminders:send')->jalaliDaysOfMonth([5, 15, 25], '09:00');
```

---

### ۲. زمان‌بندی فصلی و سالانه

در گاه‌شماری شمسی، فصل‌ها دقیقاً بر چهار فصل سال منطبق هستند:
- **فصل اول (بهار):** ماه‌های ۱، ۲، ۳ (فروردین، اردیبهشت، خرداد)
- **فصل دوم (تابستان):** ماه‌های ۴، ۵، ۶ (تیر، مرداد، شهریور)
- **فصل سوم (پاییز):** ماه‌های ۷، ۸، ۹ (مهر، آبان، آذر)
- **فصل چهارم (زمستان):** ماه‌های ۱۰، ۱۱، ۱۲ (دی، بهمن، اسفند)

```php
// اجرا در روز اول هر فصل شمسی (۱ فروردین، ۱ تیر، ۱ مهر، ۱ دی) در ساعت ۰۲:۰۰ صبح
Schedule::command('tax:seasonal-vat')->jalaliQuarterly('02:00');

// اجرا در روز مشخصی از ماه اول هر فصل (مثلاً روز پانزدهم)
Schedule::command('audit:quarterly')->jalaliQuarterlyOn(15, '04:00');

// اجرا سالانه در صبح عید نوروز (۱ فروردین ساعت ۰۸:۰۰ صبح)
Schedule::command('greeting:nowruz')->jalaliYearly('08:00');

// اجرا سالانه در تاریخ شمسی مشخص (مثلاً ۲۹ اسفند در ساعت ۲۳:۰۰)
Schedule::command('fiscal:year-end-closing')->jalaliYearlyOn(12, 29, '23:00');
```

---

### ۳. فیلترهای شرطی (برای زمان‌بندی‌های اختصاصی)

اگر دستوری دارید که فرکانس اجرای آن روزانه نیست (مثلاً هر ساعت یا هر دو ساعت یک‌بار) و می‌خواهید فقط در تاریخ‌های شمسی خاصی فعال باشد:

```php
// اجرا هر ساعت یک‌بار، اما فقط در روز اول ماه شمسی
Schedule::command('sync:final-attendance')
    ->hourly()
    ->whenJalaliDay(1);

// اجرا هر دو ساعت یک‌بار، اما فقط در آخرین روز ماه شمسی
Schedule::command('monitor:closing-activity')
    ->everyTwoHours()
    ->whenJalaliLastDayOfMonth();

// اجرا در تمام طول ماه اسفند (ماه ۱۲)
Schedule::command('countdown:nowruz')
    ->everyMinute()
    ->whenJalaliMonth(12);
```

---

### ۴. روزهای کاری و تعطیلات پایان هفته در ایران

متدهای پیش‌فرض لاراول مانند `weekdays()` (دوشنبه تا جمعه) و `weekends()` (شنبه و یک‌شنبه) مربوط به تقویم کشورهای غربی است. برای ایران می‌توانید از متدهای زیر استفاده کنید:

```php
// روزهای کاری اداری ایران (شنبه تا چهارشنبه) در ساعت ۰۷:۳۰ صبح
Schedule::command('office:morning-sync')->iranWeekdays('07:30');

// تعطیلات پایان هفته در ایران (پنج‌شنبه و جمعه) در ساعت ۱۲:۰۰ ظهر
Schedule::command('backup:weekend-database')->iranWeekends('12:00');

// روز خاصی از ایام هفته
Schedule::command('digest:weekly')->shanbeh('08:00'); // شنبه‌ها
Schedule::command('cleanup:weekly')->jomeh('23:00');   // جمعه‌ها
```

---

## جدول خلاصه متدها

| نام متد | پارامترها | توضیح |
| :--- | :--- | :--- |
| `jalaliMonthly` | `string $time = '0:0'` | روز اول هر ماه شمسی |
| `jalaliMonthlyOn` | `int $day = 1, string $time = '0:0'` | روز مشخصی از هر ماه شمسی |
| `jalaliTwiceMonthly` | `int $first = 1, int $second = 16, string $time = '0:0'` | دو بار در هر ماه شمسی |
| `jalaliLastDayOfMonth`| `string $time = '0:0'` | آخرین روز ماه شمسی (محاسبه خودکار ۲۹، ۳۰ یا ۳۱) |
| `jalaliDaysOfMonth` | `array\|int ...$days, string $time = '0:0'` | روزهای مشخصی از ماه شمسی |
| `jalaliQuarterly` | `string $time = '0:0'` | اول هر فصل شمسی (۱ فروردین، ۱ تیر، ۱ مهر، ۱ دی) |
| `jalaliQuarterlyOn` | `int $day = 1, string $time = '0:0'` | روز مشخص از ماه اول هر فصل |
| `jalaliYearly` | `string $time = '0:0'` | سالانه در روز اول فروردین (عید نوروز) |
| `jalaliYearlyOn` | `int $month = 1, int $day = 1, string $time = '0:0'` | سالانه در ماه و روز شمسی مشخص |
| `whenJalaliDay` | `int\|array ...$days` | فیلتر شرطی برای روزهای خاص ماه شمسی |
| `whenJalaliLastDayOfMonth` | - | فیلتر شرطی برای آخرین روز ماه شمسی |
| `whenJalaliMonth` | `int\|array ...$months` | فیلتر شرطی برای ماه‌های خاص شمسی |
| `iranWeekdays` | `string $time = '0:0'` | روزهای کاری ایران (شنبه تا چهارشنبه) |
| `iranWeekends` | `string $time = '0:0'` | آخر هفته ایران (پنج‌شنبه و جمعه) |
| `shanbeh` | `string $time = '0:0'` | شنبه‌ها |
| `jomeh` | `string $time = '0:0'` | جمعه‌ها |

---

## تست‌ها

برای اجرای تست‌های واحد با PHPUnit:

```bash
composer test
```

---

## لایسنس

این بسته نرم‌افزاری تحت لایسنس **MIT** منتشر شده است. برای اطلاعات بیشتر فایل [LICENSE](LICENSE) را مطالعه نمایید.

---

## توسعه‌دهنده

طراحی و پیاده‌سازی توسط **[عرفان وهاب‌پور (Erfan Vahabpour)](https://github.com/ErfanVahabpour)**  
- ایمیل: erfanvahabpour@yahoo.com  
- وب‌سایت: [erfanvahabpour.ir](https://erfanvahabpour.ir/)
