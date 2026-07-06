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
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>6Degrees</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        html, body {
            font-family: "Mona-Sans Regular", sans-serif;
            background: #010822;
            color: #fff;
            min-height: 100vh;
        }

        body {
            display: flex;
            flex-direction: column;
            position: relative;
            overflow-x: hidden;
        }

        .header {
            padding: 20px 0;
            box-sizing: border-box;
            position: relative;
            z-index: 10;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            background: #010822;
        }

        .header-inner {
            max-width: 1300px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 48px;
        }

        .header .logo { width: 120px; height: auto; display: block; }

        .lang {
            padding: 8px 30px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 50px;
            background: transparent;
            color: #fff;
            cursor: pointer;
            font-size: 14px;
            transition: 0.2s;
        }

        .lang:hover { background: rgba(255, 255, 255, 0.08); border-color: rgba(255, 255, 255, 0.4); }

        .content {
            flex: 1;
            display: flex;
            flex-direction: row;
            width: 100%;
            max-width: 1300px;
            margin: 0 auto;
        }

        .left-panel {
            flex: 0 0 52%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 48px;
            position: relative;
            z-index: 1;
        }

        .canvas-panel {
            flex: 1;
            position: relative;
            min-height: 400px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        #canvas {
            position: relative;
            width: 100%;
            height: 80%;
            max-height: 620px;
            display: block;
        }

        .card { width: 100%; max-width: 480px; }

        .brand-heading {
            font-size: 60px;
            font-weight: 600;
            line-height: 1.2;
            margin-bottom: 16px;
            font-family: "Mona-Sans Regular", sans-serif;
        }

        .logo-small { display: block; width: 100px; margin-bottom: 40px; }

        h1 { font-size: 40px; font-weight: 600; margin-bottom: 8px; font-family: "Mona-Sans Regular", sans-serif; }

        .subtitle { color: rgba(255,255,255,0.5); font-size: 14px; margin-bottom: 32px; font-family: "Mona-Sans Regular", sans-serif; }

        /* ===== بانرات الخطأ/النجاح — ستايل آبل ===== */
        .error-banner, .success-banner {
            display: flex;
            align-items: center;
            gap: 10px;
            border-radius: 12px;
            padding: 12px 16px;
            margin-bottom: 24px;
            font-size: 13px;
            font-family: "Mona-Sans Regular", sans-serif;
            font-weight: 500;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            animation: slideDown 0.35s cubic-bezier(0.22, 1, 0.36, 1);
        }

        .error-banner svg, .success-banner svg { width: 18px; height: 18px; flex-shrink: 0; }

        .error-banner {
            background: rgba(255,69,58,0.08);
            border: 1px solid rgba(255,69,58,0.2);
            color: #ff8a80;
            box-shadow: 0 4px 16px rgba(255,69,58,0.08);
        }

        .success-banner {
            background: rgba(48,209,88,0.08);
            border: 1px solid rgba(48,209,88,0.2);
            color: #5ee88a;
            box-shadow: 0 4px 16px rgba(48,209,88,0.08);
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .field { margin-bottom: 16px; }

        label { display: block; font-size: 13px; color: rgba(255,255,255,0.6); margin-bottom: 6px; font-family: "Mona-Sans Regular", sans-serif; }

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
            font-family: "Mona-Sans Regular", sans-serif;
        }

        input:focus { border-color: rgba(255,255,255,0.3); }

        input.code-input { text-align: center; font-size: 22px; letter-spacing: 12px; }

        .forgot {
            display: block;
            text-align: right;
            font-size: 12px;
            color: rgba(255,255,255,0.4);
            margin-top: 6px;
            margin-bottom: 24px;
            cursor: pointer;
        }

        html[dir="rtl"] .forgot { text-align: left; }

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
            font-family: "Mona-Sans Regular", sans-serif;
        }

        .btn:hover { opacity: 0.9; }

        .back {
            display: block;
            text-align: center;
            margin-top: 16px;
            font-size: 13px;
            color: rgba(255,255,255,0.4);
            cursor: pointer;
            font-family: "Mona-Sans Regular", sans-serif;
        }

        .back:hover { color: #fff; }

        .auth-section { display: none; }
        .auth-section.active { display: block; }

        .step { display: none; }
        .step.active { display: block; }

        .footer {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 14px 48px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            box-sizing: border-box;
            background: #010822;
            position: relative;
            z-index: 10;
        }

        .footer p { font-size: 13px; color: rgba(255,255,255,0.4); margin: 0; text-align: center; }

        #cursorDot {
            position: fixed;
            top: 0;
            left: 0;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #fff;
            pointer-events: none;
            z-index: 9999;
            transform: translate(-50%, -50%);
            transition: width 0.15s ease, height 0.15s ease;
        }

        #cursorDot.active { width: 14px; height: 14px; }

        @media (max-width: 1200px) {
            .left-panel { flex: 0 0 50%; padding: 40px; }
            .brand-heading { font-size: 32px; }
        }

        @media (max-width: 900px) {
            .content {
                flex-direction: column !important;
            }

            .left-panel {
                flex: unset;
                width: 100%;
                padding: 32px 24px;
                order: 2;
            }

            .canvas-panel {
                width: 100%;
                height: 260px;
                min-height: 260px;
                order: 1;
            }

            .brand-heading { font-size: 28px; }

            #cursorDot { display: none; }
        }

        @media (max-width: 480px) {
            .canvas-panel { height: 200px; min-height: 200px; }
            .left-panel { padding: 24px 20px; }
            .brand-heading { font-size: 24px; }
        }

        #pageLoader {
            position: fixed;
            inset: 0;
            background: rgba(1,8,34,0.85);
            backdrop-filter: blur(6px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 99999;
        }

        #pageLoader.active { display: flex; }

        .loader-spinner {
            width: 42px;
            height: 42px;
            border: 3px solid rgba(255,255,255,0.15);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
        }

        @keyframes spin { to { transform: rotate(360deg); } }

    </style>
