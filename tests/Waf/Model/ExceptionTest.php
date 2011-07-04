<?php

class Waf_Model_ExceptionTest extends PHPUnit_Framework_TestCase
{
    /**
     * 
     * @expectedException Waf_Model_Exception
     */
    function testThrowing()
    {
        throw new Waf_Model_Exception('just testing');
    }

    /**
     * Check if the Message is as expected.
     *
     */
    function testMessage()
    {
        $expected = 'just testing';
        $result = Null;
        try
        {
            throw new Waf_Model_Exception($expected);
        }
        catch (Waf_Model_Exception $e)
        {
            $result = $e->getMessage();
        }
        $this->assertEquals($expected, $result);
    }
}