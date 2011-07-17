<?php
class Conf
{
    /**
     * @var array
     */
    protected static $data = NULL;

    /**
     * Load data
     *
     * @return void
     */
    public static function load()
    {
        $json = file_get_contents(__DIR__ . '/../config.php');
        self::$data = (array)json_decode($json);
    }

    /**
     * Get key from config
     *
     * @param string $key
     * @return mixed
     */
    public static function get($key)
    {
        if ( ! self::$data)
        {
            self::load();
        }

        if (isset(self::$data[$key]))
        {
            return self::$data[$key];
        }
        else
        {
            return NULL;
        }
    }

    /**
     * @param string    $key
     * @param mixed     $value
     */
    public static function set($key, $value)
    {
        self::$data[$key] = $value;
    }

    /**
     * Save config
     * 
     * @return void
     */
    public static function save()
    {
        if (self::$data)
        {
            $data = json_encode(self::$data);
            file_put_contents(__DIR__ . '/../config.php', $data);
        }
        else
        {
            Main::_debug('NO DATA FOR SAVE');
        }
    }
}
