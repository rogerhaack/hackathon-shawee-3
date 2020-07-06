<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Coordinates extends FormRequest
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
            "start" => "required",
            "start.longitude" => "required",
            "start.latitude" => "required",
            "end" => "required",
            "end.longitude" => "required",
            "end.latitude" => "required"
        ];
    }
}
