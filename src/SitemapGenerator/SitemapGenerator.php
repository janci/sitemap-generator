<?php
/**
 * This file is part of the SitemapGenerator
 *
 * Copyright (c) 2013 Ing. Jan Svantner (http://www.janci.net)
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace SitemapGenerator;


use SimpleHtmlDom\simple_html_dom_node;
use SimpleHtmlDom\SimpleHtmlDom;
use SitemapGenerator\Utils\Strings;

class SitemapGenerator {

    private $scannedUrls;

    private $unscannedUrls;

    private $scanTime;

    public function __construct(){
    }

    public function scanSite($url, $recursively = true){
        $startTime = microtime(true);
        $this->scanUrl($url, $url);
        while(!empty($this->unscannedUrls)) {
            $scanUrl = array_shift($this->unscannedUrls);
            $this->scanUrl($scanUrl, $url);
        }
        $this->scanTime = microtime(true)-$startTime;
    }

    public function getScanTime(){
        return $this->scanTime;
    }

    protected function scanUrl($url, $startPage){
        $url = rtrim($url, "/");

        $skippedExt = array('jpg', 'png', 'pdf', 'bmp');
        foreach($skippedExt as $ext)
            if(Strings::endsWith(strtolower($url), $ext)) return;

        $context = stream_context_create(array('http' => array('header'=>'Connection: close\r\n')));
        $content = file_get_contents($url, false, $context);
        $simpleDom = new SimpleHtmlDom($content);

        /** @var simple_html_dom_node[] $links */
        $links = $simpleDom->find("a");
        foreach($links as $link){
            $siteUrl = $link->getAttribute("href");

            if(!$siteUrl) continue;
            if(!Strings::startsWith($siteUrl, '/')) continue;

            $siteUrl = str_replace($url, "", $siteUrl);
            if(isset($this->scannedUrls[$startPage.$siteUrl]))
                continue;

            $siteUrl = rtrim($siteUrl, "/");
            $this->unscannedUrls[$startPage.$siteUrl] = $startPage.$siteUrl;
        }
        $this->scannedUrls[$url] = $url;
        unset($this->unscannedUrls[$url]);
    }

    public function getScannedUrls(){
        return array_keys($this->scannedUrls);
    }

    public function getSitemapContent(){
        $urls = $this->getScannedUrls();

        $collection = new \Sitemap\Collection;
        foreach($urls as $url){
            $entry = new \Sitemap\Sitemap\SitemapEntry;
            $entry->setLocation($url);
            $entry->setLastMod(time());
            $collection->addSitemap($entry);
        }

        // There's some different formatters available.
        $collection->setFormatter(new \Sitemap\Formatter\XML\URLSet);
        $collection->setFormatter(new \Sitemap\Formatter\XML\SitemapIndex);

        return $collection->output();
    }
} 