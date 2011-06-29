<?php
class Main
{
    /**
     * Main window
     * @var Window
     */
    protected   $wMain = null;
    
    /**
     * Uow window
     * @var Window
     */
    protected $wUows   = null;
    
    /**
     * Gear window
     * @var Window
     */
    protected $wGears  = null;
    
    /**
     * Pids window
     * @var Window
     */
    protected $wPids    = null;

    /**
     * Cursor position
     * @var Cursor
     */
    private     $cursor = null;
    
    
    
    private $uows = array(
                            'uow1',
                            'uow2',
                            'uow3',
                            'uow4',
                            'uow5',
                            'uow6',
                        );

    private $gears = array(
                            'UserKeeper',
                            'ManyMaker',
                            'RedisSaver',
                            'UnixTools',
                            'Album',
                            'AlbumPhoto'
                        );
    
    /**
     * Construct
     */
    public function __construct() 
    {
        $this->wMain = new Window();
        $this->cursor = new Cursor(0, 0, false);
        ncurses_keypad($this->wMain->getWindow(), true);
        ncurses_noecho();
        
        $this->redraw();
        $this->processing();
    }
        
    /**
     * Redraw screen
     */
    public function redraw()
    {
        $this->wMain->getMaxYX($y, $x);
        $this->wMain->border();
        $this->wMain->refresh();

        $this->wUows = new Window($y - 2, $x / 2, 1, 1);
        $this->wUows->border();
        
        $current = ($this->cursor->getX() == 0) ?  $this->cursor->getY() : null;
        $this->wUows->listbox( $this->uows, $current );
        $this->wUows->refresh();

        $this->wGears = new Window( $y - 2 , ($x / 2) - 2, 1, ($x / 2) + 2);
        $this->wGears->border();
        
        $current = ($this->cursor->getX() == 1) ?  $this->cursor->getY() : null;
        $this->wGears->listbox($this->gears, $current);
        $this->wGears->refresh();        
    }
    
    /**
     * Processing press keys
     */
    public function processing()
    {
        do {
            $k = ncurses_wgetch($this->wMain->getWindow());
            if($k == XCURSES_KEY_ESC) 
            {
                ncurses_end();
                exit();
            } 
            elseif ($k == NCURSES_KEY_UP)
            {
                $this->moveCursor(0, -1);
            }
            elseif ($k == NCURSES_KEY_DOWN)
            {
                $this->moveCursor(0, 1);
            }
            elseif ($k == NCURSES_KEY_LEFT)
            {
                $this->moveCursor(-1, 0);
            }
            elseif ($k == NCURSES_KEY_RIGHT)
            {
                $this->moveCursor(1, 0);
            }
            else 
            {
                echo $k;
            }
            $this->redraw();
        } while(1);
    }
    
    /**
     * Move cursor, if avaliable
     * @param int $offsetX
     * @param int $offsetY 
     */
    private function moveCursor($offsetX, $offsetY)
    {
        $minX = 0;
        $maxX = 2;
        
        $minY = null;
        $maxY = 10;
        
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
}