<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #fdfdfc; color: #1f2937; margin: 0; padding: 0; }
        .container { max-width: 600px; mx-auto; padding: 40px; background-color: #ffffff; border-radius: 24px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); margin-top: 50px; }
        .header { text-align: center; margin-bottom: 40px; }
        .logo { width: 60px; h-60px; background-color: #6366f1; border-radius: 16px; display: inline-block; color: white; font-weight: 900; font-size: 32px; line-height: 60px; text-align: center; }
        .title { font-size: 24px; font-weight: 900; color: #111827; margin-top: 20px; }
        .content { font-size: 16px; line-height: 1.6; color: #4b5563; }
        .credentials { background-color: #f9fafb; padding: 24px; border-radius: 16px; margin: 30px 0; border: 1px solid #f3f4f6; }
        .label { font-size: 12px; font-weight: 900; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px; }
        .value { font-size: 18px; font-weight: 700; color: #111827; }
        .button { display: inline-block; background-color: #6366f1; color: #ffffff !important; padding: 16px 32px; border-radius: 12px; font-weight: 700; text-decoration: none; margin-top: 20px; }
        .footer { text-align: center; margin-top: 40px; font-size: 12px; color: #9ca3af; }
    </style>
</head>
<body>
    <div class="container" style="margin-left: auto; margin-right: auto; text-align: right;">
        <div class="header">
            <div class="logo">K</div>
            <div class="title">دعوة للانضمام إلى كاشلي</div>
        </div>
        
        <div class="content">
            أهلاً <strong>{{ $partnerName }}</strong>،<br><br>
            لقد قام <strong>{{ $ownerName }}</strong> بدعوتك للانضمام إلى منصة كاشلي كشريك لمتابعة استثماراتك وحصصك المالية بكل شفافية.
        </div>

        <div class="credentials">
            <div style="margin-bottom: 20px;">
                <div class="label">البريد الإلكتروني</div>
                <div class="value">{{ $email }}</div>
            </div>
            <div>
                <div class="label">كلمة المرور المؤقتة</div>
                <div class="value">{{ $password }}</div>
            </div>
        </div>

        <div class="content">
            يرجى تسجيل الدخول وتغيير كلمة المرور الخاصة بك فوراً لضمان أمان حسابك.
        </div>

        <div style="text-align: center;">
            <a href="{{ url('/login') }}" class="button">الدخول للوحة التحكم</a>
        </div>

        <div class="footer">
            © {{ date('Y') }} كاشلي. جميع الحقوق محفوظة.
        </div>
    </div>
</body>
</html>
