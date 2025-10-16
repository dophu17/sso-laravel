# SSO Client Files

## 📁 Generated Files

1. **SSOService.php** → `app/Services/`
2. **SSOController.php** → `app/Http/Controllers/Auth/`
3. **SSOAuthenticate.php** → `app/Http/Middleware/`
4. **sso.php** → `config/`
5. **.env.sso.example** → Merge into `.env`

## 🚀 Installation

### 1. Copy Files to Your Client App

```bash
cp SSOService.php /path/to/client/app/Services/
cp SSOController.php /path/to/client/app/Http/Controllers/Auth/
cp SSOAuthenticate.php /path/to/client/app/Http/Middleware/
cp sso.php /path/to/client/config/
```

### 2. Update .env

```env
SSO_SERVER=https://auth.your-domain.com
SSO_CALLBACK_URL=https://your-app.com/sso/callback

SESSION_DOMAIN=.your-domain.com
SESSION_COOKIE=your_session_name
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax
```

### 3. Register Middleware

In `bootstrap/app.php`:

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'sso.auth' => \App\Http\Middleware\SSOAuthenticate::class,
    ]);
})
```

### 4. Add Routes

In `routes/web.php`:

```php
use App\Http\Controllers\Auth\SSOController;

Route::get('/sso/callback', [SSOController::class, 'callback']);
Route::get('/sso/logout', [SSOController::class, 'logout']);

Route::middleware(['sso.auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
});
```

### 5. Test

```bash
php artisan cache:clear
# Visit /dashboard → should redirect to SSO
```

## 📖 Documentation

See full guide: `docs/LARAVEL-CLIENT-INTEGRATION.md`