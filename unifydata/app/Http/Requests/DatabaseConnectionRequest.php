<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DatabaseConnectionRequest extends FormRequest
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
        elseif ($type=='API')
        {

        }


    }
}
