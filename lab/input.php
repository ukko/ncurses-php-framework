#!/usr/local/bin/php -q

<?php 

define('XCURSES_KEY_LF',                 13);
define('XCURSES_KEY_CR',                 10);
define('XCURSES_KEY_ESC',                27);
define('XCURSES_KEY_PRINTABLE_MIN',      32);
define('XCURSES_KEY_PRINTABLE_MAX',     127);

###############################################################################################
function dlg_input($params = array()) {
###############################################################################################
    $title = isset($params['title']) ? $params['title'] : NULL;
    $max_length = isset($params['max_len']) ? (int)$params['max_len'] : 10;        
    $dlg_rows = isset($params['dlg_cols']) ? (int)$params['dlg_cols'] : 3;
    $dlg_cols = isset($params['dlg_cols']) ? (int)$params['dlg_cols'] : 40;
    $parent_cols = isset($params['cols']) ? (int)$params['cols'] : NULL;
    $parent_rows = isset($params['rows']) ? (int)$params['rows'] : NULL;

    $dlg_x = (int)(($parent_cols - $dlg_cols)/2);
    if($dlg_x<0) $dlg_x = 0;
    $dlg_y = (int)(($parent_rows - $dlg_rows)/2);
    if($dlg_y<0) $dlg_y = 0;
   
    if ($max_length<=0 || $dlg_rows<=0 || $dlg_cols<=0) {
        trigger_error('wrong params');
        return NULL;
    }
 
    $dlg_window = ncurses_newwin($dlg_rows, $dlg_cols, $dlg_y, $dlg_x);
    
    if (empty($dlg_window)) { 
        return NULL;
    }
    
    ncurses_wborder($dlg_window, 0, 0, 0, 0, 0, 0, 0, 0);
    if ($title) {
        ncurses_wattron($dlg_window, NCURSES_A_REVERSE);
        ncurses_mvwaddstr($dlg_window, 0, 2,' '.$title.' ');
        ncurses_wattroff($dlg_window, NCURSES_A_REVERSE);
    }
    ncurses_curs_set(1);
    ncurses_wmove($dlg_window, 2, 2);
    ncurses_wrefresh($dlg_window);
    
    $do_getch = 1;
    $input_val = '';
    $input_char = '';
    $input_len = 0;
    $cursor_x = 2;
    $cursor_y = 1;
    ncurses_wmove($dlg_window, $cursor_y, $cursor_x);
    ncurses_noecho();
    ncurses_keypad($dlg_window,TRUE);
    
    while($do_getch){
        $key_code = ncurses_wgetch($dlg_window);
        if (($key_code == XCURSES_KEY_CR) || ($key_code == XCURSES_KEY_LF)) {
    	    $do_getch = 0;
        } elseif ($key_code == NCURSES_KEY_BACKSPACE) {
            if($input_len>0) {
	        $input_len--;
	        $input_val = substr($input_val,0,$input_len);
	        $cursor_x--;
	        ncurses_mvwaddstr($dlg_window, $cursor_y, $cursor_x,' ');
	        ncurses_wmove($dlg_window, $cursor_y, $cursor_x);
            }
        } elseif ($key_code < XCURSES_KEY_PRINTABLE_MIN || $key_code > XCURSES_KEY_PRINTABLE_MAX) {
    	    continue;
        } elseif($input_len<$max_length) {
    	    $input_val .= $input_char = chr($key_code);
    	    $input_len++;
    	    $cursor_x++;
    	    ncurses_waddstr($dlg_window, $input_char);
        }
    }
    
    ncurses_delwin($dlg_window);
    
    return $input_val;
}

###################################################################################################
# main
###################################################################################################

if (!posix_isatty(STDOUT)) {
    trigger_error('wrong terminal');
    exit;
}

error_reporting(E_ALL);
ncurses_init();
$main_window = ncurses_newwin(0, 0, 0, 0);
ncurses_getmaxyx($main_window, $rows, $cols);

$input = dlg_input(
    array(
        'title' => 'sample input',
        'rows' => $rows, 
        'cols' => $cols
    )
);

ncurses_end();
echo 'Input was: '.$input."\n";

?>