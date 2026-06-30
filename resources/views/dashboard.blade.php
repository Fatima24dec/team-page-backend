@extends('layouts.app')

@section('content')
<div class="container py-5">

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>فريق العمل</h1>

        @if (Auth::user()->isAdmin())
            <button type="button" class="btn btn-primary" onclick="openCreateModal()">
                إضافة عضو جديد
            </button>
        @endif
    </div>

    <div class="row g-4">
        @foreach ($teamMembers as $member)
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card h-100">
                    @if ($member->photo)
                        <img src="{{ asset('storage/' . $member->photo) }}" class="card-img-top" alt="{{ $member->name }}">
                    @endif
                    <div class="card-body">
                        <h5 class="card-title">{{ $member->name }}</h5>
                        <p class="text-muted mb-1">{{ $member->role }}</p>

                        @if ($member->department)
                            <p class="mb-1"><strong>القسم:</strong> {{ $member->department }}</p>
                        @endif

                        @if ($member->email)
                            <p class="mb-1"><strong>الإيميل:</strong> {{ $member->email }}</p>
                        @endif

                        @if ($member->phone)
                            <p class="mb-1"><strong>الهاتف:</strong> {{ $member->phone }}</p>
                        @endif

                        @if ($member->bio)
                            <p class="card-text mt-2">{{ $member->bio }}</p>
                        @endif
                    </div>

                    @if (Auth::user()->isAdmin() || Auth::id() === $member->user_id)
                        <div class="card-footer d-flex justify-content-between">
                            <button
                                type="button"
                                class="btn btn-sm btn-outline-secondary"
                                onclick='openEditModal(@json($member))'
                            >
                                تعديل
                            </button>

                            @if (Auth::user()->isAdmin())
                                <form action="{{ route('team.destroy', $member) }}" method="POST" onsubmit="return confirm('متأكد من الحذف؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">حذف</button>
                                </form>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

</div>

{{-- المودال الموحد للإضافة والتعديل --}}
<div class="modal fade" id="memberModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <form id="memberForm" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="formMethod" name="_method" value="">

                <div class="modal-header">
                    <h5 class="modal-title" id="memberModalLabel">إضافة عضو جديد</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    {{-- يظهر فقط وقت الإضافة --}}
                    <div class="mb-3" id="userIdField">
                        <label class="form-label">اختر المستخدم</label>
                        <select name="user_id" id="userIdSelect" class="form-select">
                            <option value="">-- اختر --</option>
                            @foreach (\App\Models\User::all() as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">الاسم</label>
                        <input type="text" name="name" id="nameInput" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">المسمى الوظيفي</label>
                        <input type="text" name="role" id="roleInput" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">القسم</label>
                        <input type="text" name="department" id="departmentInput" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">الإيميل</label>
                        <input type="email" name="email" id="emailInput" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">رقم الهاتف</label>
                        <input type="text" name="phone" id="phoneInput" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">نبذة (Bio)</label>
                        <textarea name="bio" id="bioInput" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">الصورة</label>
                        <input type="file" name="photo" class="form-control">
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">حفظ</button>
                </div>

            </form>

        </div>
    </div>
</div>

<script>
    const memberModalEl = document.getElementById('memberModal');
    const memberModal = new bootstrap.Modal(memberModalEl);
    const form = document.getElementById('memberForm');
    const userIdField = document.getElementById('userIdField');

    function openCreateModal() {
        form.reset();
        form.action = "{{ route('team.store') }}";
        document.getElementById('formMethod').value = "POST";
        document.getElementById('memberModalLabel').innerText = "إضافة عضو جديد";
        userIdField.style.display = "block";
        memberModal.show();
    }

    function openEditModal(member) {
        form.reset();
        form.action = `/team/${member.id}`;
        document.getElementById('formMethod').value = "PUT";
        document.getElementById('memberModalLabel').innerText = "تعديل بيانات العضو";
        userIdField.style.display = "none"; // ما نقدر نغير اليوزر وقت التعديل

        document.getElementById('nameInput').value = member.name ?? '';
        document.getElementById('roleInput').value = member.role ?? '';
        document.getElementById('departmentInput').value = member.department ?? '';
        document.getElementById('emailInput').value = member.email ?? '';
        document.getElementById('phoneInput').value = member.phone ?? '';
        document.getElementById('bioInput').value = member.bio ?? '';

        memberModal.show();
    }
</script>

@endsection