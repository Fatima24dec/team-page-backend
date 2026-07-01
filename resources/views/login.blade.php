@php
    $initialSection = 'loginSection';
    $initialStep = 'stepEmail';

    switch (session('step')) {
        case 'login':
            $initialSection = 'loginSection';
            break;

        case 'email':
            $initialSection = 'forgotSection';
            $initialStep = 'stepEmail';
            break;

        case 'code':
            $initialSection = 'forgotSection';
            $initialStep = 'stepCode';
            break;

        case 'password':
            $initialSection = 'forgotSection';
            $initialStep = 'stepNewPassword';
            break;
    }
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>6Degrees</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: #010822;
            color: #fff;
            min-height: 100vh;
            display: flex;
            position: relative;
            overflow: hidden;
        }

      #canvas {
    position: fixed;
    top: 0;
    left: 45%;
    width: 55%;
    height: 100%;
    z-index: 0;
}

.left-panel {
    flex: 0 0 45%;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 48px;
    position: relative;
    z-index: 1;
}

        .right-panel {
            flex: 1;
            position: relative;
            z-index: 1;
        }

        .card { width: 100%; max-width: 400px; }

        .logo-small { display: block; width: 100px; margin-bottom: 40px; }

        h1 { font-size: 26px; font-weight: 600; margin-bottom: 8px; }

        .subtitle { color: rgba(255,255,255,0.5); font-size: 14px; margin-bottom: 32px; }

        .error-banner, .success-banner {
            display: flex;
            align-items: center;
            gap: 10px;
            border-radius: 10px;
            padding: 12px 14px;
            margin-bottom: 24px;
            font-size: 13px;
            animation: slideDown 0.35s cubic-bezier(0.22, 1, 0.36, 1);
        }

        .error-banner { background: rgba(255,69,58,0.1); border: 1px solid rgba(255,69,58,0.25); color: #ff6b6b; }
        .success-banner { background: rgba(48,209,88,0.1); border: 1px solid rgba(48,209,88,0.25); color: #30d158; }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .field { margin-bottom: 16px; }

        label { display: block; font-size: 13px; color: rgba(255,255,255,0.6); margin-bottom: 6px; }

        input {
            width: 100%;
            padding: 12px 16px;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 8px;
            color: #fff;
            font-size: 14px;
            outline: none;
            transition: border-color 0.2s;
        }

        input:focus { border-color: rgba(255,255,255,0.3); }

        input.code-input {
            text-align: center;
            font-size: 22px;
            letter-spacing: 12px;
        }

        .forgot {
            display: block;
            text-align: right;
            font-size: 12px;
            color: rgba(255,255,255,0.4);
            margin-top: 6px;
            margin-bottom: 24px;
            cursor: pointer;
        }

        .forgot:hover { color: #fff; }

        .btn {
            width: 100%;
            padding: 13px;
            background: #fff;
            color: #010822;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: opacity 0.2s;
        }

        .btn:hover { opacity: 0.9; }

        .back {
            display: block;
            text-align: center;
            margin-top: 16px;
            font-size: 13px;
            color: rgba(255,255,255,0.4);
            cursor: pointer;
        }

        .back:hover { color: #fff; }

        .auth-section { display: none; }
        .auth-section.active { display: block; }

        .step { display: none; }
        .step.active { display: block; }

        @media (max-width: 900px) {
            body { flex-direction: column; }
            .left-panel { flex: unset; width: 100%; padding: 40px 24px; }
            .right-panel { display: none; }
        }
    </style>
</head>
<body>

    <canvas id="canvas"></canvas>

    <div class="left-panel">
        <div class="card">
            <img src="https://6degrees.com.sa/assets/imgs/logo-light.png" alt="6 Degrees" class="logo-small">

            {{-- ===== قسم تسجيل الدخول ===== --}}
            <div class="auth-section" id="loginSection">
                <h1>Welcome Back</h1>
                <p class="subtitle">Sign in to your account</p>

                @if ($errors->has('email') && old('email') && request()->is('login'))
                    <div class="error-banner">
                        <span>{{ $errors->first('email') }}</span>
                    </div>
                @endif

                <form action="/login" method="POST">
                    @csrf
                    <div class="field">
                        <label>Email</label>
                        <input type="email" name="email" placeholder="you@6Degrees.com.sa" value="{{ old('email') }}" required>
                    </div>
                    <div class="field">
                        <label>Password</label>
                        <input type="password" name="password" placeholder="••••••••" required>
                    </div>
                    <a class="forgot" onclick="showSection('forgotSection'); showStep('stepEmail')">Forgot password?</a>
                    <button type="submit" class="btn">Sign In</button>
                </form>
            </div>

            {{-- ===== قسم نسيت كلمة المرور - 3 خطوات ===== --}}
            <div class="auth-section" id="forgotSection">

                <div class="step" id="stepEmail">
                    <h1>Forgot Password</h1>
                    <p class="subtitle">Enter your email and we'll send you a code</p>

                    @if ($errors->has('email') && request()->is('send_reset_code'))
                        <div class="error-banner">
                            <span>{{ $errors->first('email') }}</span>
                        </div>
                    @endif

                    <form action="{{ route('password.send_code') }}" method="POST">
                        @csrf
                        <div class="field">
                            <label>Email</label>
                            <input type="email" name="email" placeholder="you@example.com" value="{{ old('email') }}" required>
                        </div>
                        <button type="submit" class="btn">Send Code</button>
                    </form>

                    <a class="back" onclick="showSection('loginSection')">← Back to login</a>
                </div>

                <div class="step" id="stepCode">
                    <h1>Enter Code</h1>
                    <p class="subtitle">Enter the 4-digit code sent to your email</p>

                    @if (session('status') && request()->is('send_reset_code'))
                        <div class="success-banner">
                            <span>{{ session('status') }}</span>
                        </div>
                    @endif

                    @if ($errors->has('code') && request()->is('verify_reset_code'))
                        <div class="error-banner">
                            <span>{{ $errors->first('code') }}</span>
                        </div>
                    @endif

                    <form action="{{ route('password.verify_code') }}" method="POST">
                        @csrf
                        <input type="hidden" name="email" value="{{ session('email') }}">
                        <div class="field">
                            <label>Code</label>
                            <input type="text" name="code" class="code-input" maxlength="4" placeholder="0000" required>
                        </div>
                        <button type="submit" class="btn">Verify Code</button>
                    </form>

                    <a class="back" onclick="showStep('stepEmail')">← Back</a>
                </div>

                <div class="step" id="stepNewPassword">
                    <h1>New Password</h1>
                    <p class="subtitle">Enter your new password</p>

                    @if ($errors->has('password') && request()->is('reset_password'))
                        <div class="error-banner">
                            <span>{{ $errors->first('password') }}</span>
                        </div>
                    @endif

                    <form action="{{ route('password.update') }}" method="POST">
                        @csrf
                        <input type="hidden" name="email" value="{{ session('email') }}">
                        <input type="hidden" name="code" value="{{ session('code') }}">
                        <div class="field">
                            <label>New Password</label>
                            <input type="password" name="password" placeholder="••••••••" required>
                        </div>
                        <div class="field">
                            <label>Confirm Password</label>
                            <input type="password" name="password_confirmation" placeholder="••••••••" required>
                        </div>
                        <button type="submit" class="btn">Reset Password</button>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <div class="right-panel"></div>

    <script>
        function showSection(id) {
            document.querySelectorAll('.auth-section').forEach(el => el.classList.remove('active'));
            document.getElementById(id).classList.add('active');
        }

        function showStep(id) {
            document.querySelectorAll('.step').forEach(el => el.classList.remove('active'));
            document.getElementById(id).classList.add('active');
        }

        document.addEventListener('DOMContentLoaded', () => {
            const initialSection = "{{ $initialSection }}";
            const initialStep = "{{ $initialStep }}";
            showSection(initialSection);
            showStep(initialStep);
        });
    </script>

    <script type="module">
        import { Application } from 'https://unpkg.com/@splinetool/runtime@1.9.28/build/runtime.js';

        window.addEventListener('load', () => {
            const canvas = document.getElementById('canvas');
            const app = new Application(canvas);
            app.load('https://prod.spline.design/rah9TcbBELz5h1ps/scene.splinecode');
        });
    </script>

</body>
</html>
