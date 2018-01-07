<?php


namespace yiithings\setting;


interface DefinitionInterface
{
    public static function bind(DefinableModel $model, DefinitionInterface $definition);

    public function bindTo(DefinableModel $model);

    /**
     * Returns the named attribute value.
     *
     * @param string $name
     * @return mixed
     */
    public function getAttribute($name);

    /**
     * Sets the named attribute value.
     *
     * @param string $name
     * @param mixed  $value
     */
    public function setAttribute($name, $value);
}