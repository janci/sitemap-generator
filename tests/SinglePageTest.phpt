<?php
/**
 * This file is part of the SitemapGenerator
 *
 * Copyright (c) 2013 Ing. Jan Svantner (http://www.janci.net)
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace SitemapGenerator\Tests;

use SitemapGenerator\ScanDrivers\StringScanDriver;
use SitemapGenerator\SitemapGenerator;
use Tester\Assert;
use Tester\TestCase;

$container = require __DIR__ . '/bootstrap.php';

class SinglePageTest extends TestCase {

    private $contests;

    private function getContents(){
        if(isset($this->contests)) return $this->contests;

        $context = stream_context_create(array('http' => array('header'=>'Connection: close\r\n')));
        $dir = __DIR__.DIRECTORY_SEPARATOR.'demo-pages'.DIRECTORY_SEPARATOR;
        return $this->contests = array(
            "http://test.sk" => file_get_contents($dir."homepage.html", false, $context),
            "http://test.sk/page-no-1.html" => file_get_contents($dir."page-no-1.html", false, $context),
            "http://test.sk/page-no-2.html" => file_get_contents($dir."page-no-2.html", false, $context),
            "http://test.sk/page-no-3.html" => file_get_contents($dir."page-no-3.html", false, $context),
            "http://test.sk/page-no-4.html" => file_get_contents($dir."page-no-4.html", false, $context)
        );
    }

    public function testHomepage(){
        StringScanDriver::setPages($this->getContents());

        $siteMap = new SitemapGenerator();
        $siteMap->scanSite(StringScanDriver::fromUrl("http://test.sk/"), false);
        $urls = $siteMap->getFoundUrls();

        Assert::contains("http://test.sk", $urls);
        Assert::contains("http://test.sk/page-no-1.html", $urls);
        Assert::contains("http://test.sk/page-no-2.html", $urls);
        Assert::notContains("http://test.sk/page-no-3.html", $urls);
        Assert::notContains("http://test.sk/page-no-4.html", $urls);

        echo "Scan time:".($siteMap->getScanTime()*1000)." ms\n";
        Assert::true($siteMap->getScanTime()*1000 < 10);
    }

    public function testPageNo1(){
        StringScanDriver::setPages($this->getContents());

        $siteMap = new SitemapGenerator();
        $siteMap->scanSite(StringScanDriver::fromUrl("http://test.sk/page-no-1.html"), false);
        $urls = $siteMap->getFoundUrls();

        Assert::contains("http://test.sk", $urls);
        Assert::contains("http://test.sk/page-no-1.html", $urls);
        Assert::notContains("http://test.sk/page-no-2.html", $urls);
        Assert::contains("http://test.sk/page-no-3.html", $urls);
        Assert::contains("http://test.sk/page-no-4.html", $urls);

        echo "Scan time:".($siteMap->getScanTime()*1000)." ms\n";
        Assert::true($siteMap->getScanTime()*1000 < 10);
    }
}

id(new SinglePageTest())->run();