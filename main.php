<?php
require_once __DIR__ . '/NC/init.php';

$main = new \NC\StdWindow();
$main->setBorder();
$main->setTitle('MAIN');

$left = new \NC\Window(10, 20, 1, 1 );
$left->setBorder();
$left->setTitle('left');

ncurses_start_color();
ncurses_init_pair(1, NCURSES_COLOR_RED, NCURSES_COLOR_BLACK);
ncurses_init_pair(2, NCURSES_COLOR_GREEN, NCURSES_COLOR_BLACK);
ncurses_init_pair(3, NCURSES_COLOR_YELLOW, NCURSES_COLOR_BLACK);
ncurses_init_pair(4, NCURSES_COLOR_BLUE, NCURSES_COLOR_BLACK);
ncurses_init_pair(5, NCURSES_COLOR_CYAN, NCURSES_COLOR_BLACK);

ncurses_wcolor_set( $left->getWindow(), 2 );

$right = new \NC\Window(10, 40, 1, 21 );
$right->setBorder();
$right->setTitle('right');


$main->add( $left );
$main->add( $right );
$main->run();


