<?php

namespace Yusronarif\Laravel\Providers;

use Illuminate\Database\Connection;
use Illuminate\Support\ServiceProvider;
use Yusronarif\Laravel\Database\PgSqlConnection;

class PgsqlServiceProvider extends ServiceProvider
{

    public function register()
    {
        Connection::resolverFor('pgsql', function ($connection, $database, $prefix, $config) {
            return new PgSqlConnection($connection, $database, $prefix, $config);
        });
    }

}
