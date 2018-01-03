<?php

namespace panlatent\setting;

use panlatent\setting\models\Setting as Model;
use Yii;
use yii\base\Component;

class Setting extends Component
{
    /**
     * @var bool Autoload on the component init.
     */
    public $autoload = true;
    /**
     * @var bool Use cache when the value is true.
     */
    public $withCache = true;
    /**
     * @var array
     */
    public $lastErrors = [];
    /**
     * @var Cache
     */
    private $_cache;

    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        parent::init();

        if ($this->autoload) {
            $this->autoload();
        }
    }

    /**
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function all()
    {
        if ($this->withCache) {
            $models = $this->findWithoutCached();
            $models = array_merge($models, $this->getCache()->all());
        } elseif ($this->autoload) {
            $models = $this->findWithoutAutoloaded();
            $models = array_merge($models, $this->getCache()->all());
        } else {
            $models = Model::findAll([]);
        }

        $values = [];
        foreach ($models as $model) {
            if ( ! isset($values[$model->group])) {
                $values[$model->group] = [];
            }
            $values[$model->group][$model->name] = $model->value;
        }

        return $values;
    }

    /**
     * @param string $name
     * @param mixed  $value
     * @param string $group
     * @param string $defaultValue
     * @param null   $rule
     * @return bool
     */
    public function add($name, $value, $group = '', $defaultValue = '', $rule = null)
    {
        $model = new Model();
        $model->name = $name;
        $model->group = $group;
        $model->value = $value;
        $model->defaultValue = $defaultValue;
        $model->rule = $rule;

        if ( ! $model->save()) {
            var_dump($model->getErrors());
            return false;
        }

        return true;
    }

    /**
     * @param string $name
     * @param string $group
     * @param mixed  $defaultValue
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function get($name, $group = '', $defaultValue = null)
    {
        if (false === ($model = $this->getModel($name, $group))) {
            return $defaultValue;
        }

        return $model->value;
    }

    /**
     * @param string $name
     * @param string $group
     * @return Model|bool
     * @throws \yii\base\InvalidConfigException
     */
    public function has($name, $group = '')
    {
        if ($this->withCache && $this->getCache()->has($name, $group)) {
            return true;
        }
        if ( ! ($model = Model::findOne(['name' => $name, 'group' => $group]))) {
            return false;
        }
        if ($this->withCache) {
            $this->getCache()->set($model);
        }

        return true;
    }

    /**
     * @param string $name
     * @param mixed  $value
     * @param string $group
     * @param null   $rule
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    public function set($name, $value, $group = '', $rule = null)
    {
        if (false === ($model = $this->getModel($name, $group))) {
            return $this->add($name, $value, $group, $rule);
        }

        $model->value = $value;
        $model->rule = $rule;

        if ( ! $model->save()) {
            var_dump($model->getErrors());
            return false;
        }

        return true;
    }

    /**
     * @param string $name
     * @param string $group
     * @return bool|false|int
     * @throws \Exception
     * @throws \Throwable
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\StaleObjectException
     */
    public function remove($name, $group)
    {
        if (false === ($model = $this->getModel($name, $group))) {
            return false;
        }

        return $model->delete();
    }

    /**
     * @return Cache
     * @throws \yii\base\InvalidConfigException
     */
    public function getCache()
    {
        if ($this->_cache === null) {
            $this->setCache([
                'class' => Cache::className(),
            ]);
        }

        return $this->_cache;
    }

    /**
     * @param mixed $cache
     * @throws \yii\base\InvalidConfigException
     */
    public function setCache($cache)
    {
        if ( ! is_object($cache)) {
            $cache = Yii::createObject($cache);
        }
        $this->_cache = $cache;
    }

    /**
     * @throws \yii\base\InvalidConfigException
     */
    protected function autoload()
    {
        $models = Model::findAll(['autoload' => '1']);
        /** @var Model $model */
        foreach ($models as $model) {
            $this->getCache()->set($model);
        }
    }

    /**
     * @param string $name
     * @param string $group
     * @return bool|Model
     * @throws \yii\base\InvalidConfigException
     */
    protected function getModel($name, $group)
    {
        if ($this->withCache && $this->getCache()->has($name, $group)) {
            return $this->getCache()->get($name, $group);
        }
        if ( ! ($model = Model::findOne(['name' => $name, 'group' => $group]))) {
            return false;
        }
        if ($this->withCache) {
            $this->getCache()->set($model);
        }

        return $model;
    }

    /**
     * @return Model[]
     * @throws \yii\base\InvalidConfigException
     */
    protected function findWithoutCached()
    {
        $ids = [];
        foreach ($this->getCache() as $setting) {
            $ids[] = $setting->id;
        }

        return Model::find()->where(['not in', 'id', $ids])->all();
    }

    /**
     * @return Model[]
     */
    protected function findWithoutAutoloaded()
    {
        return Model::find()->where(['autoload' => '0'])->all();
    }
}