<?php

if (!defined('MODX_BASE_PATH')) {
	die('What are you doing? Get out of here!');
}
$e =& $modx->event;

switch ($e->name) {
    case 'OnManagerLogin':
        require MODX_BASE_PATH . 'assets/snippets/bdumper/class_evobdumper.php';
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
        global $database_server, $dbase;
        $dbase = trim($dbase, '`');
        $dumper = new evoBDumper($database_server, $dbase);
        $dumper->setDroptables(true);
        $dumper->createDump();
        break;
    default:
        return;
}