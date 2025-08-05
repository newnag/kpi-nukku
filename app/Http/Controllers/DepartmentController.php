<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::all();
        return view('departments.app', compact('departments'));
    }
    public function store(Request $request)
    {
        $request->validate(([
            'name' => 'required|string|max:255'
        ]));

        //ตรวจสอบว่ามีหน่วยงานนี้อยู่แล้วหรือไม่
        $existingDepartment = Department::where('name', $request->name)->first();
        if ($existingDepartment) {
            return redirect()->route('departments.index')->with('error', 'หน่วยงานนี้มีอยู่แล้ว');
        }
        Department::create([
            'name' => $request->name
        ]);
        // return response()->json([
        //     'message' => 'หน่วยงานถูกสร้างเรียบร้อยแล้ว',
        //     'data' => $request->name
        // ], 201);
        return redirect()->route('departments.index')->with('success','สร้างหน่วยงานเรียบร้อย');
    }
}
