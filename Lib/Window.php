<?php
class Window 
{
    private $window = null;
    
    const BORDER_STYLE_SOLID    = 1; // ┐
    const BORDER_STYLE_DOUBLE   = 2; // ╗
    const BORDER_STYLE_BLOCK    = 3; // ■

    /**
     * Create window
     *
     * @param int $rows
     * @param int $cols
     * @param int $y
     * @param int $x
     * @return void
     */
    public function __construct($rows = 0, $cols = 0, $y = 0, $x = 0) 
    {
        $this->window = ncurses_newwin($rows, $cols, $y, $x);
    }
    
    public function getWindow()
    {
        return $this->window;
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
    
    public function border($left = 0, $right = 0, $top = 0, $bottom = 0, $tl_corner = 0, 
                            $tr_corner = 0, $bl_corner = 0, $br_corner = 0) 
    {
        return ncurses_wborder($this->window, $left, $right, $top, $bottom, 
                $tl_corner, $tr_corner, $bl_corner, $br_corner);
    }
    
    public function borderStyle($style)
    {
        if ($style == self::BORDER_STYLE_SOLID) 
        {
            $this->border();
        }
        elseif($style == self::BORDER_STYLE_DOUBLE)
        {
            $this->border(226, 186, 205, 205, 201, 187, 200, 188);
//            $this->border(ord('║'), ord('║'), ord('═'), ord('═'), ord('╔'), ord('╗'), ord('╚'), ord('╝'));
        }
    }

    /**
     * Refresh (redraw) window
     */
    public function refresh()
    {
        ncurses_wrefresh($this->window);
    }
}
