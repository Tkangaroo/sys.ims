<?php
/**
 * Created by PhpStorm.
 * User: speauty
 * Date: 2019/6/21
 * Time: 15:13
 */

namespace Lib;


class Import
{
    private $namespace;

    public function __construct()
    {
        $this->setNamespace();
    }

    public function setNamespace(string $namespace = 'App'):void
    {
        $this->namespace = $namespace;
    }

    public function exists(string $className):bool
    {
        return class_exists($className);
    }

    public function get(string $className, $initialArg = null, string $namespace = 'App')
    {
        if ($namespace) $className = $namespace.'/'.$className;
        if ($this->exists($className)) throw new \Exception($className.' class not found!');
        return is_null($initialArg)?new $className():new $className($initialArg);
    }
}