<?php
/**
 * nCurses Keys
 *
 * Show nCurses key code.
 *
 * @author Kenny Parnell <kparnell@redventures.com>
 * @date Tue 05 Aug 2008 09:08:07 PM EDT
 */

define('ESCAPE_KEY', 27);

ncurses_init();
$fullscreen = ncurses_newwin(0,0,0,0);
ncurses_border(0,0, 0,0, 0,0, 0,0);
ncurses_getmaxyx($fullscreen, $y, $x);
$small = ncurses_newwin(5, 7, ($y-5)/2, ($x-7)/2);
ncurses_wborder($small, 0,0, 0,0, 0,0, 0,0);
ncurses_refresh();
ncurses_mvwaddstr($small, 5, 2, "12");
ncurses_wrefresh($small);

do {
    $k = ncurses_wgetch($small);
    if($k == ESCAPE_KEY) {
        ncurses_end();
        exit();
    } else {
        echo $k;
    }
} while(1);

/* vim:set ft=php ts=4 sw=4 et */
