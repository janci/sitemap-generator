<?php
/**
 * This file is part of the SitemapGenerator
 *
 * Copyright (c) 2014 Ing. Jan Svantner (http://www.janci.net)
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace SitemapGenerator\ScanDrivers;


use SitemapGenerator\IScanDriver;

/**
 * Driver to scanning page in array - don't use remote connect.
 * @package SitemapGenerator\ScanDrivers
 * @author Ing. Jan Svantner <posta.janci@gmail.com>
 */
class StringScanDriver implements IScanDriver {

    private static $contents;

    private $url;

    private $content;

    private function __construct($url, $content){
        $this->url = $url;
        $this->content = $content;
    }

    /**
     * Sets contents for url paths.
     * @param array $contents [ url => htmlContent ]
     */
    public static function setPages($contents){
        self::$contents = $contents;
    }

    /**
     * Create new scan driver instance by gets url of site.
     * @param $url
     * @return IScanDriver
     */
    public static function fromUrl($url) {
        $url = rtrim($url, "/");
        if(isset(self::$contents[$url])) {
            return new static($url, self::$contents[$url]);
        }
        return new static($url, "");
    }

    /**
     * Returns full url path with domain.
     * This address will be use as unique site identifier.
     * @return string
     */
    public function getUrl() {
        return $this->url;
    }

    /**
     * Returns content for source site url.
     * @return string
     */
    public function getContent() {
        return $this->content;
    }

    /**
     * Scan driver can decide about correct input data. If scan driver detect
     * not valid input source, scanning can be refused.
     * @return bool
     */
    public function validate() {
        return true;
    }
}