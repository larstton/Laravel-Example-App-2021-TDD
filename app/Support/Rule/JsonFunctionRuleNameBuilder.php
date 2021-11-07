<?php

namespace App\Support\Rule;

use App\Models\Rule;
use Illuminate\Support\Str;

class JsonFunctionRuleNameBuilder extends RuleNameBuilder
{
    protected $listKey;
    protected $searchKey;
    protected $keyFunction;
    protected $keyArgument;
    protected $lastValue = null;

    public function __construct(Rule $rule)
    {
        list($field, $key) = explode('@', $rule->check_key);
        $this->listKey = Str::lower($field);
        $this->searchKey = Str::lower($key);

        $this->keyFunction = $rule->key_function['key'] ?? null;
        $this->keyArgument = $rule->key_function['value'] ?? null;

        parent::__construct($rule);
    }

    public function getListKey()
    {
        return $this->listKey;
    }

    public function getSearchKey()
    {
        return $this->searchKey;
    }

    public function getLastValue(object $measurements)
    {
        return $this->lastValue;
    }

    public function getProblemKey()
    {
        return parent::getProblemKey().':'.$this->keyFunction;
    }

    public function getKeyFunction()
    {
        return $this->keyFunction;
    }

    public function getKeyArgument()
    {
        return $this->keyArgument;
    }
}
