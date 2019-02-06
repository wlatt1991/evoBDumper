<?
class Mysqldumper
{
    public $_isDroptables;
    public $dbname;
    public $database_server;

    public function __construct($database_server, $dbname)
    {
        $this->dbname = $dbname;
        $this->database_server = $database_server;
        $this->setDroptables(false);
    }

    public function setDroptables($state)
    {
        $this->_isDroptables = $state;
    }

    public function isDroptables()
    {
        return $this->_isDroptables;
    }

    public function createDump($callBack)
    {
        $modx = evolutionCMS();
        //print_r($modx);
        $lf = "\n";
        $result = $modx->db->query('SHOW TABLES');
        $tables = $this->result2Array(0, $result);
        foreach ($tables as $tblval) {
            $result = $modx->db->query("SHOW CREATE TABLE `{$tblval}`");
            $createtable[$tblval] = $this->result2Array(1, $result);
        }
        $output = "#{$lf}";
        $output .= "# " . addslashes($modx->config['site_name']) . " Database Dump{$lf}";
        $output .= "# MODX Version: {$modx->config['settings_version']}{$lf}";
        $output .= "#{$lf}";
        $output .= "# Host:{$this->database_server}{$lf}";
        $output .= "# Generation Time: " . $modx->toDateFormat(time()) . $lf;
        $output .= "# Server version: " . $modx->db->getVersion() . $lf;
        $output .= "# PHP Version: " . phpversion() . $lf;
        $output .= "# Database : `{$this->dbname}`{$lf}";
        $output .= "# Description: evoBDumper autobackup" . $lf;
        $output .= "#" . $lf;

        foreach ($tables as $tblval) {
            $output .= "{$lf}{$lf}# --------------------------------------------------------{$lf}{$lf}";
            $output .= "#{$lf}# Table structure for table `{$tblval}`{$lf}";
            $output .= "#{$lf}{$lf}";
            if ($this->isDroptables()) {
                $output .= "DROP TABLE IF EXISTS `{$tblval}`;{$lf}";
            }
            $output .= "{$createtable[$tblval][0]};{$lf}";
            $output .= $lf;
            $output .= "#{$lf}# Dumping data for table `{$tblval}`{$lf}#{$lf}";
            $result = $modx->db->select('*', $tblval);
            $rows = $this->loadObjectList('', $result);
            foreach ($rows as $row) {
                $insertdump = $lf;
                $insertdump .= "INSERT INTO `{$tblval}` VALUES (";
                $arr = $this->object2Array($row);
                foreach ($arr as $key => $value) {
                    $value = addslashes($value);
                    $value = str_replace(array("\r\n", "\r", "\n"), '\\n', $value);
                    $insertdump .= "'$value',";
                }
                $output .= rtrim($insertdump, ',') . ");";
            }
            // if ($callBack) {
            //     if (!$callBack($output)) {
            //         break;
            //     }

            //     $output = '';
            // }
        }
        // return ($callBack) ? true : $output;
        $today = date('Y-m-d_H-i-s_00');
        $path = "{$modx->config['snapshot_path']}{$today}.sql";
        file_put_contents($path, $output, FILE_APPEND);
    }

    public function object2Array($obj)
    {
        $array = null;
        if (is_object($obj)) {
            $array = array();
            foreach (get_object_vars($obj) as $key => $value) {
                if (is_object($value)) {
                    $array[$key] = $this->object2Array($value);
                } else {
                    $array[$key] = $value;
                }
            }
        }
        return $array;
    }

    public function loadObjectList($key = '', $resource)
    {
        $modx = evolutionCMS();
        $array = array();
        while ($row = $modx->db->getRow($resource, 'object')) {
            if ($key) {
                $array[$row->$key] = $row;
            } else {
                $array[] = $row;
            }
        }
        $modx->db->freeResult($resource);
        return $array;
    }

    public function result2Array($numinarray = 0, $resource)
    {
        $modx = evolutionCMS();
        $array = array();
        while ($row = $modx->db->getRow($resource, 'num')) {
            $array[] = $row[$numinarray];
        }
        $modx->db->freeResult($resource);
        return $array;
    }
}
