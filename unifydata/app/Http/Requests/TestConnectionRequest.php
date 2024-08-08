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
            'username' => 'nullable|string',
            'password' => 'nullable|string',
            'database' => 'required_if:type,Database|string',
        ];
    }

    /**
     * Sanitize the validated data.
     *
     * @return void
     */
    protected function passedValidation()
    {
        $this->merge([
            'name' => filter_var($this->input('name'), FILTER_SANITIZE_STRING),
            'type' => filter_var($this->input('type'), FILTER_SANITIZE_STRING),
            'base_url' => filter_var($this->input('base_url'), FILTER_SANITIZE_URL),
            'auth_type' => filter_var($this->input('auth_type'), FILTER_SANITIZE_STRING),
            'auth_credentials' => $this->sanitizeAuthCredentials($this->input('auth_credentials')),
            'host' => filter_var($this->input('host'), FILTER_SANITIZE_STRING),
            'port' => filter_var($this->input('port'), FILTER_SANITIZE_STRING),
            'username' => filter_var($this->input('username'), FILTER_SANITIZE_STRING),
            'password' => filter_var($this->input('password'), FILTER_SANITIZE_STRING),
            'database' => filter_var($this->input('database'), FILTER_SANITIZE_STRING),
        ]);
    }

    /**
     * Sanitize auth credentials.
     *
     * @param array|null $credentials
     * @return array|null
     */
    private function sanitizeAuthCredentials($credentials)
    {
        if (is_array($credentials)) {
            return array_map(function($item) {
                return filter_var($item, FILTER_SANITIZE_STRING);
            }, $credentials);
        }

        return null;
    }
}
