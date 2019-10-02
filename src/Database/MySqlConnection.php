<?php

namespace Yusronarif\Core\Database;

use Illuminate\Database\MySqlConnection as ParentConnection;
//use Illuminate\Database\Schema\MySqlBuilder as ParentBuilder;
use Yusronarif\Core\Database\Schema\Blueprint;

class MySqlConnection extends ParentConnection
{
    public function getSchemaBuilder()
    {
        if (is_null($this->schemaGrammar)) {
            $this->useDefaultSchemaGrammar();
        }

        $builder = parent::getSchemaBuilder();
        $builder->blueprintResolver(function ($table, $callback) {
            return new Blueprint($table, $callback);
        });

        return $builder;
    }
}
