<?php
/**
 * This file is part of the SitemapGenerator
 *
 * Copyright (c) 2013 Ing. Jan Svantner (http://www.janci.net)
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace SitemapGenerator\Utils;


class Strings {

    /**
     * Starts the $haystack string with the prefix $needle?
     * @param  string
     * @param  string
     * @return bool
     */
    public static function startsWith($haystack, $needle) {
        return strncmp($haystack, $needle, strlen($needle)) === 0;
    }

     /**
      * Ends the $haystack string with the suffix $needle?
      * @param  string
      * @param  string
      * @return bool
      */
     public static function endsWith($haystack, $needle) {
         return strlen($needle) === 0 || substr($haystack, -strlen($needle)) === $needle;
     }
} 