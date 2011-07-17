#!/usr/local/bin/php -q

<?php

define('XCURSES_KEY_LF',    13);
define('XCURSES_KEY_CR',    10);
define('XCURSES_KEY_ESC',   27);
define('XCURSES_KEY_SPACE',  32);

###############################################################################################
function menu_check_list($params) {
###############################################################################################
    if(!is_array($params) || empty($params)) {
        trigger_error('wrong_params');
        return NULL;
    }

    $menu = isset($params['items']) ? $params['items'] : NULL;
    $rows = isset($params['rows']) ? (int)$params['rows'] : 0;
    $cols = isset($params['cols']) ? (int)$params['cols'] : 0;
    $centered = empty($params['centered']) ? 0 : 1;
    $y_menu = isset($params['y']) ? (int)$params['y'] : 0;
    $x_menu = isset($params['x']) ? (int)$params['x'] : 0;

    if(!is_array($menu) || empty($menu) || $rows<=0 || $cols<=0 || $y_menu<0 || $x_menu<0) {
        trigger_error('wrong params');
        return NULL;
    }

    $keys = array_keys($menu);
    $n_menu = count($keys);

    $items = array();
    $checked = array();
    $current = 0;
    $width = 0;
    $height = $n_menu + 2;
    $i = 0;
    $k = NULL;
    $i_checked = NULL;

    for($i=0; $i<$n_menu; $i++) {
        $k = $keys[$i];
        $i_checked = (isset($menu[$k][1]) && $menu[$k][1] == 1) ? 1 : 0;
        $items[$i] = ' ['. ($i_checked ? '*' : ' ').'] '.$menu[$k][0];
        $width = max($width, strlen($items[$i]));
        $checked[$i] = $i_checked;
    }

    for ($i=0; $i<$n_menu; $i++) {
        $items[$i] = $items[$i].str_repeat(' ', 2 + $width - strlen($items[$i]));
    }

    $width += 4;

    if ($centered) {
        $r = ($rows - $height) >> 1;
        $c = ($cols - $width) >> 1;
    }

    $window = ncurses_newwin($height, $width, $r, $c);
    if (empty($window)) {
        trigger_error('unable to create window');
        return NULL;
    }

    ncurses_wborder($window, 0,0, 0,0, 0,0, 0,0);
    $n_items = count($items);
    for ($i = 0; $i < $n_items; $i++) {
        if ($i == $current) ncurses_wattron($window, NCURSES_A_REVERSE);
        ncurses_mvwaddstr($window, 1 + $i, 1, $items[$i]);
        if ($i == $current) ncurses_wattroff($window, NCURSES_A_REVERSE);
    }

    ncurses_wrefresh($window);
    ncurses_keypad($window,TRUE);
    ncurses_noecho();
    ncurses_curs_set(0);

    $do_loop = 1;
    $save_result = 0;
    while($do_loop) {
        $key = ncurses_wgetch($window);
        $move = 0;
        switch ($key) {
            case NCURSES_KEY_UP :
                if ($current > 0) $move = -1;
                break;
            case NCURSES_KEY_DOWN :
                if ($current < $n_menu - 1) $move = 1;
                break;
            case XCURSES_KEY_LF:
            case XCURSES_KEY_CR:
                $do_loop = 0;
                $save_result = 1;
                break;
            case XCURSES_KEY_SPACE:
                if ($checked[$current]) {
                    $checked[$current] = 0;
                    $items[$current] = ' [ ] '.substr($items[$current],5);
                } else {
                    $checked[$current] = 1;
                    $items[$current] = ' [*] '.substr($items[$current],5);
                }
                ncurses_wattron($window, NCURSES_A_REVERSE);
                ncurses_mvwaddstr($window, 1 + $current, 1, $items[$current]);
                ncurses_wattroff($window, NCURSES_A_REVERSE);
                ncurses_wrefresh($window);
                break;
            case XCURSES_KEY_ESC :
                ncurses_flushinp();
                $do_loop = 0;
                break;
        }

        if ($move) {
            ncurses_mvwaddstr($window, 1 + $current, 1, $items[$current]);
            $current += $move;
            ncurses_wattron($window, NCURSES_A_REVERSE);
            ncurses_mvwaddstr($window, 1 + $current, 1, $items[$current]);
            ncurses_wattroff($window, NCURSES_A_REVERSE);
            ncurses_wrefresh($window);
        }
    }

    ncurses_delwin($window);
    $result = NULL;
    if ($save_result) {
        for ($i=0; $i<$n_menu; $i++) {
            $result[$keys[$i]] = $checked[$i];
        }
    }

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

$result = menu_check_list(
    array (
        'items' => array (
            'dude'    => array ('The Dude',        1),
            'walter'  => array ('Walter Sobchak',  0),
            'donny'   => array ('Donny',           1),
            'maude'   => array ('Maude Lebowski',  1),
            'jesus'   => array ('Jesus Quintana',  0),
            'smokey'  => array ('Smokey',          0)
         ),
        'rows' => $rows,
        'cols' => $cols,
        'selected' => 3,
        'centered' => 1
    )
);

ncurses_end();

echo 'Result is:'.var_export($result,TRUE)."\n";
