<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApplicationController extends Controller
{
    public function __invoke()
    {
        if (!Auth::check()) {
            return view('admin.layouts.app');
        } else {
            return view('auth.login');
        }
    }
}
