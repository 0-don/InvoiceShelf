<?php

namespace Modules\HelloWorld\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HelloWorldController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('helloworld::index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('helloworld::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {}

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('helloworld::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('helloworld::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id) {}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id) {}
}
