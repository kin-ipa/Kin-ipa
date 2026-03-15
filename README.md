<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مرحباً بك - Kin-ipa</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #333;
        }

        .container {
            text-align: center;
            background: white;
            padding: 50px 40px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 600px;
            animation: slideUp 0.8s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .emoji {
            font-size: 80px;
            margin-bottom: 20px;
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-20px);
            }
        }

        h1 {
            color: #667eea;
            font-size: 48px;
            margin-bottom: 10px;
        }

        h2 {
            color: #764ba2;
            font-size: 28px;
            font-weight: 300;
            margin-bottom: 30px;
            letter-spacing: 2px;
        }

        .description {
            color: #666;
            font-size: 18px;
            line-height: 1.8;
            margin-bottom: 40px;
        }

        .features {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin: 40px 0;
            text-align: right;
        }

        .feature {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 12px;
            border-left: 4px solid #667eea;
            transition: transform 0.3s ease;
        }

        .feature:hover {
            transform: translateX(-5px);
        }

        .feature-icon {
            font-size: 32px;
            margin-bottom: 10px;
        }

        .feature h3 {
            color: #667eea;
            font-size: 16px;
            margin-bottom: 8px;
        }

        .feature p {
            color: #777;
            font-size: 14px;
        }

        .buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 40px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }

        .btn-secondary {
            background: white;
            color: #667eea;
            border: 2px solid #667eea;
        }

        .btn-secondary:hover {
            background: #667eea;
            color: white;
            transform: translateY(-3px);
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #999;
            font-size: 14px;
        }

        @media (max-width: 600px) {
            .container {
                margin: 20px;
                padding: 30px 20px;
            }

            h1 {
                font-size: 36px;
            }

            h2 {
                font-size: 20px;
            }

            .features {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="emoji">👋</div>
        <h1>أهلاً وسهلاً!</h1>
        <h2>مرحباً بك في موقعي</h2>
        
        <p class="description">
            أنا Kin-ipa، وهذا هو موقعي الشخصي على الإنترنت. 
            هنا تستطيع معرفة المزيد عني ومتابعة مشاريعي.
        </p>

        <div class="features">
            <div class="feature">
                <div class="feature-icon">💻</div>
                <h3>مطور</h3>
                <p>أشتغل على مشاريع برمجية مثيرة</p>
            </div>
            <div class="feature">
                <div class="feature-icon">🚀</div>
                <h3>مبتكر</h3>
                <p>أحب ابتكار حلول جديدة</p>
            </div>
            <div class="feature">
                <div class="feature-icon">📚</div>
                <h3>متعلم</h3>
                <p>دائماً أتعلم تقنيات جديدة</p>
            </div>
            <div class="feature">
                <div class="feature-icon">🤝</div>
                <h3>متعاون</h3>
                <p>أحب العمل مع فريق</p>
            </div>
        </div>

        <div class="buttons">
            <button class="btn btn-primary" onclick="alert('شكراً للزيارة! 😊')">
                تواصل معي
            </button>
            <a href="https://github.com/kin-ipa" class="btn btn-secondary" target="_blank">
                GitHub
            </a>
        </div>

        <div class="footer">
            <p>© 2026 Kin-ipa | جميع الحقوق محفوظة</p>
        </div>
    </div>
</body>
</html>
