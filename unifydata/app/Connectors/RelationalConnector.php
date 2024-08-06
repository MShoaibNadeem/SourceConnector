<?php
namespace App\Connectors;
use PDO;
use PDOException;
use Gemini\Laravel\Facades\Gemini;

class RelationalConnector implements ConnectionTesterInterface
{
    public function testConnection($configurations)
    {
        dd("hello");
        $username=$configurations['username'];
        $password=$configurations['password'];
        // $connectionString = $this->fetchConnectionStringFromGemini($configurations);
        //dd($connectionString);

        $dsn=$this->getDsn($configurations);
        dd($dsn);
        try {
            $pdo = new PDO($dsn,$username,$password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return ['success' => true, 'message' => 'Connection successful'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Connection failed', 'error' => $e->getMessage()];
        }
    }


    private function getDsn($configurations): string
    {
        $type = $configurations['type'];
        $name=$configurations['name'];
        $host = $configurations['host'];
        $port = $configurations['port'];
        $database = $configurations['database'];
        $username = $configurations['username'];
        $password = $configurations['password'];
        $charset = $configurations['charset'] ?? 'utf8mb4';

        switch ($name) {
            case 'MySQL':
                $dsn = "mysql:host=$host;port=$port;dbname=$database;charset=$charset";
                break;

            case 'PostgreSQL':
                $dsn = "pgsql:host=$host;port=$port;dbname=$database";
                break;

            case 'SQLite':
                $dsn = "sqlite:$database";
                break;

            case 'Oracle Database':
                $dsn = "oci:dbname=//$host:$port/$database;charset=$charset";
                break;

            case 'Microsoft SQL Server':
                $dsn = "sqlsrv:Server=$host,$port;Database=$database";
                break;

            case 'IBM Db2':
                $dsn = "ibm:DRIVER={IBM DB2 ODBC DRIVER};DATABASE=$database;HOSTNAME=$host;PORT=$port;PROTOCOL=TCPIP;UID=$username;PWD=$password;";
                break;

            case 'MariaDB':
                $dsn = "mysql:host=$host;port=$port;dbname=$database;charset=$charset";
                break;

            case 'SAP HANA':
                $dsn = "odbc:DRIVER={HDBODBC};SERVERNODE=$host:$port;DATABASE=$database";
                break;

            case 'Amazon Aurora':
                $dsn = "mysql:host=$host;port=$port;dbname=$database;charset=$charset";
                break;

            case 'Google Cloud SQL':
                $dsn = "mysql:host=$host;port=$port;dbname=$database;charset=$charset";
                break;

            case 'Teradata':
                $dsn = "odbc:DRIVER={Teradata};DBCNAME=$host;DATABASE=$database";
                break;

            case 'Snowflake':
                $dsn = "odbc:Driver={SnowflakeDSIIDriver};Server=$host;Database=$database;Warehouse=YOUR_WAREHOUSE;Role=YOUR_ROLE;UID=$username;PWD=$password;";
                break;

            case 'Informix':
                $dsn = "informix:host=$host;service=$port;database=$database;server=YOUR_SERVER;protocol=onsoctcp;EnableScrollableCursors=1";
                break;

            case 'Sybase':
                $dsn = "dblib:host=$host:$port;dbname=$database";
                break;

            default:
                throw new \InvalidArgumentException("Unsupported database type: $type");
        }

        return $dsn;
    }

    private function fetchConnectionStringFromGemini($configurations)
    {
        $prompt = "Generate a PDO connection string for the following configurations:".json_encode($configurations);

        $response = Gemini::geminiPro()->generateContent($prompt);
        dd($response);


        // Fetching only the required part from the response
        $textContent = $response->candidates[0]->content->parts[0]->text;

        // Removing ``` from beginning and end
        $trimmedText = trim($textContent, '`');
        echo $trimmedText;
        echo "\n";
        return $trimmedText;
    }
}
