# MobiCard ScanAPI

Transform user checkout experiences with our intelligent card scanning technology. Let users scan or upload payment cards to automatically extract and validate card data.

- **Extract**: Card number, expiry date, brand, and EXIF metadata
- **Validate**: Automatic validation checks and risk assessment
- **Tokenize**: Seamless integration with MobiToken for secure storage
- **Flexible**: Two implementation methods with full code samples
- **Fast**: Reduce checkout time by up to 70%

---

## Installation

Follow these complete installation steps to set up the `mobicardsystems/laravel-scanapi` package in your Laravel project.

### 1. Install via Composer

Run the following command in your terminal:

```bash
composer require mobicardsystems/laravel-scanapi
```

### 2. Publish Configuration File

This copies `config/scanapi.php` to your Laravel config directory, allowing you to customize the package behavior.

```bash
php artisan vendor:publish --tag=scanapi-config
```

### 3. Add Environment Variables

Add the following credentials and settings to your `.env` file:

```env
SCANAPI_VERSION=2.0
SCANAPI_MODE=LIVE
SCANAPI_MERCHANT_ID=4
SCANAPI_API_KEY=your_api_key_here
SCANAPI_SECRET_KEY=your_secret_key_here
SCANAPI_SERVICE_ID=20000
SCANAPI_SERVICE_TYPE=1
SCANAPI_BASE_URL=[https://mobicardsystems.com/api/v1](https://mobicardsystems.com/api/v1)
```

### 4. Clear Configuration Cache

To ensure your environment changes are recognized, clear the configuration cache:

```bash
php artisan config:clear
```

If you use configuration caching in a production environment:

```bash
php artisan config:cache
```

### 5. (Optional) Publish Views

If you want to customize the look and feel of the scanner views:

```bash
php artisan vendor:publish --tag=scanapi-views
```

### 6. (Optional) Publish Assets

To publish the necessary CSS, JS, and image assets:

```bash
php artisan vendor:publish --tag=scanapi-assets
```

### 6. Test the Package

https://your-project-url/mobicard/scan
```
