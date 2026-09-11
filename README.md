<div align="center">

# steel unnamed project | Formal Qaideg.com

**A modern, full-featured e-commerce platform built with Laravel 12**

*Customer storefront · Filament admin dashboard · Analytics · Reporting*

[![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?style=flat&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.4-777BB4?style=flat&logo=php&logoColor=white)](https://www.php.net)
[![Filament](https://img.shields.io/badge/Filament-4-F59E0B?style=flat)](https://filamentphp.com)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=flat&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)
[![License](https://img.shields.io/badge/License-MIT-blue.svg)](#license)

</div>

---

## Overview

Qaid Store is a bilingual (English and Arabic) Laravel 12 e-commerce application. It combines a Blade/Tailwind storefront with a Filament 4 admin panel for catalog management, orders, customers, SEO, wholesale leads, reporting, and security monitoring.

The project has two primary surfaces:

| Surface | Responsibility |
|---|---|
| Storefront | Product discovery, packages, routines, home content, cart, checkout, account pages, contact, and wholesale sales. |
| Admin panel | Products, categories, customers, orders, coupons, content/settings, SEO, wholesale requests, exports, and operational monitoring. |

The normal request flow is: route -> middleware/controller -> model/service/action -> Blade view or Filament resource. Translatable content is stored in locale-specific translation tables through Astrotomic Laravel Translatable.

## Key Features

- Public storefront for products, packages, routines, home content, contact, and wholesale sales
- Guest and authenticated cart and checkout flows
- Coupon validation with protection against stacking coupons with product discounts
- Order creation, tracking, customer invoices, and admin order management
- Product publishing controls, product trials, sales, brands, and category filtering
- Per-product bilingual SEO title, description, and keyword/tag fields
- Per-page bilingual SEO settings for major public pages
- Wholesale price quote requests with admin review and Excel export
- Product view analytics with session/authenticated-user de-duplication
- Security event logging and admin dashboard monitoring
- English and Arabic storefront localization with locale-aware routes and RTL rendering
- Vacation Mode for temporarily pausing checkout while keeping the storefront and cart browsing active

## Tech Stack

### Backend

- PHP 8.4+
- Laravel 12
- Filament 4
- Laravel Fortify for authentication
- Laravel Socialite for Google login
- Astrotomic Laravel Translatable for bilingual database content
- `maatwebsite/excel` for XLSX exports
- `jenssegers/agent` for User-Agent and operating-system parsing
- MySQL in production; SQLite is supported for local testing

### Frontend

- Blade templates
- Tailwind CSS
- Vite
- Alpine.js and vanilla JavaScript for storefront interactions
- Font Awesome

## Recent Highlights

<details>
<summary><strong>1. Coupons no longer stack with product discounts</strong></summary>

<br>

Coupon validation rejects coupons for products that already have a sale price or discount, preventing conflicting totals at checkout.

**Touched files:** `app/Http/Controllers/Web/CouponValidateController.php` · `app/Commands/CreateOrderCommand.php` · `app/Models/Coupon.php` · `resources/views/web/pages/checkout.blade.php`
</details>

<details>
<summary><strong>2. Product publish/unpublish and brand administration</strong></summary>

<br>

Admins can filter products by brand and toggle publication status. Unpublished products are excluded from public product listings and home sections.

**Touched files:** `database/migrations/2026_05_25_225641_add_is_published_to_products_table.php` · `app/Filament/Resources/Products/Tables/ProductsTable.php` · `app/Filament/Resources/Products/Schemas/ProductForm.php` · `app/Http/Controllers/Web/HomeController.php` · `app/Http/Controllers/Web/ShopController.php`
</details>

<details>
<summary><strong>3. Sales dashboard and Excel reporting</strong></summary>

<br>

The admin panel provides sales summaries, pending-order visibility, and a formatted multi-sheet XLSX export with totals, status breakdowns, styling, and EGP number formats.

**Touched files:** `app/Filament/Resources/SalesSummary/Pages/ListSalesSummary.php` · `app/Filament/Resources/SalesSummary/SalesSummaryResource.php` · `app/Exports/SalesSummaryExport.php` · `app/Filament/Widgets/PendingOrdersOverviewWidget.php`
</details>

<details>
<summary><strong>4. Product view analytics</strong></summary>

<br>

Product views are recorded once per authenticated user or session and shown on product pages without inflating counts from repeat refreshes.

**Touched files:** `database/migrations/2026_09_01_000000_create_product_views_table.php` · `app/Models/ProductView.php` · `app/Models/Product.php` · `app/Http/Controllers/Web/ProductController.php` · `resources/views/web/pages/shop/show.blade.php` · `tests/Feature/ProductViewTrackingTest.php`
</details>

<details>
<summary><strong>5. Wholesale Sales submission and quote download flow</strong></summary>

<br>

The bilingual Wholesale Sales page collects business name, Egyptian phone number, and message behind a Terms & Conditions checkbox. A valid submission is saved, the user is redirected back to the same page for an inline success message, and the admin-uploaded PDF starts downloading automatically. Missing or unreadable PDFs return a localized error instead of claiming success.

**Touched files:** `routes/web.php` · `app/Http/Controllers/Web/WholesaleController.php` · `app/Models/WholesaleRequest.php` · `database/migrations/2026_09_07_110000_create_wholesale_requests_table.php` · `resources/views/web/pages/wholesale-sales.blade.php` · `resources/lang/en/wholesale.php` · `resources/lang/ar/wholesale.php`
</details>

<details>
<summary><strong>6. Wholesale Requests admin management</strong></summary>

<br>

Wholesale submissions are available in a dedicated Filament resource with newest-first sorting, search, status tracking (`New`, `Contacted`, `Closed`), hard-delete confirmation, and a filtered XLSX export. The request also records IP address, raw User-Agent, parsed operating system, and submission language.

**Touched files:** `app/Filament/Resources/WholesaleRequests/WholesaleRequestResource.php` · `app/Filament/Resources/WholesaleRequests/Pages/ManageWholesaleRequests.php` · `app/Exports/WholesaleRequestsExport.php` · `app/Models/WholesaleRequest.php` · `database/migrations/2026_09_08_100000_add_device_data_to_wholesale_requests_table.php`
</details>

<details>
<summary><strong>7. Egyptian phone number validation</strong></summary>

<br>

Wholesale phone numbers are validated in the browser and again on the server. Accepted values use `010`, `011`, `012`, or `015` with 11 local digits, or the equivalent `+20` and `0020` international prefix. Invalid values receive English or Arabic feedback based on the active locale.

**Touched files:** `app/Http/Controllers/Web/WholesaleController.php` · `resources/views/web/pages/wholesale-sales.blade.php` · `resources/lang/en/wholesale.php` · `resources/lang/ar/wholesale.php`

> Note: the current wholesale flow keeps this rule inline in `WholesaleController.php`. The existing reusable phone infrastructure is `app/Rules/PhoneValidationRule.php` and `app/Services/PhoneNumberService.php`.
</details>

<details>
<summary><strong>8. Independent bilingual per-page SEO metadata</strong></summary>

<br>

SEO metadata is independently editable for Home, Shop, Contact, Wholesale Sales, Terms, Routines, and Packages. Each page has English and Arabic Meta Title, Meta Description, and Meta Keywords/Tags fields. The shared layout renders the correct page record with sensible fallbacks, while the legacy home JSON is migrated into the new structure.

**Touched files:** `app/Filament/Resources/SeoSettings/SeoSettingsResource.php` · `app/Filament/Resources/SeoSettings/Schemas/SeoSettingsForm.php` · `app/Filament/Resources/SeoSettings/Pages/EditSeoSettings.php` · `app/Helpers/helper.php` · `app/Models/BuisnessSetting.php` · `database/migrations/2026_09_07_100000_add_seo_fields_to_buisness_setting_translations.php` · `database/migrations/2026_09_07_100001_migrate_legacy_home_seo.php` · `database/seeders/SeoSettingsSeeder.php` · `resources/views/web/layouts/main.blade.php`
</details>

<details>
<summary><strong>9. Product-level Meta Tags</strong></summary>

<br>

Products have independent English and Arabic Meta Title, Meta Description, and Meta Keywords/Tags fields. Product pages render the active translation, falling back to product name, description, category, and brand when fields are empty.

**Touched files:** `app/Filament/Resources/Products/Schemas/ProductForm.php` · `app/Filament/Resources/Products/Pages/CreateProduct.php` · `app/Filament/Resources/Products/Pages/EditProduct.php` · `app/Models/Product.php` · `app/Models/Translations/ProductTranslation.php` · `database/migrations/2026_03_21_182833_add_seo_meta_to_product_translations_table.php` · `database/migrations/2026_09_07_100002_add_meta_keywords_to_product_translations_table.php` · `resources/views/web/pages/shop/show.blade.php`
</details>

<details>
<summary><strong>10. Vacation Mode checkout pause</strong></summary>

<br>

The admin can enable a bilingual Vacation Mode that displays a site-wide banner in the active locale and blocks new customer orders without disabling browsing, cart building, wholesale requests, or contact forms.

**Touched files:** `app/Filament/Resources/VacationSettings/` · `app/Helpers/helper.php` · `app/Http/Controllers/Web/OrderController.php` · `resources/views/web/layouts/main.blade.php` · `resources/views/web/pages/checkout.blade.php`

**Admin flow:** open the Filament Business Settings section and use the `Vacation Mode` resource to toggle the feature and edit the English/Arabic vacation message.

**Customer behavior:** the banner appears globally, checkout action buttons are hidden/disabled, and any checkout request is rejected server-side with the same vacation message flashed back to the user.
</details>

<details>
<summary><strong>11. Security, customer, and migration stability improvements</strong></summary>

<br>

Admin login and protected-path events are logged, customer profiles and order tracking are available, and migrations have been kept compatible with SQLite-based local testing where possible.

**Touched files:** `app/Jobs/LogSecurityEventJob.php` · `app/Services/SecurityEventLogger.php` · `app/Models/SecurityEvent.php` · `app/Filament/Widgets/DangerZoneWidget.php` · `app/Providers/FortifyServiceProvider.php` · `app/Http/Controllers/Web/ProfileController.php` · `database/migrations/2026_01_08_073730_change_on_order_items_table.php`
</details>

<details>
<summary><strong>12. Evolution API WhatsApp order confirmations and admin connection management</strong></summary>

<br>

The application can send automatic bilingual WhatsApp order confirmations through a self-hosted Evolution API instance using a connected WhatsApp Business number. The super-admin-only WhatsApp Connection page provides QR linking, live connection status, linked phone visibility, reconnect, and logout controls. It also provides editable English and Arabic confirmation templates with `{customer_name}`, `{order_number}`, and `{order_total}` placeholders.

Confirmations are dispatched as queued jobs after the order-created event, so WhatsApp API delays or failures do not block checkout or replace the existing email confirmations. The integration is configuration-driven, logs failures safely, retries transient send failures, and records `whatsapp_sent_at` for admin delivery visibility. When the instance is disconnected, the admin dashboard shows a warning and links back to the connection page.

**Touched files:** `.env.example` · `config/services.php` · `app/Commands/CreateOrderCommand.php` · `app/Events/OrderCreated.php` · `app/Listeners/OrderCreationListener.php` · `app/Jobs/SendWhatsAppOrderConfirmation.php` · `app/Services/EvolutionWhatsAppService.php` · `app/Services/PhoneNumberService.php` · `app/Models/Order.php` · `app/Models/BuisnessSetting.php` · `app/Models/Translations/BuisnessSettingTranslation.php` · `app/Filament/Pages/WhatsAppConnection.php` · `app/Filament/Widgets/WhatsAppConnectionAlert.php` · `app/Providers/AppServiceProvider.php` · `app/Providers/Filament/AdminPanelProvider.php` · `app/Filament/Resources/Orders/Tables/OrdersTable.php` · `app/Filament/Resources/Orders/Schemas/OrderInfolist.php` · `database/migrations/2026_09_11_000000_add_whatsapp_sent_at_to_orders_table.php` · `resources/views/filament/pages/whatsapp-connection.blade.php` · `resources/views/filament/widgets/whatsapp-connection-alert.blade.php`
</details>

## Getting Started

### Prerequisites

- PHP 8.4+
- Composer
- Node.js and npm
- MySQL, or SQLite for local development and tests

### Installation

```bash
# 1. Clone the repository
git clone https://github.com/<your-username>/qaid-store.git
cd qaid-store

# 2. Install PHP and JavaScript dependencies
composer install
npm install

# 3. Create and configure the environment
cp .env.example .env
php artisan key:generate

# 4. Run database migrations
php artisan migrate

# 5. Build frontend assets
npm run build

# 6. Start the local development environment
composer run dev
```

Configure database, `APP_URL`, mail, queue, storage, and OAuth values in `.env` before using the corresponding features. The wholesale quote PDF is managed through the Filament Wholesale Price Quote resource and must be uploaded/configured before download requests can succeed.

`composer run dev` starts the Laravel server, queue listener, log viewer, and Vite together.

### Vacation Mode

Use the Filament admin panel to enable Vacation Mode from the Business Settings section:

1. Open the admin dashboard.
2. Go to `Business Settings` -> `Vacation Mode`.
3. Toggle the switch to enable the pause.
4. Edit the English and Arabic vacation message.
5. Save the setting.

Once enabled:
- A site-wide banner appears across storefront pages.
- Checkout submission buttons are hidden/disabled.
- Any direct checkout request is rejected server-side and redirected back with a flash error message.
- Browsing, cart updates, wholesale forms, and contact forms remain available.

## Useful Commands

| Command | Description |
|---|---|
| `composer install` | Install PHP dependencies. |
| `npm install` | Install frontend dependencies. |
| `php artisan migrate` | Run pending migrations. |
| `php artisan migrate:status` | Inspect migration state. |
| `php artisan db:seed --class=SeoSettingsSeeder` | Seed/update default page SEO records. |
| `php artisan route:list` | Inspect storefront, API, and account routes. |
| `php artisan test` | Run the test suite. |
| `php artisan test --filter=ProductViewTrackingTest` | Run product analytics tests. |
| `php artisan queue:work` | Process queued jobs. |
| `php artisan view:clear` | Clear compiled Blade views. |
| `npm run dev` | Start Vite in development mode. |
| `npm run build` | Build production frontend assets. |
| `composer run dev` | Start the full local development stack. |

## Project Structure

The repository root contains Laravel bootstrap/configuration files, frontend tooling, and the application directories below.

```text
app/
├── Commands/                 Order and domain command objects.
├── DTOs/                     Typed data transfer objects such as OrderDto.
├── Enums/                    OrderStatus and admin-role enumerations.
├── Events/                   Domain events such as OrderCreated.
├── Exports/                  XLSX export classes.
├── Filament/                 Admin resources, pages, forms, tables, and widgets.
├── Helpers/                  Globally autoloaded helpers, including SEO resolution.
├── Http/                     Web controllers, middleware, and form/request handling.
├── Interfaces/               Contracts such as CartStorageInterface.
├── Jobs/                     Queued contact, security, and notification work.
├── Listeners/                Event listeners such as order creation handling.
├── Mail/                     Customer and admin order emails.
├── Managers/                 Cart storage manager and storage implementations.
├── Models/                   Eloquent models and translation models.
├── Providers/                Laravel, Fortify, and Filament configuration.
├── Rules/                    Reusable validation rules.
├── Services/                 Cart, order, and phone-number services.
└── Traits/                   Reusable model behavior.

config/                       Application, database, mail, queue, session, and package config.
database/
├── factories/                Model factories for tests/seeding.
├── migrations/               Complete database schema history.
└── seeders/                  Default data and administrative setup.
resources/
├── css/                      Tailwind/application styles.
├── js/                       Vite entrypoints and storefront JavaScript.
├── lang/en/                  English translations.
├── lang/ar/                  Arabic translations.
└── views/                    Blade layouts, pages, components, emails, and account views.
routes/                       Public web routes and console routes.
tests/                        Feature and unit tests.
public/                       Public assets, compiled builds, images, fonts, and storage link.
```

## Detailed File Reference

### HTTP Controllers

`app/Http/Controllers/Web/` contains storefront request entrypoints. Controllers gather models and delegate domain work; Blade views render the response.

```text
HomeController.php             Home categories, featured products, and home SEO.
ShopController.php             Shop listing data, categories, routines, and brands.
ProductController.php           Product detail pages, trial products, view tracking, and brand data.
ProductsApiCpntroller.php       AJAX product filtering by trial/category/brand inputs.
CartController.php              Add, update, remove, and clear cart operations.
OrderController.php             Checkout display and order submission.
CouponValidateController.php    Coupon validation endpoint used by checkout.
WholesaleController.php         Wholesale form validation, lead storage, quote availability, and PDF download.
ContactController.php            Contact form handling and queued contact processing.
TermsController.php              Loads editable Terms content for the public page.
RoutineController.php            Routine index/detail pages.
PackagesController.php           Package detail pages.
PackagesApiController.php        Package data endpoint used by storefront interactions.
CustomerTrafficController.php   Authenticated customer traffic recording.
ProfileController.php            Customer profile editing.
BuyerOrderController.php         Customer order list, detail, and receive actions.
LocaleController.php             Locale switching between English and Arabic.
```

Other controllers include `OrderInvoiceController.php` for invoice downloads, `ArtisanController.php` for the protected queue endpoint, and `Web/Auth/GoogleAuthController.php` for Google OAuth.

### Filament Admin Resources

Every resource normally has a resource class plus `Pages/`, `Schemas/`, and `Tables/` classes. `AdminPanelProvider.php` discovers resources from `app/Filament/Resources`.

```text
WholesaleRequests/WholesaleRequestResource.php
    Admin list/search/filter for wholesale leads; inline status updates, hard delete, access control, and row detail fields.
WholesaleRequests/Pages/ManageWholesaleRequests.php
    List page header action for filtered XLSX export.
Products/ProductResource.php
    Product CRUD and authorization entrypoint.
Products/Schemas/ProductForm.php
    Product, bilingual content, image, sales, category, and per-language SEO fields.
Products/Pages/CreateProduct.php and EditProduct.php
    Persist English and Arabic product translations separately.
Orders/OrderResource.php
    Order list, editing, status/payment controls, and order details.
Customers/CustomerResource.php
    Customer management and order relation manager.
Categories/CategoryResource.php
    Bilingual category management.
Coupons/CouponResource.php
    Coupon CRUD, scope, validity, and discount configuration.
SalesSummary/SalesSummaryResource.php
    Sales reporting entrypoint and XLSX action.
SeoSettings/SeoSettingsResource.php
    Separate SEO records for Home, Shop, Contact, Wholesale, Terms, Routines, and Packages.
SeoSettings/Schemas/SeoSettingsForm.php
    English/Arabic title, description, and keywords fields with length guidance.
WholesalePriceQuote/WholesalePriceQuoteResource.php
    Admin upload/configuration for the PDF downloaded by wholesale visitors.
ContactMessages/ContactMessageResource.php
    Contact submissions and reviewed state.
BuisnessInfos, ContentManagement, TermsSettings, OrderSettings
    Editable business and content settings stored through BuisnessSetting records.
AdminUsers, Cities, Packages, Routines, SalesAdminInvoices
    Supporting administration for users, delivery cities, catalog groupings, and completed sales.
```

### WhatsApp Order Confirmations

```text
app/Services/EvolutionWhatsAppService.php
    Config-driven Evolution API wrapper for text messages, connection status, QR retrieval, and logout; failures are logged and returned safely.
app/Jobs/SendWhatsAppOrderConfirmation.php
    Queued order-confirmation job that normalizes the customer phone, fetches the locale-specific template, replaces placeholders, sends the message, retries API failures, and records delivery time.
app/Filament/Pages/WhatsAppConnection.php
    Super-admin-only connection page with QR polling, connection status, reconnect/logout actions, bilingual template editing, placeholder guidance, and soft placeholder warnings.
app/Filament/Widgets/WhatsAppConnectionAlert.php
    Dashboard warning widget that polls the Evolution connection and links admins to the connection page when WhatsApp is unavailable.
resources/views/filament/pages/whatsapp-connection.blade.php
    Connection status, QR code, warning, reconnect, and embedded bilingual message-template form UI.
resources/views/filament/widgets/whatsapp-connection-alert.blade.php
    Persistent disconnected-state warning shown on the admin dashboard.
app/Models/BuisnessSetting.php
app/Models/Translations/BuisnessSettingTranslation.php
    Existing Astrotomic translatable settings pattern used to store the `whatsapp-message` English/Arabic template values.
app/Models/Order.php
    Exposes customer phone/name accessors and carries the nullable WhatsApp delivery timestamp.
app/Filament/Resources/Orders/Tables/OrdersTable.php
app/Filament/Resources/Orders/Schemas/OrderInfolist.php
    Show the WhatsApp confirmation timestamp in the admin order list/detail views.
app/Providers/Filament/AdminPanelProvider.php
    Registers the dashboard WhatsApp warning widget; Filament discovers the page from `app/Filament/Pages`.
app/Commands/CreateOrderCommand.php
    Creates the order and emits the `OrderCreated` event after the order transaction.
app/Events/OrderCreated.php
    Carries the order payload and captures the customer locale for queued notifications.
app/Listeners/OrderCreationListener.php
    Dispatches the WhatsApp job alongside the existing customer/admin email notifications.
app/Providers/AppServiceProvider.php
    Registers `OrderCreationListener` for the `OrderCreated` event.
app/Services/PhoneNumberService.php
    Validates and formats customer phone numbers before the job sends them to Evolution API.
config/services.php
    Maps `EVOLUTION_API_URL`, `EVOLUTION_API_KEY`, and `EVOLUTION_INSTANCE` into the Evolution service configuration.
.env.example
    Documents placeholder values for the three Evolution API environment keys.
```

The Evolution API instance is external infrastructure. Set `EVOLUTION_API_URL`, `EVOLUTION_API_KEY`, and `EVOLUTION_INSTANCE` in `.env`; connect the dedicated WhatsApp Business number from the admin QR page.

Admin access is resource-specific. For example, Wholesale Requests allows super admins, sales admins, or users with the `customers` extra permission; SEO requires super admin or `seo_settings` permission.

### Models and Services

```text
app/Models/WholesaleRequest.php       Wholesale submission: business, contact, status, IP, User-Agent, OS, locale.
app/Models/Product.php                Product entity with translatable name, description, meta title, description, and keywords.
app/Models/Translations/ProductTranslation.php
                                      Product locale rows and slug synchronization behavior.
app/Models/BuisnessSetting.php        Keyed editable settings and translated values/SEO fields.
app/Models/Order.php and OrderItem.php Order aggregates and purchased line items.
app/Models/Coupon.php                 Coupon rules, scopes, and validity data.
app/Models/Category.php               Product categories and translations.
app/Models/ProductView.php            De-duplicated product view records.
app/Models/ContactMessage.php         Contact submissions.
app/Models/User.php                   Admin/customer roles and authorization helpers.

app/Services/CartService.php          Cart operations and totals.
app/Services/OrderService.php         Reusable order processing support.
app/Services/PhoneNumberService.php   Country-aware phone parsing/validation support.
app/Rules/PhoneValidationRule.php     Reusable phone validation rule using PhoneNumberService.
app/Helpers/helper.php                Global settings lookup, page SEO fallback resolution, and storage URLs.
```

The current WholesaleController uses an exact Egyptian-mobile regex for the wholesale form and a localized message. The reusable `PhoneValidationRule` remains available for other forms and uses the phone-number service with Egypt as its region.

### Exports

```text
app/Exports/SalesSummaryExport.php
    Multi-sheet sales workbook with orders, summary formulas, styling, and currency formats.
app/Exports/WholesaleRequestsExport.php
    Filter-aware wholesale XLSX export with business/contact data, status, language, IP, and OS.
```

### Important Migrations

```text
2025_11_12_143124_create_products_table.php
2025_11_12_143920_create_product_translations_table.php
    Product core fields and bilingual product content.
2026_03_21_182833_add_seo_meta_to_product_translations_table.php
    Product Meta Title and Meta Description columns.
2026_09_07_100002_add_meta_keywords_to_product_translations_table.php
    Product Meta Keywords/Tags column.
2026_01_01_101901_create_buisness_settings_table.php
2026_01_01_103329_create_buisness_setting_translations_table.php
    Keyed settings and translated values.
2026_09_07_100000_add_seo_fields_to_buisness_setting_translations.php
    Per-page translated SEO columns.
2026_09_07_100001_migrate_legacy_home_seo.php
    Converts legacy shared home SEO JSON into the home page record.
2026_09_07_110000_create_wholesale_requests_table.php
    Wholesale business name, phone, message, status, IP, locale, and timestamps.
2026_09_08_100000_add_device_data_to_wholesale_requests_table.php
    Raw User-Agent and parsed operating system columns.
2026_09_01_000000_create_product_views_table.php
    Product view analytics records.
2025_11_28_143504_create_orders_table.php
    Order header data; related migrations add payment, status, admin, and coupon fields.
2026_09_11_000000_add_whatsapp_sent_at_to_orders_table.php
    Adds the nullable `whatsapp_sent_at` timestamp used to show WhatsApp confirmation delivery status.
2026_03_21_225132_create_coupons_table.php
    Coupon definitions and discount rules.
```

Never edit an old migration that has already run in a shared environment. Add a new migration for schema changes.

### Blade Views and Frontend Files

```text
resources/views/web/layouts/main.blade.php
    Shared HTML shell, locale direction, dynamic title/meta tags, Vite assets, footer, and global flash messages.
resources/views/web/pages/home.blade.php
    Home hero, categories, featured products, and home-specific additions.
resources/views/web/pages/shop/index.blade.php
    Shop filters and product browsing shell.
resources/views/web/pages/shop/show.blade.php
    Product detail, product SEO tags, product image/social metadata, and view counter.
resources/views/web/pages/wholesale-sales.blade.php
    Bilingual lead form, Terms checkbox, inline success/error states, and automatic quote download.
resources/views/web/pages/contact.blade.php
    Contact form and contact information.
resources/views/web/pages/terms.blade.php
    Editable Terms content plus privacy disclosure for wholesale IP/device data.
resources/views/web/pages/routine/*.blade.php
    Routine index and detail pages.
resources/views/web/pages/shop/packages/show.blade.php
    Package detail page.
resources/views/web/account/*.blade.php
    Customer profile, order list, and order detail screens.
resources/views/components/
    Shared navbar, footer, errors, flash messages, and reusable UI components.
resources/views/emails/
    Customer/admin order and contact email templates.
```

### Language Files

`resources/lang/en` and `resources/lang/ar` contain storefront strings. Wholesale-specific messages live in `wholesale.php`, including validation, success, download failure, labels, and Terms text. `privacy.php` contains the bilingual device-data disclosure. When adding user-visible text, add both locale entries and use `trans()` or `__()` in Blade/controller code.

## How the Pieces Connect

### Wholesale Sales

1. `routes/web.php` places the public routes inside the `locale` middleware group.
2. `GET wholesale-sales` calls `WholesaleController@index` and renders `web.pages.wholesale-sales`.
3. The form posts to `POST wholesale-sales/price-quote`.
4. `WholesaleController::submit()` validates business name, the Egyptian phone format, message, and accepted Terms.
5. The controller verifies the configured PDF through the `wholesale-price-quote` `BuisnessSetting` before saving.
6. A `WholesaleRequest` row is created in `wholesale_requests`; IP, raw User-Agent, parsed OS, and locale are captured server-side.
7. The controller redirects back with a localized success message and download URL.
8. The Blade success state fetches the PDF endpoint and starts the download automatically; failures replace success with a localized error.
9. `WholesaleRequestResource` lists, filters, searches, updates status, exports, and hard-deletes requests in Filament.

### Product SEO and View Tracking

1. `products/{product}` is handled by `ProductController::show()`.
2. The controller resolves the base product or trial product and records a unique `ProductView` for the current user/session.
3. `ProductForm.php` stores English and Arabic product translation fields, including SEO fields, through the create/edit pages.
4. `shop/show.blade.php` selects the active translated title, description, and keywords, with product/category/brand fallbacks.
5. The view emits description, keywords, Open Graph, Twitter, and product image metadata.

### Static Page SEO

1. `SeoSettingsSeeder` creates `seo-page-*` settings for major public pages.
2. `SeoSettingsResource` exposes each record to authorized admins with English/Arabic tabs.
3. `getPageSeo()` in `app/Helpers/helper.php` resolves the current locale and applies defaults for empty fields.
4. `main.blade.php` maps route names to page SEO records and renders the title and metadata in the shared `<head>`.
5. Product pages remain record-specific and add their own product metadata through `@push('meta')`.

### Coupons and Checkout

1. Checkout is rendered by `OrderController` and `checkout.blade.php`.
2. The browser posts coupon codes to `CouponValidateController`.
3. Coupon model scope and validity rules are checked before the response is returned.
4. `CreateOrderCommand` recalculates totals and prevents coupon/product-sale stacking before creating the order.
5. Order creation emits events/listeners that trigger confirmation and admin notification work.

### Orders

1. Cart and checkout routes are grouped under the locale middleware and checkout guard.
2. `CartController` delegates cart storage through the cart manager/storage abstractions.
3. `OrderController::store()` validates checkout input and invokes order creation logic.
4. `CreateOrderCommand`, `OrderDto`, events, and listeners create the order and send notifications.
5. Admin order resources manage status and payment fields; customer account routes expose order history and invoices.

### WhatsApp Order Confirmation

1. `CreateOrderCommand` creates the order and emits `OrderCreated` after the transaction commits; the event captures the request locale.
2. `OrderCreationListener` keeps the existing email/admin notifications and dispatches `SendWhatsAppOrderConfirmation` separately.
3. The queued job fetches the saved `whatsapp-message` translation for the order locale, falling back to the default English/Arabic template when no setting exists.
4. `{customer_name}`, `{order_number}`, and `{order_total}` are replaced with the order values before sending.
5. `PhoneNumberService` validates and formats the customer phone as E.164.
6. `EvolutionWhatsAppService` calls the configured Evolution API `sendText` endpoint; failures are logged, retried by the job, and never block checkout or email delivery.
7. The super-admin WhatsApp Connection page manages QR linking and connection state, while the dashboard alert highlights a disconnected instance and order admin views show `whatsapp_sent_at`.

### Contact, Customers, and Security

- Contact submissions are validated by `ContactController`, stored as `ContactMessage`, and processed by `ContactMessageJob`.
- Customer profile and order pages are protected by `auth` and `EnsureCustomer` middleware.
- Login, admin access, and protected-path activity are recorded through security jobs/services and surfaced by Filament widgets.

## Where to Make Common Changes

| Change | Start here |
|---|---|
| Change the WhatsApp confirmation message template | The super-admin `WhatsApp Connection` page; values are stored as the translated `whatsapp-message` `BuisnessSetting`. |
| Change WhatsApp connection/QR logic | `app/Services/EvolutionWhatsAppService.php` and `app/Filament/Pages/WhatsAppConnection.php`. |
| Change when/how the confirmation is sent | `app/Jobs/SendWhatsAppOrderConfirmation.php` and `app/Listeners/OrderCreationListener.php`. |
| Change Evolution API credentials or instance | `config/services.php` and `.env` keys `EVOLUTION_API_URL`, `EVOLUTION_API_KEY`, and `EVOLUTION_INSTANCE`. |
| Change Egyptian wholesale phone rules | `app/Http/Controllers/Web/WholesaleController.php` and the client validator in `resources/views/web/pages/wholesale-sales.blade.php`. |
| Reuse country-aware phone validation | `app/Rules/PhoneValidationRule.php` and `app/Services/PhoneNumberService.php`. |
| Change wholesale form fields or success UI | `resources/views/web/pages/wholesale-sales.blade.php`. |
| Change wholesale persistence | `app/Models/WholesaleRequest.php` and a new migration in `database/migrations`. |
| Change request status options or admin permissions | `app/Filament/Resources/WholesaleRequests/WholesaleRequestResource.php`. |
| Change wholesale XLSX columns/formatting | `app/Exports/WholesaleRequestsExport.php`. |
| Change quote PDF upload/download behavior | `app/Filament/Resources/WholesalePriceQuote/` and `app/Http/Controllers/Web/WholesaleController.php`. |
| Add a new static page SEO record | Extend `SeoSettingsSeeder`, the page list in `ListSeoSettings.php`, and the route-name map in `resources/views/web/layouts/main.blade.php`; use `getPageSeo()` for fallbacks. |
| Change SEO admin fields/limits | `app/Filament/Resources/SeoSettings/Schemas/SeoSettingsForm.php`. |
| Change product SEO fields | `app/Filament/Resources/Products/Schemas/ProductForm.php`, product translation migrations, and `resources/views/web/pages/shop/show.blade.php`. |
| Change public routes or middleware | `routes/web.php` and the relevant middleware in `app/Http/Middleware`. |
| Change bilingual text | Add matching keys to `resources/lang/en` and `resources/lang/ar`. |
| Change cart behavior | `app/Managers`, `app/Interfaces/CartStorageInterface.php`, and `app/Http/Controllers/Web/CartController.php`. |
| Change order totals/status flow | `app/Commands/CreateOrderCommand.php`, `app/DTOs/OrderDto.php`, order models, and order resources. |
| Change coupon behavior | `app/Http/Controllers/Web/CouponValidateController.php`, `app/Models/Coupon.php`, and `app/Commands/CreateOrderCommand.php`. |

## Access Control Notes

Filament resources use `canAccess()` and `shouldRegisterNavigation()` to hide features from unauthorized users. Wholesale Requests is available to super admins, sales admins, and users with the `customers` extra permission. SEO Settings requires a super admin or `seo_settings` extra permission. Keep resource visibility and record actions aligned when adding admin capabilities.

## Testing

The project includes feature and unit tests for core behavior, including product view tracking and order logic.

```bash
# Run the full suite
php artisan test

# Run one feature test
php artisan test --filter=ProductViewTrackingTest

# Syntax-check a changed PHP file
php -l app/Http/Controllers/Web/WholesaleController.php
```

For new workflows, test both English and Arabic requests, validation failures, fallback behavior, and authorization. Wholesale changes should cover invalid Egyptian phone values, missing quote PDFs, successful lead persistence, and admin status/export/delete behavior.

## Troubleshooting

- If a quote download says unavailable, check the Wholesale Price Quote admin setting and the public storage disk path.
- If a new translation key displays as its key, add it to both `resources/lang/en` and `resources/lang/ar` and clear compiled views if needed.
- If an admin resource is missing from navigation, check its `canAccess()` result and the Filament resource discovery path in `AdminPanelProvider.php`.
- If metadata appears stale, clear Blade/config caches and confirm the active locale and `seo-page-*` record.
- If a migration fails on a fresh SQLite database, inspect migration ordering and avoid database-specific SQL in new migrations.
- If WhatsApp confirmations are not sending, check the WhatsApp Connection admin page for a disconnected state and confirm `EVOLUTION_API_URL`, `EVOLUTION_API_KEY`, and `EVOLUTION_INSTANCE` are set in `.env`.

## Roadmap / Notes for Future Development

- Keep controllers thin by moving reusable business logic into dedicated services or actions as complexity grows.
- Extract the Egyptian phone pattern into a dedicated rule if it is reused outside the wholesale form.
- Keep admin permissions explicit and clearly scoped.
- Maintain regression coverage for pricing, validation, SEO, wholesale requests, and exports.
- Keep export/reporting logic isolated in dedicated export classes.
- Continue validating new workflows with feature tests before deployment.

