<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Standard;
use Illuminate\Http\Request;

class CategorieController extends Controller
{
    public function index(){
        $standards =Standard::orderBy('id','asc')->get();
        $categories =Category::OrderBy('id','asc')->get();
        return view('categories.app',compact('standards','categories'));
    }
}
