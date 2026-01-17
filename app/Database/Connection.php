<?php

namespace App\Database;

use PDO;

class Connection
{
    public static function get()
    {
        return new PDO(
            "mysql:host=localhost;dbname=projectsolati_db",
            "root",
            "",
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
    }
}
