<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\User;

class ProfileRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        $email = auth()->user()->email;
        return [
            'name' => ['required', 'string', 'max:255'],
            'avatar' => ['required', 'image', 'max:10000'],
            'email' => ['required', 'email', Rule::unique(User::class)->ignore($email, 'email'), 'max:255'],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'avatar' => ['nullable', 'image', 'max:10000']
        ];
    }
}
