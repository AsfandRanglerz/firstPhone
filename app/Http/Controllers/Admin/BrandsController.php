<?php

namespace App\Http\Controllers\Admin;

use App\Models\Brand;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BrandsController extends Controller
{
    //

    public function index()   {
        $brands = Brand::all();
        return view('admin.brands.index', compact('brands'));
    }

    public function create()  {
        return view('admin.brands.create');
    } 


public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|unique:brands,name',
    ]);

    $brand = Brand::create([
        'name' => $request->name,
        'slug' => $request->slug ?? Str::slug($request->name),
    ]);

    return response()->json([
        'status' => 'success',
        'message' => 'Brand created successfully',
        'data' => $brand
    ]);
}


public function edit($id) {
    $brand = Brand::find($id);
    return view('admin.brands.edit');
}

public function update(Request $request, $id)
{
    $request->validate([
        'name' => 'required|string|unique:brands,name,' . $id,
    ]);

    $brand = Brand::findOrFail($id);

    $brand->update([
        'name' => $request->name,
        'slug' => $request->slug ?? Str::slug($request->name),
    ]);

    return response()->json([
        'status' => 'success',
        'message' => 'Brand updated successfully',
        'data' => $brand
    ]);
}


public function delete($id) {
    $find = Brand::find($id);

    $find->delete();

    return redirect()->route('brands.index')->with(['success' => 'Brand delete successfully']);
    
}

public function modelView(){
    return view('admin.brands.models');
} 

}