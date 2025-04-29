<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FacturaController extends Controller
{
    public function index()
    {

       return view('facturacion.index');
    }

    public function dashboard()
    {

       return view('facturacion.dashboard');
    }
}
