<?php

namespace ArKID\Perbanas\Core\Database;

use ArKID\Perbanas\Core\Database\Schema\Blueprint;
use Illuminate\Database\PostgresConnection;
use Illuminate\Database\Schema\PostgresBuilder;

class PgSqlConnection extends PostgresConnection
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
