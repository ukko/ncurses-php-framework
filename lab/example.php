<?php



    /*

    ** ncurses1.php written 2003 by ALeX Kazik

    ** feel free to use this code

    ** Version: 1.0 (2003-06-19)

    */



    /* start me with "allkeys" as arument to use all keys to exit */



    // read this file as text

    $text = file($_SERVER['PATH_TRANSLATED']);

    

    // strip off any whitespaces (space, newline, ...) at the end of line

    // and replace tabs by 4 spaces

    foreach($text AS $k=>$v)

        if(preg_match('/^(.*?)\s*$/', strtr($v, array("\t" => '    ')), $r))

            $text[$k] = $r[1];

    

    // question

    $question = array(

        ' This is an more complex example for ncurses',

        ' Use the arrow keys to scroll / ESC or Q to exit '

    );

    

    // keys to exit

    if(($argc >= 2) && ($argv[1] == 'allkeys'))

        $keys = TRUE;

    else

        $keys = array(27, ord('q'), ord('Q')); // 27 == ESCAPE

    

    // init ncurses

    ncurses_init();

    

    // display text, and ask...

    $char = ncurses_show_text('ncurses example', $text, $question, $keys);

    

    // end ncurses

    ncurses_end();



    // the end...

    echo 'You\'ve pressed character number '.$char.' = 0x'.sprintf('%02x', $char);

    if(($char < 256) && ($char != 127) && (($char & 0x7f) >= 0x20))

        echo ' = "'.chr($char).'"';

    echo "\n";



    /*

    ** ncurses_show_text($title, $text, $question, $keys=TRUE)

    **

    ** $title = string to display in above the upper window

    **

    ** $text = array of strings to display in the upper window

    **         (scrollable)

    **         text text MUST NOT contain any tab, newline, linefeed

    **         or any other control character

    **

    ** $question = array of strings to display in the lower window

    **

    ** $keys = array of keys to exit (may override scrolling)

    **         or true to accept all keys (except scrolling)

    **

    ** return = the pressed key

    **          characters above 255 are special keys (F1, DEL, ...)

    */



    function ncurses_show_text($title, $text, $question, $keys=TRUE){



        // prepare text

        $textH = count($text);

        $textW = 1;

        $textLEN = array();

        for($i=0; $i<$textH; $i++){

            $textLEN[$i] = strlen($text[$i]);

            if($textLEN[$i] > $textW)

                $textW = $textLEN[$i];

        }



        // create text pad (invisible window)

        $textWIN = ncurses_newpad($textH, $textW);

        

        // fill it with text

        for($i=0; $i<$textH; $i++)

            ncurses_mvwaddstr($textWIN, $i, 0, $text[$i]);



        // prepare question

        $questionH = count($question);

        $questionLastW = strlen($question[$questionH-1]);

        

        // initialize...

        $posX = $posY = 0;

        $screenH = $screenW = 0;



        // loop around...

        while(1){



            // get actual screen size

            $oldH = $screenH;

            $oldW = $screenW;

            ncurses_getmaxyx(STDSCR, $screenH, $screenW);



            // something changed?

            if(($screenH != $oldH) || ($screenW != $oldW)){

                if($oldH > 0){

                    ncurses_delwin($upperWIN);

                    ncurses_delwin($lowerWIN);

                }



                ncurses_erase();

                ncurses_refresh(STDSCR);

    

                $upperWIN = ncurses_newwin($screenH-(2+$questionH), $screenW, 0, 0);

                $lowerWIN = ncurses_newwin(2+$questionH, $screenW, $screenH-(2+$questionH), 0);



                $upperH = $screenH-(4+$questionH);

                $upperW = $screenW-2;



                $copyH = ($upperH > $textH) ? $textH : $upperH;

                $copyW = ($upperW > $textW) ? $textW : $upperW;



                // border lower window

                ncurses_wborder($lowerWIN, 0, 0, 0, 0, 0, 0, 0, 0);

            

                // print text in lower window

                for($i=0; $i < $questionH; $i++)

                    ncurses_mvwaddstr($lowerWIN, $i+1, 1, $question[$i]);

            }

            

            // check and fix positions

            if($posY < 0 || $upperH >= $textH)

                $posY = 0;

            else if(($upperH + $posY) > $textH)

                $posY = $textH - $upperH;



            if($posX < 0 || $upperW >= $textW)

                $posX = 0;

            else if(($upperW + $posX) > $textW)

                $posX = $textW - $upperW;

    

            // border upper window

            ncurses_wborder($upperWIN, 0, 0, 0, 0, 0, 0, 0, 0);



            // draw title and info line

            ncurses_wattron($upperWIN, NCURSES_A_REVERSE);

            ncurses_mvwaddstr($upperWIN, 0, 2,' '.$title.' ');

            if($upperH < $textH)

                ncurses_mvwaddstr($upperWIN, $upperH+1, 2, ' line '.($posY+1).'-'.($posY+$copyH).'/'.$textH.' ');

            ncurses_wattroff($upperWIN, NCURSES_A_REVERSE);



            // draw < and > at left/right side when horizontal scrolling is nesseccary

            if($upperW < $textW){

                for($i=0; $i<$copyH; $i++){

                    if($textLEN[$i+$posY] > $copyW+$posX)

                        ncurses_mvwaddstr($upperWIN, $i+1, $screenW-1, '>');

                    if($posX > 0 && $textLEN[$i+$posY] > 0)

                        ncurses_mvwaddstr($upperWIN, $i+1, 0, '<');

                }

            }



            // draw upper window

            ncurses_wrefresh($upperWIN);



            // copy a part of the text (pad) to the screen

            ncurses_prefresh($textWIN, $posY, $posX, 1, 1, $upperH, $upperW);



            // move cursor to end of last line of question

            ncurses_wmove($lowerWIN, $questionH, $questionLastW+1);



            // draw lower window

            ncurses_wrefresh($lowerWIN);



            // get a character and do...

            $char = ncurses_getch();

            if(is_array($keys) && (array_search($char, $keys) !== FALSE))

                break;

            else if($char == NCURSES_KEY_UP)

                $posY--;

            else if($char == NCURSES_KEY_DOWN)

                $posY++;

            else if($char == NCURSES_KEY_LEFT)

                $posX--;

            else if($char == NCURSES_KEY_RIGHT)

                $posX++;

            else if($char == NCURSES_KEY_PPAGE)

                $posY -= $copyH-1;

            else if($char == NCURSES_KEY_NPAGE)

                $posY += $copyH-1;

            else if($char == 362) // HOME

                $posX = 0;

            else if($char == 385) // END

                $posX = 99999;

            else if(($char == 410) || ($char == -1)){

                // these "characters" are pressed on resizing

            }else if($keys === TRUE)

                break;



        } //end loop

        

        // free all resources

        ncurses_delwin($textWIN);

        ncurses_delwin($upperWIN);

        ncurses_delwin($lowerWIN);

        

        // return the pressed character

        return $char;

        

    } // end function


?>
