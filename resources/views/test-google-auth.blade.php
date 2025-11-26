<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>اختبار تسجيل الدخول عبر Google</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            padding: 40px;
            max-width: 600px;
            width: 100%;
        }
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
            font-size: 28px;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 12px 24px;
            background: #4285f4;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            width: 100%;
            margin-bottom: 20px;
        }
        .btn:hover {
            background: #357ae8;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(66, 133, 244, 0.4);
        }
        .btn:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
        }
        .result {
            margin-top: 30px;
            padding: 20px;
            background: #f5f5f5;
            border-radius: 8px;
            display: none;
        }
        .result.show {
            display: block;
        }
        .result.success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        .result.error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        .result h3 {
            margin-top: 0;
            margin-bottom: 15px;
        }
        .result pre {
            background: white;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
            font-size: 12px;
            max-height: 400px;
            overflow-y: auto;
        }
        .loading {
            display: none;
            text-align: center;
            padding: 20px;
        }
        .loading.show {
            display: block;
        }
        .spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #4285f4;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 10px;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .info {
            background: #e7f3ff;
            border: 1px solid #b3d9ff;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            color: #004085;
        }
        .info p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔐 اختبار تسجيل الدخول عبر Google</h1>
        
        <div class="info">
            <p><strong>هذه صفحة اختبار فقط</strong></p>
            <p>استخدم هذه الصفحة لاختبار تسجيل الدخول عبر Google للفرونت إند والموبايل</p>
        </div>

        <button id="googleAuthBtn" class="btn" onclick="startGoogleAuth()">
            <svg width="20" height="20" viewBox="0 0 24 24">
                <path fill="currentColor" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                <path fill="currentColor" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                <path fill="currentColor" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                <path fill="currentColor" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
            </svg>
            تسجيل الدخول عبر Google
        </button>

        <div class="loading" id="loading">
            <div class="spinner"></div>
            <p>جاري التحميل...</p>
        </div>

        <div class="result" id="result"></div>
    </div>

    <script>
        const apiBaseUrl = '{{ url("/api/v1/auth") }}';
        
        async function startGoogleAuth() {
            const btn = document.getElementById('googleAuthBtn');
            const loading = document.getElementById('loading');
            const result = document.getElementById('result');
            
            btn.disabled = true;
            loading.classList.add('show');
            result.classList.remove('show');
            
            try {
                // Get Google Auth URL
                const response = await fetch(`${apiBaseUrl}/google/url`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    }
                });
                
                const data = await response.json();
                
                if (data.success && data.data.url) {
                    // Redirect to Google
                    window.location.href = data.data.url;
                } else {
                    throw new Error('فشل في الحصول على رابط Google');
                }
            } catch (error) {
                showResult('error', 'خطأ', error.message);
                btn.disabled = false;
                loading.classList.remove('show');
            }
        }

        function showResult(type, title, content) {
            const result = document.getElementById('result');
            const loading = document.getElementById('loading');
            
            loading.classList.remove('show');
            result.className = `result ${type} show`;
            result.innerHTML = `
                <h3>${title}</h3>
                <pre>${typeof content === 'object' ? JSON.stringify(content, null, 2) : content}</pre>
            `;
        }

        // Check if we have a code in the URL (callback from Google)
        window.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const code = urlParams.get('code');
            const error = urlParams.get('error');
            
            if (error) {
                showResult('error', 'خطأ من Google', error);
                return;
            }
            
            if (code) {
                exchangeCode(code);
            }
        });

        async function exchangeCode(code) {
            const loading = document.getElementById('loading');
            const result = document.getElementById('result');
            const btn = document.getElementById('googleAuthBtn');
            
            btn.disabled = true;
            loading.classList.add('show');
            result.classList.remove('show');
            
            try {
                const response = await fetch(`${apiBaseUrl}/google/exchange`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        code: code
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showResult('success', 'نجح تسجيل الدخول! 🎉', {
                        message: data.message,
                        user: data.data.user,
                        token: data.data.token,
                        is_new_user: data.data.is_new_user
                    });
                } else {
                    showResult('error', 'فشل تسجيل الدخول', data.error || data);
                }
            } catch (error) {
                showResult('error', 'خطأ', error.message);
            } finally {
                btn.disabled = false;
                loading.classList.remove('show');
            }
        }
    </script>
</body>
</html>

