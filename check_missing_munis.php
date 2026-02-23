<?php
require_once 'api-votes/Config/Config.php';
require_once 'api-votes/Libraries/Core/Conexion.php';
require_once 'api-votes/Libraries/Core/Mysql.php';

class CheckMissing extends Mysql
{
    public function run()
    {
        $json = json_decode(file_get_contents('api-votes/Json/Config.json'), true);
        $munis = [];
        foreach ($json['munis'] as $m) {
            if ($m['dptomuni'] == '15') {
                $munis[strtoupper(trim($m['namemuni']))] = $m['idmuni'];
            }
        }

        $csv = fopen('act_votos.csv', 'r');
        fgetcsv($csv, 1000, ';');
        $csvMunis = [];
        while (($row = fgetcsv($csv, 1000, ';')) !== FALSE) {
            $name = strtoupper(trim(mb_convert_encoding($row[5], 'UTF-8', 'Windows-1252')));
            $csvMunis[$name] = true;
        }
        fclose($csv);

        echo "Checking Missing Municipalities (Magdalena):\n";
        foreach (array_keys($csvMunis) as $name) {
            if (!isset($munis[$name])) {
                echo "MISSING IN Config.json: $name\n";
            } else {
                $muniID = $munis[$name];
                $sql = "SELECT COUNT(*) as c FROM zones WHERE muni_zone = $muniID";
                $res = $this->select($sql, []);
                if ($res['c'] == 0) {
                    echo "ZERO ZONES IN DB: $name (ID: $muniID)\n";
                }
            }
        }
    }
}

$c = new CheckMissing();
$c->run();
