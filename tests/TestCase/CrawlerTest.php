<?php
namespace WebCrawler\Tests\TestCase;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

use WebCrawler\Crawler;

class CrawlerTest extends TestCase
{
    public $presetDomain;
    public $crawler;
    
    public function setUp()
    {
        parent::setUp();
        
        $this->presetDomain = 'http://www.boxofficemojo.com/?ref_=amzn_nav_ftr';//'http://www.funimation.com/';
        $this->crawler = new Crawler($this->presetDomain);
    }
    
    public function tearDown()
    {
        parent::tearDown();
        
        $this->crawler = null;
    }
    
    public function validUrlsDataProvider()
    {
        return [
            ['https://google.com'],
            ['http://somesite.com'],
            ['www.domain.com'],
            ['location.com'],
        ];
    }
    
    /**
     * 
     * @dataProvider validUrlsDataProvider
     */
    public function testValidConstruction($url)
    {
        $this->crawler = new Crawler($url);
        
        $this->assertInstanceOf(Crawler::class, $this->crawler);
    }
    
    public function invalidUrlsDataProvider()
    {
        return [
            ['324 432'],
            ['https://go daddy.com'],
        ];
    }
    
    /**
     * 
     * @dataProvider invalidUrlsDataProvider
     *
     * @param type $url
     */
    public function testFailingConstruction($url)
    {
        $this->expectException(InvalidArgumentException::class);
        
        $this->crawler = new Crawler($url);
    }
    
    public function urlsDataProvider()
    {
        return [
            ['https://google.com', true],
            ['http://somesite.com', true],
            ['www.domain.com', true],
            ['location.com', true],
            ['324 432', false],
            ['https://go daddy.com', false],
        ];
    }
    
    /**
     * 
     * @dataProvider urlsDataProvider
     *
     * @param type $url
     * @param type $expectedBoolean
     */
    public function testSetRoot($url, $expectedBoolean)
    {
        $result = $this->crawler->setRoot($url);
        
        $this->assertEquals($expectedBoolean, $result);
    }
    
    public function testGetRoot()
    {
        $result = $this->crawler->getRoot();
        
        $this->assertEquals($this->presetDomain, $result);
    }
    
    public function testCrawl()
    {
        $this->crawler->crawl();
        $this->markTestIncomplete('Not yet implemented');
    }
}
