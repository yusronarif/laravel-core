<?php

namespace Yusronarif\Core\Database\Eloquent;

use Illuminate\Database\Eloquent\Builder as ParentBuilder;
use Yusronarif\Core\Database\Query\Builder as QueryBuilder;

class Builder extends ParentBuilder
{
    /**
     * Create a new Eloquent query builder instance.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @return void
     */
    public function __construct(QueryBuilder $query)
    {
        parent::__construct($query);
    }
}
