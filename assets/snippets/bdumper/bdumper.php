<?php
if (!defined('MODX_BASE_PATH')) {die('What are you doing? Get out of here!');}
//$modx = evolutionCMS();
global $modx;
echo 'test';
print_r($modx);
print_r('adsf');
/*
require MODX_BASE_PATH . 'assets/snippets/bdumper/class_mysqldumper.php';
$modx = evolutionCMS();
$modx->config['snapshot_path'] = MODX_BASE_PATH . 'assets/backup/';

if (!is_dir(rtrim($modx->config['snapshot_path'], '/'))) {
    mkdir(rtrim($modx->config['snapshot_path'], '/'));
    @chmod(rtrim($modx->config['snapshot_path'], '/'), 0777);
}

if (!file_exists("{$modx->config['snapshot_path']}.htaccess")) {
    $htaccess = "order deny,allow\ndeny from all\n";
    file_put_contents("{$modx->config['snapshot_path']}.htaccess", $htaccess);
}

@set_time_limit(120);
$dbase = trim($dbase, '`');
$dumper = new evoBDumper($database_server, 'rrr');
$dumper->setDroptables(true);
$dumper->createDump();
*/