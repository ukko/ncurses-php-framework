<?php
class Main extends Ncurses
{
    /**
     * Main window
     * @var Window
     */
    protected   $wMain = null;
    
    /**
     * Left panel window
     * @var Listbox
     */
    protected $wLPanel   = null;
    
    /**
     * Right panel window
     * @var Listbox
     */
    protected $wRPanel  = null;

    /**
     * Cursor position
     * @var Cursor
     */
    private     $cursor = null;
    
    /**
     * Construct
     */
    public function start()
    {
        $this->wMain = new Window();
        $this->cursor = new Cursor(0, 0, false);
        ncurses_keypad($this->wMain->getWindow(), true);
        ncurses_noecho();

        $this->wMain->getMaxYX($y, $x);
        $this->wMain->border();

        $this->wLPanel = new Listbox($y - 2, $x / 2, 1, 1);
        $this->wLPanel->border();
        $this->wRPanel = new Listbox( $y - 2 , ($x / 2) - 2, 1, ($x / 2) + 2);
        $this->wRPanel->border();

        $this->redraw();
        $this->processing();

    }
        
    /**
     * Redraw screen
     */
    public function redraw()
    {
        $this->wMain->refresh();

        $current = ($this->cursor->getX() == 0) ?  $this->cursor->getY() : null;

        $this->wLPanel->setItems(FS::getList(Conf::get('lpath')));
        $this->wLPanel->drawList( $current );
        $this->wLPanel->refresh();

        $current = ($this->cursor->getX() == 1) ?  $this->cursor->getY() : null;
        $this->wRPanel->setItems(FS::getList(Conf::get('rpath')));
        $this->wRPanel->drawList( $current );
        $this->wRPanel->refresh();


    }
    
    /**
     * Processing press keys
     */
    public function processing()
    {
        do
        {
            $k = ncurses_wgetch($this->wMain->getWindow());
            if( $k == self::XCURSES_KEY_ESC )
            {
                ncurses_end();
                exit();
            } 
            elseif( $k == NCURSES_KEY_UP )
            {
                $this->moveCursor(0, -1);
            }
            elseif( $k == NCURSES_KEY_DOWN )
            {
                $this->moveCursor(0, 1);
            }
            elseif( $k == NCURSES_KEY_LEFT )
            {
                $this->moveCursor(-1, 0);
            }
            elseif( $k == NCURSES_KEY_RIGHT )
            {
                $this->moveCursor(1, 0);
            }
            elseif( $k == self::XCURSES_KEY_TAB )
            {
                $this->moveCursor($this->cursor->getX() ? -1 : 1, 0);
            }
            elseif( $k == self::XCURSES_KEY_LF)
            {
                $this->cd();
            }
            else 
            {
                echo $k;
            }
            $this->redraw();
        }
        while(1);
    }

    private function cd ()
    {
        $items = $this->cursor->getWindow()->getItems();
    }

    /**
     * Move cursor, if available
     * @param int $offsetX
     * @param int $offsetY 
     */
    private function moveCursor($offsetX, $offsetY)
    {
        $minX = 0;
        $maxX = 1;

        if ( ( $offsetX + $this->cursor->getX() ) > $maxX )
        {
            $x = $maxX;
        } 
        elseif( ( $offsetX + $this->cursor->getX() ) < $minX )
        {
            $x = $minX;
        } 
        else 
        {
            $x = (int) ( $offsetX + $this->cursor->getX() );
        }

        $minY = null;

        if ( $x == 0 )
        {
            $this->cursor->setWindow($this->wLPanel);
        }
        else
        {
            $this->cursor->setWindow($this->wLPanel);
        }

        $maxY = count( $this->cursor->getWindow()->getItems() ) - 1;

        if ( ( $offsetY + $this->cursor->getY() ) > $maxY )
        {
            $y = $maxY;
        }
        elseif ( ( $offsetY + $this->cursor->getY() ) < 0 )
        {
            $y = $minY;
        }
        else
        {
            $y = (int) ( ($offsetY + $this->cursor->getY() ) );
        }
        
        $this->cursor->setPosition($x, $y);
    }

    public function __destruct()
    {
        Conf::save();
        parent::__destruct();
    }

    public static function _debug()
    {
        $msg = '';

        foreach(func_get_args() as $arg)
        {
            $msg .= var_export($arg, true) . PHP_EOL;
        }
        
        file_put_contents(__DIR__ . '/../error.log', $msg,  FILE_APPEND);
    }


}
