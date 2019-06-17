<?php

namespace Yusronarif\Laravel\Providers;

use Yusronarif\Laravel\Database\MySqlConnection;
use Illuminate\Database\Connection;
use Illuminate\Support\ServiceProvider;

class MysqlServiceProvider extends ServiceProvider
{

    public function register()
    {
        Connection::resolverFor('mysql', function ($connection, $database, $prefix, $config) {
            return new MySqlConnection($connection, $database, $prefix, $config);
        });
    }

}
