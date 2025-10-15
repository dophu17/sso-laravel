# 🚀 Laravel Client SSO - Quick Setup Commands

## ⚡ Tự động cài đặt SSO cho Client (Patent Monitor / Bookcase)

### Option 1: Manual Copy Files

**Tải files từ repository và copy vào client app:**

```bash
# 1. Tạo thư mục nếu chưa có
mkdir -p app/Services
mkdir -p app/Http/Middleware
mkdir -p app/Http/Controllers/Auth

# 2. Copy files (xem docs/LARAVEL-CLIENT-INTEGRATION.md để lấy code)
# - app/Services/SSOService.php
# - app/Http/Middleware/SSOAuthenticate.php
# - app/Http/Controllers/Auth/SSOController.php
# - config/sso.php (optional)
```

---

### Option 2: Artisan Command (Recommended)

Tạo command để tự động generate files.

#### 1. Tạo Artisan Command

Chạy trong **Auth Server** (auth.balocco-local.info):

```bash
php artisan make:command GenerateSSOClientFiles
```

#### 2. Code cho command

File: `app/Console/Commands/GenerateSSOClientFiles.php`

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GenerateSSOClientFiles extends Command
{
    protected $signature = 'sso:generate-client-files {--output=}';
    protected $description = 'Generate SSO client files for Laravel clients (Patent Monitor, Bookcase)';

    public function handle()
    {
        $outputPath = $this->option('output') ?: storage_path('sso-client-files');

        // Create output directory
        if (!File::exists($outputPath)) {
            File::makeDirectory($outputPath, 0755, true);
        }

        $this->info("Generating SSO client files...");

        // Generate files
        $this->generateSSOService($outputPath);
        $this->generateSSOController($outputPath);
        $this->generateSSOMiddleware($outputPath);
        $this->generateSSOConfig($outputPath);
        $this->generateEnvExample($outputPath);
        $this->generateReadme($outputPath);

        $this->info("✅ Files generated successfully!");
        $this->info("📁 Location: " . $outputPath);
        $this->line("");
        $this->info("📋 Next steps:");
        $this->line("1. Copy files to your client app (Patent Monitor / Bookcase)");
        $this->line("2. Update .env with SSO configuration");
        $this->line("3. Register middleware in bootstrap/app.php");
        $this->line("4. Add routes in routes/web.php");
        $this->line("");
        $this->info("📖 Full documentation: docs/LARAVEL-CLIENT-INTEGRATION.md");
    }

    private function generateSSOService($path)
    {
        $content = <<<'PHP'
<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SSOService
{
    protected $ssoServer;
    protected $callbackUrl;

    public function __construct()
    {
        $this->ssoServer = config('sso.server', env('SSO_SERVER'));
        $this->callbackUrl = config('sso.callback_url', env('SSO_CALLBACK_URL'));
    }

    public function getCheckUrl(): string
    {
        return $this->ssoServer . '/api/sso/verify-session?callback=' . urlencode($this->callbackUrl);
    }

    public function verifyToken(string $sessionToken): ?array
    {
        try {
            $response = Http::timeout(10)
                ->asForm()
                ->post($this->ssoServer . '/api/sso/verify-session', [
                    'sso_session' => $sessionToken
                ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['success']) && $data['success']) {
                    return $data['data'];
                }
            }

            Log::error('SSO verification failed', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return null;

        } catch (\Exception $e) {
            Log::error('SSO verification exception', ['message' => $e->getMessage()]);
            return null;
        }
    }

    public function getLogoutUrl(): string
    {
        return $this->ssoServer . '/logout?callback=' . urlencode($this->callbackUrl);
    }

    public function storeUserSession(array $userData): void
    {
        session([
            'sso_user' => [
                'id' => $userData['user_id'],
                'name' => $userData['user_name'],
                'email' => $userData['user_email'],
                'jwt_token' => $userData['jwt_token'] ?? null,
                'login_time' => $userData['login_time'] ?? now()->toIso8601String(),
                'authenticated' => true,
            ]
        ]);
    }

    public function getUser(): ?array
    {
        return session('sso_user');
    }

    public function isAuthenticated(): bool
    {
        $user = $this->getUser();
        return $user && isset($user['authenticated']) && $user['authenticated'];
    }

    public function clearSession(): void
    {
        session()->forget('sso_user');
    }
}
PHP;

        File::put($path . '/SSOService.php', $content);
        $this->line("✓ Generated: SSOService.php");
    }

    private function generateSSOController($path)
    {
        $content = <<<'PHP'
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\SSOService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SSOController extends Controller
{
    protected $ssoService;

    public function __construct(SSOService $ssoService)
    {
        $this->ssoService = $ssoService;
    }

    public function callback(Request $request)
    {
        $sessionToken = $request->query('sso_session');
        
        if (!$sessionToken) {
            Log::warning('SSO callback without session token');
            return redirect('/')->with('error', 'SSO authentication failed: No session token');
        }

        $userData = $this->ssoService->verifyToken($sessionToken);

        if (!$userData) {
            Log::error('SSO token verification failed');
            return redirect('/')->with('error', 'SSO authentication failed: Invalid token');
        }

        $this->ssoService->storeUserSession($userData);

        Log::info('SSO login successful', [
            'user_id' => $userData['user_id'],
            'user_email' => $userData['user_email']
        ]);

        return redirect()->intended('/dashboard')->with('success', 'Đăng nhập thành công!');
    }

    public function logout(Request $request)
    {
        $user = $this->ssoService->getUser();
        
        $this->ssoService->clearSession();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($user) {
            Log::info('SSO logout', ['user_id' => $user['id']]);
        }

        return redirect($this->ssoService->getLogoutUrl());
    }
}
PHP;

        File::put($path . '/SSOController.php', $content);
        $this->line("✓ Generated: SSOController.php");
    }

    private function generateSSOMiddleware($path)
    {
        $content = <<<'PHP'
<?php

namespace App\Http\Middleware;

use App\Services\SSOService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SSOAuthenticate
{
    protected $ssoService;

    public function __construct(SSOService $ssoService)
    {
        $this->ssoService = $ssoService;
    }

    public function handle(Request $request, Closure $next): Response
    {
        if ($this->ssoService->isAuthenticated()) {
            return $next($request);
        }

        return redirect($this->ssoService->getCheckUrl());
    }
}
PHP;

        File::put($path . '/SSOAuthenticate.php', $content);
        $this->line("✓ Generated: SSOAuthenticate.php");
    }

    private function generateSSOConfig($path)
    {
        $content = <<<'PHP'
<?php

return [
    'server' => env('SSO_SERVER', 'https://auth.balocco-local.info'),
    'callback_url' => env('SSO_CALLBACK_URL', env('APP_URL') . '/sso/callback'),
    'session_key' => 'sso_user',
    'timeout' => 10,
    'allowed_domains' => [
        'auth.balocco-local.info',
    ],
];
PHP;

        File::put($path . '/sso.php', $content);
        $this->line("✓ Generated: sso.php (config)");
    }

    private function generateEnvExample($path)
    {
        $content = <<<'ENV'
# SSO Configuration
SSO_SERVER=https://auth.balocco-local.info
SSO_CALLBACK_URL=${APP_URL}/sso/callback

# Session Configuration (important for SSO)
SESSION_DOMAIN=.balocco-local.info
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax
SESSION_LIFETIME=120
ENV;

        File::put($path . '/.env.sso.example', $content);
        $this->line("✓ Generated: .env.sso.example");
    }

    private function generateReadme($path)
    {
        $content = <<<'MD'
# SSO Client Files

## 📁 Files Generated

1. **SSOService.php** → Copy to `app/Services/`
2. **SSOController.php** → Copy to `app/Http/Controllers/Auth/`
3. **SSOAuthenticate.php** → Copy to `app/Http/Middleware/`
4. **sso.php** → Copy to `config/`
5. **.env.sso.example** → Merge into your `.env`

## 🚀 Installation Steps

### 1. Copy Files

```bash
# From this directory, copy to your client app
cp SSOService.php /path/to/client-app/app/Services/
cp SSOController.php /path/to/client-app/app/Http/Controllers/Auth/
cp SSOAuthenticate.php /path/to/client-app/app/Http/Middleware/
cp sso.php /path/to/client-app/config/
```

### 2. Update .env

Add these lines to your client app's `.env`:

```env
SSO_SERVER=https://auth.balocco-local.info
SSO_CALLBACK_URL=https://your-app.com/sso/callback

SESSION_DOMAIN=.balocco-local.info
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

Route::get('/sso/callback', [SSOController::class, 'callback'])->name('sso.callback');
Route::get('/sso/logout', [SSOController::class, 'logout'])->name('sso.logout');

Route::middleware(['sso.auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
});
```

### 5. Test

```bash
# Clear cache
php artisan cache:clear

# Visit protected route
# Should redirect to SSO server for login
```

## 📖 Full Documentation

See: `docs/LARAVEL-CLIENT-INTEGRATION.md`
MD;

        File::put($path . '/README.md', $content);
        $this->line("✓ Generated: README.md");
    }
}
```

---

## 🎯 Sử dụng Command

### Chạy trong Auth Server:

```bash
# Generate files to default location (storage/sso-client-files)
php artisan sso:generate-client-files

