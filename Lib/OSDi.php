<?php
/**
 * Created by PhpStorm.
 * User: Speauty
 * Date: 2019/6/21
 * Time: 16:16
 */
namespace Lib;


/**
 * Class OSDi
 * this is a container class named OSDi follow Di.
 * to support me to use namespace without the es frameset,
 * and I can define a class not only in the App directory,
 * like creating a directory named Lib to store some extensions which are import to me,
 * so, the is a personal tool, you have no privilege to modify!
 * @package Lib
 */
class OSDi
{
    private $container = [];

    private static $instance;

    /**
     * the only entrance of this class
     * @return OSDi
     */
    public static function getInstance():OSDi
    {
        if(!isset(self::$instance) || !self::$instance instanceof OSDi){
            self::$instance = new static();
        }
        return self::$instance;
    }

    /**
     * throw the exception
     * @param string $msg
     * @throws \Exception
     */
    private function throw(string $msg):void
    {
        throw new \Exception($msg);
    }

    /**
     * to check the key in the current container
     * @param string $key
     * @return bool
     */
    public function exists(string $key):bool
    {
        return isset($this->container[$key]);
    }

    /**
     * to inject the class needed
     * @param string $key
     * @param string $className
     * @param bool $isForceCover
     * @param bool $directNew
     * @param mixed ...$args
     * @throws \Exception
     */
    public function set(string $key, string $className, $isForceCover = false, $directNew = false, ...$args):void
    {
        if (!isset($this->container[$key]) || $isForceCover) {
            $this->container[$key] = [
                'class' => $className,
                'obj' => null,
                'params' => $args
            ];
            if ($directNew) $this->build($key);
        }
    }

    /**
     * @param string $key
     * @throws \Exception
     */
    public function build(string $key)
    {
        if (!$this->exists($key)) {
            $this->throw('the '.$key.' not found');
        }
        $current = &$this->container[$key];
        if (!(is_object($current['obj']) || is_callable($current['obj']))) {
            if (class_exists($current['class'])) {
                $current['obj'] = new $current['obj'](...$current['params']);
            } else {
                $this->throw('the class '.$current['class'].' not found');
            }
        }
    }

    /**
     * to get the class from a key with some arguments which to new one object
     * @param string $key
     * @param mixed ...$args
     * @return mixed
     * @throws \Exception
     */
    public function get(string $key, ...$args)
    {
        if ($this->exists($key)) {
            $current = $this->container[$key];
            /** 如果带有参数或者没有实例化可能要重新实例化一波 */
            if ($args || !$current['obj']) {
                if ($args) $current['params'] = $args;
                $current['obj'] = null;
                $this->build($key);
            }
            $current = $this->container[$key];
            return $current['obj'];
        } else {
            $this->throw('the '.$key.' not found');
        }
    }

    /**
     * to remove a object on the basis of a key in current container
     * @param string $key
     */
    public function remove(string $key):void
    {
        if ($this->exists($key)) unset($this->container[$key]);
    }

    /**
     * flush the container
     */
    public function flush():void
    {
        $this->container = [];
    }

    /**
     * test the container run correctly which no return
     */
    public function test():void
    {
        var_dump('this is OSDi class');
    }
}
