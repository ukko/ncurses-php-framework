<?php
/**
 * 
 */
class Cursor 
{
    /**
     * Store cursor position 
     * 
     * @var array($x, $y)
     */
    protected $position = array();
    
    /**
     * Visible flag
     * 
     * @var bool
     */
    protected $visible  = null;

    /**
     * Store window
     *
     * @var Window
     */
    protected $window = null;
    
    /**
     * Construct
     * 
     * @param int $x
     * @param int $y
     * @param bool $visible 
     */
    public function __construct($x = null, $y = null, $visible = null) 
    {
        $this->setPosition($x, $y);
        $this->setVisible($visible);
    }
    
    /**
     * Get cursor position
     * 
     * @return array ($x, $y)
     */
    public function getPosition()
    {
        return $this->position;
    }
    
    /**
     * Return X cursor position
     * 
     * @return int
     */
    public function getX()
    {
        return $this->position[0];
    }
    
    
    /**
     * Return Y cursor position
     * 
     * @return int
     */
    public function getY()
    {
        return $this->position[1];
    }


    /**
     * Set cursor position
     * 
     * @param int $x
     * @param int $y 
     */
    public function setPosition($x, $y)
    {
        $this->position = array($x, $y);
    }
    
    /**
     * Return visible flag
     * 
     * @return bool
     */
    public function getVisible()
    {
        return $this->visible;
    }
   
    
    /**
     * Set visible flag
     * 
     * @param bool $visible 
     */
    public function setVisible($visible)
    {
        ncurses_curs_set((int)$visible);
        $this->visible = $visible;
    }

    /**
     * Set window
     *
     * @param Window $window
     * @return void
     */
    public function setWindow( $window )
    {
       $this->window = $window;
    }

    /**
     * @return Window
     */
    public function getWindow()
    {
        return $this->window;
    }
}
