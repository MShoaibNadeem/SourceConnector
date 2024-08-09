<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TestConnectionRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Update with your authorization logic
    }

    public function rules()
    {
        $type = $this->input('type');
        $name = $this->input('name');

        if ($type == 'Database') {
            $rules = [
                'username' => 'required|string',
                'password' => 'required|string',
                'host' => 'required|string',
                'port' => 'required|integer',
                'database' => 'required|string',
            ];

            // Add specific rules based on the database name
            switch ($name) {
                case 'MySQL':
                case 'InfluxDB':
                case 'MariaDB':
                case 'PostgreSQL':
                case 'Google Cloud SQL':
                case 'SQLite':
                case 'Sybase':
                case 'Oracle Database':
                case 'Microsoft SQL Server':
                case 'IBM Db2':
                case 'SAP HANA':
                case 'Teradata':
                case 'Snowflake':
                case 'Informix':
                case 'Amazon Aurora':
                    $rules['charset'] = 'nullable|string';
                    break;
                case 'MongoDB':
                    $rules = [
                        'database' => 'required|string',
                        'username' => 'nullable|string',
                        'password' => 'nullable|string',
                    ];
                    break;
                case 'Elasticsearch':
                    $rules = [
                        'hosts' => 'required|array',
                        'port' => 'nullable|integer',
                        'username' => 'nullable|string',
                        'password' => 'nullable|string',
                    ];
                    break;
                case "Redis":
                    $rules = [
                        'host' => 'required|string',
                        'port' => 'nullable|integer',
                        'scheme' => 'nullable|string',
                    ];
                    break;
                default:
                    throw new \InvalidArgumentException("Unsupported database type: $type");
            }

            return $rules;
        }
        //Write code for API here Hamza bhai
        elseif ($type == 'API') {
            $rules = [
                'base_url' => 'required|url',
                'auth_type' => 'required|string|in:No_Auth,API_Key,Bearer,Basic_HTTP,Session_Token,OAuth',
            ];

            switch ($this->input('auth_type')) {
                case 'API_Key':
                    $rules['auth_credentials.api_key'] = 'required|string';
                    $rules['auth_credentials.inject_into'] = 'required|string|in:Query Parameter,Header,Body data (urlencoded form),Body JSON payload';
                    $rules['auth_credentials.parameter_name'] = 'required|string';
                    break;
                case 'Bearer':
                    $rules['auth_credentials.token'] = 'required|string';
                    break;

                case 'Basic_HTTP':
                    $rules['auth_credentials.username'] = 'required|string';
                    $rules['auth_credentials.password'] = 'required|string';
                    break;

                case 'Session_Token':
                    $rules['auth_credentials.session_token'] = 'required|string';
                    break;

                case 'OAuth':
                    $rules['auth_credentials.token_url'] = 'required|url';
                    $rules['auth_credentials.client_id'] = 'required|string';
                    $rules['auth_credentials.client_secret'] = 'required|string';
                    $rules['auth_credentials.scopes'] = 'nullable|string';
                    break;

                case 'No_Auth':
                default:
                    break;
            }
            return $rules;
        }


    }
}
