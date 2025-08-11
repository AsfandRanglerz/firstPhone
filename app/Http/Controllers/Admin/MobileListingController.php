<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\MobileListing;
use App\Http\Controllers\Controller;

class MobileListingController extends Controller
{
    public function index()
    {
        $mobiles = MobileListing::all();
        return view('admin.mobilelisting.index', compact('mobiles'));
    }

    public function show($id)
    {
        $mobiles = MobileListing::findOrFail($id);
        return view('admin.mobilelisting.show', compact('mobiles'));
    }

    public function mobileListingCounter()
    {
        $count = MobileListing::where('status', 2)->count(); 
        return response()->json(['count' => $count]);
    }

     public function approve($id)
    {
        $mobile = MobileListing::findOrFail($id);
        $mobile->status = 0; // 0 = Approved
        $mobile->save();

        return redirect()->route('mobile.index')->with('success', 'Mobile Listing Approved Successfully');
    }
 
   public function reject($id)
    {
        $mobile = MobileListing::findOrFail($id);
        $mobile->status = 1; // 1 = Rejected
        $mobile->save();

        return redirect()->route('mobile.index')->with('success', 'Mobile Listing Rejected');
    }

    public function delete($id)
    {
        $mobile = MobileListing::findOrFail($id); 
        $mobile->delete();
        return redirect()->route('mobile.index')->with('success', 'Mobile Listing Deleted Successfully');
    }

    

    
}
