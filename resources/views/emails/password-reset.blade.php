<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt lại mật khẩu</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 0 0 10px 10px;
        }
        .button {
            display: inline-block;
            background: #007bff;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
        }
        .button:hover {
            background: #0056b3;
        }
        .warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🔐 Đặt lại mật khẩu</h1>
        <p>SSO Server - Session Sharing</p>
    </div>
    
    <div class="content">
        <h2>Xin chào {{ $user->name }},</h2>
        
        <p>Bạn đã yêu cầu đặt lại mật khẩu cho tài khoản của mình. Nhấn vào nút bên dưới để tạo mật khẩu mới:</p>
        
        <div style="text-align: center;">
            <a href="{{ $resetUrl }}" class="button">Đặt lại mật khẩu</a>
        </div>
        
        <div class="warning">
            <strong>⚠️ Lưu ý quan trọng:</strong>
            <ul>
                <li>Link này chỉ có hiệu lực trong <strong>60 phút</strong></li>
                <li>Link này chỉ có thể sử dụng <strong>1 lần</strong></li>
                <li>Nếu bạn không yêu cầu đặt lại mật khẩu, hãy bỏ qua email này</li>
            </ul>
        </div>
        
        <p>Nếu nút không hoạt động, bạn có thể copy và paste link sau vào trình duyệt:</p>
        <p style="background: #e9ecef; padding: 10px; border-radius: 5px; word-break: break-all;">
            {{ $resetUrl }}
        </p>
        
        @if($redirect)
        <p><strong>Thông tin:</strong> Sau khi đặt lại mật khẩu, bạn sẽ được chuyển hướng về ứng dụng đã yêu cầu.</p>
        @endif
    </div>
    
    <div class="footer">
        <p>Email này được gửi từ hệ thống SSO Server</p>
        <p>Nếu bạn có bất kỳ câu hỏi nào, vui lòng liên hệ với quản trị viên hệ thống.</p>
        <p>© {{ date('Y') }} SSO Server. Tất cả quyền được bảo lưu.</p>
    </div>
</body>
</html>
