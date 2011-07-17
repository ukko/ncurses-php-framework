#!/usr/local/bin/php -q
<?php

define('XCURSES_KEY_LF',    13);
define('XCURSES_KEY_CR',    10);
define('XCURSES_KEY_ESC',   27);

###############################################################################################
function dialog($params) {
###############################################################################################
    if (empty($params) || !is_array($params) ) {
        trigger_error('params must be non-empty array');
        return NULL;
    }

    $message = isset($params['message']) ? $params['message'] : ''; 

    $buttons = (!empty($params['buttons']) && is_array($params['buttons'])) ? 
        $params['buttons'] : array('OK');  
    $n_buttons = count($buttons);
    for ($i=0;$i<$n_buttons;$i++) {
        $buttons[$i] = ' '.$buttons[$i].' ';
    }
 
    $parent_rows = (isset($params['rows']) && ($params['rows']>0)) ? 
        (int)$params['rows'] : 25;
    $parent_cols = (isset($params['cols']) && ($params['cols']>0)) ? 
        (int)$params['cols'] : 80;

    if (empty($message) || empty($buttons) || $parent_rows<=0 || $parent_cols<=0) {
        trigger_error('wrong params');
        return NULL;
    }

    $message_lines = split("\n",$message);
    $message_width = 0;
    $n_message_lines = count($message_lines);
    for ($i=0; $i<$n_message_lines; $i++) {
        $message_width = max(strlen($message_lines[$i]),$message_width);
    }

    $buttons_delim = '  ';
    $buttons_delim_len = strlen($buttons_delim);
    $buttons_len = strlen(implode($buttons_delim, $buttons));

    $width  = 4 + max($buttons_len + 2*$buttons_delim_len,$message_width);
    $height = 4 + $n_message_lines;
    $dlg_y = ($parent_rows > $height) ? 
	(($parent_rows - $height) >> 1) : 1;
    $dlg_x = ($parent_cols > $width) ? 
        (($parent_cols - $width) >> 1) : 1;

    $window = ncurses_newwin($height, $width, $dlg_y, $dlg_x);
    if (empty($window)) {
        trigger_error('unable to create window');
        return NULL;
    }

    ncurses_wborder($window, 0,0, 0,0, 0,0, 0,0);
    $i_x = 0;
    $i_y = 0;
    for ($i = 0; $i<$n_message_lines; $i++) {
        $i_y = 1 + $i;
        $i_x = 1 + (($width - 2 - strlen($message_lines[$i])) >> 1);
        ncurses_mvwaddstr($window, $i_y, $i_x, rtrim($message_lines[$i]));
    }

    $buttons_data = array();
    $buttons_shift_x = 1 + (($width - 1 - $buttons_len) >> 1);
    $buttons_shift_y = 2 + $n_message_lines;
    $i_title = '';
    $i_x = $buttons_shift_x;
    for ($i = 0; $i < $n_buttons; $i++) {
        $i_title = $buttons[$i];
        $buttons_data[] = array(
            'x' => $i_x, 
            's' => $i_title
        );
        if (0 == $i) ncurses_wattron($window, NCURSES_A_REVERSE);
        ncurses_mvwaddstr($window, $buttons_shift_y, $i_x, $i_title);
        if (0 == $i) ncurses_wattroff($window, NCURSES_A_REVERSE);
        $i_x += strlen($i_title) + $buttons_delim_len;
    }

    ncurses_wrefresh($window);
    ncurses_keypad($window,TRUE);  
    ncurses_curs_set(0);
    ncurses_noecho();

    $result = -1;
    $do_loop = 1;
    $move = 0;
    $current = 0;

    while ($do_loop) {
        $key = ncurses_wgetch($window);
        $move = 0;
        switch ($key) {
            case NCURSES_KEY_LEFT:
                if ($current > 0) $move = -1;
                break;
            case NCURSES_KEY_RIGHT:
                if ($current < $n_buttons-1) $move = 1;
                break;
            case XCURSES_KEY_LF:
            case XCURSES_KEY_CR:
                $result = $current;
                $do_loop = 0;
                break;
            case XCURSES_KEY_ESC:
		$do_loop = 0;
                break;
        }
        
        if (0 == $do_loop) {
            ncurses_flushinp();
        } elseif ($move) {
            ncurses_mvwaddstr($window, $buttons_shift_y, 
                $buttons_data[$current]['x'], $buttons_data[$current]['s']);
            $current += $move;
            ncurses_wattron($window, NCURSES_A_REVERSE);
            ncurses_mvwaddstr($window, $buttons_shift_y, 
                $buttons_data[$current]['x'], $buttons_data[$current]['s']);
            ncurses_wattroff($window, NCURSES_A_REVERSE);
            ncurses_wrefresh($window);
        }
    }

    ncurses_delwin($window);

    return $result;
}

###################################################################################################
function my_error_handler($errno, $errstr, $errfile, $errline) {
###################################################################################################
    global $errors;
    $errors[] = 'ERROR #'.$errno.': '.$errstr.' ('.$errfile.', line '.$errline.')';
    return TRUE;
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
$errors = array();
set_error_handler('my_error_handler');

$main_window = ncurses_newwin(0, 0, 0, 0);
ncurses_getmaxyx($main_window, $rows, $cols);
ncurses_keypad($main_window,TRUE);

$message = <<< EOM
Mr. Treehorn draws a lot of water in this town. You don't draw shit, Lebowski. 
Now we got a nice, quiet little beach community here, and I aim to keep it 
nice and quiet. So let me make something plain. I don't like 
you sucking around, bothering our citizens, Lebowski. 
I don't like your jerk-off name. I don't like your jerk-off face. 
I don't like your jerk-off behavior, and I don't like you, jerk-off. 
Do I make myself clear?
EOM;
$result = dialog(
    array (
        'message' => $message,
        'rows' => -1,
        'cols' => -1, 
	'buttons' => array ('Yes','No','I\'m sorry, I wasn\'t listening')
    )
);

ncurses_end();

if (!empty($errors)) {
    print join("\n",$errors)."\n";
}

?>