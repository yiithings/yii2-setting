<?php


namespace yiithings\setting;

use yii\base\Component;
use yiithings\setting\models\Setting as Model;

class Cache extends Component implements \Iterator
{
    /**
     * @var Model[]
     */
    protected $store = [];

    public function all()
    {
        return array_values($this->store);
    }

    public function add(Model $model)
    {
        $key = $this->getKey($model->name, $model->group);
        if ( ! $this->has($model->name, $model->group)) {
            $this->store[$key] = $model;
        }
    }

    public function get($name, $group = '')
    {
        $key = $this->getKey($name, $group);

        return $this->store[$key];
    }

    public function has($name, $group = '')
    {
        $key = $this->getKey($name, $group);

        return isset($this->store[$key]);
    }

    public function set(Model $model)
    {
        $key = $this->getKey($model->name, $model->group);
        $this->store[$key] = $model;
    }

    public function remove($name, $group = '')
    {
        $key = $this->getKey($name, $group);
        unset($this->store[$key]);
    }

    public function flush()
    {
        $this->store = [];
    }

    public function current()
    {
        return current($this->store);
    }

    public function next()
    {
        return next($this->store);
    }

    public function key()
    {
        return key($this->store);
    }

    public function valid()
    {
        return key($this->store) !== null;
    }

    public function rewind()
    {
        return reset($this->store);
    }

    protected function getKey($name, $group)
    {
        return $name . '@' . $group;
    }
}