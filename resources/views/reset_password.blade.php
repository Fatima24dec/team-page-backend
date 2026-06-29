<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password — 6 Degrees</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #010822; color: #fff;
            min-height: 100vh; display: flex; align-items: center; justify-content: center;
        }
        .card {
            background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.1);
            border-radius: 16px; padding: 48px; width: 100%; max-width: 420px;
        }
        .logo { display: block; width: 120px; margin: 0 auto 32px; }
        h1 { font-size: 22px; font-weight: 600; text-align: center; margin-bottom: 8px; }
        .subtitle { text-align: center; color: rgba(255,255,255,0.5); font-size: 14px; margin-bottom: 32px; }
        .field { margin-bottom: 16px; }
        label { display: block; font-size: 13px; color: rgba(255,255,255,0.6); margin-bottom: 6px; }
        input {
            width: 100%; padding: 12px 16px; background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.12); border-radius: 8px;
            color: #fff; font-size: 14px; outline: none;
        }
        input:focus { border-color: rgba(255,255,255,0.3); }
        .btn {
            width: 100%; padding: 13px; background: #fff; color: #010822;
            border: none; border-radius: 8px; font-size: 14px; font-weight: 600;
            cursor: pointer; margin-top: 8px;
        }
        .btn:hover { opacity: 0.9; }
    </style>
</head>
<body>
    <div class="card">
        <img src="https://6degrees.com.sa/assets/imgs/logo-light.png" alt="6 Degrees" class="logo">
        <h1>Reset Password</h1>
        <p class="subtitle">Enter your new password</p>

        <form action="/reset-password" method="POST">
            @csrf
            <input type="hidden" name="token" value="{{ request('token') }}">
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
</body>
</html>