<?php

namespace yiithings\setting\definitions;

use yiithings\setting\Definition;

class Url extends Definition
{
    public function rules()
    {
        return [
            ['url', 'defaultScheme' => 'http'],
        ];
    }
}