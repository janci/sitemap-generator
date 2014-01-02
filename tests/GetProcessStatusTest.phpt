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

use SitemapGenerator\IScanDriver;
use SitemapGenerator\ScanDrivers\StringScanDriver;
use SitemapGenerator\SitemapGenerator;
use Tester\Assert;
use Tester\TestCase;

$container = require __DIR__ . '/bootstrap.php';

class GetProcessStatusTest extends TestCase {

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

    private $checkValue = false;
    private $expectedValues = array();
    public function checkCallback(SitemapGenerator $sitemapGenerator, IScanDriver $currentScanPage){
        $this->checkValue = $sitemapGenerator->getProgressStatus();
        if(!empty($this->expectedValues)) {
            $expected = array_shift($this->expectedValues);
            Assert::same($expected, $this->checkValue);
        }
    }

    public function testStatusForSinglePage(){
        StringScanDriver::setPages($this->getContents());
        $this->checkValue = false;
        $this->expectedValues  = false;

        $siteMap = new SitemapGenerator();
        $siteMap->onScanSite[] = array($this, 'checkCallback');

        $siteMap->scanSite(StringScanDriver::fromUrl("http://test.sk/"), false);
        $siteMap->getFoundUrls();

        Assert::equal("100.00", $this->checkValue);
    }

    public function testStatusForMultiPage(){
        StringScanDriver::setPages($this->getContents());
        $this->checkValue = false;

        //scanner started with information about 3 pages
        $this->expectedValues = array("33.33", "40.00", "60.00", "80.00", "100.00");

        $siteMap = new SitemapGenerator();
        $siteMap->onScanSite[] = array($this, 'checkCallback');
        $siteMap->scanSite(StringScanDriver::fromUrl("http://test.sk/"));
        $siteMap->getFoundUrls();

        Assert::equal("100.00", $this->checkValue);
    }

    public function testStatusForMultiPageFromSubpage(){
        StringScanDriver::setPages($this->getContents());
        $this->checkValue = false;

        //scanner started with information about 4 pages
        $this->expectedValues = array("25.00", "40.00", "60.00", "80.00", "100.00");

        $siteMap = new SitemapGenerator();
        $siteMap->onScanSite[] = array($this, 'checkCallback');
        $siteMap->scanSite(StringScanDriver::fromUrl("http://test.sk/page-no-1.html"));
        $siteMap->getFoundUrls();

        Assert::equal("100.00", $this->checkValue);
    }


}

id(new GetProcessStatusTest())->run();