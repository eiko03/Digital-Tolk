<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class DistanceFeedRequest extends FormRequest
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
            'distance' => 'sometimes',
            'time' => 'sometimes',
            'jobid' => 'sometimes',
            'session_time' => 'sometimes',
            'flagged' => 'sometimes',
            'admincomment' => 'required',
            'manually_handled' => 'required',
            'by_admin' => 'required',
        ];
    }
}