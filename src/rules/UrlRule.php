<?php

namespace yiithings\setting\rules;

use yiithings\setting\Rule;

class UrlRule extends Rule
{
    public function rules()
    {
        return [
            ['url', 'defaultScheme' => 'http'],
        ];
    }
}