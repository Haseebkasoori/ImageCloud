<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class SearchPostRequest extends FormRequest
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
        // dd(request('created_at'));
        return [
            'created_at' => 'date_format:Y-m-d|before_or_equal:'.Date('Y-m-d'),
            'today'=>'date_format:Y-m-d|in:'.Date('Y-m-d'),
            'image_name'=>'string',
            'extension'=>'in:png,jpeg,bmp,gif,jpg,svg,webp',
            'visibility' => 'in:Private,Public,Hidden',
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
