<?php
/**
 * Example: Callback Handler with SSO Session Verification
 * 
 * This demonstrates how to verify SSO session and get user data
 */

session_start();

// Get SSO session token from URL
$ssoSessionToken = $_GET['sso_session'] ?? null;
$status = $_GET['status'] ?? null;

if ($status === 'success' && $ssoSessionToken) {
    // Verify SSO session with API
    $ch = curl_init('http://localhost:8000/api/sso/verify-session');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
        'sso_session' => $ssoSessionToken
    ]));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode == 200) {
        $sessionData = json_decode($response, true);
        
        if ($sessionData['success']) {
            // Save to YOUR web's session
            $_SESSION['sso_verified'] = true;
            $_SESSION['user_id'] = $sessionData['data']['user_id'];
            $_SESSION['user_name'] = $sessionData['data']['user_name'];
            $_SESSION['user_email'] = $sessionData['data']['user_email'];
            $_SESSION['jwt_token'] = $sessionData['data']['jwt_token'];
            $_SESSION['login_time'] = $sessionData['data']['login_time'];
            $_SESSION['logged_in'] = true;
            
            $userData = $sessionData['data'];
            $verified = true;
        }
    }
}

$verified = $verified ?? false;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Web - SSO Login</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: system-ui, -apple-system, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        .success { color: #10b981; font-size: 48px; text-align: center; margin-bottom: 20px; }
        .error { color: #ef4444; font-size: 48px; text-align: center; margin-bottom: 20px; }
        h1 { color: #1f2937; text-align: center; margin-bottom: 10px; }
        .subtitle { color: #6b7280; text-align: center; margin-bottom: 30px; font-size: 18px; }
        .section {
            background: #f9fafb;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid #e5e7eb;
        }
        .section h2 { color: #1f2937; margin-bottom: 15px; font-size: 18px; font-weight: 600; }
        .info-grid { display: grid; grid-template-columns: 150px 1fr; gap: 10px; margin-bottom: 10px; }
        .label { font-weight: 600; color: #6b7280; }
        .value { color: #1f2937; word-break: break-all; }
        .highlight { background: #dbeafe; padding: 20px; border-radius: 8px; border-left: 4px solid #3b82f6; }
        .highlight strong { color: #1e40af; }
        pre {
            background: #1f2937;
            color: #10b981;
            padding: 15px;
            border-radius: 8px;
            font-size: 12px;
            overflow-x: auto;
            margin: 10px 0;
        }
        .btn {
            display: inline-block;
            background: #3b82f6;
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: background 0.3s;
        }
        .btn:hover { background: #2563eb; }
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }
        .badge-success { background: #d1fae5; color: #065f46; }
        .badge-error { background: #fee2e2; color: #991b1b; }
    </style>
</head>
<body>
    <div class="container">
        <?php if ($verified): ?>
            <div class="success">🎉</div>
            <h1>✅ SSO Login Verified!</h1>
            <p class="subtitle">User đã được xác thực thành công qua SSO Server</p>
            
            <div class="section">
                <h2>👤 User Information</h2>
                <div class="info-grid">
                    <div class="label">User ID:</div>
                    <div class="value"><?php echo htmlspecialchars($userData['user_id']); ?></div>
                    <div class="label">Name:</div>
                    <div class="value"><?php echo htmlspecialchars($userData['user_name']); ?></div>
                    <div class="label">Email:</div>
                    <div class="value"><?php echo htmlspecialchars($userData['user_email']); ?></div>
                    <div class="label">Login Time:</div>
                    <div class="value"><?php echo htmlspecialchars($userData['login_time']); ?></div>
                </div>
            </div>
            
            <div class="section">
                <h2>✅ Session Status</h2>
                <div class="highlight">
                    <p><strong>✅ User logged in on YOUR web (Port <?php echo $_SERVER['SERVER_PORT']; ?>)</strong></p>
                    <p style="margin-top: 10px; color: #6b7280;">Session được tạo và lưu trên server của bạn</p>
                </div>
            </div>
            
            <div class="section">
                <h2>💾 YOUR Web Session Data</h2>
                <pre><?php echo json_encode($_SESSION, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES); ?></pre>
                <p style="margin-top: 10px;">
                    <span class="badge badge-success">Session Active</span>
                    <span style="margin-left: 10px; color: #6b7280;">User có thể truy cập protected pages</span>
                </p>
            </div>
            
            <div class="section">
                <h2>🔑 JWT Token (saved in session)</h2>
                <p style="color: #6b7280; font-size: 14px; margin-bottom: 10px;">Token này được lưu trong $_SESSION và có thể dùng cho API calls</p>
                <pre><?php echo htmlspecialchars($_SESSION['jwt_token']); ?></pre>
            </div>
            
            <div class="section">
                <h2>🎯 Next Steps</h2>
                <ul style="list-style: none; padding: 0;">
                    <li style="padding: 10px; margin-bottom: 8px; background: #f3f4f6; border-radius: 5px;">
                        ✅ User đã login trên YOUR web
                    </li>
                    <li style="padding: 10px; margin-bottom: 8px; background: #f3f4f6; border-radius: 5px;">
                        ✅ Session được tạo và lưu trên server
                    </li>
                    <li style="padding: 10px; margin-bottom: 8px; background: #f3f4f6; border-radius: 5px;">
                        ✅ JWT token sẵn sàng để call API
                    </li>
                    <li style="padding: 10px; background: #f3f4f6; border-radius: 5px;">
                        ✅ User có thể access protected pages
                    </li>
                </ul>
            </div>
            
            <div style="text-align: center; margin-top: 30px;">
                <a href="<?php echo $_SERVER['REQUEST_URI']; ?>" class="btn">🔄 Refresh Page</a>
                <a href="http://localhost:8000" class="btn" style="background: #6b7280;">🏠 Back to SSO</a>
            </div>
            
        <?php else: ?>
            <div class="error">❌</div>
            <h1>Login Required</h1>
            <p class="subtitle">Bạn chưa login hoặc session đã hết hạn</p>
            
            <div class="section">
                <h2>🔐 Login via SSO</h2>
                <p style="margin-bottom: 15px; color: #6b7280;">Click button bên dưới để login qua SSO Server</p>
                <a href="http://localhost:8000/login?callback=<?php echo urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" class="btn">
                    Login with SSO
                </a>
            </div>
            
            <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
                <div class="section">
                    <h2>ℹ️ Session Info</h2>
                    <p><span class="badge badge-success">Session exists</span></p>
                    <pre><?php echo json_encode($_SESSION, JSON_PRETTY_PRINT); ?></pre>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>

