<?php

namespace ArKID\Perbanas\Core\Providers;

use ArKID\Perbanas\Core\Database\PgSqlConnection;
use Illuminate\Database\Connection;
use Illuminate\Support\ServiceProvider;

class PgsqlServiceProvider extends ServiceProvider
{

    public function register()
    {
        Connection::resolverFor('pgsql', function ($connection, $database, $prefix, $config) {
            return new PgSqlConnection($connection, $database, $prefix, $config);
        });
    }

}
