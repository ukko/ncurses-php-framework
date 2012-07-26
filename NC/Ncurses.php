<?php
namespace NC;

class Ncurses
{
    const XCURSES_KEY_LF    = 13;
    const XCURSES_KEY_CR    = 10;
    const XCURSES_KEY_ESC   = 27;
    const XCURSES_KEY_TAB   = 9;

    public function __construct()
    {
        ncurses_init();
    }

    public function __destruct()
    {
        ncurses_end();
    }
}
