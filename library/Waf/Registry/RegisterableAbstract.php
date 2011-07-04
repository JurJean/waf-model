<?php
/**
 * Provides an abstract for implementing Waf_Registry_RegisterableInterface
 *
 * Simulates late static binding under PHP 5.2. May be a performance issue,
 * in which case direct implementation of Waf_Registry_RegisterableInterface
 * should be preferred
 * 
 * @category   Waf
 * @package    Waf_Registry
 * @subpackage Registerable
 * @version    $Id: RegisterableAbstract.php 34 2010-01-27 12:39:17Z rick $
 */
abstract class Waf_Registry_RegisterableAbstract implements Waf_Registry_RegisterableInterface
{
    /**
     * Enters the object in the registry, using the name of the class as the index.
     */
    public function register()
    {
        Waf_Registry::set(get_class($this), $this);
    }

    /**
     * Get the unique instance of the Object from the Registry.
     *
     * @return mixed - the object
     * @throws Waf_Exception if the object is not in the registry.
     */
    public static function getRegistered()
    {
        $class = self::get_called_class();

        if (Waf_Registry::isRegistered($class)) {
            return Waf_Registry::get($class);
        } else {
            throw new Waf_Exception($class . ' not registered');
        }
    }

    /**
     * Check if the Object is in the Registry.
     *
     * @return bool
     */
    public static function isRegistered()
    {
        $class = self::get_called_class();
        return Waf_Registry::isRegistered($class);
    }
    

    /**
     * Fakes late static binding call 'get_called_class()'
     * for PHP 5.2
     * 
     * Stolen from http://nl.php.net/manual/en/function.get-called-class.php#92845
     * 
     * @return string
     */
    private static function get_called_class()
    {
        if (!function_exists('get_called_class')) {
            $bt = debug_backtrace();
            $l = 0;
            do {
                $l++;
                $lines = file($bt[$l]['file']);
                $callerLine = $lines[$bt[$l]['line']-1];
                preg_match(
                    '/([a-zA-Z0-9\_]+)::'.$bt[$l]['function'].'/',
                    $callerLine,
                    $matches
                );
                          
                if ($matches[1] == 'self') {
                    $line = $bt[$l]['line']-1;
                    while ($line > 0 && strpos($lines[$line], 'class') === false) {
                       $line--;                  
                    }
                    preg_match('/class[\s]+(.+?)[\s]+/si', $lines[$line], $matches);
                }
            } while ($matches[1] == 'parent'  && $matches[1]);
            return $matches[1];
        } else {
            return get_called_class();
        }
    }
}
