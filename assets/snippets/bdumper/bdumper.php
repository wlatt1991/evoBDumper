<?php
if (!defined('MODX_BASE_PATH')) {die('What are you doing? Get out of here!');}
require MODX_BASE_PATH . 'assets/snippets/bdumper/class_mysqldumper.php';
global $path;
$modx = evolutionCMS();

function snapshot(&$dumpstring)
{
    global $path;
    file_put_contents($path, $dumpstring, FILE_APPEND);
    return true;
}

function parsePlaceholder($tpl = '', $ph = array())
{
    if (empty($ph) || empty($tpl)) {
        return $tpl;
    }

    foreach ($ph as $k => $v) {
        $k = "[+{$k}+]";
        $tpl = str_replace($k, $v, $tpl);
    }
    return $tpl;
}

$modx->config['snapshot_path'] = MODX_BASE_PATH . 'assets/backup/';

if (!is_dir(rtrim($modx->config['snapshot_path'], '/'))) {
    mkdir(rtrim($modx->config['snapshot_path'], '/'));
    @chmod(rtrim($modx->config['snapshot_path'], '/'), 0777);
}

if (!file_exists("{$modx->config['snapshot_path']}.htaccess")) {
    $htaccess = "order deny,allow\ndeny from all\n";
    file_put_contents("{$modx->config['snapshot_path']}.htaccess", $htaccess);
}

if (!is_writable(rtrim($modx->config['snapshot_path'], '/'))) {
    echo parsePlaceholder($_lang["bkmgr_alert_mkdir"], $modx->config['snapshot_path']);
    exit;
}

$today = date('Y-m-d_H-i-s_00');
$path = "{$modx->config['snapshot_path']}{$today}.sql";
@set_time_limit(120);
$dbase = trim($dbase, '`');
$dumper = new Mysqldumper($database_server, $dbase);
$dumper->setDroptables(true);
$dumper->createDump('snapshot');
