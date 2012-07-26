<?php

class SubWindow extends Window
{
    /**
     * @var Window
     */
    protected $parent;

    public function __construct(Window $parent, $rows = 0, $cols = 0, $y = 0, $x = 0)
    {
        $this->parent = $parent;
        parent::__construct($rows, $cols, $y, $x);
    }
}
