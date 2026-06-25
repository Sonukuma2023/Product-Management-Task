<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
class UserControlller extends Controller
{
   public function index(){
    
        return Inertia::render('User/UserDashboard');
    }
}
