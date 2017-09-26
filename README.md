# Web Crawler

## Description

*This is a work in progress and is not yet complete*

Web Crawler is an open source technology which will enable users to crawl
through the a collection of webpages and executing customized analyzers
on each page.

## Installation

Add the library to your PHP project using composer.

```sh
composer require travy/web-crawler
```

##  Use Case

The Crawler will automatically pull all URL addresses listed under an HTML
anchor tag on the root URL.  Each page that is visited will be run through
a collection of Analyzers.  These Analyzers can perform various tasks needed
for the use of the application such as pruning the markup in order to build
a search engine, or almost anything else that can be analyzed.

###  Custom Analyzer

Analyzers can be created by extending the `AbstractAnalyzer` class

```php
class MyAnalyzer extends AbstractAnalyzer
{
    public function analyze($url, $html, Dom $parser)
    {
        //  perform tasks
    }
}
```

###  Analyzer Registry

The `AnalyzerRegistry` will contain a list of all Analyzers that should be
used while crawling the web.  Each analyzer will be assigned a unique key
so that fields can be manipulated if needed.

```php
$analyzer = new MyAnalyzer();

$analyzerRegistry = new AnalyzerRegistry();
$analyzerRegistry->registrer($analyzer, 'add-to-database');

$crawler = new Crawler('https://google.com', $analyzerRegsitry);
$crawler->crawl();
```

