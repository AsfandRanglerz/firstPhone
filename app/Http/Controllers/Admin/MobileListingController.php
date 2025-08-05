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

    public function delete($id)
    {
        $mobile = MobileListing::findOrFail($id); 
        $mobile->delete();
        return redirect()->route('mobile.index')->with('success', 'Mobile listing deleted successfully');
    }

     public function active(Request $request, $id)
    {
        $data = MobileListing::find($id);
    
        if (!$data) {
            return redirect()->route('mobile.index')->with([
                'action' => false,
                'message' => 'Mobile listing not found',
            ]);
        }
    
        $data->update([
            'action' => $request->action,
        ]);
    
        $message['name'] = $data->name;

        $mobilelisting = MobileListing::findOrFail($id);
        $mobilelisting->update([
            'status' => 0  // Set status to Approved when action is taken
        ]);
        // if (Auth::guard('subadmin')->check()) {
        //     $subadmin = Auth::guard('subadmin')->user();
        //     $subadminName = $subadmin->name;
        //     SubAdminLog::create([
        //         'subadmin_id' => Auth::guard('subadmin')->id(),
        //         'section' => 'License Approvals',
        //         'action' => 'Approve',
        //         'message' => "SubAdmin {$subadminName} Approved License {$data->name}",
        //     ]);
        // }
        try {
            // Mail::to($data->email)->send(new LicenseApprovalActivated($message));

            return redirect()->route('mobile.index')->with([
                'action' => true,
                'message' => 'Mobile listing approved successfully',
            ]);
        } catch (\Throwable $th) {
            return back()->with([
                'action' => false,
                'message' => 'Failed to send email: ' . $th->getMessage(),
            ]);
        }
    }
    



    public function deactive(Request $request, $id)
{
    $data = MobileListing::find($id);

    if (!$data) {
        return redirect()->route('mobile.index')->with([
            'action' => false,
            'message' => 'Mobile Listing not found',
        ]);
    }

    $data->update([
        'action' => $request->action,
    ]);

    $message['reason'] = $request->reason;
    $message['name'] = $data->name;

 $mobilelisting = MobileListing::findOrFail($id);
    $mobilelisting->update([
        'status' => 1  // Set status to Rejected when action is taken
    ]);
    // if (Auth::guard('subadmin')->check()) {
    //     $subadmin = Auth::guard('subadmin')->user();
    //     $subadminName = $subadmin->name;
    //     SubAdminLog::create([
    //         'subadmin_id' => Auth::guard('subadmin')->id(),
    //         'section' => 'License Approvals',
    //         'action' => 'Reject',
    //         'message' => "SubAdmin {$subadminName} Rejected License {$data->name}",
    //     ]);
    // }
    try {
        // Mail::to($data->email)->send(new LicenseApprovalDeActivated($message));

        return redirect()->route('mobile.index')->with([
            'action' => true,
            'message' => 'Mobile listing rejected successfully',
        ]);
    } catch (\Throwable $th) {
        return back()->with([
            'action' => false,
            'message' => 'Failed to send email: ' . $th->getMessage(),
        ]);
    }
}

    
}
