<?php

namespace Yusronarif\Laravel\Database;

use Yusronarif\Laravel\Database\Schema\Blueprint;
use Illuminate\Database\MySqlConnection as ParentConnection;
//use Illuminate\Database\Schema\MySqlBuilder as ParentBuilder;

class MySqlConnection extends ParentConnection
{
    public function getSchemaBuilder()
    {
        if(is_null($this->schemaGrammar)) {
            $this->useDefaultSchemaGrammar();
        }

        $builder = parent::getSchemaBuilder();
        $builder->blueprintResolver(function ($table, $callback) {
            return new Blueprint($table, $callback);
        });

        return $builder;
    }

}
