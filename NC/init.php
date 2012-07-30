<?php

declare(ticks = 1);

if ( ! posix_isatty(STDOUT)) {
    trigger_error('wrong terminal');
    exit;
}

function sig_handler($signo)
{
    switch ($signo)
    {
        case SIGINT:
            ncurses_end();
            exit('exit');
        case SIGTERM:
            ncurses_end();
            exit('exit');
        default:
            // other
    }
}

pcntl_signal(SIGTERM, 'sig_handler');
pcntl_signal(SIGINT, 'sig_handler');

function my_error_handler( $errno, $errstr, $errfile, $errline )
{
    $msg = date('H:m:i') . ': ' . $errno . ': '.$errstr.PHP_EOL.'(FILE:'.$errfile.',LINE:'.$errline.')' . PHP_EOL;
    file_put_contents(__DIR__ . '/error.log', $msg, FILE_APPEND);
}

set_error_handler('my_error_handler');


function autoload($className)
{
    $className = ltrim($className, '\\');
    $fileName  = '';
    $namespace = '';
    if ($lastNsPos = strripos($className, '\\')) {
        $namespace = substr($className, 0, $lastNsPos);
        $className = substr($className, $lastNsPos + 1);
        $fileName  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
    }
    $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

    require $fileName;
}

spl_autoload_register('autoload');
