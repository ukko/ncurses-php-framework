<?php
namespace NC;

class StdWindow extends Window
{
    protected $window       = null;

    /**
     * Create window
     *
     * @param   int $rows
     * @param   int $cols
     * @param   int $y
     * @param   int $x
     * @return  Window
     */
    public function __construct($rows = 0, $cols = 0, $y = 0, $x = 0)
    {
        ncurses_init();

        parent::__construct( $rows = 0, $cols = 0, $y = 0, $x = 0 );
    }

    public function run()
    {
        while (1)
        {
            $this->refresh();
            usleep(100);
        }
    }

    public function __destruct()
    {
        ncurses_end();
    }
}
