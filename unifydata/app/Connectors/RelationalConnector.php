<?php
namespace App\Connectors;
use PDO;
use PDOException;
use Illuminate\Support\Facades\Response;

class RelationalConnector implements ConnectionTesterInterface
{
    //Main Interface function
    public function testConnection($type,$name,$configurations)
    {
        $username=$configurations['username'];
        $password=$configurations['password'];
        //Gnerating relevant dsn on the basis of Database Name
        $dsn=$this->getDsn($type,$name,$configurations);
        try {
            $pdo = new PDO($dsn,$username,$password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return Response::success('Connection Successful',200);
        } catch (PDOException $e) {
            return Response::error('Connection failed',$e->getMessage(),400);
        }
    }
    //Generating DSN on the basis of Database Name
    private function getDsn($type,$name,$configurations): string
    {
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
}
