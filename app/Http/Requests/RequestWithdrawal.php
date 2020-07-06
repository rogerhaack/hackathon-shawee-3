<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RequestWithdrawal extends FormRequest
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
            "*.mlProductId" => "required",
            "*.saleId" => "required",
            "*.title" => "required",
            "*.length" => "required|integer",
            "*.width" => "required|integer",
            "*.height" => "required|integer",
            "*.weight" => "required|integer"
        ];
    }
}
