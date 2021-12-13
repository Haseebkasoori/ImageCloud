<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateUserRequest extends FormRequest
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
            'id' => 'required|exists:users,_id',
            'name' => 'string|regex:/^[a-zA-Z ]+$/u',
            'age' => 'numeric|min:12',
            'profile_image' => 'valid_image',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $data['message']=$validator->errors();
        throw new HttpResponseException(response()->error($data, 400));
    }

    public function all($keys = null)
    {
    // Add route parameters to validation data
        return array_merge(parent::all(), $this->route()->parameters());
    }
}
