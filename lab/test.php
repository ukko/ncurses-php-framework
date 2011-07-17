<?php
/**
 * test
 *
 * test
 *
 * @author Kenny Parnell <kparnell@redventures.com>
 * @date Tue 05 Aug 2008 07:31:43 PM EDT
 */

$showHelp = true;                       // hide help by default
ncurses_init();                         // initialize ncurses
$char = ncurses_show_screen($showHelp); // show program and capture character that causes exit
ncurses_end();                          // clean up ncurses

function ncurses_show_screen($showHelp) {

    // basic settings
    ncurses_noecho();
    ncurses_curs_set(0);

    // set app title
    $title = "Red Ventures Selenium Runner";

    // commands to be listed in help
    $commands = array(
        'q'      => 'quit',
        'r'      => 'run selected tests',
        'space'  => 'toggle test selection',
        'enter'  => 'run highlighted tests',
        'up'     => 'move up',
        'down'   => 'move down',
        'pgUp'   => 'scroll description up',
        'pgDown' => 'scroll description down',
        '?/h'    => 'show this help panel'
    );

    // create a fullscreen window
    $fullscreen = ncurses_newwin(0,0,0,0);
    ncurses_getmaxyx($fullscreen, $max_y, $max_x);

    // enter the main event loop
    $do_loop = 1;
    while($do_loop) {
        // calculate width of help window columns
        $c = $t = 0;
        foreach($commands as $cmd=>$txt) {
            $c = ( strlen($cmd) > $c ) ? strlen($cmd) : $c;
            $t = ( strlen($txt) > $t ) ? strlen($txt) : $t;
        }
        $h = count($commands);  // calculate the help windows height
        
        if( $showHelp ) {
            if( !empty($helpWin) ) {
                ncurses_delwin($helpWin);
            }
            $helpWin = ncurses_newwin($h+4, $c+$t+5, ($max_y-$h-4)/2, ($max_x-$c-$t-5)/2);
            ncurses_wborder($helpWin, 0,0, 0,0, 0,0, 0,0);
            $i = 0;
            foreach($commands as $cmd=>$txt) {
                ncurses_mvwaddstr($helpWin, 2+$i, 2, $cmd);
                ncurses_mvwaddstr($helpWin, 2+$i, 2+$c+1, $txt);
            }

            if( !empty($helpWin) )
                ncurses_wrefresh($helpWin);
            else
                ncurses_refresh();
        }
        if(empty($helpWin))
            $key = ncurses_getch();
        else
            $key = ncurses_wgetch($helpWin);
        switch ($key) {
            case 27 :
                ncurses_flushinp();
                $do_loop = 0;
                break;
            default :
                $showHelp = ( $showHelp === true ) ? false : true;
                ncurses_show_screen($showHelp);
        }
    }
}

/* vim:set ft=php ts=4 sw=4 et */
