<?php

namespace Yusronarif\Core\Database;

use Illuminate\Database\PostgresConnection as ParentConnection;
//use Illuminate\Database\Schema\PostgresBuilder as ParentBuilder;
use Yusronarif\Core\Database\Schema\Blueprint;

class PgSqlConnection extends ParentConnection
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
