<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PagesController extends Controller
{
    public function index(){
        $title = "Welcome to Running Blog!";
        //return view('pages.index', compact('title')); moze i sa with umesto compact
        return view('pages.index')->with('title', $title);
    }

    public function about(){
        $title = "About us";
        return view('pages.about')->with('title', $title);
    }

    public function services(){
        $data = array(
            'title' => 'Services',
            'services' => ['Training plan', 'Diet meal plan', 'Running shoes recommendations']
        );
        return view('pages.services')-> with($data);
    }
}
