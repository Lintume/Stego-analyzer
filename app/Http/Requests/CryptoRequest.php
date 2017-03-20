<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CryptoRequest extends FormRequest
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
            'pictures.original' => 'required:string',
            'pictures.containers.*.base64Picture' => 'required:string',
            'pictures.containers.*.bytes' => 'required|integer'
        ];
    }
}
