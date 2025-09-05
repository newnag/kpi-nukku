<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::with('department', 'roles')->orderBy('id', 'asc')->get();
        $departments = Department::orderBy('name')->pluck('name'); // รายชื่อหน่วยงาน
        $roles = Role::orderBy('name')->pluck('name');
        return view('users.management.app', compact('users', 'departments', 'roles'));
    }

    public function create()
    {
        $roles = Role::orderBy('name')->pluck('name');
        $users = User::with('department', 'roles')->orderBy('id', 'asc')->get();
        $departments = Department::orderBy('name')->get(['id', 'name']);
        return view('users.management.create', compact('departments', 'roles', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([

            'name' => 'required|string|max:100',
            'password' => 'required|string|min:6',
            'email' => 'required|email|max:50|unique:users',
            'phone' => 'required|string|max:20',
            'department_id' => 'required|integer',
            'status' => 'required|boolean',
            'role' => 'required|exists:roles,name', // ✅ ตรวจสอบชื่อบทบาท
        ]);

        $user = User::create([

            'name' => $request->name,
            'password' => Hash::make($request->password),
            'email' => $request->email,
            'phone' => $request->phone,
            'department_id' => $request->department_id,
            'status' => $request->status,
            'remember_token' => null
        ]);

        // ✅ กำหนดบทบาท
        $user->assignRole($request->role);

        return redirect()->route('users.index')->with('success', 'เพิ่มผู้ใช้งานสำเร็จ');
    }


    // public function show($id)
    // {
    //     $user = User::findOrFail($id);
    //     return view('users.show', compact('user'));
    // }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $departments = Department::orderBy('name')->get();
        $roles = Role::pluck('name');
        return view('users.management.edit', compact('user', 'departments', 'roles'));
    }
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:100'],
            'email'         => ['required', 'email', 'max:100', Rule::unique('users', 'email')->ignore($user->id)],
            'phone'         => ['required', 'string', 'max:20'],
            'department_id' => ['required', 'integer', 'exists:departments,id'],
            'status'        => ['required', 'in:0,1'],
            'role'          => ['required', 'exists:roles,name'],
            'password'      => ['nullable', 'confirmed', 'min:8'],
        ]);

        $updateData = [
            'name'          => $validated['name'],
            'email'         => $validated['email'],
            'phone'         => $validated['phone'],
            'department_id' => (int) $validated['department_id'],
            'status'        => (int) $validated['status'],
        ];
        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $affected = User::whereKey($id)->update($updateData);
        $user->syncRoles([$validated['role']]);

        return redirect()->route('users.index')
            ->with('success', $affected ? 'อัปเดตแล้ว' : 'ไม่มีการเปลี่ยนแปลง');
    }


    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('users.index')->with('success', 'ลบผู้ใช้งานสำเร็จ');
    }
}
