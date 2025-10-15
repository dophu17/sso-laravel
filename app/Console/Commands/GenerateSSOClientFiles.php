<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GenerateSSOClientFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sso:generate-client-files {--output=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate SSO client files for Laravel clients (Patent Monitor, Bookcase)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $outputPath = $this->option('output') ?: storage_path('sso-client-files');

        // Create output directory
        if (!File::exists($outputPath)) {
            File::makeDirectory($outputPath, 0755, true);
        }

        $this->info("🔐 Generating SSO client files...");
        $this->newLine();

        // Generate files
        $this->generateSSOService($outputPath);
        $this->generateSSOController($outputPath);
        $this->generateSSOMiddleware($outputPath);
        $this->generateSSOConfig($outputPath);
        $this->generateEnvExample($outputPath);
        $this->generateReadme($outputPath);

        $this->newLine();
        $this->info("✅ Files generated successfully!");
        $this->info("📁 Location: " . $outputPath);
        $this->newLine();
        
        $this->info("📋 Next steps:");
        $this->line("1. Copy files to your client app (Patent Monitor / Bookcase)");
        $this->line("2. Update .env with SSO configuration");
        $this->line("3. Register middleware in bootstrap/app.php");
        $this->line("4. Add routes in routes/web.php");
        $this->newLine();
        
        $this->info("📖 Full documentation: docs/LARAVEL-CLIENT-INTEGRATION.md");

        return Command::SUCCESS;
    }

    private function generateSSOService($path)
    {
        $content = $this->getSSOServiceContent();
        File::put($path . '/SSOService.php', $content);
        $this->line("✓ Generated: SSOService.php");
    }

    private function generateSSOController($path)
    {
        $content = $this->getSSOControllerContent();
        File::put($path . '/SSOController.php', $content);
        $this->line("✓ Generated: SSOController.php");
    }

    private function generateSSOMiddleware($path)
    {
        $content = $this->getSSOMiddlewareContent();
        File::put($path . '/SSOAuthenticate.php', $content);
        $this->line("✓ Generated: SSOAuthenticate.php");
    }

    private function generateSSOConfig($path)
    {
        $content = $this->getSSOConfigContent();
        File::put($path . '/sso.php', $content);
        $this->line("✓ Generated: sso.php (config)");
    }

    private function generateEnvExample($path)
    {
        $content = $this->getEnvExampleContent();
        File::put($path . '/.env.sso.example', $content);
        $this->line("✓ Generated: .env.sso.example");
    }

    private function generateReadme($path)
    {
        $content = $this->getReadmeContent();
        File::put($path . '/README.md', $content);
        $this->line("✓ Generated: README.md");
    }

    private function getSSOServiceContent()
    {
        return <<<'PHP'
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

    /**
     * Get SSO check URL
     */
    public function getCheckUrl(): string
    {
        return $this->ssoServer . '/api/sso/verify-session?callback=' . urlencode($this->callbackUrl);
    }

    /**
     * Verify SSO session token
     */
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

    /**
     * Get SSO logout URL
     */
    public function getLogoutUrl(): string
    {
        return $this->ssoServer . '/logout?callback=' . urlencode($this->callbackUrl);
    }

    /**
     * Store user in session
     */
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

    /**
     * Get user from session
     */
    public function getUser(): ?array
    {
        return session('sso_user');
    }

    /**
     * Check if authenticated
     */
    public function isAuthenticated(): bool
    {
        $user = $this->getUser();
        return $user && isset($user['authenticated']) && $user['authenticated'];
    }

    /**
     * Clear session
     */
    public function clearSession(): void
    {
        session()->forget('sso_user');
    }
}
PHP;
    }

    private function getSSOControllerContent()
    {
        return <<<'PHP'
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

    /**
     * Handle SSO callback
     */
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

    /**
     * Handle SSO logout
     */
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
    }

    private function getSSOMiddlewareContent()
    {
        return <<<'PHP'
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

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($this->ssoService->isAuthenticated()) {
            return $next($request);
        }

        return redirect($this->ssoService->getCheckUrl());
    }
}
PHP;
    }

    private function getSSOConfigContent()
    {
        return <<<'PHP'
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
    }

    private function getEnvExampleContent()
    {
        return <<<'ENV'
# SSO Configuration
SSO_SERVER=https://auth.balocco-local.info
SSO_CALLBACK_URL=${APP_URL}/sso/callback

# Session Configuration (important for SSO)
SESSION_DOMAIN=.balocco-local.info
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax
SESSION_LIFETIME=120
ENV;
    }

    private function getReadmeContent()
    {
        return <<<'MD'
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
MD;
    }
}
