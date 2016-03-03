<?php
/**
 * Created by OxGroup.
 * User: Aliaxander
 * Date: 24.02.16
 * Time: 15:54
 */

namespace Ox\Router;

/**
 * Class Helper
 *
 * @package Ox\Router
 */
class Helper
{
    /**
     * @param $input
     *
     * @return string
     */
    public static function fixStandardRoute($input)
    {
        if (substr($input, -1) !== "/") {
            $input .= "/";
        }
        if ($input{0} !== "/") {
            $input = "/" . $input;
        }
        return $input;
    }

    /**
     * @param $input
     *
     * @return mixed|string
     */
    public static function getMacrosMatch($input)
    {
        $before = array(":num",
            ":char",
            ":charNum",
            ":text",
            ":img",
            "/",
        );
        $after = array(
            "[0-9]*",
            "[A-Za-z]*",
            "[A-Za-z0-9-]*",
            "[A-Za-z0-9- .,:%+;]*",
            ".*[.](png|jpg|jpeg|gif)",
            '\/',
        );
        $routePreg = str_replace($before, $after, $input);
        $routePreg = "/^" . $routePreg . "$/i";
        return $routePreg;
    }
}
