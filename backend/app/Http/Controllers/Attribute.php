<?php

namespace App\Http\Controllers;

use App\Models\Attribute as ModelsAttribute;
use Illuminate\Http\Request;

class Attribute extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $attributes = ModelsAttribute::with('attributeValues')->get();
        $color = $attributes->firstWhere('id', 2);
        $size = $attributes->firstWhere('id', 1);
        return response()->json([
            // 'attributes' => $attributes,
            'colors' => $color,
            'sizes' => $size,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
