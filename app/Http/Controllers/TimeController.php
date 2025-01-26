<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TimeController extends Controller
{
    public function index()
    {
        return Time::all();
    }

    public function store(Request $request)
    {
        $request->validate(['nome' => 'required|string|max:255']);
        return Time::create($request->all());
    }
}
