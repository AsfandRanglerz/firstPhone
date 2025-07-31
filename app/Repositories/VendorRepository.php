<?php

namespace App\Repositories;


use App\Models\Vendor;
use Illuminate\Support\Facades\Mail;
use App\Repositories\Interfaces\VendorRepositoryInterface;

class VendorRepository implements VendorRepositoryInterface
{
    public function all()
    {
        return Vendor::orderBy('id', 'desc')->get();
    }

    public function find($id)
    {
        return Vendor::find($id);
    }

    public function create(array $data)
    {
        return Vendor::create($data);
    }

    public function update($id, array $data)
    {
        $user = Vendor::findOrFail($id);
        $user->update($data);
        return $user;
    }

    public function delete($id)
    {
        $user = Vendor::find($id);
        if ($user) {
            $user->delete();
            return true;
        }
        return false;
    }

    public function toggleStatus($id, $status, $reason = null)
    {
        $user = Vendor::find($id);
        if (!$user) return null;

        $user->toggle = $status;
        $user->save();

        if ($status == 0 && $reason) {
            $this->sendDeactivationEmail($user, $reason);
        }

        return $user;
    }

    protected function sendDeactivationEmail($user, $reason)
    {
        $data = [
            'name' => $user->name,
            'email' => $user->email,
            'reason' => $reason
        ];

        try {
            Mail::send('emails.user_deactivated', $data, function($message) use ($user) {
                $message->to($user->email, $user->name)
                    ->subject('Account Deactivation Notification');
            });
        } catch (\Exception $e) {
            \Log::error("Failed to send email: " . $e->getMessage());
        }
    }
}
