<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InicioController extends Controller
{
    public function __construct(){
        $this->tittle = "INICIO";
    }

    public function home(){
        return view('home')->with(['tittle' => $this->tittle]);
    }
}
