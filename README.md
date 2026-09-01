# WooCommerce Checkout Email Length Validator

A lightweight WooCommerce plugin that rejects checkout billing email addresses whose local part (the text before `@`) is shorter than a configurable minimum. The default minimum is 4 characters.

## Features
- Validates billing email during WooCommerce checkout.
- Leaves malformed-email validation to WooCommerce/WordPress and only enforces the local-part length rule.
- No database tables, options, personal-data logging, cookies, external requests, or frontend assets.
- Uses WooCommerce checkout validation hooks and does not access order storage directly.
- Declares HPOS compatibility because it does not depend on legacy order storage.
- Filterable minimum length.

## Requirements
- WordPress 6.0+
- PHP 7.4+
- WooCommerce

## Installation
1. Upload the plugin ZIP through **Plugins → Add New → Upload Plugin**.
2. Activate **WooCommerce Checkout Email Length Validator**.
3. Checkout validation is enabled automatically.

## Usage
With the default configuration, `abc@example.com` is rejected while `abcd@example.com` passes this specific rule. WooCommerce may still reject an address for its own validation reasons.

## Configuration
Change the minimum local-part length with:

```php
add_filter( 'wcelv_minimum_email_local_part_length', function () {
    return 5;
} );
```

## Security
The plugin does not implement its own checkout nonce handling. It runs inside WooCommerce's checkout validation lifecycle, where WooCommerce owns request integrity and checkout processing. Browser input is sanitized and the plugin does not make authorization decisions from client-controlled data.

## Privacy
No personal information is persisted by this plugin. The billing email is inspected only during the checkout request.

## Performance
The validation is constant-time for the small email input and performs no database queries, writes, remote requests, or asset loading.

## HPOS compatibility
Compatible. The plugin does not read or write WooCommerce order storage and declares `custom_order_tables` compatibility through `FeaturesUtil` when available.

## Checkout Blocks limitation
This plugin is designed around WooCommerce PHP checkout validation hooks. Store API / Checkout Blocks validation can evolve independently; validate the exact WooCommerce version and checkout implementation used by the target site before claiming Blocks-specific coverage.

## GitHub Description
Lightweight WooCommerce checkout validation for enforcing a minimum billing email local-part length.

## License
GPL-3.0

## Author
Amirreza Shayesteh Far  
https://github.com/amirrezashf

---

# اعتبارسنجی طول ایمیل تسویه‌حساب ووکامرس

این افزونه طول بخش قبل از `@` در ایمیل صورتحساب را هنگام Checkout بررسی می‌کند. حداقل طول پیش‌فرض ۴ کاراکتر است.

## امکانات
- بررسی ایمیل صورتحساب هنگام Checkout.
- عدم ذخیره ایمیل یا اطلاعات شخصی.
- بدون جدول، تنظیمات دیتابیس، Cookie، درخواست خارجی یا فایل frontend.
- امکان تغییر حداقل طول با filter `wcelv_minimum_email_local_part_length`.
- عدم وابستگی به ساختار ذخیره‌سازی سفارش و سازگار با HPOS.

## نصب
ZIP افزونه را از مسیر **Plugins → Add New → Upload Plugin** نصب و فعال کنید.

## نحوه عملکرد
برای مقدار پیش‌فرض، ایمیلی مانند `abc@example.com` به علت کوتاه بودن بخش قبل از `@` رد می‌شود، اما `abcd@example.com` از این قانون عبور می‌کند. اعتبارسنجی عمومی ساختار ایمیل همچنان بر عهده WordPress/WooCommerce است.

## امنیت و حریم خصوصی
ورودی sanitize می‌شود و هیچ اطلاعات شخصی توسط افزونه ذخیره یا log نمی‌شود. افزونه به nonce داخلی Checkout ووکامرس تکیه می‌کند و nonce موازی یا سفارشی ایجاد نمی‌کند.

## محدودیت Checkout Blocks
اعتبارسنجی اصلی افزونه بر lifecycle PHP تسویه‌حساب WooCommerce است. برای ادعای پوشش اختصاصی Checkout Blocks/Store API باید نسخه و پیاده‌سازی دقیق WooCommerce سایت هدف جداگانه تست شود.

## مجوز
GPL-3.0
