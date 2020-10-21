<?php

namespace Yusronarif\Core\Database\Eloquent;

use Illuminate\Database\Eloquent\Builder as BaseBuilder;
use Yusronarif\Core\Database\Query\Builder as QueryBuilder;

class Builder extends BaseBuilder
{
    public function __construct(\Illuminate\Database\Query\Builder $query)
    {
        parent::__construct($query);
    }
}
