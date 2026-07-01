<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - 6Degrees</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: #010822;
            color: #fff;
            min-height: 100vh;
        }

        header.top-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px 48px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            background: rgba(1,8,34,0.6);
            backdrop-filter: blur(12px);
            position: sticky;
            top: 0;
            z-index: 10;
        }

        header.top-bar img { width: 100px; }

        .logout-btn {
            padding: 8px 20px;
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 999px;
            background: transparent;
            color: #fff;
            font-size: 13px;
            cursor: pointer;
        }

        .logout-btn:hover { background: rgba(255,255,255,0.08); }

        main.dashboard-main {
            padding: 48px;
            max-width: 1100px;
            margin: 0 auto;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }

        .section-title {
            font-size: 28px;
            font-weight: 400;
            margin-bottom: 8px;
        }

        .section-sub {
            color: rgba(255,255,255,0.4);
            font-size: 14px;
            margin-bottom: 32px;
        }

        .success-banner {
            display: flex;
            align-items: center;
            gap: 10px;
            background: rgba(48,209,88,0.1);
            border: 1px solid rgba(48,209,88,0.25);
            color: #30d158;
            border-radius: 10px;
            padding: 12px 14px;
            margin-bottom: 24px;
            font-size: 13px;
        }

        .success-banner svg { flex-shrink: 0; width: 16px; height: 16px; }

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
            flex-direction: column;
            gap: 12px;
        }

        .member-top {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            font-weight: 700;
            color: #6ea8d6;
            flex-shrink: 0;
            overflow: hidden;
        }

        .avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .member-info h3 { font-size: 14px; font-weight: 600; margin-bottom: 2px; }
        .member-info p { font-size: 12px; color: rgba(255,255,255,0.4); }

        .member-meta {
            font-size: 12px;
            color: rgba(255,255,255,0.45);
            line-height: 1.6;
        }

        .member-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 8px;
        }

        .edit-btn, .delete-btn {
            padding: 6px 14px;
            border-radius: 6px;
            font-size: 12px;
            cursor: pointer;
            border: 1px solid rgba(255,255,255,0.15);
            background: transparent;
            color: rgba(255,255,255,0.6);
        }

        .edit-btn:hover { border-color: rgba(255,255,255,0.4); color: #fff; }
        .delete-btn:hover { border-color: #ff6b6b; color: #ff6b6b; }

        .users-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

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

        .role-select {
            padding: 8px 14px;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 8px;
            color: #fff;
            font-size: 13px;
            outline: none;
            cursor: pointer;
        }

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

        .modal-box {
            background: #0c1530;
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 16px;
            padding: 32px;
            width: 100%;
            max-width: 420px;
            max-height: 85vh;
            overflow-y: auto;
        }

        .modal-box h2 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 24px;
        }

        .field { margin-bottom: 16px; }

        .field label {
            display: block;
            font-size: 13px;
            color: rgba(255,255,255,0.6);
            margin-bottom: 6px;
        }

        .field input {
            width: 100%;
            padding: 12px 16px;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 8px;
            color: #fff;
            font-size: 14px;
            outline: none;
        }

        .field input:focus { border-color: rgba(255,255,255,0.3); }

        .modal-actions {
            display: flex;
            gap: 10px;
            margin-top: 8px;
        }

        .modal-actions button[type="button"] {
            flex: 1;
            padding: 11px;
            background: transparent;
            border: 1px solid rgba(255,255,255,0.15);
            color: rgba(255,255,255,0.7);
            border-radius: 8px;
            font-size: 13px;
            cursor: pointer;
        }

        .modal-actions button[type="submit"] {
            flex: 1;
            padding: 11px;
            background: #fff;
            color: #010822;
            border: none;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
        }
    </style>
</head>
<body>

    <header class="top-bar">
        <img src="https://6degrees.com.sa/assets/imgs/logo-light.png" alt="6 Degrees">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="logout-btn">Logout</button>
        </form>
    </header>

    <main class="dashboard-main">

        @if (session('success'))
            <div class="success-banner">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.5"/>
                    <path d="M8 12l2.5 2.5L16 9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        {{-- ===== قسم الفريق ===== --}}
        <div class="section-header">
            <div>
                <h1 class="section-title">Team Dashboard</h1>
                <p class="section-sub">Manage your team members</p>
            </div>
        </div>

        <div class="grid">
            @foreach ($teamMembers as $member)
                <div class="member-card">
                    <div class="member-top">
                        <div class="avatar">
                            @if ($member->photo)
                                <img src="{{ asset('storage/' . $member->photo) }}" alt="{{ $member->name }}">
                            @else
                                {{ strtoupper(substr($member->name, 0, 1)) }}
                            @endif
                        </div>
                        <div class="member-info">
                            <h3>{{ $member->name }}</h3>
                            <p>{{ $member->role === 'admin' ? 'Admin' : ($member->department ?? 'Team Member') }}</p>
                        </div>
                    </div>

                    <div class="member-meta">
                        @if ($member->department) القسم: {{ $member->department }}<br> @endif
                        {{ $member->email }}<br>
                        @if ($member->phone) {{ $member->phone }} @endif
                    </div>

                    @if (Auth::user()->isAdmin() || Auth::id() === $member->id)
                        <div class="member-actions">
                            <button type="button" class="edit-btn" onclick='openEditModal(@json($member))'>Edit</button>

                            @if (Auth::user()->isAdmin() && $member->id !== Auth::id())
                                <form action="{{ route('team.destroy', $member) }}" method="POST" onsubmit="return confirm('متأكد من الحذف؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="delete-btn">Delete</button>
                                </form>
                            @endif
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        {{-- ===== قسم User Management (للأدمن فقط) ===== --}}
        @if (Auth::user()->isAdmin())
            <section style="margin-top: 64px;">
                <h1 class="section-title">User Management</h1>
                <p class="section-sub">Invite new team members and assign roles</p>

                <form action="{{ route('users.invite') }}" method="POST" style="display: flex; gap: 12px; margin-bottom: 24px; align-items: flex-end;">
                    @csrf

                    <div class="field" style="flex: 1; margin-bottom: 0;">
                        <label>Name</label>
                        <input type="text" name="name" placeholder="Full name" required>
                    </div>

                    <div class="field" style="flex: 1; margin-bottom: 0;">
                        <label>Email</label>
                        <input type="email" name="email" placeholder="email@6degrees.com.sa" required>
                    </div>

                    <button type="submit" class="logout-btn" style="white-space: nowrap; padding: 12px 24px; background: #fff; color: #010822; border: none; font-weight: 600;">
                        Send Invite
                    </button>
                </form>

                @error('email')
                    <div class="success-banner" style="background: rgba(255,69,58,0.1); border-color: rgba(255,69,58,0.25); color: #ff6b6b;">
                        <span>{{ $message }}</span>
                    </div>
                @enderror

                <div class="users-list">
                    @foreach ($users as $u)
                        <div class="user-row">
                            <div>
                                <p class="name">{{ $u->name }}</p>
                                <p class="email">{{ $u->email }}</p>
                            </div>

                            <form action="{{ route('users.updateRole', $u) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <select
                                    name="role"
                                    class="role-select"
                                    onchange="this.form.submit()"
                                    {{ $u->id === Auth::id() ? 'disabled' : '' }}
                                >
                                    <option value="user" {{ $u->role === 'user' ? 'selected' : '' }}>User</option>
                                    <option value="admin" {{ $u->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                </select>
                            </form>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

    </main>

    {{-- ===== مودال التعديل ===== --}}
    <div class="modal-overlay" id="memberModal">
        <div class="modal-box">

            <form id="memberForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <h2>Edit Member</h2>

                <div class="field">
                    <label>Name</label>
                    <input type="text" name="name" id="nameInput" required>
                </div>

                <div class="field">
                    <label>Title</label>
                    <input type="text" name="role" id="roleInput" required>
                </div>

                <div class="field">
                    <label>Department</label>
                    <input type="text" name="department" id="departmentInput">
                </div>

                <div class="field">
                    <label>Phone</label>
                    <input type="text" name="phone" id="phoneInput" required>
                </div>

                <div class="field">
                    <label>Bio</label>
                    <input type="text" name="bio" id="bioInput">
                </div>

                <div class="field">
                    <label>Photo</label>
                    <input type="file" name="photo">
                </div>

                <div class="modal-actions">
                    <button type="button" onclick="closeModal()">Cancel</button>
                    <button type="submit">Save</button>
                </div>
            </form>

        </div>
    </div>

    <script>
        const memberModal = document.getElementById('memberModal');
        const form = document.getElementById('memberForm');

        function openEditModal(member) {
            form.reset();
            form.action = `/team/${member.id}`;

            document.getElementById('nameInput').value = member.name ?? '';
            document.getElementById('roleInput').value = member.role ?? '';
            document.getElementById('departmentInput').value = member.department ?? '';
            document.getElementById('phoneInput').value = member.phone ?? '';
            document.getElementById('bioInput').value = member.bio ?? '';

            memberModal.classList.add('active');
        }

        function closeModal() {
            memberModal.classList.remove('active');
        }
    </script>

</body>
</html>