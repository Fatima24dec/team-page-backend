<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TeamMembersController extends Controller
{
public function dashboard()
{
    $currentUser = Auth::user();

    // نجيب باقي الأعضاء (غير المستخدم الحالي) مرتبين بالأحدث
    $others = User::where('id', '!=', $currentUser->id)->latest()->get();

    // نحط المستخدم الحالي أول، وبعده الباقين
    $teamMembers = collect([$currentUser])->merge($others);

    $users = User::latest()->get();
    $departments = User::whereNotNull('department')
        ->where('department', '!=', '')
        ->distinct()
        ->pluck('department');

    return view('dashboard', compact('teamMembers', 'users', 'departments'));
}

    public function edit(User $teamMember)
    {
        if (!Auth::user()->isAdmin() && $teamMember->id !== Auth::id()) {
            abort(403, __('messages.no_permission_edit'));
        }

        return view('team.edit', compact('teamMember'));
    }

   public function update(Request $request, User $teamMember)
{
    if (!Auth::user()->isAdmin() && $teamMember->id !== Auth::id()) {
        abort(403, __('messages.no_permission_edit'));
    }

    $removePhoto = $request->input('remove_photo') === '1';

    // إذا مو admin، يقدر يعدل بس الجوال والصورة
if (!Auth::user()->isAdmin()) {
    $validated = $request->validate([
        'phone' => 'required|string|max:30',
    ]);
    $fieldsToCheck = ['phone'];
} else {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'position'   => 'nullable|string|max:255',
            'phone'      => 'required|string|max:30',
            'department' => 'nullable|string|max:255',
            'bio'        => 'nullable|string',
            'photo'      => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);
        $fieldsToCheck = ['name', 'position', 'phone', 'department', 'bio'];
    }

    $hasChanges = false;
    foreach ($fieldsToCheck as $field) {
        if (($validated[$field] ?? null) != $teamMember->$field) {
            $hasChanges = true;
            break;
        }
    }

if (Auth::user()->isAdmin()) {
    if ($request->hasFile('photo') || ($removePhoto && $teamMember->photo)) {
        $hasChanges = true;
    }
}

    if (!$hasChanges) {
        return redirect()->route('team.dashboard')
            ->with('info', __('messages.no_changes'));
    }

if (Auth::user()->isAdmin()) {

    if ($request->hasFile('photo')) {
        if ($teamMember->photo) {
            Storage::disk('public')->delete($teamMember->photo);
        }

        $validated['photo'] = $request->file('photo')->store('team-photos', 'public');

    } elseif ($removePhoto && $teamMember->photo) {

        Storage::disk('public')->delete($teamMember->photo);
        $validated['photo'] = null;
    }
}

    $teamMember->update($validated);

    return redirect()->route('team.dashboard')
        ->with('success', __('messages.data_updated'));
}


    public function destroy(User $teamMember)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, __('messages.no_permission_edit'));
        }

        if ($teamMember->id === Auth::id()) {
            abort(403, __('messages.no_permission_edit'));
        }

        if ($teamMember->photo) {
            Storage::disk('public')->delete($teamMember->photo);
        }

        $teamMember->delete();

        return redirect()->route('team.dashboard')->with('success', __('messages.data_updated'));
    }

 public function teams()
{
    $users = User::all()->map(function ($user) {

        $user->photo = $user->photo
            ? asset('storage/' . $user->photo)
            : null;

        return $user;
    });

    return response()->json($users);
}

}