# Generate to custom location
php artisan sso:generate-client-files --output=/path/to/output
```

### Output:

```
Generating SSO client files...
✓ Generated: SSOService.php
✓ Generated: SSOController.php
✓ Generated: SSOAuthenticate.php
✓ Generated: sso.php (config)
✓ Generated: .env.sso.example
✓ Generated: README.md
✅ Files generated successfully!
📁 Location: /path/to/output

📋 Next steps:
1. Copy files to your client app (Patent Monitor / Bookcase)
2. Update .env with SSO configuration
3. Register middleware in bootstrap/app.php
4. Add routes in routes/web.php

📖 Full documentation: docs/LARAVEL-CLIENT-INTEGRATION.md
```

---

## 📦 Copy Files to Client Apps

### Patent Monitor:

```bash
# Sau khi generate, copy files
cd storage/sso-client-files

# Copy to Patent Monitor
cp SSOService.php /path/to/patent-monitor/app/Services/
cp SSOController.php /path/to/patent-monitor/app/Http/Controllers/Auth/
cp SSOAuthenticate.php /path/to/patent-monitor/app/Http/Middleware/
cp sso.php /path/to/patent-monitor/config/
```

### Bookcase:

```bash
# Copy to Bookcase
cp SSOService.php /path/to/bookcase/app/Services/
cp SSOController.php /path/to/bookcase/app/Http/Controllers/Auth/
cp SSOAuthenticate.php /path/to/bookcase/app/Http/Middleware/
cp sso.php /path/to/bookcase/config/
```

---

## 🔧 Configuration for Each Client

### Patent Monitor (.env):

```env
APP_NAME="Patent Monitor"
APP_URL=https://patent-monitor.balocco-local.info

SSO_SERVER=https://auth.balocco-local.info
SSO_CALLBACK_URL=https://patent-monitor.balocco-local.info/sso/callback

SESSION_DOMAIN=.balocco-local.info
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax
SESSION_LIFETIME=120
```

### Bookcase (.env):

```env
APP_NAME="Bookcase"
APP_URL=https://bookcase.balocco-local.info

SSO_SERVER=https://auth.balocco-local.info
SSO_CALLBACK_URL=https://bookcase.balocco-local.info/sso/callback

SESSION_DOMAIN=.balocco-local.info
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax
SESSION_LIFETIME=120
```

---

## ✅ Quick Checklist

### For Each Client App:

- [ ] Generate files: `php artisan sso:generate-client-files`
- [ ] Copy files to client app
- [ ] Update `.env` with SSO config
- [ ] Register middleware in `bootstrap/app.php`
- [ ] Add routes in `routes/web.php`
- [ ] Clear cache: `php artisan cache:clear`
- [ ] Test: Visit `/dashboard` → should redirect to SSO
- [ ] Test: Login → should redirect back with user info
- [ ] Test: Visit other client → should auto-login

---

**Updated:** 2025-10-15

