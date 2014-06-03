<?php

class DocConfig
{
    public static $config = array();
    public static $cookies = array();

    protected $cookie_domain;

    /**
     * Create the skeleton XML structure to start building on.
     *
     * @param array $cookies Cookies (including proxy cookies) from $_COOKIE array.
     * @param string $cookie_domain Domain name to store cookies.
     * @param string $link_type_default Default linkType used by BmlElements
     */
    public function __construct(array $cookies, $cookie_domain, $link_type_default) {
        $this->load_cookies($cookies);
        list($screen_w, $screen_h) = explode('x', $this->binusys_size);
        $this->load_layout_vals($screen_w, $screen_h);
        self::$config['link_type_default'] = $link_type_default;
        $this->cookie_domain = $cookie_domain;
    }

    /**
     * Loads a config file based on the screen size and populates this classes fields
     * with the values.
     *
     * @throws BmlConfigException If the config contains an unknown key.
     */
    protected function load_layout_vals($screen_w, $screen_h)
    {
        $num_screen_pixels = $screen_w * $screen_h;
        if ($num_screen_pixels <= 26000) {
            self::$config = require 'conf/conf_s.php';
        } elseif ($num_screen_pixels <= 40000) {
            self::$config = require 'conf/conf_m.php';
        } elseif ($num_screen_pixels <= 100000) {
            self::$config = require 'conf/conf_l.php';
        } elseif ($num_screen_pixels <= 185000) {
            self::$config = require 'conf/conf_xl.php';
        } else {
            self::$config = require 'conf/conf_xxl.php';
        }
        self::$config['screen_w'] = $screen_w;
        self::$config['screen_h'] = $screen_h;
    }

    /**
     * Parse the an array of raw cookies into a hierarchical array. The proxy cookie
     * values are pseudo arrays delimited by pipes. This only stores proxy cookies.
     *
     * @param $raw_cookies The same as what is stored in $_COOKIE.
     */
    protected function load_cookies($raw_cookies)
    {
        // check if the proxy cookies are set
        $is_proxy_cookies_set = false;
        foreach ($raw_cookies as $cookie_key => $_) {
            if (strpos($cookie_key, 'binusys') !== false) {
                $is_proxy_cookies_set = true;
                break;
            }
        }
        if ($is_proxy_cookies_set) {
            // if they are parse them into a hierarchical array
            foreach ($raw_cookies as $key => $val) {
                if (strpos($key, 'binusys') !== false
                    || strpos($key, 'binuprof') !== false
                ) {
                    self::$cookies[$key] = $this->proxy_cookie_2_array($val);
                }
            }
        } else {
            // otherwise load default values
            self::$cookies = require_once 'conf/default_cookies.php';
        }
    }

    /**
     * Helper function to parse proxy cookie values into associative arrays. If there
     * is just one value it is left as a string.
     *
     * @param $cookie_val
     * @return array|string
     */
    private function proxy_cookie_2_array($cookie_val)
    {
        $val_parts = explode('|', $cookie_val);
        if (count($val_parts) === 1) {
            return $val_parts[0];
        } else {
            $array = array();
            foreach ($val_parts as $sub_key_val) {
                if ($sub_key_val) {
                    $sub_key_val_parts = explode(':', $sub_key_val);
                    if (count($sub_key_val_parts) === 1) {
                        // there's no key
                        $array[] = $sub_key_val;
                    } else {
                        $array[$sub_key_val_parts[0]] = $sub_key_val_parts[1];
                    }
                }
            }
            return $array;
        }
    }

    /**
     * Returns a config/cookie item.
     *
     * @param string $config_item The config item key.
     * @return string The config value.
     * @throws InvalidArgumentException If the key does not exist.
     */
    public function __get($config_item)
    {
        $getable = array_merge(self::$cookies, self::$config);
        if (!isset($getable[$config_item])) {
            throw new InvalidArgumentException(
                'There is no config item ' . $config_item . '!'
            );
        }
        return $getable[$config_item];
    }

    /**
     * The proxy shares screens between users. This cookie forces the proxy to
     * avoid doing this across different screen sizes.
     */
    protected function set_screensize_refresh_cookie()
    {
        setcookie(
            'screen',
            self::$config['screen_w'] . 'x' . self::$config['screen_h'],
            time() + 86400 * 356,
            dirname($_SERVER['SCRIPT_NAME']),
            $this->cookie_domain
        );
    }
}
