Sitemap Generator
===========================================================

Sitemap Generator is PHP library to generate sitemap.xml from page link. Scan process searches a-href
 elements on the page and creates map with links (without links to external pages). Scanning can be used only for one page, when will
 gets links from the input link. Second  option is using scanner to scan recursively. For this option scanner will
 scan all found links on website. Scanner accepts links starting with "/" or with domain name (website URL).

Scanning process can run several minutes for bigger pages or smaller connect. The best practise is to run
script from command line (CLI), not from website using web server.


Installation
------------

The best way how to install Sitemap Generator is using Composer:

```
php composer.phar require janci/sitemap-generator
```

Sitemap Generator requires PHP 5.3.0 or later.


Usage examples
-----------------

To find all links on the website, use:

```php
    $siteMap = new SitemapGenerator();
    $siteMap->scanSite(new UrlScanDriver("http://www.example.com/"));
    $urls = $siteMap->getFoundUrls();
```

To get result as sitemap.xml, use:

```php
    $siteMap = new SitemapGenerator();
    $siteMap->scanSite(new UrlScanDriver("http://www.example.com/"));
    $sitemapXML = $siteMap->getSitemapContent();

    file_put_contents('sitemap.xml', $sitemapXML);
```

Previous two examples use recursive scanning (default). For single page scan is required to set "false" as second
parameter of method SitemapGenerator::scanSite:


```php
    $siteMap = new SitemapGenerator();
    $siteMap->scanSite(new UrlScanDriver("http://www.example.com/"), false);
    ...
```

To show progress information can be used handler function. Handler must be registered before calling scanSite method.

```php
    $siteMap = new SitemapGenerator();
    $siteMap->onScanSite[] = function($siteMapGenerator, $scanDriver) {
        echo "{$siteMapGenerator->getProgressStatus()}%\n";
    };
    $siteMap->scanSite(new UrlScanDriver("http://www.example.com/"));
    ...
```

Thats' all!