<?php
/**
 * Example: Your Web Callback Handler
 * Port: 8001 (or any other port different from SSO)
 * 
 * This demonstrates how to receive JWT token from SSO
 * and create session on YOUR web
 */

session_start();

// Receive parameters from SSO callback
$status = $_GET['status'] ?? null;
$userId = $_GET['user_id'] ?? null;
$userName = $_GET['user_name'] ?? null;
$userEmail = $_GET['user_email'] ?? null;
$jwtToken = $_GET['jwt_token'] ?? null;
$loginTime = $_GET['login_time'] ?? $_GET['register_time'] ?? null;

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Web - SSO Login Success</title>
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
        h1 { color: #1f2937; text-align: center; margin-bottom: 10px; }
        .subtitle { color: #6b7280; text-align: center; margin-bottom: 30px; }
        .section {
            background: #f9fafb;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .section h2 { color: #1f2937; margin-bottom: 15px; font-size: 18px; }
        .info-grid { display: grid; grid-template-columns: 150px 1fr; gap: 10px; }
        .label { font-weight: 600; color: #6b7280; }
        .value { color: #1f2937; word-break: break-all; }
        .token-box {
            background: #1f2937;
            color: #10b981;
            padding: 15px;
            border-radius: 5px;
            font-family: monospace;
            font-size: 11px;
            word-break: break-all;
            max-height: 150px;
            overflow-y: auto;
            margin: 10px 0;
        }
        .btn {
            background: #3b82f6;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            margin-right: 10px;
            transition: background 0.3s;
        }
        .btn:hover { background: #2563eb; }
        .btn-success { background: #10b981; }
        .btn-success:hover { background: #059669; }
        .response { margin-top: 15px; display: none; }
        .status { text-align: center; margin: 10px 0; font-weight: 600; display: none; }
        .status.show { display: block; }
    </style>
</head>
<body>
    <div class="container">
        <?php if ($status === 'success' && $jwtToken): ?>
            <?php
            // Save JWT token to YOUR web's session
            $_SESSION['sso_jwt_token'] = $jwtToken;
            $_SESSION['user_id'] = $userId;
            $_SESSION['user_name'] = $userName;
            $_SESSION['user_email'] = $userEmail;
            $_SESSION['logged_in'] = true;
            $_SESSION['login_time'] = $loginTime;
            
            // Verify token with SSO API
            $ch = curl_init('http://localhost:8000/api/user');
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $jwtToken,
                'Accept: application/json'
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            $apiData = $httpCode == 200 ? json_decode($response, true) : null;
            ?>
            
            <div class="success">🎉</div>
            <h1>✅ Login Successful on YOUR WEB!</h1>
            <p class="subtitle">You are now logged in on port <?php echo $_SERVER['SERVER_PORT']; ?></p>
            
            <div class="section">
                <h2>👤 User Info (from callback)</h2>
                <div class="info-grid">
                    <div class="label">User ID:</div>
                    <div class="value"><?php echo htmlspecialchars($userId); ?></div>
                    <div class="label">Name:</div>
                    <div class="value"><?php echo htmlspecialchars($userName); ?></div>
                    <div class="label">Email:</div>
                    <div class="value"><?php echo htmlspecialchars($userEmail); ?></div>
                    <div class="label">Login Time:</div>
                    <div class="value"><?php echo htmlspecialchars($loginTime); ?></div>
                </div>
            </div>
            
            <div class="section">
                <h2>🔑 JWT Token (saved in YOUR session)</h2>
                <div class="token-box"><?php echo htmlspecialchars($jwtToken); ?></div>
                <button class="btn" onclick="copyToken()">📋 Copy Token</button>
                <div class="status" id="copy-status">✅ Token copied!</div>
            </div>
            
            <div class="section">
                <h2>✅ API Verification Result</h2>
                <?php if ($apiData && $apiData['success']): ?>
                    <p style="color: #10b981; font-weight: 600;">✅ Token verified successfully!</p>
                    <pre style="background: #1f2937; color: #10b981; padding: 15px; border-radius: 5px; font-size: 12px; overflow-x: auto;"><?php echo json_encode($apiData, JSON_PRETTY_PRINT); ?></pre>
                <?php else: ?>
                    <p style="color: #ef4444; font-weight: 600;">❌ Token verification failed!</p>
                    <p>HTTP Code: <?php echo $httpCode; ?></p>
                <?php endif; ?>
            </div>
            
            <div class="section">
                <h2>💾 YOUR Web Session (Port <?php echo $_SERVER['SERVER_PORT']; ?>)</h2>
                <pre style="background: #f3f4f6; padding: 15px; border-radius: 5px; font-size: 12px; overflow-x: auto;"><?php echo print_r($_SESSION, true); ?></pre>
                <p style="margin-top: 10px; color: #10b981; font-weight: 600;">
                    ✅ Session created on YOUR web!<br>
                    ✅ You can now access protected pages on port <?php echo $_SERVER['SERVER_PORT']; ?>
                </p>
            </div>
            
            <div class="section">
                <h2>🚀 Test API from YOUR web</h2>
                <button class="btn btn-success" onclick="testAPI()">Test API Now</button>
                <div class="response" id="api-response"></div>
            </div>
            
            <script>
                const jwtToken = '<?php echo addslashes($jwtToken); ?>';
                
                function copyToken() {
                    navigator.clipboard.writeText(jwtToken).then(() => {
                        document.getElementById('copy-status').classList.add('show');
                        setTimeout(() => {
                            document.getElementById('copy-status').classList.remove('show');
                        }, 2000);
                    });
                }
                
                function testAPI() {
                    const responseDiv = document.getElementById('api-response');
                    responseDiv.style.display = 'block';
                    responseDiv.innerHTML = '<p>Testing API...</p>';
                    
                    fetch('http://localhost:8000/api/user', {
                        headers: {
                            'Authorization': 'Bearer ' + jwtToken,
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        responseDiv.innerHTML = '<pre style="background: #1f2937; color: #10b981; padding: 15px; border-radius: 5px; margin-top: 10px;">' + JSON.stringify(data, null, 2) + '</pre>';
                    })
                    .catch(err => {
                        responseDiv.innerHTML = '<p style="color: red;">Error: ' + err.message + '</p>';
                    });
                }
            </script>
            
        <?php else: ?>
            <h1>❌ Login Failed or No Callback Data</h1>
            <p>Please login via SSO first:</p>
            <a href="http://localhost:8000/login?callback=<?php echo urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" class="btn">
                Login with SSO
            </a>
        <?php endif; ?>
    </div>
</body>
</html>

