<?php

namespace App\Http\Controllers;

use App\Models\SaleTemplate;
use Illuminate\Http\Request;

class SaleTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(SaleTemplate::all()->map(function ($template) {
            $template->cart_items = json_decode($template->cart_items);
            return $template;
        }));
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
        $request->validate([
            'name' => 'required|string',
            'note'=> 'nullable|string',
        ]);

        $template = SaleTemplate::create([
            'name' => $request->name,
            'note' => $request->note,
            'cart_items' => json_encode($request->cart_items),
        ]);

        return response()->json(['message' => 'Template created successfully!', 'success'=> true], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(SaleTemplate $saleTemplate)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SaleTemplate $saleTemplate)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SaleTemplate $saleTemplate)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SaleTemplate $saleTemplate)
    {
        $saleTemplate->delete();
        return response()->json(['message' => 'Template deleted successfully!', 'success'=> true], 200);
    }
}
