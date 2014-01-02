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

use SitemapGenerator\Utils\Strings;
use Sunra\PhpSimple\HtmlDomParser;

require_once("functions.php");

class SitemapGenerator {

    private $scannedUrls = array();

    private $unscannedUrls = array();

    private $scanTime = 0;

    public $onScanSite;


    public function __construct(){
        $this->onScanSite = array();
    }

    /**
     * Returns domain link from url.
     * @param $url
     * @return string
     */
    protected function getDomainLink($url) {
        $parsedLink = parse_url($url);
        if($parsedLink == false) return "";

        $domainLink = http_build_url($url,
            $parsedLink,
            HTTP_URL_STRIP_PATH | HTTP_URL_STRIP_QUERY | HTTP_URL_STRIP_FRAGMENT
        );
        return $domainLink;
    }

    /**
     * Scan website to links. If sets recursively to true, will be scanned all linked
     * pages from this site.
     * @param IScanDriver $site
     * @param bool $recursively
     */
    public function scanSite(IScanDriver $site, $recursively = true){
        $startTime = microtime(true);

        $startPage = rtrim( $this->getDomainLink($site->getUrl()), "/" );
        $this->scanUrl($site, $startPage);

        if($recursively) {
            foreach($this->onScanSite as $fn) $fn($this, $site);
            while(!empty($this->unscannedUrls)) {
                $scanUrl = array_shift($this->unscannedUrls);
                $this->scanUrl($scanUrl, $startPage);
                foreach($this->onScanSite as $fn) $fn($this, $scanUrl);
            }
        } else {
            $this->scannedUrls = $this->scannedUrls + $this->unscannedUrls;
            foreach($this->onScanSite as $fn) $fn($this, $site);
        }
        $this->scanTime = microtime(true)-$startTime;
    }


    /**
     * Returns progress of scanning in percentage (0-100) round to two decimals.
     * @return float
     */
    public function getProgressStatus(){
        $scannedCount = count($this->scannedUrls);
        $allCount = count($this->unscannedUrls + $this->scannedUrls);

        if($allCount == 0) return "100.00";
        return number_format( min(1, $scannedCount / $allCount )*100, 2);
    }

    /**
     * Returns time for last scanning. If scan wasn't start returns zero.
     * @return float
     */
    public function getScanTime(){
        return $this->scanTime;
    }

    /**
     * Scan one url for links ands update scanned and not scanned links.
     * @param IScanDriver $site
     * @param $startPage
     */
    protected function scanUrl(IScanDriver $site, $startPage){
        if(!$site->validate()) return;

        $content = $site->getContent($site);

        /** @var \simple_html_dom $simpleDom */
        $simpleDom = HtmlDomParser::str_get_html($content);

        /** @var \simple_html_dom_node[] $links */
        $links = $simpleDom->find("a");
        foreach($links as $link){
            $siteUrl = $link->getAttribute("href");

            if(!$siteUrl) continue;
            if(!Strings::startsWith($siteUrl, '/')) continue;

            $siteUrl = str_replace($site->getUrl(), "", $siteUrl);
            $siteUrl = rtrim($siteUrl, "/");

            $newSite = $site::fromUrl($startPage.$siteUrl);
            if(isset($this->scannedUrls[$newSite->getUrl()]))
                continue;

            $this->unscannedUrls[$newSite->getUrl()] = $newSite;
        }
        $this->scannedUrls[$site->getUrl()] = $site;
        unset($this->unscannedUrls[$site->getUrl()]);
    }

    /**
     * Returns all url addresses found on website.
     * @return array
     */
    public function getFoundUrls(){
        return array_keys($this->scannedUrls);
    }

    /**
     * Returns scanned url links in sitemap xml standard.
     * @return mixed
     */
    public function getSitemapContent(){
        $urls = $this->getFoundUrls();

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