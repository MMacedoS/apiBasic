<?php

namespace App\Http\Controllers\v1\Home;

use App\Http\Controllers\Controller;
use App\Http\Request\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        return $this->respondJson(['message' => 'Welcome to the Home Controller!']);
    }
}
