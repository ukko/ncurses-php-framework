#!/usr/local/bin/php -q

<?php 

define('XCURSES_KEY_LF',    13);
define('XCURSES_KEY_CR',    10);
define('XCURSES_KEY_ESC',   27);

###################################################################################################
function menu_select($params) {
###################################################################################################
    if(!is_array($params) || empty($params)) {
        trigger_error('wrong params');
        return NULL;
    }

    $menu = isset($params['items']) ? $params['items'] : NULL;
    $rows = isset($params['rows']) ? (int)$params['rows'] : 0;
    $cols = isset($params['cols']) ? (int)$params['cols'] : 0;
    $selected = isset($params['selected']) ? (int)$params['selected'] : 0;
    $centered = empty($params['centered']) ? 0 : 1;
    $y_menu = isset($params['y']) ? (int)$params['y'] : 0;
    $x_menu = isset($params['x']) ? (int)$params['x'] : 0;
   
    if(!is_array($menu) || empty($menu) || $rows<=0 || $cols<=0 || $y_menu<0 || $x_menu<0) {
        trigger_error('wrong params');
        return NULL;
    }

    $keys = array_keys($menu);
    $values = array();

    $current = 0;
    $width = 0;
    $height = count($menu) + 2;

    foreach ($menu as $value) {
        $width = max($width, strlen($value));
    }

    $i = 0;
    foreach ($menu as $k => $v) {
        $values[$i] = ' '.$v.str_repeat(' ',1 + $width - strlen($v));
        if ($k == $selected) $current = $i;
        $i++;
    }

    $width += 4;

    if ($centered) {
        $y_menu = ($rows - $height) >> 1;
        $x_menu = ($cols - $width) >> 1;
    }

    $window = ncurses_newwin($height, $width, $y_menu, $x_menu);
    if (empty($window)) {
        trigger_error('unable to create window');
        return NULL;
    }

    ncurses_wborder($window, 0,0, 0,0, 0,0, 0,0);

    for ($a = 0; $a < count($values); $a++) {
        if ($a == $current) ncurses_wattron($window, NCURSES_A_REVERSE);
        ncurses_mvwaddstr($window, 1 + $a, 1, $values[$a]);
        if ($a == $current) ncurses_wattroff($window, NCURSES_A_REVERSE);
    }

    ncurses_wrefresh($window);
    ncurses_keypad($window,TRUE);
    ncurses_curs_set(0);

    do {
        $key = ncurses_wgetch($window);
        $move = 0;
        switch ($key) {
            case NCURSES_KEY_UP :
                if ($current > 0) $move = -1;
                break;
            case NCURSES_KEY_DOWN :
                if ($current < count($values) - 1) $move = 1;
                break;
            case XCURSES_KEY_LF:
            case XCURSES_KEY_CR :
                $result = $keys[$current];
                break;
            case XCURSES_KEY_ESC :
                ncurses_flushinp();
                $result = '';
                break;
        }

        if ($move) {
            ncurses_mvwaddstr($window, 1 + $current, 1, $values[$current]);
            $current += $move;
            ncurses_wattron($window, NCURSES_A_REVERSE);
            ncurses_mvwaddstr($window, 1 + $current, 1, $values[$current]);
            ncurses_wattroff($window, NCURSES_A_REVERSE);
            ncurses_wrefresh($window);
        }
    } while (!isset($result));

    ncurses_delwin($window);
    return $result;
}

###################################################################################################
# main
###################################################################################################

if (!posix_isatty(STDOUT)) {
    trigger_error('wrong terminal');
    exit;
}

ncurses_init();
error_reporting(E_ALL);

$main_window = ncurses_newwin(0, 0, 0, 0);
ncurses_getmaxyx($main_window, $rows, $cols);

$result = menu_select(
    array (
        'items' => array (
            'dude'    => 'The Dude',
            'walter'  => 'Walter Sobchak',
            'donny'   => 'Donny',
            'maude'   => 'Maude Lebowski',
            'jesus'   => 'Jesus Quintana',
            'smokey'  => 'Smokey'
         ),
        'rows' => $rows,
        'cols' => $cols,
        'selected' => 3,
        'centered' => 1
    )
);

ncurses_end();

echo 'Result is:'.$result."\n";

?>