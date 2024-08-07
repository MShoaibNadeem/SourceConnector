<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TestConnectionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required',
            'type' => 'required',
            'base_url' => 'required_if:type,API|url',
            'auth_type' => 'required_if:type,API|string',
            'auth_credentials' => 'required_if:type,API|array|nullable',
            'host' => 'required_if:type,Database|string',
            'port' => 'required_if:type,Database|string',
            'username' => 'required_if:type,Database|string|nullable',
            'password' => 'required_if:type,Database|string|nullable',
            'database' => 'required_if:type,Database|string',
        ];
    }
}
