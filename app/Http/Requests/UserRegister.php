<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRegister extends FormRequest
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
            'name' => 'required|min:5|max:255',
            'email' => 'required|email',
            'password' => 'required|min:9',
            'confirmPassword' => 'required|same:password',
            "document" => 'required|min:11',
            "type" => 'required|in:S,D',
            "phone" => "required",
            "phone.areaCode" => "required|min:2|max:2",
            "phone.number" => "required|min:8|max:9",
            "address" => "required",
            "address.street" => "required|max:255",
            "address.district" => "required|max:255",
            "address.zipCode" => "required|min:8|max:8",
            "address.city" => "required|max:255",
            "address.state" => "required|min:2|max:2",
            "address.number" => "present|max:255",
            "address.complement" => "present|max:255",
            "car" => "present",
            "car.model" => "present|max:255",
            "car.plate" => "present|max:20",
            "car.measures" => 'present',
            "car.measures.height" => 'present|integer',
            "car.measures.width" => 'present|integer',
            "car.measures.length" => 'present|integer',
        ];
    }
}
