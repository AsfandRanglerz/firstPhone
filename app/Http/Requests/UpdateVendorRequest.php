<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVendorRequest extends FormRequest
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
                        'name' => 'sometimes|required',
            'email' => [
                'sometimes',
                'required',
                'email',
                'regex:/^[\w\.\-]+@[a-zA-Z0-9\-]+\.[a-zA-Z]{2,6}$/',
                'unique:users,email,' . $this->route('id')
            ],
            'phone' => 'sometimes|required|regex:/^[0-9]+$/|max:15',
            'password' => 'nullable|min:6',
            'location' => 'sometimes|required|string|max:255',
            'cnic_front' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'cnic_back'  => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'shop_images'   => 'nullable|array|max:5',
            'shop_images.*' => 'image|mimes:jpg,jpeg,png|max:2048',
        ];
    }
}
