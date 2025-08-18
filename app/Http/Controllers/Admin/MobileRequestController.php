<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\MobileRequest;
use App\Http\Controllers\Controller;

class MobileRequestController extends Controller
{
    public function index() //
    {
        $mobilerequests = MobileRequest::with('brand', 'model')->get();
        return view('admin.mobilerequest.index', compact('mobilerequests'));
    } 

    public function show($id)
    {
        $mobilerequests = MobileRequest::findOrFail($id);
        return view('admin.mobilerequest.show', compact('mobilerequests'));
    }

    public function mobileRequestCounter()
    {
        $count = MobileRequest::where('status', 2)->count();
        return response()->json(['count' => $count]);
    }
    
    public function delete($id)
    {
        $mobilerequest = MobileRequest::findOrFail($id);
        $mobilerequest->delete();
        return redirect()->route('mobilerequest.index')->with('success', 'Mobile Request Deleted Successfully');
    }
}
