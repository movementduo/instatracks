<?php

class Database extends PDO
{
    public function __construct($cfg)
    {
        
        $dns = $cfg['database']['driver'] .
        ':host=' . $cfg['database']['host'] .
        ((!empty($cfg['database']['port'])) ? (';port=' . $cfg['database']['port']) : '') .
        ';dbname=' . $cfg['database']['schema'];
        
        parent::__construct($dns, $cfg['database']['username'], $cfg['database']['password']);
    }
}
?>
