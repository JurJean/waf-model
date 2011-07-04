<?php
class Waf_ModelTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->_model = new Waf_Model;
        $this->mapperDriver = $this->getMockForAbstractClass('Waf_Model_Mapper_Driver_DriverAbstract');
    }

    public function tearDown()
    {
        unset($this->_model);
    }

    /**
     * @expectedException Waf_Model_Exception
     */
    public function testGetApplicationPathFailsIfNotSetAndNotDefined()
    {
        $this->_model->getApplicationPath();
    }
    
    public function testGetBasePathIfNoneSet()
    {
        $this->_model->setApplicationPath('/foo/bar/application'); 
        $this->assertEquals('/foo/bar/application/models', $this->_model->getBasePath());
    }
    
    public function testGetBasePathIfSet()
    {
        $this->_model->setBasePath('/foo/bar/app/models');
        $this->assertEquals('/foo/bar/app/models', $this->_model->getBasePath());
    }
    
    public function testGetBasePathIfSetAsOption()
    {
        $this->_model->setOption('basePath', '/foo/bar/app/models');
        $this->assertEquals('/foo/bar/app/models', $this->_model->getBasePath());
    }
    
    public function testGetBasePathIfSetViaConstructor()
    {
        $model = new Waf_Model(array('basepath' => '/bar/app/models'));
        $this->assertEquals('/bar/app/models', $model->getBasePath());
    }

    public function testInitResourceLoader()
    {
        $this->_model->setOption('basePath', '/foo/bar/app/models');
        $this->assertType(
            'Zend_Loader_Autoloader_Resource',
            $this->_model->initResourceLoader()
        );
    }

    public function testSetStorageHandler()
    {
        $storageHandler = new Waf_Model_Storage;
        $this->_model->setStorageHandler($storageHandler);
        $this->assertSame($storageHandler, $this->_model->getStorageHandler());
    }

    public function testGetStorageHandlerIfNotSet()
    {
        $this->assertType('Waf_Model_Storage', $this->_model->getStorageHandler());
    }

    public function testAddGetMapper()
    {
        $mapper = new Waf_ModelTest_TestMapper();
        $this->_model->addMapper('Foo', $mapper);
        $this->assertType('Waf_ModelTest_TestMapper', $this->_model->getMapper('Foo'));
    }

    public function testAddMapperByEntityNameAndOptions()
    {
        $this->_model->setApplicationPath('../application');
        $this->_model->addMapper('Foo', array(
            'entityName' => 'Foo'
        ));
        $this->assertType('Waf_Model_Mapper', $this->_model->getMapper('Foo'));
    }

    public function testAddMapperSetsModel()
    {
        $mapper = new Waf_ModelTest_TestMapper();
        $this->_model->addMapper('Foo', $mapper);
        $this->assertEquals($mapper->getModel(), $this->_model);
    }

    public function testGetMapperByEntity()
    {
        $mapper = new Waf_ModelTest_TestMapper();
        $entity = new Waf_ModelTest_TestEntity();
        $this->_model->addMapper('Waf_ModelTest_TestEntity', $mapper);
        $this->assertSame($mapper, $this->_model->getMapper($entity));
    }

    /**
     * @expectedException Waf_Model_Exception
     */
    public function testGetMapperDriverFailNotSet()
    {
        $this->_model->getMapperDriver();
    }

    public function testHasMapperDriverFalse()
    {
        $this->assertFalse($this->_model->hasMapperDriver());
    }

    public function testSetGetMapperDriver()
    {
        $this->_model->setMapperDriver($this->mapperDriver);
        $this->assertSame($this->mapperDriver, $this->_model->getMapperDriver());
    }

    public function testSetHasMapperDriver()
    {
        $this->_model->setMapperDriver($this->mapperDriver);
        $this->assertTrue($this->_model->hasMapperDriver());
    }

    public function testSetMapperDriverGetMapper()
    {
        $mapper = new Waf_Model_Mapper();
        $this->mapperDriver
            ->expects($this->any())
            ->method('loadMapper')
            ->will($this->returnValue($mapper));
        $this->_model->setApplicationPath('../application');
        $this->_model->setMapperDriver($this->mapperDriver);
        $this->assertSame(
            $mapper,
            $this->_model->getMapper('Waf_ModelTest_TestEntity')
        );
    }

    public function testGetEntityManagerDefault()
    {
        $this->assertType('Waf_Model_EntityManager', $this->_model->getEntityManager());
    }

    public function testSetGetEntityManager()
    {
        $entityManager = new Waf_ModelTest_TestEntityManager();
        $this->_model->setEntityManager($entityManager);

        $this->assertEquals(
            spl_object_hash($entityManager),
            spl_object_hash($this->_model->getEntityManager())
        );
    }
}

class Waf_ModelTest_TestEntityManager extends Waf_Model_EntityManager
{
    
}

class Waf_ModelTest_TestMapper extends Waf_Model_Mapper
{
    
}

class Waf_ModelTest_TestEntity extends Waf_Model_Entity
{

}