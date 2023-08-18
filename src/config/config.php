<?php

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");

class Conexion
{
    private $host = "sql8005.site4now.net";
    private $user = "db_a8cb81_rutcore_admin";
    private $pw = "jeremyxd55";
    private $db = "db_a8cb81_rutcore";

    public function Conectar()
    {
        $cnx = "sqlsrv:Server=$this->host;Database=$this->db";
        $conectar = new PDO($cnx, $this->user, $this->pw);
        $conectar->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conectar;
    }
}

?>