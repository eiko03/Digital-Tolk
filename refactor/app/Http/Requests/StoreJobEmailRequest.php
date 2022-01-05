<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class StoreJobEmailRequest extends FormRequest
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
            'user_type' => 'required',
            'user_email' => 'required|email',
            'log_data' => 'required',
            'due' => 'required',
            'dateChanged' => 'required',
            'from_language_id' => 'required',
        ];
    }
}