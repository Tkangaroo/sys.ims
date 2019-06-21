<?php
/**
 * Created by PhpStorm.
 * User: speauty
 * Date: 2019/6/21
 * Time: 16:16
 */
namespace Lib;

namespace Lib;

class OSDi
{
    private $container = [];

    private static $instance;

    /**
     * 单例入口
     * @return SDi
     */
    public static function getInstance()
    {
        if(!isset(self::$instance)){
            self::$instance = new static();
        }
        return self::$instance;
    }

    /**
     * @param string $msg
     * @throws Exception
     */
    private function throw(string $msg):void
    {
        throw new Exception($msg);
    }

    /**
     * @param string $key
     * @return bool
     */
    public function exists(string $key):bool
    {
        return isset($this->container[$key]);
    }

    /**
     * @param string $key
     * @param string $className
     * @param bool $isForceCover
     * @param bool $directNew
     * @param mixed ...$arg
     * @throws Exception
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
     * @throws Exception
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
     * @param string $key
     * @param mixed ...$args
     * @return mixed
     * @throws Exception
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

    public function remove(string $key):void
    {
        if ($this->exists($key)) unset($this->container[$key]);
    }

    public function flush():void
    {
        $this->container = [];
    }

    public function test():void
    {
        var_dump('this is OSDi class');
    }
}
