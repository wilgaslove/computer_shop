<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;

class RegisteredUserController extends Controller
{

public function store(Request $request)
{
    dd([
        'controller' => 'RegisteredUserController',
        'method' => $request->method(),
        'data' => $request->all(),
    ]);
}
  
}