<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('messages.dashboard') }} - 6 Degrees</title>
<link rel="icon" type="image/png" href="{{ asset('favicon.ico') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css">

    <style>
        * {
    font-family: "Mona-Sans Regular", sans-serif;
}

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Mona-Sans Regular', -apple-system, 'Segoe UI', sans-serif;
            background: #010822;
            color: #fff;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        header.top-bar {
            padding: 16px 0;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            background: rgba(1,8,34,0.6);
            backdrop-filter: blur(12px);
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .top-bar-inner {
            max-width: 1300px;
            margin: 0 auto;
            padding: 0 48px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        header.top-bar img { width: 100px; }

        .header-right { display: flex; align-items: center; gap: 10px; }

        .logout-btn {
            padding: 8px 20px;
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 999px;
            background: transparent;
            color: #fff;
            font-size: 13px;
            cursor: pointer;
            font-family: "Mona-Sans Regular", sans-serif;
        }

        .logout-btn:hover { background: rgba(255,255,255,0.08); }

        .profile-menu { position: relative; }

        .profile-trigger {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 4px 10px;
            border-radius: 999px;
            border: none;
            background: transparent;
            cursor: pointer;
            transition: background 0.15s;
        }

        .profile-trigger:hover { background: rgba(255,255,255,0.06); }

        .profile-avatar-sm {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 700;
            color: #6ea8d6;
            flex-shrink: 0;
            overflow: hidden;
        }

        .profile-avatar-sm img { width: 100%; height: 100%; object-fit: cover; }

        .profile-name-sm { font-size: 13px; font-weight: 600; color: #fff; }

        .profile-trigger .chevron { width: 14px; height: 14px; color: rgba(255,255,255,0.5); transition: transform 0.15s; }
        .profile-menu.open .profile-trigger .chevron { transform: rotate(180deg); }

        .profile-dropdown {
            position: absolute;
            top: calc(100% + 10px);
            inset-inline-end: 0;
            width: 260px;
            background: #0c1530;
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 16px 40px rgba(0,0,0,0.45);
            opacity: 0;
            visibility: hidden;
            transform: translateY(-8px);
            transition: opacity 0.18s ease, transform 0.18s ease;
            z-index: 50;
        }

        .profile-menu.open .profile-dropdown { opacity: 1; visibility: visible; transform: translateY(0); }

        .profile-dropdown-header {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 20px;
            background: linear-gradient(135deg, #1c3f9c, #0c1c4a);
        }

        .profile-avatar-lg {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            background: rgba(255,255,255,0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            font-weight: 700;
            color: #fff;
            flex-shrink: 0;
            overflow: hidden;
        }

        .profile-avatar-lg img { width: 100%; height: 100%; object-fit: cover; }

        .profile-dropdown-name { font-size: 14px; font-weight: 700; color: #fff; margin-bottom: 2px; }
        .profile-dropdown-email { font-size: 12px; color: rgba(255,255,255,0.65); word-break: break-all; }

        .profile-dropdown-body { padding: 8px; }

        .profile-dropdown-item {
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
            padding: 10px 12px;
            border-radius: 10px;
            font-size: 13px;
            color: rgba(255,255,255,0.85);
            background: none;
            border: none;
            cursor: pointer;
            font-family: "Mona-Sans Regular", sans-serif;
            text-align: start;
        }

        .profile-dropdown-item:hover { background: rgba(255,255,255,0.06); color: #fff; }
        .profile-dropdown-item svg { width: 16px; height: 16px; flex-shrink: 0; color: rgba(255,255,255,0.4); }

        .profile-dropdown-divider { height: 1px; background: rgba(255,255,255,0.08); margin: 4px 10px; }

        .profile-dropdown-logout {
            width: calc(100% - 20px);
            margin: 8px 10px 10px;
            padding: 10px;
            border-radius: 10px;
            border: none;
            background: rgba(255,69,58,0.1);
            color: #ff6b6b;
            font-size: 13px;
            font-weight: 600;
            font-family: "Mona-Sans Regular", sans-serif;
            cursor: pointer;
            transition: background 0.15s;
        }

        .profile-dropdown-logout:hover { background: rgba(255,69,58,0.18); }

        main.dashboard-main {
            padding: 48px;
            max-width: 1100px;
            margin: 0 auto;
            flex: 1;
            width: 100%;
        }

        .section-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; }
        .section-title { font-size: 28px; font-weight: 400; margin-bottom: 8px; font-family: 'mona-sans regular', sans-serif; }
        .section-sub { color: rgba(255,255,255,0.4); font-size: 14px; margin-bottom: 24px; font-family: "Mona-Sans Regular", sans-serif; }

        .filter-row { display: flex; align-items: center; gap: 12px; margin-bottom: 28px; }

        .filter-label {
            font-size: 12px;
            color: rgba(255,255,255,0.45);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            white-space: nowrap;
            font-family: "Mona-Sans Regular", sans-serif;
        }

        .department-filter { width: auto; min-width: 260px; }
        .custom-select-label { white-space: nowrap; }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 18px;
        }

        .member-card {
            background: rgba(255,255,255,0.05);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 20px;
            padding: 10px;
            display: flex;
            flex-direction: row;
            align-items: stretch;
            gap: 12px;
            color: #fff;
            position: relative;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            transition: transform 0.2s ease, border-color 0.2s ease;
            min-height: 140px;
        }

        .member-card:hover { transform: translateY(-3px); border-color: rgba(255,255,255,0.2); }

        [dir="ltr"] .member-card { flex-direction: row-reverse; }
        [dir="rtl"] .member-card { flex-direction: row; }

        .member-text-block {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
            gap: 6px;
            text-align: start;
            padding: 6px 4px;
        }

        .avatar {
            width: 110px;
            min-width: 110px;
            border-radius: 12px;
            background: #131e38;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            letter-spacing: 2px;
            font-weight: 700;
            color: #6ea7d68c;
            flex-shrink: 0;
            overflow: hidden;
            align-self: stretch;
        }

        .avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: top center;
            display: block;
        }

        .member-info h3 { font-size: 16px; font-weight: 700; color: #fff; margin-bottom: 2px; font-family: "Mona-Sans Regular", sans-serif; }

.member-info p {
    font-size: 14px;
    color: rgba(255,255,255,0.70);
    font-weight: 400;
    font-family: "Mona-Sans Regular", sans-serif;
    padding-bottom: 6px;
    border-bottom: 1px solid rgba(255,255,255,0.08);
    margin-bottom: 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

        .member-meta { font-size: 12px; color: rgba(255,255,255,0.4); line-height: 1.5; font-family: "Mona-Sans Regular", sans-serif; font-weight: 400;}
        .member-meta .meta-label { color: rgba(255, 255, 255, 0.70); font-weight: 600; font-family: "Mona-Sans Regular", sans-serif; }

.member-bio {
    font-size: 12px;
    color: rgba(255,255,255,0.4);
    line-height: 1.5;
    font-style: italic;
    font-family: "Mona-Sans Regular", sans-serif;
    font-weight: 400;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

        .contact-actions {
            display: flex;
            gap: 8px;
            padding-top: 10px;
            margin-top: auto;
            border-top: 1px solid rgba(255,255,255,0.08);
        }

        .contact-btn {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: rgba(255,255,255,0.07);
            border: 1px solid rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #7fb0ff;
            text-decoration: none;
            transition: background 0.15s, transform 0.15s;
        }

        .contact-btn:hover { background: rgba(127,176,255,0.15); transform: translateY(-2px); }
        .contact-btn svg { width: 15px; height: 15px; }

        .card-menu {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 5;
        }

        [dir="rtl"] .card-menu {
            right: auto;
            left: 10px;
        }

        [dir="ltr"] .member-card .card-menu {
            right: 10px;
            left: auto;
        }

.card-menu-trigger {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    border: none;
    background: rgba(1,8,34,0.5);
    color: rgba(255,255,255,0.6);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: background 0.15s;
}

.card-menu-trigger:hover { background: rgba(255,255,255,0.15); color: #fff; }
.card-menu-trigger svg { width: 14px; height: 14px; }

        .card-menu-dropdown {
            position: absolute;
            top: calc(100% + 6px);
            right: 0;
            left: auto;
            background: #0c1530;
            border-radius: 12px;
            box-shadow: 0 12px 32px rgba(0,0,0,0.4);
            border: 1px solid rgba(255,255,255,0.12);
            min-width: 140px;
            overflow: hidden;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-6px);
            transition: opacity 0.15s ease, transform 0.15s ease;
            z-index: 30;
        }

        [dir="rtl"] .card-menu-dropdown {
            right: auto;
            left: 0;
        }

        .card-menu.open .card-menu-dropdown {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .card-menu-item {
            display: flex;
            align-items: center;
            gap: 8px;
            width: 100%;
            padding: 10px 14px;
            font-size: 13px;
            color: rgba(255,255,255,0.8);
            background: none;
            border: none;
            cursor: pointer;
            text-align: start;
            font-family: "Mona-Sans Regular", sans-serif;
        }

        .card-menu-item:hover { background: rgba(255,255,255,0.06); color: #fff; }
        .card-menu-item.danger { color: #ff6b6b; }
        .card-menu-item svg { width: 15px; height: 15px; flex-shrink: 0; }

        .users-list { display: flex; flex-direction: column; gap: 12px; }

        .user-row {
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 12px;
            padding: 16px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .user-row p.name { font-size: 14px; font-weight: 600; }
        .user-row p.email { font-size: 12px; color: rgba(255,255,255,0.4); }

        .custom-select { position: relative; width: 130px; user-select: none; }

        .custom-select-trigger {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 10px 16px;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 8px;
            color: #fff;
            font-size: 13px;
            font-family: "Mona-Sans Regular", sans-serif;
            cursor: pointer;
            transition: border-color 0.15s;
        }

        .custom-select-trigger:hover { border-color: rgba(255,255,255,0.35); }
        .custom-select.open .custom-select-trigger { border-color: rgba(255,255,255,0.4); }

        .custom-select .chevron { width: 14px; height: 14px; transition: transform 0.15s; flex-shrink: 0; }
        .custom-select.open .chevron { transform: rotate(180deg); }

        .custom-select-menu {
            position: absolute;
            top: calc(100% + 6px);
            inset-inline-start: 0;
            right: 0;
            background: #0c1530;
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.4);
            opacity: 0;
            visibility: hidden;
            transform: translateY(-6px);
            transition: opacity 0.15s ease, transform 0.15s ease;
            z-index: 20;
            max-height: 240px;
            overflow-y: auto;
        }

        .custom-select.open .custom-select-menu { opacity: 1; visibility: visible; transform: translateY(0); }

        .custom-select-option {
            padding: 10px 14px;
            font-size: 13px;
            font-family: "Mona-Sans Regular", sans-serif;
            color: rgba(255,255,255,0.75);
            cursor: pointer;
            transition: background 0.12s;
            white-space: nowrap;
        }

        .custom-select-option:hover { background: rgba(255,255,255,0.08); color: #fff; }
        .custom-select-option.selected { background: rgba(255,255,255,0.1); color: #fff; font-weight: 600; }
        .custom-select.disabled { opacity: 0.5; pointer-events: none; }

        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(1,8,34,0.75);
            backdrop-filter: blur(4px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 999;
        }

        .modal-overlay.active { display: flex; }

        /* ── Modal بعمودين ── */
        .modal-box {
            background: #0c1530;
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 16px;
            padding: 32px;
            width: 100%;
            max-width: 680px;
            max-height: 85vh;
            overflow-y: auto;
        }

        .modal-box h2 { font-size: 18px; font-weight: 600; margin-bottom: 24px; font-family: "Mona-Sans Regular", sans-serif; }

        .modal-form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0 20px;
        }

        .modal-form-full { grid-column: 1 / -1; }

        @media (max-width: 600px) {
            .modal-form-grid { grid-template-columns: 1fr; }
            .modal-box { max-width: 95vw; padding: 20px; }
        }

        .field { margin-bottom: 16px; }
        .field label { display: block; font-size: 13px; color: rgba(255,255,255,0.6); margin-bottom: 6px; font-family: "Mona-Sans Regular", sans-serif; }

        .field input {
            width: 100%;
            padding: 12px 16px;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 8px;
            color: #fff;
            font-size: 14px;
            outline: none;
            font-family: "Mona-Sans Regular", sans-serif;
        }

        .field input:focus { border-color: rgba(255,255,255,0.3); }

        .photo-preview-wrap {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
            flex-wrap: wrap;
        }

        .photo-preview-box {
            width: 64px;
            height: 64px;
            border-radius: 16px;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.15);
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .photo-preview-box img { width: 100%; height: 100%; object-fit: cover; display: none; }

        .photo-pick-btn {
            padding: 9px 16px;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 8px;
            color: #fff;
            font-size: 12px;
            cursor: pointer;
            font-family: "Mona-Sans Regular", sans-serif;
        }

        .photo-pick-btn:hover { background: rgba(255,255,255,0.14); }

        .modal-actions { display: flex; gap: 10px; margin-top: 28px; }

        .modal-actions button[type="button"] {
            flex: 1;
            padding: 11px;
            background: transparent;
            border: 1px solid rgba(255,255,255,0.15);
            color: rgba(255,255,255,0.7);
            border-radius: 8px;
            font-size: 13px;
            font-family: "Mona-Sans Regular", sans-serif;
            cursor: pointer;
        }

        .modal-actions button[type="submit"] {
            flex: 1;
            padding: 11px;
            background: #fff;
            color: #010822;
            border: none;
            border-radius: 8px;
            font-family: "Mona-Sans Regular", sans-serif;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
        }

        .crop-modal-box {
            background: #0c1530;
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 16px;
            padding: 24px;
            width: 100%;
            max-width: 440px;
        }

        .crop-modal-box h2 { font-size: 16px; font-weight: 600; margin-bottom: 16px; font-family: "Mona-Sans Regular", sans-serif; }

        .crop-canvas-wrap {
            width: 100%;
            height: 320px;
            background: #05081a;
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 18px;
        }

        .crop-canvas-wrap img { display: block; max-width: 100%; }

        .footer {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px 48px;
            border-top: 1px solid rgba(255,255,255,0.1);
        }

        .footer p { font-size: 13px; color: rgba(255,255,255,0.4); margin: 0; }

        #toastContainer {
            position: fixed;
            top: 24px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 8px;
            align-items: center;
        }

        .toast {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            border-radius: 999px;
            font-size: 13px;
            background: rgba(28, 28, 32, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255,255,255,0.1);
            color: #fff;
            box-shadow: 0 8px 24px rgba(0,0,0,0.3);
            animation: toastIn 0.25s cubic-bezier(0.22, 1, 0.36, 1);
            white-space: nowrap;
        }

        .toast.error { color: #ff9b9b; }
        .toast.info { color: #7fb0ff; }
        .toast svg { flex-shrink: 0; width: 15px; height: 15px; }
        .toast.hide { animation: toastOut 0.25s ease forwards; }

        @keyframes toastIn {
            from { opacity: 0; transform: translateY(-10px) scale(0.95); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        @keyframes toastOut {
            from { opacity: 1; transform: translateY(0) scale(1); }
            to { opacity: 0; transform: translateY(-10px) scale(0.95); }
        }

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

        @media (max-width: 900px) {
            #cursorDot { display: none; }
            .profile-name-sm { display: none; }
        }

        @media (max-width: 500px) {
            .member-card { flex-direction: column; align-items: flex-start; }
            [dir="rtl"] .member-card { align-items: flex-end; }
            .member-text-block { width: 100%; }
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

        .confirm-modal-box {
            background: #0c1530;
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 16px;
            padding: 28px;
            width: 100%;
            max-width: 360px;
            text-align: center;
        }

        .confirm-modal-box p {
            font-size: 14px;
            color: #fff;
            line-height: 1.6;
            margin-bottom: 24px;
            font-family: "Mona-Sans Regular", sans-serif;
        }

        .field-error {
    display: none;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    color: #ff6b6b;
    margin-top: 6px;
    font-family: "Mona-Sans Regular", sans-serif;
}

.field-error.show { display: flex; }

.field-error svg { width: 14px; height: 14px; flex-shrink: 0; }

.field.has-error input {
    border-color: rgba(255,107,107,0.5);
}

@media (max-width: 768px) {
    main.dashboard-main {
        padding: 24px 16px;
    }

    .top-bar-inner {
        padding: 0 16px;
    }

    .grid {
        grid-template-columns: 1fr;
    }

    .filter-row {
        flex-wrap: wrap;
        gap: 8px;
    }

    .department-filter {
        min-width: unset;
        width: 100%;
    }

    .section-title {
        font-size: 22px;
    }

    .users-list .user-row {
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
    }

    section > form {
        flex-direction: column !important;
    }

    section > form .field {
        width: 100% !important;
    }

    .footer {
        padding: 14px 16px;
    }
}

    </style>
</head>
<body>

    <div id="toastContainer"></div>

    <header class="top-bar">
        <div class="top-bar-inner">
            <img src="https://6degrees.com.sa/assets/imgs/logo-light.png" alt="6 Degrees">

            <div class="header-right">
                <button type="button" class="logout-btn" onclick="location.href='{{ route('lang.switch', app()->getLocale() === 'ar' ? 'en' : 'ar') }}'">
                    {{ app()->getLocale() === 'ar' ? 'English' : 'العربية' }}
                </button>

                <div class="profile-menu">
                    <button type="button" class="profile-trigger">
                        <div class="profile-avatar-sm">
                            @if (Auth::user()->photo)
                                <img src="{{ asset('storage/' . Auth::user()->photo) }}" alt="{{ Auth::user()->name }}">
                            @else
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            @endif
                        </div>
                        <span class="profile-name-sm">{{ Auth::user()->name }}</span>
                        <svg class="chevron" viewBox="0 0 24 24" fill="none">
                            <path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>

                    <div class="profile-dropdown">
                        <div class="profile-dropdown-header">
                            <div class="profile-avatar-lg">
                                @if (Auth::user()->photo)
                                    <img src="{{ asset('storage/' . Auth::user()->photo) }}" alt="{{ Auth::user()->name }}">
                                @else
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                @endif
                            </div>
                            <div>
                                <p class="profile-dropdown-name">{{ Auth::user()->name }}</p>
                                <p class="profile-dropdown-email">{{ Auth::user()->email }}</p>
                            </div>
                        </div>

                        <div class="profile-dropdown-body">
                            <button type="button" class="profile-dropdown-item" onclick='openEditModal(@json(Auth::user()))'>
                                <svg viewBox="0 0 24 24" fill="none"><path d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM4 20c0-4 4-6 8-6s8 2 8 6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                {{ __('messages.my_data') }}
                            </button>
                        </div>

                        <div class="profile-dropdown-divider"></div>

                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="profile-dropdown-logout">{{ __('messages.logout') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="dashboard-main">

        <div class="section-header">
            <div>
                <h1 class="section-title">{{ __('messages.dashboard') }}</h1>
                <p class="section-sub">{{ __('messages.manage_team') }}</p>
            </div>
        </div>

        <div class="filter-row">
            <span class="filter-label">{{ __('messages.filter_department') }}</span>
            <div class="custom-select department-filter">
                <button type="button" class="custom-select-trigger">
                    <span class="custom-select-label">{{ __('messages.all_departments') }}</span>
                    <svg class="chevron" viewBox="0 0 24 24" fill="none">
                        <path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
                <div class="custom-select-menu">
                    <div class="custom-select-option selected" data-value="all">{{ __('messages.all_departments') }}</div>
                    @foreach ($departments as $dept)
                        <div class="custom-select-option" data-value="{{ $dept }}">{{ $dept }}</div>
                    @endforeach
                </div>
            </div>
        </div>


        
        <div class="grid" id="membersGrid">
            @foreach ($teamMembers as $member)
                <div class="member-card" data-department="{{ strtolower(trim($member->department ?? '')) }}">

                    @if (Auth::user()->isAdmin() || Auth::id() === $member->id)
                        <div class="card-menu">
                            <button type="button" class="card-menu-trigger">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <circle cx="12" cy="5" r="1.8"/>
                                    <circle cx="12" cy="12" r="1.8"/>
                                    <circle cx="12" cy="19" r="1.8"/>
                                </svg>
                            </button>
                            <div class="card-menu-dropdown">
                                <button type="button" class="card-menu-item" onclick='openEditModal(@json($member))'>
                                    <svg viewBox="0 0 24 24" fill="none"><path d="M12 20h9M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    {{ __('messages.edit') }}
                                </button>

                                @if (Auth::user()->isAdmin() && $member->id !== Auth::id())
                                    <form action="{{ route('team.destroy', $member) }}" method="POST" id="delete-form-{{ $member->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="card-menu-item danger" onclick="confirmDelete({{ $member->id }}, '{{ addslashes($member->name) }}')">
                                            <svg viewBox="0 0 24 24" fill="none"><path d="M3 6h18M8 6V4a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2m2 0v14a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V6h12z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            {{ __('messages.delete') }}
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endif

                    <div class="member-text-block">
                        <div class="member-info">
                            <h3>{{ $member->name }}</h3>
                            <p>{{ $member->position ?: ($member->role === 'admin' ? __('messages.admin') : __('messages.team_member')) }}</p>
                        </div>

                        @if ($member->department)
                            <div class="member-meta">
                                <span class="meta-label">{{ __('messages.department') }}:</span> {{ $member->department }}
                            </div>
                        @endif

                        @if ($member->bio)
                            <div class="member-bio">{{ $member->bio }}</div>
                        @endif

                        <div class="contact-actions">
                            <a href="mailto:{{ $member->email }}" class="contact-btn" title="{{ $member->email }}">
                                <svg viewBox="0 0 24 24" fill="none"><path d="M3 6h18v12H3V6zm0 0l9 7 9-7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </a>
                            @if ($member->phone)
                                <a href="tel:{{ $member->phone }}" class="contact-btn" title="{{ $member->phone }}">
                                    <svg viewBox="0 0 24 24" fill="none"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6 19.8 19.8 0 0 1-3.1-8.7A2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1.9.3 1.8.6 2.7a2 2 0 0 1-.4 2.1L8.1 9.7a16 16 0 0 0 6.2 6.2l1.2-1.2a2 2 0 0 1 2.1-.4c.9.3 1.8.5 2.7.6a2 2 0 0 1 1.7 2.1z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </a>
                            @endif
                        </div>
                    </div>

                    <div class="avatar">
                        @if ($member->photo)
                            <img src="{{ asset('storage/' . $member->photo) }}" alt="{{ $member->name }}">
                        @else
                            {{ strtoupper(substr($member->name, 0, 1)) }}{{ strtoupper(substr(strstr($member->name, ' ') ?: '', 1, 1)) }}
                        @endif
                    </div>

                </div>
            @endforeach
        </div>

        @if (Auth::user()->isAdmin())
            <section style="margin-top: 64px;">
                <h1 class="section-title">{{ __('messages.user_management') }}</h1>
                <p class="section-sub">{{ __('messages.invite_subtitle') }}</p>

                <form action="{{ route('users.invite') }}" method="POST" style="display: flex; gap: 12px; margin-bottom: 24px; align-items: flex-end;">
                    @csrf
                    <div class="field" style="flex: 1; margin-bottom: 0;">
                        <label>{{ __('messages.name') }}</label>
                        <input type="text" name="name" placeholder="{{ __('messages.full_name') }}" required>
                    </div>
                    <div class="field" style="flex: 1; margin-bottom: 0;">
                        <label>{{ __('messages.email') }}</label>
                        <input type="email" name="email" placeholder="email@6degrees.com.sa" required>
                    </div>
                    <button type="submit" class="logout-btn" style="white-space: nowrap; padding: 12px 24px; background: #fff; color: #010822; border: none; font-weight: 600;">
                        {{ __('messages.send_invite') }}
                    </button>
                </form>

                <div class="users-list">
                    @foreach ($users as $u)
                        <div class="user-row">
                            <div>
                                <p class="name">{{ $u->name }}</p>
                                <p class="email">{{ $u->email }}</p>
                            </div>

                            <form action="{{ route('users.updateRole', $u) }}" method="POST" class="role-form">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="role" value="{{ $u->role }}">

                                <div class="custom-select {{ $u->id === Auth::id() ? 'disabled' : '' }}">
                                    <button type="button" class="custom-select-trigger">
                                        <span class="custom-select-label">{{ $u->role === 'admin' ? __('messages.admin') : __('messages.user') }}</span>
                                        <svg class="chevron" viewBox="0 0 24 24" fill="none">
                                            <path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </button>
                                    <div class="custom-select-menu">
                                        <div class="custom-select-option {{ $u->role === 'user' ? 'selected' : '' }}" data-value="user">{{ __('messages.user') }}</div>
                                        <div class="custom-select-option {{ $u->role === 'admin' ? 'selected' : '' }}" data-value="admin">{{ __('messages.admin') }}</div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

    </main>

    {{-- Modal التعديل بعمودين --}}
    <div class="modal-overlay" id="memberModal">
        <div class="modal-box">
            <form id="memberForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <h2>{{ __('messages.edit_member') }}</h2>

                {{-- الصورة - كاملة العرض --}}
                <div class="field modal-form-full">
                    <label>{{ __('messages.photo') }}</label>
                    <div class="photo-preview-wrap">
                        <div class="photo-preview-box" id="photoPreviewBox">
                            <img id="photoPreviewImg" src="" alt="">
                        </div>
                        <button type="button" class="photo-pick-btn" onclick="document.getElementById('photoRawInput').click()">
                            {{ __('messages.add_photo') }}
                        </button>
                        <button type="button" class="photo-pick-btn" id="removePhotoBtn" onclick="toggleRemovePhoto()" style="background: rgba(255,69,58,0.1); border-color: rgba(255,69,58,0.3); color: #ff6b6b;">
                            {{ __('messages.remove_photo') }}
                        </button>
                        <input type="file" id="photoRawInput" accept="image/*" style="display:none;">
                        <input type="hidden" name="remove_photo" id="removePhotoCheckbox" value="">
                    </div>
                </div>

                {{-- الحقول بعمودين --}}
                <div class="modal-form-grid">
<div class="field" id="nameField">
    <label>{{ __('messages.name') }}</label>
    <input type="text" name="name" id="nameInput" required>
    <span class="field-error" id="nameError">
        <svg viewBox="0 0 24 24" fill="none"><path d="M12 9v4M12 17h.01M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
        <span>{{ __('messages.field_required') }}</span>
    </span>
</div>
                    <div class="field">
                        <label>{{ __('messages.title') }}</label>
                        <input type="text" name="position" id="positionInput">
                    </div>
                    <div class="field">
                        <label>{{ __('messages.department') }}</label>
                        <input type="text" name="department" id="departmentInput">
                    </div>
<div class="field" id="phoneField">
    <label>{{ __('messages.phone') }}</label>
    <input type="text" name="phone" id="phoneInput" required>
    <span class="field-error" id="phoneError">
        <svg viewBox="0 0 24 24" fill="none"><path d="M12 9v4M12 17h.01M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
        <span>{{ __('messages.field_required') }}</span>
    </span>
</div>
                    <div class="field modal-form-full">
                        <label>{{ __('messages.bio') }}</label>
                        <input type="text" name="bio" id="bioInput">
                    </div>
                </div>

                <div class="modal-actions">
                    <button type="button" onclick="closeModal()">{{ __('messages.cancel') }}</button>
                    <button type="submit">{{ __('messages.save') }}</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal-overlay" id="cropModal">
        <div class="crop-modal-box">
            <h2>{{ __('messages.photo') }}</h2>
            <div class="crop-canvas-wrap">
                <img id="cropImage" src="" alt="">
            </div>
            <div class="modal-actions">
                <button type="button" onclick="cancelCrop()">{{ __('messages.cancel') }}</button>
                <button type="button" onclick="confirmCrop()">{{ __('messages.save') }}</button>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="confirmModal">
        <div class="confirm-modal-box">
            <p id="confirmModalText"></p>
            <div class="modal-actions">
                <button type="button" onclick="closeConfirmModal()">{{ __('messages.cancel') }}</button>
                <button type="button" id="confirmModalYes" style="background:#ff6b6b; color:#fff;">{{ __('messages.delete') }}</button>
            </div>
        </div>
    </div>

    <footer class="footer">
        <p>{{ __('messages.footer_text') }}</p>
    </footer>

    <div id="pageLoader"><div class="loader-spinner"></div></div>
    <div id="cursorDot"></div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>
    <script>
        const memberModal = document.getElementById('memberModal');
        const form = document.getElementById('memberForm');

        // حفظ البيانات الأصلية للمقارنة
        let originalData = {};

        function openEditModal(member) {
            form.reset();
            form.action = `/team/${member.id}`;
            document.getElementById('nameInput').value = member.name ?? '';
            document.getElementById('positionInput').value = member.position ?? '';
            document.getElementById('departmentInput').value = member.department ?? '';
            document.getElementById('phoneInput').value = member.phone ?? '';
            document.getElementById('bioInput').value = member.bio ?? '';
            document.getElementById('removePhotoCheckbox').value = '';

            // حفظ البيانات الأصلية
            originalData = {
                name: member.name ?? '',
                position: member.position ?? '',
                department: member.department ?? '',
                phone: member.phone ?? '',
                bio: member.bio ?? '',
            };

            const previewImg = document.getElementById('photoPreviewImg');
            if (member.photo) {
                previewImg.src = '/storage/' + member.photo;
                previewImg.style.display = 'block';
            } else {
                previewImg.src = '';
                previewImg.style.display = 'none';
            }

            croppedFile = null;
            memberModal.classList.add('active');
            document.querySelectorAll('.card-menu.open, .profile-menu.open').forEach(m => m.classList.remove('open'));
        }

        function closeModal() { memberModal.classList.remove('active'); }

        const cropModal = document.getElementById('cropModal');
        const cropImage = document.getElementById('cropImage');
        const rawInput = document.getElementById('photoRawInput');
        const previewImg = document.getElementById('photoPreviewImg');
        let cropper = null;
        let croppedFile = null;

        rawInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = (ev) => {
                cropImage.src = ev.target.result;
                cropModal.classList.add('active');
                if (cropper) cropper.destroy();
                cropper = new Cropper(cropImage, {
                    aspectRatio: 1, viewMode: 1, dragMode: 'move',
                    background: false, autoCropArea: 1,
                });
            };
            reader.readAsDataURL(file);
        });

        function cancelCrop() {
            cropModal.classList.remove('active');
            rawInput.value = '';
            if (cropper) { cropper.destroy(); cropper = null; }
        }

        function confirmCrop() {
            if (!cropper) return;
            cropper.getCroppedCanvas({ width: 400, height: 400 }).toBlob((blob) => {
                croppedFile = new File([blob], 'photo.jpg', { type: 'image/jpeg' });
                document.getElementById('removePhotoCheckbox').value = '';
                const previewUrl = URL.createObjectURL(blob);
                previewImg.src = previewUrl;
                previewImg.style.display = 'block';
                cropModal.classList.remove('active');
                cropper.destroy();
                cropper = null;
            }, 'image/jpeg', 0.9);
        }

        function toggleRemovePhoto() {
            const input = document.getElementById('removePhotoCheckbox');
            const btn = document.getElementById('removePhotoBtn');
            if (input.value === '1') {
                input.value = '';
                btn.style.opacity = '1';
            } else {
                input.value = '1';
                btn.style.opacity = '0.6';
                previewImg.style.display = 'none';
                previewImg.src = '';
                croppedFile = null;
            }
        }

        // فحص التغييرات قبل الحفظ
      form.addEventListener('submit', (e) => {
    // إعادة تصفير أي أخطاء سابقة
    document.querySelectorAll('.field-error').forEach(el => el.classList.remove('show'));
    document.querySelectorAll('.field').forEach(el => el.classList.remove('has-error'));

    let hasError = false;

    const nameVal = document.getElementById('nameInput').value.trim();
    if (!nameVal) {
        document.getElementById('nameError').classList.add('show');
        document.getElementById('nameField').classList.add('has-error');
        hasError = true;
    }

    const phoneVal = document.getElementById('phoneInput').value.trim();
    if (!phoneVal) {
        document.getElementById('phoneError').classList.add('show');
        document.getElementById('phoneField').classList.add('has-error');
        hasError = true;
    }

    if (hasError) {
        e.preventDefault();
        return;
    }

    const currentData = {
        name: document.getElementById('nameInput').value,
        position: document.getElementById('positionInput').value,
        department: document.getElementById('departmentInput').value,
        phone: document.getElementById('phoneInput').value,
        bio: document.getElementById('bioInput').value,
    };

    const removePhoto = document.getElementById('removePhotoCheckbox').value === '1';
    const hasChanges = croppedFile || removePhoto ||
        Object.keys(originalData).some(k => originalData[k] !== currentData[k]);

    if (!hasChanges) {
        e.preventDefault();
        closeModal();
        showToast(@json(__('messages.no_changes')), 'info');
        return;
    }

    if (croppedFile) {
        const dt = new DataTransfer();
        dt.items.add(croppedFile);
        let hiddenPhotoInput = form.querySelector('input[name="photo"]');
        if (!hiddenPhotoInput) {
            hiddenPhotoInput = document.createElement('input');
            hiddenPhotoInput.type = 'file';
            hiddenPhotoInput.name = 'photo';
            hiddenPhotoInput.style.display = 'none';
            form.appendChild(hiddenPhotoInput);
        }
        hiddenPhotoInput.files = dt.files;
    }

    showPageLoading();
});
        // حذف بتأكيد
        function confirmDelete(id, name) {
            showConfirm(
                @json(__('messages.confirm_delete')) + ' ' + name + '?',
                () => {
                    showPageLoading();
                    document.getElementById('delete-form-' + id).submit();
                }
            );
        }

        document.querySelectorAll('.card-menu-trigger').forEach(trigger => {
            trigger.addEventListener('click', (e) => {
                e.stopPropagation();
                const menu = trigger.closest('.card-menu');
                document.querySelectorAll('.card-menu.open, .profile-menu.open').forEach(m => {
                    if (m !== menu) m.classList.remove('open');
                });
                menu.classList.toggle('open');
            });
        });

        const profileMenu = document.querySelector('.profile-menu');
        const profileTrigger = document.querySelector('.profile-trigger');

        profileTrigger.addEventListener('click', (e) => {
            e.stopPropagation();
            document.querySelectorAll('.card-menu.open').forEach(m => m.classList.remove('open'));
            profileMenu.classList.toggle('open');
        });

        document.addEventListener('click', () => {
            document.querySelectorAll('.card-menu.open, .profile-menu.open').forEach(m => m.classList.remove('open'));
        });
function showConfirm(message, onConfirm, buttonText = null, buttonColor = null) {
    const modal = document.getElementById('confirmModal');
    document.getElementById('confirmModalText').textContent = message;
    modal.classList.add('active');

    const yesBtn = document.getElementById('confirmModalYes');
    yesBtn.textContent = buttonText || @json(__('messages.delete'));
    yesBtn.style.background = buttonColor || '#ff6b6b';
    yesBtn.style.color = buttonColor ? '#010822' : '#fff';

    const newYesBtn = yesBtn.cloneNode(true);
    yesBtn.parentNode.replaceChild(newYesBtn, yesBtn);
    newYesBtn.addEventListener('click', () => {
        modal.classList.remove('active');
        onConfirm();
    });
}

        function closeConfirmModal() {
            document.getElementById('confirmModal').classList.remove('active');
        }

        function showPageLoading() {
            document.getElementById('pageLoader').classList.add('active');
        }

        // فلتر الأقسام - إصلاح
        function filterMembers(dept) {
            document.querySelectorAll('#membersGrid .member-card').forEach(card => {
                const cardDept = (card.dataset.department || '').trim().toLowerCase();
                const filterDept = dept.trim().toLowerCase();
                card.style.display = (filterDept === 'all' || cardDept === filterDept) ? '' : 'none';
            });
        }
document.querySelectorAll('.custom-select').forEach(select => {
            if (select.classList.contains('disabled')) return;

            const trigger = select.querySelector('.custom-select-trigger');
            const label = select.querySelector('.custom-select-label');
            const options = select.querySelectorAll('.custom-select-option');
            const isDeptFilter = select.classList.contains('department-filter');
            const hiddenInput = isDeptFilter ? null : select.parentElement.querySelector('input[name="role"]');

            trigger.addEventListener('click', (e) => {
                e.stopPropagation();
                document.querySelectorAll('.custom-select.open').forEach(s => {
                    if (s !== select) s.classList.remove('open');
                });
                select.classList.toggle('open');
            });

            options.forEach(option => {
                option.addEventListener('click', () => {
                    const value = option.dataset.value;
                    select.classList.remove('open');

                    if (isDeptFilter) {
                        label.textContent = option.textContent;
                        options.forEach(o => o.classList.remove('selected'));
                        option.classList.add('selected');
                        filterMembers(value);
                        return;
                    }

                    if (hiddenInput.value === value) return;

                    const userName = select.closest('.user-row').querySelector('.name').textContent;
                    const roleLabel = option.textContent.trim();
                    const message = @json(__('messages.confirm_role_change'))
                        .replace(':name', userName)
                        .replace(':role', roleLabel);

                    showConfirm(message, () => {
                        label.textContent = option.textContent;
                        options.forEach(o => o.classList.remove('selected'));
                        option.classList.add('selected');
                        hiddenInput.value = value;
                        showPageLoading();
                        select.closest('form').submit();
                    }, '{{ __("messages.confirm") }}', '#fff');
                });
            });
        });

        document.addEventListener('click', () => {
            document.querySelectorAll('.custom-select.open').forEach(s => s.classList.remove('open'));
        });

        function showToast(message, type = 'success') {
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            const icons = {
                success: '<svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.5"/><path d="M8 12l2.5 2.5L16 9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',
                error: '<svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.5"/><path d="M12 8v5M12 16h.01" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>',
                info: '<svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.5"/><path d="M12 11v5M12 8h.01" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>',
            };
            toast.innerHTML = (icons[type] || icons.info) + `<span>${message}</span>`;
            container.appendChild(toast);
            setTimeout(() => {
                toast.classList.add('hide');
                setTimeout(() => toast.remove(), 250);
            }, 3000);
        }

        @if (session('success'))
            showToast(@json(session('success')), 'success');
        @endif

        @if (session('info'))
            showToast(@json(session('info')), 'info');
        @endif

        @if ($errors->any())
            showToast(@json($errors->first()), 'error');
        @endif

        const cursorDot = document.getElementById('cursorDot');
        document.addEventListener('mousemove', (e) => {
            cursorDot.style.left = e.clientX + 'px';
            cursorDot.style.top = e.clientY + 'px';
        });
        document.addEventListener('mousedown', () => cursorDot.classList.add('active'));
        document.addEventListener('mouseup', () => cursorDot.classList.remove('active'));

        function toggleRemovePhoto() {
            const input = document.getElementById('removePhotoCheckbox');
            const btn = document.getElementById('removePhotoBtn');
            const hasCurrentPhoto = previewImg.style.display !== 'none' && previewImg.src;
            if (!hasCurrentPhoto) return;

            if (input.value === '1') {
                input.value = '';
                btn.style.opacity = '1';
            } else {
                input.value = '1';
                btn.style.opacity = '0.6';
                previewImg.style.display = 'none';
                previewImg.src = '';
                croppedFile = null;
            }
        }

    </script>

</body>
</html>