<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class UserUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $userId = $this->route('id');

        return [
            'prefix' => 'required|string|max:10|in:Mr.,Mrs.',
            'firstname' => 'required|string|max:50',
            'middlename' => 'required|string|max:50',
            'lastname' => 'required|string|max:50',
            'suffixname' => 'required|string|max:10|in:Jr,II,III,IV',
            'username' => 'required|string|max:50|unique:users,username,' . $userId,
            'email' => 'required|email|max:100|unique:users,email,'. $userId,
            'photo' => 'nullable|url',
            'type' => 'required|string|in:user,admin',
        ];
    }

    public function messages()
    {
        return [
            'prefix.in' => 'Prefix must be either "Mr" or "Mrs"',
            'firstname.required' => 'Firstname is required.',
            'lastname.required' => 'Lastname is required.',
            'username.unique' => 'Username is already taken.',
            'email.unique' => 'Email address is already registered.',
            'type.in' => 'Type must be either "user" or "admin".',
            'suffixname.in' => 'Suffixname must be either "Jr" / "II" / "III" / "IV"',
        ];
    }
}
