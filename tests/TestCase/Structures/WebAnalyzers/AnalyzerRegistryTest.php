<?php
namespace WebCrawler\Tests\TestCase\Structures\WebAnalyzers;

use WebCrawler\Structures\WebAnalyzers\AnalyzerRegistry;
use WebCrawler\Structures\WebAnalyzers\AbstractAnalyzer;

use PHPUnit\Framework\TestCase;

use InvalidArgumentException;

class AnalyzerRegistryTest extends TestCase
{
    private $registry;
    private $mockAnalyzer;
    
    public function setUp()
    {
        parent::setUp();
        
        $this->registry = new AnalyzerRegistry();
        $this->mockAnalyzer = $this->getMockBuilder(AbstractAnalyzer::class)
                ->setMethods(['analyze'])
                ->getMock();
    }
    
    public function tearDown()
    {
        parent::tearDown();
        
        $this->registry = null;
        $this->mockAnalyzer = null;
    }
    
    /**
     * Specifies an invalid list of options to provide to the add methods 
     * <em>$analyzer</em> parameter.
     *
     * @return type
     */
    public function nonAnalyzerDataProvider()
    {
        return [
            [false],
            [true],
            ['this is some text'],
            [date('Ymd')],
        ];
    }
    
    /**
     * Should throw an exception with an invalid analyzer.
     *
     * @dataProvider nonAnalyzerDataProvider
     *
     * @param mixed $analyzer
     *
     * @return void
     */
    public function testAddThrowsInvalidArgumentException($analyzer)
    {
        $this->expectException(InvalidArgumentException::class);
        
        $this->registry->add($analyzer);
    }
    
    /**
     * Supplies a list of testable operations.
     *
     * @return array
     */
    public function validAnalyzerValueDataProviders()
    {
        return [
            [null],
            ['abra'],
            ['kadabra'],
            [50],
        ];
    }
    
    /**
     * Test the returned value of the add operation.
     *
     * @dataProvider validAnalyzerValueDataProviders
     *
     * @param mixed $name
     *
     * @return void
     */
    public function testAddOperationFunctional($name)
    {
        $result = $this->registry->add($this->mockAnalyzer, $name, false);
        
        $this->assertTrue($result);
    }
    
    /**
     * Supplies a list of analyzers that should fail to insert.
     *
     * @return array
     */
    public function invalidAnalyzerValueDataProviders()
    {
        return [
            [0],
            ['alpha'],
            ['gamma'],
            [20],
        ];
    }
    
    /**
     * Test that the add operation fails with duplicate data.
     *
     * @dataProvider invalidAnalyzerValueDataProviders
     *
     * @param mixed $name
     *
     * @return void
     */
    public function testAddOperationOnDuplicateNames($name)
    {
        $result = $this->registry->add($this->mockAnalyzer, $name, false);
        $result = $this->registry->add($this->mockAnalyzer, $name, false);
        
        $this->assertFalse($result);
    }
    
    public function containsKeysDataProvider()
    {
        return [
            ['alpha'],
            [5],
            ['yemin'],
            [80],
        ];
    }
    
    /**
     * Test that keys will be searchable.
     *
     * @dataProvider containsKeysDataProvider
     *
     * @param mixed $searchedName
     *
     * @return void
     */
    public function testContainsOnKeys($searchedName)
    {
        $names = ['alpha','beta',5,20,'eighty'];
        foreach ($names as $name) {
            $this->registry->add($this->mockAnalyzer, $name, true);
        }
        
        $wasContained = $this->registry->contains($searchedName);
        
        $this->assertEquals(in_array($searchedName, $names), $wasContained);
    }
    
    /**
     * Provides an Analyzer which was added and one which wasn't
     *
     * @return array
     */
    public function containableAnalyzersDataProvider()
    {
        $newAnalyzer = $this->getMockBuilder(AbstractAnalyzer::class)
                ->setMethods(['analyze'])
                ->getMock();
        
        return [
            [$this->mockAnalyzer],
            [$newAnalyzer],
        ];
    }
    
    /**
     * Test ability to search for Analyzers.
     *
     * @dataProvider containableAnalyzersDataProvider
     *
     * @param AbstractAnalyzer $searchedAnalyzer
     *
     * @return void
     */
    public function testContainsOnAnalyzer($searchedAnalyzer)
    {
        $this->registry->add($this->mockAnalyzer, 'Ivy', false);
        $wasContained = $this->registry->contains($searchedAnalyzer);
        
        $this->assertEquals($searchedAnalyzer === $this->mockAnalyzer, $wasContained);
    }
    
    public function testRemovePasses()
    {
        $this->registry->add($this->mockAnalyzer, 'Ivy');
        $result = $this->registry->remove('Ivy');
        
        $this->assertTrue($result);
        
        $this->registry->add($this->mockAnalyzer, 'Bulba');
        $result = $this->registry->remove($this->mockAnalyzer);
        
        $this->assertTrue($result);
    }
    
    /**
     * Retrieves an array of fields that should not be containable in the
     * registry.
     *
     * @return array
     */
    public function nonContainingFeildsDataProvider()
    {
        $newAnalyzer = $this->getMockBuilder(AbstractAnalyzer::class)
                ->setMethods(['analyze'])
                ->getMock();
        
        return [
            ['Bulba'],
            [$newAnalyzer],
        ];
    }
    
    /**
     * Checks remove will fail when data does not exists.
     *
     * @dataProvider nonContainingFeildsDataProvider
     *
     * @param mixed $item
     *
     * @return void
     */
    public function tesstRemoveFails($item)
    {
        $this->registry->add($this->mockAnalyzer, 'Ivy');
        $result = $this->registry->remove($item);
        
        $this->assertFalse($result);
    }
}