</head>
<body>

    <header class="header">
        <div class="header-inner">
            <img src="https://6degrees.com.sa/assets/imgs/logo-light.png" alt="6Degrees Logo" class="logo">
            <button type="button" class="lang" onclick="location.href='{{ route('lang.switch', app()->getLocale() === 'ar' ? 'en' : 'ar') }}'">
                {{ app()->getLocale() === 'ar' ? 'English' : 'العربية' }}
            </button>
        </div>
    </header>

    <div class="content">

        <div class="left-panel">
            <div class="card">
                <h2 class="brand-heading">{{ __('messages.brand_heading') }}</h2>

                {{-- ===== قسم تسجيل الدخول ===== --}}
                <div class="auth-section" id="loginSection">
                    <h1>{{ __('messages.welcome_back') }}</h1>
                    <p class="subtitle">{{ __('messages.sign_in_subtitle') }}</p>

                    @if ($errors->has('email') && old('email') && request()->is('login'))
                        <div class="error-banner">
                            <svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.6"/><path d="M12 8v5M12 16h.01" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
                            <span>{{ $errors->first('email') }}</span>
                        </div>
                    @endif

                    <form action="/login" method="POST">
                        @csrf
                        <div class="field">
                            <label>{{ __('messages.email') }}</label>
                            <input type="email" name="email" placeholder="email@6Degrees.com.sa" value="{{ old('email') }}" required>
                        </div>
                        <div class="field">
                            <label>{{ __('messages.password') }}</label>
                            <input type="password" name="password" placeholder="••••••••" required>
                        </div>
                        <a class="forgot" onclick="showSection('forgotSection'); showStep('stepEmail')">{{ __('messages.forgot_password') }}</a>
                        <button type="submit" class="btn">{{ __('messages.sign_in') }}</button>
                    </form>
                </div>

                {{-- ===== قسم نسيت كلمة المرور - 3 خطوات ===== --}}
                <div class="auth-section" id="forgotSection">

                    <div class="step" id="stepEmail">
                        <h1>{{ __('messages.forgot_password_title') }}</h1>
                        <p class="subtitle">{{ __('messages.forgot_password_subtitle') }}</p>

                        @if ($errors->has('email') && request()->is('send_reset_code'))
                            <div class="error-banner">
                                <svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.6"/><path d="M12 8v5M12 16h.01" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
                                <span>{{ $errors->first('email') }}</span>
                            </div>
                        @endif

                        <form action="{{ route('password.send_code') }}" method="POST">
                            @csrf
                            <div class="field">
                                <label>{{ __('messages.email') }}</label>
                                <input type="email" name="email" placeholder="email@6Degrees.com.sa" value="{{ old('email') }}" required>
                            </div>
                            <button type="submit" class="btn">{{ __('messages.send_code') }}</button>
                        </form>

                        <a class="back" onclick="showSection('loginSection')">{{ __('messages.back_to_login') }}</a>
                    </div>

                    <div class="step" id="stepCode">
                        <h1>{{ __('messages.enter_code') }}</h1>
                        <p class="subtitle">{{ __('messages.enter_code_subtitle') }}</p>

                        @if (session('status') && request()->is('send_reset_code'))
                            <div class="success-banner">
                                <svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.6"/><path d="M8 12l2.5 2.5L16 9" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                <span>{{ session('status') }}</span>
                            </div>
                        @endif

                        @if ($errors->has('code') && request()->is('verify_reset_code'))
                            <div class="error-banner">
                                <svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.6"/><path d="M12 8v5M12 16h.01" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
                                <span>{{ $errors->first('code') }}</span>
                            </div>
                        @endif

                        <form action="{{ route('password.verify_code') }}" method="POST">
                            @csrf
                            <input type="hidden" name="email" value="{{ session('email') }}">
                            <div class="field">
                                <label>{{ __('messages.code') }}</label>
                                <input type="text" name="code" class="code-input" maxlength="4" placeholder="0000" required>
                            </div>
                            <button type="submit" class="btn">{{ __('messages.verify_code') }}</button>
                        </form>

                        <a class="back" onclick="showStep('stepEmail')">{{ __('messages.back') }}</a>
                    </div>

                    <div class="step" id="stepNewPassword">
                        <h1>{{ __('messages.new_password') }}</h1>
                        <p class="subtitle">{{ __('messages.new_password_subtitle') }}</p>

                        @if ($errors->has('password') && request()->is('reset_password'))
                            <div class="error-banner">
                                <svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.6"/><path d="M12 8v5M12 16h.01" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
                                <span>{{ $errors->first('password') }}</span>
                            </div>
                        @endif

                        <form action="{{ route('password.update') }}" method="POST">
                            @csrf
                            <input type="hidden" name="email" value="{{ session('email') }}">
                            <input type="hidden" name="code" value="{{ session('code') }}">
                            <div class="field">
                                <label>{{ __('messages.new_password') }}</label>
                                <input type="password" name="password" placeholder="••••••••" required>
                            </div>
                            <div class="field">
                                <label>{{ __('messages.confirm_password') }}</label>
                                <input type="password" name="password_confirmation" placeholder="••••••••" required>
                            </div>
                            <button type="submit" class="btn">{{ __('messages.reset_password') }}</button>
                        </form>
                    </div>

                </div>
            </div>
        </div>

        <div class="canvas-panel">
            <canvas id="canvas"></canvas>
        </div>

    </div>

    <footer class="footer">
        <p>{{ __('messages.footer_text') }}</p>
    </footer>

    <div id="cursorDot"></div>

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
            showSection("{{ $initialSection }}");
            showStep("{{ $initialStep }}");
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

    <script>
        const canvas3d = document.getElementById('canvas');
        const cursorDot = document.getElementById('cursorDot');

        let hovering = false;
        let targetX = 0, targetY = 0;
        let curX = 0, curY = 0;

        document.addEventListener('mousemove', (e) => {
            cursorDot.style.left = e.clientX + 'px';
            cursorDot.style.top = e.clientY + 'px';
        });

        document.addEventListener('mousedown', () => cursorDot.classList.add('active'));
        document.addEventListener('mouseup', () => cursorDot.classList.remove('active'));

        canvas3d.addEventListener('mouseenter', () => hovering = true);
        canvas3d.addEventListener('mouseleave', () => hovering = false);

        canvas3d.addEventListener('mousemove', (e) => {
            const rect = canvas3d.getBoundingClientRect();
            targetX = ((e.clientX - rect.left) / rect.width - 0.5) * 2;
            targetY = ((e.clientY - rect.top) / rect.height - 0.5) * 2;
        });

        function animate() {
            const tx = hovering ? targetX : 0;
            const ty = hovering ? targetY : 0;

            curX += (tx - curX) * 0.08;
            curY += (ty - curY) * 0.08;

            canvas3d.style.transform = `translate(${curX * 20}px, ${curY * 12}px)`;

            requestAnimationFrame(animate);
        }
        animate();
    </script>

<script>
        function showPageLoading() {
            document.getElementById('pageLoader').classList.add('active');
        }

        document.querySelectorAll('form').forEach(f => {
            f.addEventListener('submit', () => showPageLoading());
        });

        document.querySelector('.lang')?.addEventListener('click', () => showPageLoading());
    </script>

<div id="pageLoader"><div class="loader-spinner"></div></div>

</body>
</html>