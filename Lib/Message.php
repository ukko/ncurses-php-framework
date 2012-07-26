<?php

class Message extends SubWindow
{
    /**
     * @var string
     */
    protected $text;

    /**
     * @var string
     */
    protected $title;

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function setText($text)
    {
        $this->text = $text;
    }

    public function __construct(Window $parent, $rows = 0, $cols = 0, $y = 0, $x = 0)
    {
        $width  = 80;
        $height = 10;
        $x      = ( $parent->getMaxX() - $width ) / 2;
        $y      = ( $parent->getMaxY() - $height ) / 2;

        parent::__construct($parent, $height, $width, $y, $x);
    }

    /**
     * Construct
     */
    public function start()
    {

        ncurses_keypad( $this->getWindow(), true );
        ncurses_noecho();

        $this->setBorder();
        ncurses_wattron($this->getWindow(), NCURSES_A_BOLD);
        ncurses_mvwaddstr($this->getWindow(), 0, 1, $this->title);
        ncurses_wattroff($this->getWindow(), NCURSES_A_BOLD);

        ncurses_mvwaddstr($this->getWindow(), 2, 2, $this->text);
        $this->processing();
    }

    /**
     * Processing press keys
     */
    public function processing()
    {
        $do = true;
        do
        {
            $k = ncurses_wgetch($this->getWindow());
            if( $k == Ncurses::XCURSES_KEY_ESC )
            {
                $do = false;
            }
            elseif( $k == Ncurses::XCURSES_KEY_LF)
            {
                $do = false;
            }
            $this->refresh();
        }
        while($do);
    }

    /**
     * Message box
     *
     * @param   Window  $parent
     * @param   string  $text
     * @param   string  $title
     * @return void
     */
    public static function box(Window $parent, $text, $title)
    {
        $msg = new self($parent);
        $msg->setText($text);
        $msg->setTitle($title);
        $msg->start();
        unset($msg);
    }

    public function __destruct()
    {
        ncurses_delwin( $this->getWindow() );
        $this->parent->refresh();
    }
}
