<?php

namespace Yusronarif\Core\Database\Query;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Query\Builder as ParentBuilder;
use Illuminate\Database\Query\Grammars\Grammar;
use Illuminate\Database\Query\Processors\Processor;

class Builder extends ParentBuilder
{
    /**
     * Create a new query builder instance.
     *
     * @param  \Illuminate\Database\ConnectionInterface  $connection
     * @param  \Illuminate\Database\Query\Grammars\Grammar|null  $grammar
     * @param  \Illuminate\Database\Query\Processors\Processor|null  $processor
     * @return void
     */
    public function __construct(ConnectionInterface $connection,
                                Grammar $grammar = null,
                                Processor $processor = null)
    {
        parent::__construct($connection, $grammar, $processor);
    }
}
