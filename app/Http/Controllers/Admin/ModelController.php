<?php

namespace App\Http\Controllers\Admin;

use App\Models\MobileModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;

class ModelController extends Controller
{
    public function index()
    {
        $models = MobileModel::all();
        return view('admin.brands.models', compact('models'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|array',
            'name.*' => 'required|string|distinct|unique:models,name',
        ]);

        $createdModels = [];

        foreach ($request->name as $name) {
            $createdModels[] = MobileModel::create([
                'name' => $name,
                'slug' => Str::slug($name),
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Model(s) created successfully',
            'data' => $createdModels,
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
            'slug' => $request->slug ?? Str::slug($request->name),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Model updated successfully',
            'data' => $model,
        ]);
    }

    public function destroy($id)
    {
        $model = MobileModel::findOrFail($id);
        $model->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Model deleted successfully',
        ]);
    }
}