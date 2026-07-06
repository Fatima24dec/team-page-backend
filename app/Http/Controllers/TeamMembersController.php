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
        $teamMembers = User::latest()->get();
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

        $removePhoto = $request->boolean('remove_photo');

        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'position'   => 'nullable|string|max:255',
            'phone'      => 'required|string|max:30',
            'department' => 'nullable|string|max:255',
            'bio'        => 'nullable|string',
            'photo'      => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            if ($teamMember->photo) {
                Storage::disk('public')->delete($teamMember->photo);
            }
            $validated['photo'] = $request->file('photo')->store('team-photos', 'public');
        } elseif ($removePhoto && $teamMember->photo) {
            Storage::disk('public')->delete($teamMember->photo);
            $validated['photo'] = null;
        }

        $teamMember->update($validated);

        return redirect()->route('team.dashboard')->with('success', __('messages.data_updated'));
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
}