<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UserStoreUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $user_id = $this->route('user_id') ?? null;
        return [
            'firstname' => 'required|string|max:255',
            'secondname' => 'nullable|string|max:255',
            'lastname' => 'required|string|max:255',
            'username' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users', 'username')->ignore($user_id)
            ],
            'email' => [
                'required',
                'string',
                'max:255',
                'email',
                Rule::unique('users', 'email')->ignore($user_id)
            ],
            'password' => ($user_id ? 'nullable' : 'required') . '|string|min:8|max:255',
            'image' => 'nullable|file|image',
            'roles' => 'array',
            'roles.*' => 'integer|exists:roles,id',
            'permissions' => 'array',
            'permissions.*' => 'integer|exists:permissions,id',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if(empty($validator->errors()->all())){
                //$this->checkImageName($validator);
            }
        });
    }

    private function checkImageName($validator)
    {
        if($this->file('image')) {
            $image_name = $this->file('image')->getClientOriginalName();
            if(strlen($image_name) > 244)
            {
                $validator->errors()->add('image', "The image name is too long");
            }
        }
    }
}
