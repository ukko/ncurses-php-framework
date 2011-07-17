#!/usr/local/bin/php -q

<?php 

define('XCURSES_KEY_LF',    13);
define('XCURSES_KEY_CR',    10);
define('XCURSES_KEY_ESC',   27);

if (!posix_isatty(STDOUT)) {
    trigger_error('wrong terminal');
    exit;
}

ncurses_init();

$quotes = <<<EOT
----------------------------------------------
Usage: 
    ESC - exit
    any other key - browse windows
----------------------------------------------

We will encourage you to develop
the three great virtues of a programmer:
laziness, impatience, and hubris (Larry Wall)

A good programmer is someone who looks
both ways before crossing a one-way
street (Doug Linder)

Managing senior programmers is like
herding cats (Dave Platt)
EOT;

$lines = explode("\n",$quotes);
$n_lines = count($lines);
$window_width = 0;
for ($i=0; $i<$n_lines; $i++) {
    $window_width = max($window_width,strlen($lines[$i]));
}
$window_width += 4;

$x_coords = array(10,14,18);
$y_coords = array(10,12,8);
for ($i=0; $i<3; $i++) {
    $windows[$i] = ncurses_newwin(4+$n_lines, $window_width, $y_coords[$i], $x_coords[$i]);
    ncurses_wborder($windows[$i], 0,0, 0,0, 0,0, 0,0);
    ncurses_wattron($windows[$i], NCURSES_A_REVERSE);
    ncurses_mvwaddstr($windows[$i], 0, 2, ' window #'.$i.' ');
    ncurses_wattroff($windows[$i], NCURSES_A_REVERSE);
    for ($j=0; $j<$n_lines; $j++) {
        ncurses_mvwaddstr($windows[$i], 2+$j, 2, $lines[$j]);
    }
    ncurses_wrefresh($windows[$i]);
    $panels[$i] = ncurses_new_panel($windows[$i]);
}

ncurses_update_panels();

ncurses_curs_set(0);
ncurses_noecho();
$i = 0;
$k = NULL;
while(XCURSES_KEY_ESC != $k) {
    $k = ncurses_getch();
    ncurses_top_panel($panels[$i%3]);
    ncurses_update_panels();
    ncurses_doupdate();
    $i++;
}

ncurses_end();

?>