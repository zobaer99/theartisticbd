# Innova E-Commerce - Advanced Single Vendor Platform

A high-performance single-vendor e-commerce platform built with Laravel, featuring advanced caching, optimization, and professional invoice printing.

## 🔧 Server Management & Troubleshooting

This repository includes comprehensive SSH server access and troubleshooting tools:

### Quick Start - Fix Forbidden Errors
```bash
# On Windows: Double-click
connect-server.bat

# On Linux/Mac/Git Bash:
bash server-manager.sh
```

### Available Tools
- **server-manager.sh** - Master control script for all operations
- **connect-server.bat** - Windows SSH connection tool with interactive menu
- **server-connect-and-fix.sh** - Unix/Linux SSH connection and fix tool
- **fix-forbidden.sh** - Automated permission and cache fixes
- **server-health-check.sh** - Comprehensive health diagnostics (35+ checks)
- **server-diagnostic.sh** - Quick diagnostic tool

### Server Details
- **IP**: 217.196.54.155
- **Port**: 65002
- **Username**: u448576780

### Documentation
- **SSH-IMPLEMENTATION-GUIDE.txt** - Complete implementation and usage guide
- **README-FIX-FORBIDDEN.txt** - Comprehensive forbidden error troubleshooting
- **QUICK-FIX-GUIDE.txt** - Fast reference for common fixes
- **CONNECT_TO_SERVER.txt** - Connection instructions
- **SERVER_SETUP_COMMANDS.txt** - Manual command reference

For complete server troubleshooting guide, see [SSH-IMPLEMENTATION-GUIDE.txt](./SSH-IMPLEMENTATION-GUIDE.txt)

## 🚀 Key Features

### Smart Cache Management System
- **Automatic Cache Invalidation** - Cache auto-clears when data is updated
- **Selective Cache Clearing** - Clear specific cache types (settings, categories, brands, products, sliders)
- **Performance Optimized** - Intelligent caching strategy with optimal durations
- **Model Observers** - Automatic cache invalidation via Laravel observers

### Cache Management Tools
```bash
# Artisan Commands
php artisan cache:clear-global                    # Clear all global cache
php artisan cache:clear-global --type=settings    # Clear only settings cache
php artisan cache:clear-global --type=categories  # Clear only categories cache
php artisan cache:clear-global --type=brands      # Clear only brands cache
php artisan cache:clear-global --type=products    # Clear only products cache
php artisan cache:clear-global --type=sliders     # Clear only sliders cache
```

### HTTP Cache Management API
```bash
POST /admin/clear-cache          # Clear all cache
POST /admin/clear-cache/settings # Clear settings cache
POST /admin/clear-cache/sliders  # Clear sliders cache
# ... etc for other types
```

### Performance Improvements
- **Database Query Optimization** - Reduced from 221+ queries to ~50-80 per page
- **Theme1-Only Optimization** - Eliminated unused theme processing
- **Aggressive Caching** - Strategic cache durations for different data types
- **Query Logging** - Slow query detection and logging

### Print Invoice Optimization
- **Responsive Design** - Mobile-friendly invoice printing
- **Professional Layout** - Clean, one-page optimized design
- **Simplified Header** - Logo-only header design
- **Print Media Queries** - Optimized for A4 printing

## 🛠️ Technical Stack

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains over 2000 video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the Laravel [Patreon page](https://patreon.com/taylorotwell).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Cubet Techno Labs](https://cubettech.com)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[Many](https://www.many.co.uk)**
- **[Webdock, Fast VPS Hosting](https://www.webdock.io/en)**
- **[DevSquad](https://devsquad.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[OP.GG](https://op.gg)**
- **[WebReinvent](https://webreinvent.com/?utm_source=laravel&utm_medium=github&utm_campaign=patreon-sponsors)**
- **[Lendio](https://lendio.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
