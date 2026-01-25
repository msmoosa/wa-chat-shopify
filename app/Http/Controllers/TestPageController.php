<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestPageController extends Controller
{
    /**
     * Display the test page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('test-page');
    }
}
