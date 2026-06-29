<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — 6 Degrees</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #010822; color: #fff; min-height: 100vh; }

        header {
            display: flex; align-items: center; justify-content: space-between;
            padding: 20px 48px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            background: rgba(1,8,34,0.6);
            backdrop-filter: blur(12px);
            position: sticky; top: 0; z-index: 10;
        }

        .logo { width: 100px; }

        .logout {
            padding: 8px 20px; border: 1px solid rgba(255,255,255,0.2);
            border-radius: 999px; background: transparent; color: #fff;
            font-size: 13px; cursor: pointer; text-decoration: none;
        }
        .logout:hover { background: rgba(255,255,255,0.08); }

        main { padding: 48px; max-width: 1000px; margin: 0 auto; }

        h1 { font-size: 28px; font-weight: 400; margin-bottom: 8px; }
        .sub { color: rgba(255,255,255,0.4); font-size: 14px; margin-bottom: 40px; }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 16px;
        }

        .member-card {
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 12px;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .avatar {
            width: 48px; height: 48px; border-radius: 50%;
            background: rgba(255,255,255,0.1);
            display: flex; align-items: center; justify-content: center;
            font-size: 18px; font-weight: 700; color: #6ea8d6;
            flex-shrink: 0;
        }

        .info h3 { font-size: 14px; font-weight: 600; margin-bottom: 2px; }
        .info p { font-size: 12px; color: rgba(255,255,255,0.4); }

        .edit-btn {
            margin-left: auto;
            padding: 6px 14px;
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 6px;
            background: transparent;
            color: rgba(255,255,255,0.6);
            font-size: 12px;
            cursor: pointer;
            text-decoration: none;
        }
        .edit-btn:hover { border-color: rgba(255,255,255,0.4); color: #fff; }
    </style>
</head>
<body>
    <header>
        <img src="https://6degrees.com.sa/assets/imgs/logo-light.png" alt="6 Degrees" class="logo">
        <a href="/login" class="logout">Logout</a>
    </header>

    <main>
        <h1>Team Dashboard</h1>
        <p class="sub">Manage your team members</p>

        <div class="grid">
            {{-- Team members will go here from database --}}
            <div class="member-card">
                <div class="avatar">AA</div>
                <div class="info">
                    <h3>Atheer Al-Otaibi</h3>
                    <p>Managing Director</p>
                </div>
                <a href="#" class="edit-btn">Edit</a>
            </div>
        </div>
    </main>
</body>
</html>