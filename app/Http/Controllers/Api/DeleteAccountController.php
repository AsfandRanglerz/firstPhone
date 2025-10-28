<?php

namespace App\Http\Controllers\Api;

use Exception;
use Illuminate\Http\Request;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Mail\AccountDeletion;
use App\Mail\VendorAccountDeletion;
use Illuminate\Support\Facades\Mail;
class DeleteAccountController extends Controller
{
    public function deleteAccount(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return ResponseHelper::error(null, 'User not authenticated', 'error', 401);
            }

            // Perform account deletion logic here
            $user->delete();
            Mail::to($user->email)->send(new AccountDeletion($user));

            return ResponseHelper::success(null, 'Account deleted successfully', null, 200);
        } catch (Exception $e) {
            return ResponseHelper::error($e->getMessage(), 'An error occurred while deleting the account', 'error', 500);
        }
    }

    public function vendordeleteAccount(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return ResponseHelper::error(null, 'User not authenticated', 'error', 401);
            }

            // Perform account deletion logic here
            $user->delete();
            Mail::to($user->email)->send(new VendorAccountDeletion($user));
            return ResponseHelper::success(null, 'Account deleted successfully', null, 200);
        } catch (Exception $e) {
            return ResponseHelper::error($e->getMessage(), 'An error occurred while deleting the account', 'error', 500);
        }
    }
}
