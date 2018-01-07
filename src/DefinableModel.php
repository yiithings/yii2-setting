<?php

namespace yiithings\setting;

interface DefinableModel
{
    /**
     * Gets definition object.
     *
     * @return DefinitionInterface
     */
    public function getDefinition();

    /**
     * Sets definition object.
     *
     * @param DefinitionInterface $definition
     * @return mixed
     */
    public function setDefinition($definition);

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