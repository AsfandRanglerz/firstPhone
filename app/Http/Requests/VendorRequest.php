<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VendorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'email' => [
                'required',
                'email',
                'regex:/^[\w\.\-]+@[a-zA-Z0-9\-]+\.[a-zA-Z]{2,6}$/'
            ],
            'phone' => 'required|regex:/^[0-9]+$/|max:15',
            'password' => 'required|min:6',
            'location' => 'required|string|max:255',
            'cnic_front' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'cnic_back'  => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'shop_images'   => 'required|array|max:5',
            'shop_images.*' => 'image|mimes:jpg,jpeg,png|max:2048',
        ];
    }
}
