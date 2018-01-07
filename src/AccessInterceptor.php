<?php

namespace yiithings\setting;

/**
 * Trait AccessInterceptor
 *
 * @package yiithings\setting
 */
trait AccessInterceptor
{
    public function __get($name)
    {
        $getter = 'get' . $name;
        if (method_exists($this, $getter)) {
            return $this->$getter();
        }

        return parent::__get($name);
    }

    public function __set($name, $value)
    {
        $setter = 'set' . $name;
        if (method_exists($this, $setter)) {
            $this->$setter($value);

            return;
        }
        parent::__set($name, $value);
    }

    public function __unset($name)
    {
        $setter = 'set' . $name;
        if (method_exists($this, $setter)) {
            $this->$setter(null);
            return;
        }
        parent::__unset($name);
    }
}