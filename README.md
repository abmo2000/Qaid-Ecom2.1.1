<div align="center">

# 🛍️ Qaid Store

**A modern, full-featured e-commerce platform built with Laravel 12**

*Customer storefront · Filament admin dashboard · Analytics · Reporting*

[![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?style=flat&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.4-777BB4?style=flat&logo=php&logoColor=white)](https://www.php.net)
[![Filament](https://img.shields.io/badge/Filament-4-F59E0B?style=flat)](https://filamentphp.com)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=flat&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)
[![License](https://img.shields.io/badge/License-MIT-blue.svg)](#license)

</div>

---

## 📖 Overview

**Qaid Store** is a complete e-commerce application built on **Laravel 12**, combining a polished customer-facing storefront with a powerful **Filament**-based admin panel. It handles the full lifecycle of an online store — product discovery, cart and checkout, coupons and discounts, order processing, reporting, and operational monitoring — while giving admins the tools they need to run the business day to day.

The project is organized around two core areas:

| Area | Description |
|---|---|
| 🛒 **Storefront** | Public-facing pages for browsing products, packages, and routines; cart and checkout for guests and authenticated customers; order tracking. |
| ⚙️ **Admin Panel** | A Filament-powered dashboard for managing products, categories, coupons, customers, sales reporting, and security monitoring. |

---

## ✨ Key Features

- 🛍️ Public storefront for products, packages, routines, and home content
- 🛒 Guest and authenticated cart & checkout flows
- 🏷️ Coupon validation with discount-stacking protection
- 📦 Order creation, tracking, and invoice access
- 🧑‍💼 Full Filament admin suite — products, categories, routines, coupons, customers, sales
- 📊 Sales summary reporting with one-click Excel export
- 👀 Product view analytics ("X users have seen this product")
- 🌍 Multi-language storefront (English & Arabic)
- 🔐 Security event logging and admin activity monitoring

---

## 🧰 Tech Stack

### Backend
- **PHP** 8.4
- **Laravel** 12
- **Filament** 4
- **Laravel Fortify** — authentication
- **Laravel Socialite** — social login
- **Astrotomic Laravel Translatable** — multi-language content
- **MySQL** (production) / **SQLite** (local testing)
- **Maatwebsite Excel** — spreadsheet exports

### Frontend
- **Blade** templates
- **Tailwind CSS**
- **Vite**
- **Alpine.js**
- Vanilla JS for cart & checkout interactions
- **Font Awesome**

---

## 🆕 Recent Highlights

A summary of the most recent features and stability improvements now powering the live business logic:

<details>
<summary><strong>1. Coupons no longer stack with product discounts</strong></summary>

<br>

Coupon validation now rejects any attempt to apply a coupon to a product that already carries a sale price or discount, preventing conflicting final totals at checkout.

**Touched files:** `CouponValidateController.php` · `CreateOrderCommand.php` · `Coupon.php` · `checkout.blade.php`
</details>

<details>
<summary><strong>2. Brand filter on the admin product list</strong></summary>

<br>

Admins can now filter the product catalog by brand directly from the Filament products table — a big time-saver for larger catalogs.

**Touched files:** `ListProducts.php` · `ProductsTable.php` · `ProductResource.php`
</details>

<details>
<summary><strong>3. Product publish / unpublish toggle</strong></summary>

<br>

Products can be marked as published or unpublished from the admin panel. Unpublished products are automatically hidden from the storefront — no manual data cleanup required.

**Touched files:** `add_is_published_to_products_table.php` migration · `ProductForm.php` · `ProductsTable.php` · `HomeController.php` · `ShopController.php`
</details>

<details>
<summary><strong>4. Pending orders dashboard widget</strong></summary>

<br>

A new widget on the admin dashboard surfaces a live count and value summary of orders awaiting action, giving operations teams instant visibility into bottlenecks.

**Touched files:** `PendingOrdersOverviewWidget.php` · `AdminPanelProvider.php`
</details>

<details>
<summary><strong>5. Excel export for the Sales Summary report</strong></summary>

<br>

A header action on the Sales Summary page now generates a downloadable `Sales-Summary-Report.xlsx` workbook containing order data and a summary sheet.

**Touched files:** `ListSalesSummary.php` · `SalesSummaryExport.php`
</details>

<details>
<summary><strong>6. Excel export formatting matched to business template</strong></summary>

<br>

The export was refined to match a provided sample workbook: formula-driven totals, averages, and status-based breakdowns, plus proper header styling, number formats, borders, column widths, and EGP-formatted currency values across two sheets (**Sales Orders** and **Summary**).

**Touched files:** `SalesSummaryExport.php`
</details>

<details>
<summary><strong>7. Product view analytics</strong></summary>

<br>

Each product page now tracks unique views (once per session or authenticated user) and displays a live counter — e.g. *"12 users have seen this product"* — giving store owners insight into product popularity without inflated repeat-view counts.

**Touched files:** `create_product_views_table.php` migration · `ProductView.php` · `Product.php` · `ProductController.php` · `shop/show.blade.php` · `ProductViewTrackingTest.php`
</details>

<details>
<summary><strong>8. Migration & database stability improvements</strong></summary>

<br>

A legacy migration was updated to remove database-specific syntax, ensuring smooth SQLite-based local testing and more reliable migrations across environments.

**Touched files:** `add_columns_to_orders_table.php`
</details>

<details>
<summary><strong>9. Security logging & admin auditing</strong></summary>

<br>

Admin login attempts, access events, and failed/protected-path attempts are now logged, with a dedicated dashboard widget surfacing recent security activity for review.

**Touched files:** `LogSecurityEventJob.php` · `SecurityEventLogger.php` · `CustomLogin.php` · `DangerZoneWidget.php` · `SecurityEvent.php`
</details>

<details>
<summary><strong>10. Customer profile & order tracking improvements</strong></summary>

<br>

Customers can now edit their profile details, follow clearer order status progression, submit contact form messages for admin review, and are redirected properly back to the storefront after login.

**Touched files:** `FortifyServiceProvider.php` · `UpdateUserProfileInformation.php` · `ProfileController.php` · `profile.blade.php` · `orders/show.blade.php` · `navbar.blade.php` · `CustomerResource.php`
</details>

---

## 🚀 Getting Started

### Prerequisites

- PHP 8.4+
- Composer
- Node.js & npm
- MySQL (or SQLite for local development)

### Installation

```bash
# 1. Clone the repository
git clone https://github.com/<your-username>/qaid-store.git
cd qaid-store

# 2. Install PHP & JS dependencies
composer install
npm install

# 3. Set up your environment
cp .env.example .env
php artisan key:generate

# 4. Configure your .env
#    (database, queue, mail, and app URL settings)

# 5. Run migrations
php artisan migrate

# 6. Build frontend assets
npm run build

# 7. Start the dev environment
composer run dev
```

`composer run dev` runs the Laravel app and frontend tooling together for local development.

---

## 🛠️ Useful Commands

| Command | Description |
|---|---|
| `php artisan migrate` | Run database migrations |
| `php artisan test` | Run the test suite |
| `php artisan queue:work` | Process queued jobs |
| `npm run dev` | Start Vite in development mode |
| `npm run build` | Build production frontend assets |

---

## 📁 Project Structure

```text
app/
├── Commands/
├── DTOs/
├── Enums/
├── Events/
├── Factories/
├── Filament/
├── Helpers/
├── Http/
├── Interfaces/
├── Jobs/
├── Listeners/
├── Mail/
├── Models/
├── Providers/
├── Rules/
├── Services/
└── Traits/

config/
├── app.php
├── database.php
├── filament.php
├── mail.php
├── queue.php
└── session.php

database/
├── factories/
├── migrations/
└── seeders/

resources/
├── css/
├── js/
├── lang/
└── views/

routes/
├── web.php
└── console.php

public/
├── assets/
├── build/
├── css/
└── js/

storage/
├── app/
├── framework/
└── logs/

tests/
├── Feature/
└── Unit/
```

---

## 🔄 Key Business Flows

### Storefront
1. Customer visits a product, collection, or home page.
2. A controller gathers product or order data.
3. The Blade view renders the storefront content.
4. Customer adds items to the cart and proceeds through checkout.

### Checkout & Orders
1. Customer submits order details and an optional coupon code.
2. Coupon logic and discount validation run.
3. Order creation is processed through the command and event pipeline.
4. The order becomes visible to admins in the Filament panel.
5. The customer can access their invoice and track the order status.

### Admin
1. Admins log in through the Filament panel.
2. Role and permission checks control access.
3. Admins manage products, orders, customers, and reports.
4. Summary exports and dashboard widgets provide operational visibility.

---

## ✅ Testing

The project includes feature tests covering core behaviors such as product view tracking and order logic.

```bash
# Run the full test suite
php artisan test

# Run a specific test
php artisan test --filter=ProductViewTrackingTest
```

> The `ProductViewTrackingTest` verifies that a product view is only counted once per session/user.

---

## 🗺️ Roadmap / Notes for Future Development

- Keep controllers thin — move reusable business logic into dedicated services or actions as complexity grows.
- Keep admin permissions explicit and clearly scoped.
- Maintain strong regression coverage for pricing, orders, and storefront analytics.
- Keep export/reporting logic isolated in dedicated export classes, separate from view logic.
- Continue validating new workflows with feature tests before deployment.


