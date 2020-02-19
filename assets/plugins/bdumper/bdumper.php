//<?php
/**
 * evoBDumper
 *
 * Plugin to automatically create backups
 * 
 * @category    plugin
 * @version     0.1
 * @author      Wlatt
 * @internal    @modx_category Manager and Admin
 * @internal    @properties &templates=ID шаблонов на которых рабатывает плагин;text;7 &jquery=jQuery;text;0
 * @internal    @events OnLoadWebDocument,OnWebPagePrerender
 * @internal    @installset base
 */

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
}