<?php

namespace ArKID\Perbanas\Core\Database\Schema;

use DB;
use Closure;
use Illuminate\Database\Schema\Blueprint as BaseBlueprint;

class Blueprint extends BaseBlueprint
{
    /**
     * Create a new schema blueprint.
     *
     * @param  string  $table
     * @param  \Closure|null  $callback
     * @param  string  $prefix
     * @return void
     */
    public function __construct($table, Closure $callback = null, $prefix = '')
    {
        parent::__construct($table, $callback, $prefix);
    }

    /**
     * Add nullable creation and update timestamps to the table.
     *
     * @param  int  $precision
     * @return void
     */
    public function timestamps($precision = 0)
    {
        $this->timestamp('created_at', $precision)->nullable();
        $this->string('created_by', 50)->nullable();

        $this->timestamp('updated_at', $precision)->nullable();
        $this->string('updated_by', 50)->nullable();
    }

    /**
     * Add a "deleted at" timestamp for the table.
     *
     * @param  string  $column
     * @param  int  $precision
     * @return \Illuminate\Database\Schema\ColumnDefinition
     */
    public function softDeletes($column = 'deleted_at', $precision = 0)
    {
        $this->timestamp($column, $precision)->nullable();
        $this->string('deleted_by', 50)->nullable();
        $this->timestamp('restore_at', $precision)->nullable();
        $this->string('restore_by', 50)->nullable();
    }

}
