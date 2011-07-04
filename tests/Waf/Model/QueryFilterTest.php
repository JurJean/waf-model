<?php
class Waf_Model_QueryFilterTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->queryFilter = new Waf_Model_QueryFilter();
    }

    public function tearDown()
    {
        unset($this->queryFilter);
    }

    public function testHasNotFilter()
    {
        $this->assertFalse($this->queryFilter->hasFilter(
            'Waf_Model_QueryFilterTest_TestQueryFilter1'
        ));
    }

    public function testAddHasFilter()
    {
        $this->queryFilter->addFilter(
            new Waf_Model_QueryFilterTest_TestQueryFilter1()
        );

        $this->assertTrue(
            $this->queryFilter->hasFilter(
                'Waf_Model_QueryFilterTest_TestQueryFilter1'
            )
        );
    }

    public function testGetFiltersArray()
    {
        $this->assertType(
            'array',
            $this->queryFilter->getFilters()
        );
    }

    public function testAddFilterGetFilters()
    {
        $this->queryFilter->addFilter(
            new Waf_Model_QueryFilterTest_TestQueryFilter1()
        );

        $this->assertEquals(1, count($this->queryFilter->getFilters()));
    }

    /**
     * @expectedException Waf_Model_QueryFilter_Exception
     */
    public function testGetFilter()
    {
        $this->queryFilter->getFilter('Waf_Model_QueryFilterTest_TestQueryFilter1');
    }

    public function testAddFilterGetFilter()
    {
        $this->queryFilter->addFilter(
            new Waf_Model_QueryFilterTest_TestQueryFilter1()
        );

        $this->assertType(
            'Waf_Model_QueryFilterTest_TestQueryFilter1',
            $this->queryFilter->getFilter('Waf_Model_QueryFilterTest_TestQueryFilter1')
        );
    }

    public function testAddFilterByString()
    {
        $this->queryFilter->addFilter(
            'Waf_Model_QueryFilterTest_TestQueryFilter2'
        );
        $this->assertType(
            'Waf_Model_QueryFilterTest_TestQueryFilter2',
            $this->queryFilter->getFilter(
                'Waf_Model_QueryFilterTest_TestQueryFilter2'
            )
        );
    }

    /**
     * @expectedException Waf_Model_QueryFilter_Exception
     */
    public function testAddFilterNotAFilterInterface()
    {
        $this->queryFilter->addFilter(
            new Waf_Model_QueryFilterTest_TestNotAQueryFilter()
        );
    }

    public function testSetFiltersGetFilters()
    {
        $this->queryFilter->setFilters(array(
            new Waf_Model_QueryFilterTest_TestQueryFilter1(),
            new Waf_Model_QueryFilterTest_TestQueryFilter2()
        ));

        $this->assertEquals(2, count($this->queryFilter->getFilters()));
    }

    public function testSetFiltersByArray()
    {
        $this->queryFilter->setNamespace('Waf_Model_QueryFilterTest');
        $filters = array(
            'testQueryFilter1' => array('hell'),
            'testQueryFilter2' => array('yeah')
        );
        $this->queryFilter->setFilters($filters);
        $this->assertEquals(
            'yeah',
            $this->queryFilter
                ->getFilter('Waf_Model_QueryFilterTest_TestQueryFilter2')
                ->value
        );
    }

    public function testAddFilterClearFiltersIsArray()
    {
        $this->queryFilter->addFilter(
            new Waf_Model_QueryFilterTest_TestQueryFilter1()
        );
        $this->queryFilter->clearFilters();

        $this->assertType('array', $this->queryFilter->getFilters());
    }

    public function testAddFilterClearFiltersCount()
    {
        $this->queryFilter->addFilter(
            new Waf_Model_QueryFilterTest_TestQueryFilter1()
        );
        $this->queryFilter->clearFilters();

        $this->assertEquals(0, count($this->queryFilter->getFilters()));
    }

    public function testFilterCallsLoadedFilters()
    {
        $filter1 = new Waf_Model_QueryFilterTest_TestQueryFilter1();
        $filter2 = new Waf_Model_QueryFilterTest_TestQueryFilter2();
        $query   = new Waf_Model_QueryFilterTest_TestQuery();
        
        $this->queryFilter->setFilters(array(
            $filter1
        ));
        $this->queryFilter->filter($query);

        $this->assertTrue($query->filter1);
    }

    public function testHasNoNamespace()
    {
        $this->assertFalse($this->queryFilter->hasNamespace());
    }

    public function testSetHasNamespace()
    {
        $this->queryFilter->setNamespace('Foo');
        $this->assertTrue($this->queryFilter->hasNamespace());
    }

    /**
     * @expectedException Waf_Model_QueryFilter_Exception
     */
    public function testGetNamespaceFail()
    {
        $this->queryFilter->getNamespace();
    }

    public function testSetGetNamespace()
    {
        $this->queryFilter->setNamespace('Bar_');
        $this->assertEquals('Bar', $this->queryFilter->getNamespace());
    }

    public function testAddNamespacedFilter()
    {
        $this->queryFilter->setNamespace(get_class($this));
        $this->queryFilter->addFilter('TestQueryFilter1');

        $this->assertTrue($this->queryFilter->hasFilter(
            'Waf_Model_QueryFilterTest_TestQueryFilter1'
        ));
    }
}

class Waf_Model_QueryFilterTest_TestQueryFilter1
    implements Waf_Model_QueryFilter_QueryFilterInterface
{
    public $value;
    public $filter = false;

    public function __construct($value = null)
    {
        $this->value = $value;
    }

    public function filter($query)
    {
        $query->filter1 = true;
    }
}

class Waf_Model_QueryFilterTest_TestQueryFilter2
    implements Waf_Model_QueryFilter_QueryFilterInterface
{
    public $value;
    public $filter = false;

    public function __construct($value = null)
    {
        $this->value = $value;
    }

    public function filter($query)
    {
        $query->filter2 = true;
    }
}

class Waf_Model_QueryFilterTest_TestNotAQueryFilter
{
    
}

class Waf_Model_QueryFilterTest_TestQuery
{
    public $filter1 = false;
    public $filter2 = false;
}