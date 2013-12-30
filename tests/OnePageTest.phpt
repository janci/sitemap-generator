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

use SitemapGenerator\SitemapGenerator;
use Tester\TestCase;

$container = require __DIR__ . '/bootstrap.php';

class OnePageTest extends TestCase {

    public function testHomepage(){
        $siteMap = new SitemapGenerator();
        $siteMap->scanSite("http://j5new.janci.net");
        $output = $siteMap->getSitemapContent();
        echo "Generated in ".$siteMap->getScanTime()." s\n";
    }
}

id(new OnePageTest())->run();