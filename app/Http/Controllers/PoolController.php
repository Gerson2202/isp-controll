<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PoolController extends Controller
{
    public function index(){

        return view('pooles.index');
    }
}
