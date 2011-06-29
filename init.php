<?php
define('XCURSES_KEY_LF',    13);
define('XCURSES_KEY_CR',    10);
define('XCURSES_KEY_ESC',   27);

if (!posix_isatty(STDOUT)) {
    trigger_error('wrong terminal');
    exit;
}

function my_error_handler($errno, $errstr, $errfile, $errline) {
    $msg = 'ERROR #'.$errno.': '.$errstr.'(FILE:'.$errfile.',LINE:'.$errline.')';
    file_put_contents(__DIR__ . '/error.log', $msg, FILE_APPEND);
}
set_error_handler('my_error_handler');

ncurses_init();
require __DIR__ . '/lib/cursor.php';
require __DIR__ . '/lib/window.php';
require_once __DIR__ . '/main.php';

$main = new Main();

ncurses_end();