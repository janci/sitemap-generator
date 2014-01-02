Sitemap Generator
===========================================================

Sitemap Generator is the php library to generate sitemap.xml from page link. Scan process search a-href
 elements on the page and create links map from their. Scanning can be use only for one page, when will
 gets links from the input link. Second option is used scanner recursive. For this option scanner will
 scan all searched links on website. Accepted links to post scanning must be start with "/" or with domain
 name, what is equals to input website domain.


Installation
------------

The best way how to install is to [download a latest package](https://github.com/janci/sitemap-generator/releases)
or use a Composer:

```
php composer.phar require janci/sitemap-generator
```

Sitemap Generator requires PHP 5.3.0 or later.


Examples of usage
-----------------

Can be use for only gets all links on the website:

```php
    $siteMap = new SitemapGenerator();
    $siteMap->scanSite(new UrlScanDriver("http://test.sk/"));
    $urls = $siteMap->getFoundUrls();
```

For gets sitemap.xml content:


```php
    $siteMap = new SitemapGenerator();
    $siteMap->scanSite(new UrlScanDriver("http://test.sk/"));
    $sitemapXML = $siteMap->getSitemapContent();

    file_put_contents('sitemap.xml', $sitemapXML);
```

Previous two examples use recursive scanning (default). For single page scan is required sets second
parameter of method SitemapGenerator::scanSite to false:


```php
    $siteMap = new SitemapGenerator();
    $siteMap->scanSite(new UrlScanDriver("http://test.sk/"), false);
    $urls = $siteMap->getFoundUrls();
```

Thats' all!