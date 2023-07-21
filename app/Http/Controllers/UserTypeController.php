<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserTypeController extends Controller
{
    public function index()
    {
        return view('admin.user-type.list');
    }
}
