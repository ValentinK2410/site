<?php
/**
 * Импорт справочных терминов в глоссарий.
 * Запуск: php import-glossary-terms.php (из корня WordPress)
 */

if ( php_sapi_name() !== 'cli' ) {
	die( 'Запускайте только из командной строки' );
}

require_once __DIR__ . '/wp-load.php';

if ( ! class_exists( 'Glossary_Tooltips' ) ) {
	die( "Плагин Glossary Tooltips не активен.\n" );
}

$created = Glossary_Tooltips::instance()->do_import_default_terms();
echo "Добавлено терминов: $created\n";
