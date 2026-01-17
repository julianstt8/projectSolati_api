<?php

namespace App\Database;

use PDO;

class Connection
{
    public static function get()
    {
        return new PDO(
            "pgsql:host=localhost;port=5432;dbname=projectsolati_db",
            "postgres",
            "123456",
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
    }
}
