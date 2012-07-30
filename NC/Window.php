<?php
namespace NC;

class Window
{
    /**
     * @var \NC\Window
     */
    protected $parent   = null;

    protected $window   = null;

    protected $childs   = array();

    protected $title    = null;

    protected $changed  = true;

    /**
     * @param \NC\Window $parent
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    /**
     * @return \NC\Window
     */
    public function getParent()
    {
        return $this->parent;
    }



    /**
     * Create window
     *
     * @param   int $rows
     * @param   int $cols
     * @param   int $y
     * @param   int $x
     * @return  \NC\Window
     */
    public function __construct($rows = 0, $cols = 0, $y = 0, $x = 0)
    {
        $this->setWindow( ncurses_newwin( $rows, $cols, $y, $x ) );

        $this->setChanged( true );
    }

    public function add( Window $child )
    {
        $this->childs[] = $child;
        $child->setParent( $this->getWindow() );

        $this->setChanged( true );
    }

    public function getWindow()
    {
        return $this->window;
    }

    public function setWindow( $window )
    {
        $this->window = $window;
    }

    public function getMaxYX(&$y, &$x)
    {
        return ncurses_getmaxyx($this->window, $y, $x);
    }

    public function getMaxX()
    {
        $x = $y = null;
        ncurses_getmaxyx($this->window, $y, $x);
        return $x;
    }

    public function getMaxY()
    {
        $x = $y = null;
        ncurses_getmaxyx($this->window, $y, $x);
        return $y;
    }

    /**
     * Set borders window
     *
     * @param   int     $left       |
     * @param   int     $right      |
     * @param   int     $top        -
     * @param   int     $bottom     _
     * @param   int     $tl_corner  +
     * @param   int     $tr_corner  +
     * @param   int     $bl_corner  +
     * @param   int     $br_corner  +
     * @return  int
     */
    public function setBorder($left = 0, $right = 0, $top = 0, $bottom = 0, $tl_corner = 0, $tr_corner = 0, $bl_corner = 0, $br_corner = 0)
    {
        return ncurses_wborder(
                                $this->window,
                                $left, $right, $top, $bottom,
                                $tl_corner, $tr_corner, $bl_corner, $br_corner
                            );

        $this->setChanged( true );
    }

    /**
     * Set color
     *
     * @param   int     $pair
     * @param   int     $foreground
     * @param   int     $background
     * @return void
     */
    public function setColor($pair, $foreground = 0, $background = 0)
    {
        if ( ncurses_has_colors() )
        {
            ncurses_start_color();
            ncurses_init_pair($pair, $foreground, $background);
            ncurses_wcolor_set($this->getWindow(), $pair);
        }

        $this->setChanged( true );

        return $pair;
    }

    /**
     * Refresh (redraw) window
     */
    public function refresh()
    {
        if ( ! $this->getChanged() )
        {
            return false;
        }
        else
        {
            $this->setChanged( false );
        }

        ncurses_wrefresh($this->getWindow());

        foreach ( $this->childs as $child )
        {
            $child->refresh();
        }
    }

    public function setTitle($title)
    {
        $this->title = $title;
        ncurses_wattron($this->getWindow(), NCURSES_A_BOLD);
        ncurses_mvwaddstr( $this->getWindow(), 0, 2, ' ' . $title . ' ' );
        ncurses_wattroff($this->getWindow(), NCURSES_A_BOLD);

        $this->setChanged( true );
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setChanged($changed)
    {
        $this->changed = $changed;
    }

    public function getChanged()
    {
        return $this->changed;
    }
}
