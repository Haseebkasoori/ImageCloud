<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class SavePostRequest extends FormRequest
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
            'visibility' => 'required|in:Private,Public,Hidden',
            'share_with'=>'required_if:visibility,Private|array',
            'attachment' => 'array'
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $data['message']=$validator->errors();
        throw new HttpResponseException(response()->error($data, 400));
    }
}

