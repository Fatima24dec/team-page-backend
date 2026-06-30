<?php

namespace App\Http\Controllers;

use App\Models\TeamMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeamMembersController extends Controller
{

    public function dashboard()
    {
        $teamMembers = TeamMember::latest()->get();
        return view('team.dashboard', compact('teamMembers'));
    }

    // صفحة إضافة عضو جديد (admin فقط)
    public function create()
    {
        return view('team.create');
    }

    // حفظ عضو جديد (admin فقط)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id'    => 'required|exists:users,id|unique:team_members,user_id',
            'name'       => 'required|string|max:255',
            'role'      => 'required|string|max:255',
            'email'      => 'nullable|email|max:255',
            'phone'      => 'required|string|max:30',
            'department' => 'nullable|string|max:255',
            'bio'        => 'nullable|string',
            'photo'      => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('team-photos', 'public');
        }

        TeamMember::create($validated);

        return redirect()->route('team.dashboard')->with('success', 'تمت إضافة العضو بنجاح.');
    }

    // صفحة تعديل عضو
    public function edit(TeamMember $teamMember)
    {
        // لو مو admin، يقدر يعدل بس على بياناته هو
        if (!Auth::user()->isAdmin() && $teamMember->user_id !== Auth::id()) {
            abort(403, 'غير مصرح لك بتعديل بيانات هذا العضو.');
        }

        return view('team.edit', compact('teamMember'));
    }

    // تحديث بيانات عضو
    public function update(Request $request, TeamMember $teamMember)
    {
        if (!Auth::user()->isAdmin() && $teamMember->user_id !== Auth::id()) {
            abort(403, 'غير مصرح لك بتعديل بيانات هذا العضو.');
        }

        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'role'      => 'required|string|max:255',
            'email'      => 'nullable|email|max:255',
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

    // حذف عضو (admin فقط)
    public function destroy(TeamMember $teamMember)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'غير مصرح لك بحذف الأعضاء.');
        }

        $teamMember->delete();

        return redirect()->route('team.dashboard')->with('success', 'تم حذف العضو بنجاح.');
    }
}
