<?php

if ( ! posix_isatty(STDOUT)) {
    trigger_error('wrong terminal');
    exit;
}

function my_error_handler( $errno, $errstr, $errfile, $errline )
{
    $msg = 'ERROR #'.$errno.': '.$errstr.'(FILE:'.$errfile.',LINE:'.$errline.')' . PHP_EOL;
    file_put_contents(__DIR__ . '/error.log', $msg, FILE_APPEND);
}
set_error_handler('my_error_handler');

require_once __DIR__ . '/App/Conf.php';
require_once __DIR__ . '/App/FS.php';

require __DIR__ . '/Lib/Ncurses.php';
require __DIR__ . '/Lib/Cursor.php';
require __DIR__ . '/Lib/Window.php';
require __DIR__ . '/Lib/Listbox.php';


require_once __DIR__ . '/App/Main.php';

$main = new Main();
$main->start();
