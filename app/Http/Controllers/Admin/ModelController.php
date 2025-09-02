<?php

namespace App\Http\Controllers\Admin;

use App\Models\Brand;
use App\Models\MobileModel;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ModelController extends Controller
{
   public function index($id)
{
   $brand = Brand::with(['mobileModels' => function($query) {
    $query->orderBy('id', 'desc'); 
}])->findOrFail($id);
    $models = $brand->mobileModels;

    return view('admin.brands.models', compact('models' , 'brand'));
}

 public function store(Request $request)
{
    $request->validate([
        'brand_id' => 'required|exists:brands,id', // ✅ brand exist hona chahiye
        'name'     => 'required|array',
        'name.*'   => 'required|string|distinct|unique:models,name',
    ]);

    $createdModels = [];

    foreach ($request->name as $name) {
        $createdModels[] = MobileModel::create([
            'brand_id' => $request->brand_id, // ✅ brand id store correctly
            'name'     => $name,
        ]);
    }

    return response()->json([
        'message' => 'Models Created Successfully',
        'data'    => $createdModels
    ]);
}



    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|unique:models,name,' . $id,
        ]);

        $model = MobileModel::findOrFail($id);
        $model->update([
            'name' => $request->name,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Model Updated Successfully',
            'data' => $model,
        ]);
    }

    public function destroy($id)
    {
        $model = MobileModel::findOrFail($id);
        $model->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Model Deleted Successfully',
        ]);
    }
}