# E-Commerce Marketplace

A full-featured multi-vendor e-commerce marketplace built with **Laravel 12**, **Tailwind CSS v4**, and **Alpine.js**. The platform supports four distinct user roles — Admin, Vendor, Customer, and Delivery Agent — each with dedicated dashboards and workflows.

## Tech Stack

| Layer     | Technology                           |
|-----------|--------------------------------------|
| Backend   | PHP 8.4, Laravel 12                  |
| Frontend  | Blade, Tailwind CSS v4, Alpine.js v3 |
| Database  | PostgreSQL                           |
| Payments  | Stripe                               |
| Bundler   | Vite 7                               |
| Testing   | Pest 4                               |
| PDF       | DomPDF                               |

## Features

### Storefront
- Product catalog with category & brand filtering
- Full-text product search
- Product detail pages with variants & attributes
- Shopping cart with coupon support
- Stripe checkout with webhook integration
- Vendor store pages
- Campaign & promotional landing pages
- Wishlist & recently viewed products
- Customer reviews with image uploads

### Customer Dashboard
- Order history & order detail views
- Return & refund requests
- Dispute management
- Address book management
- Messaging / conversations with vendors
- Profile management
- Notification center

### Vendor Dashboard
- Sales analytics & revenue reports
- Product management (CRUD, images, variants, attributes)
- Inventory management with low-stock alerts
- Order management & fulfillment
- Coupon & campaign management
- Shipment tracking
- Return request handling
- Payout history
- Store settings & profile
- Messaging with customers

### Admin Panel
- Platform-wide dashboard & analytics
- User management
- Product moderation (approval workflow)
- Category & brand management
- Order management with bulk actions
- Banner management
- Campaign management
- Shipment oversight
- Dispute resolution
- Return management
- Vendor payout processing
- Revenue & sales reports (with PDF export)
- App-wide settings (logo, currency, shipping fees, tax, etc.)

### Delivery Agent Dashboard
- Assigned shipment management
- Delivery status updates & tracking events

### Authentication & Security
- Email/password login with rate limiting
- Social login via Laravel Socialite
- Email verification
- Password reset flow
- Role-based access control with policies & middleware

### Emails & Notifications
- Order confirmation & status update emails
- Vendor new-order notifications
- Abandoned cart reminders
- Return status & vendor return notifications
- Password reset emails
- In-app notification system

## Architecture

```
app/
├── Console/          # Artisan commands (e.g. low-stock alerts)
├── Enums/            # OrderStatus, UserRole, PaymentStatus, etc.
├── Events/           # Domain events
├── Http/
│   ├── Controllers/
│   │   ├── Admin/        # Admin panel controllers
│   │   ├── Api/          # REST API (cart, search, wishlist, webhooks)
│   │   ├── Auth/         # Login, register, social auth, password reset
│   │   ├── Customer/     # Customer dashboard controllers
│   │   ├── Delivery/     # Delivery agent controllers
│   │   ├── Storefront/   # Public storefront controllers
│   │   └── Vendor/       # Vendor dashboard controllers
│   └── Requests/         # Form request validation classes
├── Listeners/
├── Mail/             # Mailable classes
├── Models/           # Eloquent models (31 models)
├── Notifications/    # Per-role notification classes
├── Policies/         # Authorization policies
├── Providers/
└── Services/         # Business logic (Cart, Order, Payment, Stripe, etc.)
```

## Prerequisites

- PHP 8.2+
- Composer
- Node.js & npm
- PostgreSQL
- [Laravel Herd](https://herd.laravel.com/) (recommended) or any PHP development server

## Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd e-commerce
   ```

2. **Run the setup script**
   ```bash
   composer setup
   ```
   This will install PHP & JS dependencies, generate an app key, run migrations, and build assets.

3. **Configure environment**

   Copy `.env.example` to `.env` (done automatically by `composer setup`) and update:
   ```dotenv
   # Database
   DB_CONNECTION=pgsql
   DB_DATABASE=e_commerce
   DB_USERNAME=postgres
   DB_PASSWORD=your_password

   # Stripe
   STRIPE_KEY=pk_test_...
   STRIPE_SECRET=sk_test_...
   STRIPE_WEBHOOK_SECRET=whsec_...

   # Marketplace defaults
   MARKETPLACE_CURRENCY=USD
   MARKETPLACE_VENDOR_COMMISSION_RATE=10
   ```

4. **Seed the database** (optional)
   ```bash
   php artisan db:seed
   ```

5. **Access the application**

   If using Laravel Herd, the app is automatically available at `http://e-commerce.test`.

   Otherwise, start the dev server:
   ```bash
   composer run dev
   ```

## Demo Accounts

After running `php artisan db:seed`, the following accounts are available (all use password: `password`):

| Role           | Email                        | Dashboard Route        |
|----------------|------------------------------|------------------------|
| Admin          | `admin@marketplace.test`     | `/admin/dashboard`     |
| Delivery Agent | `delivery@marketplace.test`  | `/delivery/dashboard`  |
| Vendor         | *(5 random seeded emails)*   | `/vendor/dashboard`    |
| Customer       | *(10 random seeded emails)*  | Storefront (`/`)       |

Login at `/login`. After authentication, users are automatically redirected to their role-specific dashboard. Social login (Google & GitHub) is also available if configured.

## Development

```bash
# Start all services (server, queue, logs, vite) concurrently
composer run dev

# Build frontend assets for production
npm run build

# Run the test suite
composer run test

# Run specific tests
php artisan test --compact --filter=OrderTest

# Format PHP code
vendor/bin/pint --dirty
```

## Testing

The project uses **Pest 4** for testing with feature and unit test suites covering:

- Authentication & authorization flows
- Storefront browsing & checkout
- Admin bulk operations & moderation
- Vendor reports & order management
- Delivery shipment workflows
- Stripe payment & webhook handling
- Campaign & coupon logic
- Dispute & return workflows

```bash
php artisan test --compact
```

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
