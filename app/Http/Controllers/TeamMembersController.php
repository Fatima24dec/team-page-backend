<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeamMembersController extends Controller
{
    public function dashboard()
    {
        $teamMembers = User::latest()->get();
        $users = User::latest()->get();

        return view('dashboard', compact('teamMembers', 'users'));
    }

    public function edit(User $teamMember)
    {
        if (!Auth::user()->isAdmin() && $teamMember->id !== Auth::id()) {
            abort(403, 'غير مصرح لك بتعديل بيانات هذا العضو.');
        }

        return view('team.edit', compact('teamMember'));
    }

    public function update(Request $request, User $teamMember)
    {
        if (!Auth::user()->isAdmin() && $teamMember->id !== Auth::id()) {
            abort(403, 'غير مصرح لك بتعديل بيانات هذا العضو.');
        }

        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'role'       => 'required|string|max:255',
            'phone'      => 'required|string|max:30',
            'department' => 'nullable|string|max:255',
            'bio'        => 'nullable|string',
            'photo'      => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('team-photos', 'public');
        }

        $teamMember->update($validated);

        return redirect()->route('team.dashboard')->with('success', 'تم تحديث البيانات بنجاح.');
    }

    public function destroy(User $teamMember)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'غير مصرح لك بحذف الأعضاء.');
        }

        if ($teamMember->id === Auth::id()) {
            abort(403, 'لا يمكنك حذف حسابك الخاص.');
        }

        $teamMember->delete();

        return redirect()->route('team.dashboard')->with('success', 'تم حذف العضو بنجاح.');
    }
}
