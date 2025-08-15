<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;


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
        return view('users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'prefix' => 'required|string|max:10',
            'name' => 'required|string|max:100',
            'employee_id' => 'required|string|max:20|unique:users',
            'password' => 'required|string|min:6',
            'email' => 'required|email|max:50|unique:users',
            'phone' => 'required|string|max:20',
            'department_id' => 'required|integer',
            'status' => 'required|boolean',
            'role' => 'required|exists:roles,name', // ✅ ตรวจสอบชื่อบทบาท
        ]);

        $user = User::create([
            'prefix' => $request->prefix,
            'name' => $request->name,
            'employee_id' => $request->employee_id,
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
        $departments = Department::orderBy('name')->pluck('name'); // รายชื่อหน่วยงาน
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'prefix' => 'required|string|max:10',
            'name' => 'required|string|max:100',
            'employee_id' => 'required|string|max:20|unique:users,employee_id,' . $id,
            'email' => 'required|email|max:50|unique:users,email,' . $id,
            'phone' => 'required|string|max:20',
            'department_id' => 'required|integer',
            'status' => 'required|boolean',
            'role' => 'required|exists:roles,name', // ✅ ตรวจสอบบทบาท
        ]);

        $updateData = [
            'prefix' => $request->prefix,
            'name' => $request->name,
            'employee_id' => $request->employee_id,
            'email' => $request->email,
            'phone' => $request->phone,
            'department_id' => $request->department_id,
            'status' => $request->status
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        // ✅ เปลี่ยนบทบาท (แทนที่ role เดิม)
        $user->syncRoles([$request->role]);

        return redirect()->route('users.index')->with('success', 'อัพเดทผู้ใช้งานสำเร็จ');
    }


    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('users.index')->with('success', 'ลบผู้ใช้งานสำเร็จ');
    }
}
