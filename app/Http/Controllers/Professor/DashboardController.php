<?php

namespace App\Http\Controllers\Professor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Aqui pode ser customizado futuramente (avisos, agenda, etc.)
        return view('professor.dashboard');
    }
}
