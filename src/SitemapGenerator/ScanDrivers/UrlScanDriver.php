<?php
/**
 * This file is part of the SitemapGenerator
 *
 * Copyright (c) 2013 Ing. Jan Svantner (http://www.janci.net)
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace SitemapGenerator\ScanDrivers;


use SitemapGenerator\IScanDriver;
use SitemapGenerator\Utils\Strings;

class UrlScanDriver implements IScanDriver {



    private $url;

    protected $ignoredExtensions = array('jpg', 'png', 'pdf', 'bmp');

    public function __construct($url){
        $url = rtrim($url, "/");
        $this->url = $url;
    }

    /**
     * Returns content for source site url.
     * @return string
     */
    public function getContent()
    {
        $context = stream_context_create(array('http' => array('header'=>'Connection: close\r\n')));
        $content = file_get_contents($this->url, false, $context);
        return $content;
    }

    /**
     * Create new scan driver instance by gets url of site.
     * @param $url
     * @return IScanDriver
     */
    public static function fromUrl($url)
    {
        return new static($url);
    }

    /**
     * Scan driver can decide about correct input data. If scan driver detect
     * not valid input source, scanning can be refused.
     * @return bool
     */
    public function validate()
    {
        foreach($this->ignoredExtensions as $ext)
            if(Strings::endsWith(strtolower($this->url), $ext)) return false;
        return true;
    }

    /**
     * Returns full url path with domain.
     * This address will be use as unique site identifier.
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }
}