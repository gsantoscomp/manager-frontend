<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProcedureController extends Controller
{
    public function index()
    {
        return view('admin.procedure.list');
    }
}
