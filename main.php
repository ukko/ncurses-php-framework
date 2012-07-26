<?php
require_once __DIR__ . '/NC/init.php';

$main = new \NC\MainWindow();
$main->setBorder();



$left = new \NC\Window( $main->getMaxY(), $main->getMaxX() / 2, 0, 0 );
$left->setBorder();

$main->add( $left );

$main->run();
