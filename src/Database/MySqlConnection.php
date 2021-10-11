<?php

namespace Yusronarif\Core\Database;

use Illuminate\Database\MySqlConnection as BaseConnection;
//use Illuminate\Database\Schema\MySqlBuilder as BaseBuilder;
use Yusronarif\Core\Database\Schema\Blueprint;

class MySqlConnection extends BaseConnection
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
