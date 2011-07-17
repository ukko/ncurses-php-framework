<?php
/**
 *
 */
class FS
{
    public static function getList($dir)
    {
        if ( ! realpath($dir) )
        {
            $dir = $_SERVER['HOME'];
        }
        $path = realpath($dir);
        $result = array();

        foreach (scandir($path) as $item)
        {
            if ($item == '.') continue;
            
            $result[] = array(
                'title' => $item,
                'bold'  => is_dir($path . '/' . $item),
            );
        }
        return $result;
    }
}
