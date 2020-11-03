<?php

namespace Yusronarif\Core\Database\Schema;

use App\Models\User;
use Closure;
use Illuminate\Database\Schema\Blueprint as BaseBlueprint;
use Illuminate\Support\Facades\Schema;
use Yusronarif\Core\Support\Str;

class Blueprint extends BaseBlueprint
{
    private $tableUser = '';

    /**
     * Create a new schema blueprint.
     *
     * @param string        $table
     * @param \Closure|null $callback
     * @param string        $prefix
     *
     * @return void
     */
    public function __construct($table, Closure $callback = null, $prefix = '')
    {
        parent::__construct($table, $callback, $prefix);

        $userModel = config('yusronarifCore.model.users');
        $this->tableUser = (new $userModel)->getTable();
    }

    /**
     * Add nullable creation and update timestamps to the table.
     *
     * @param int $precision
     *
     * @return void
     */
    public function timestamps($precision = 0)
    {
        $this->timestamp('created_at', $precision)->nullable();
        $this->foreignId('created_by')->constrained($this->tableUser)->nullable();

        $this->timestamp('updated_at', $precision)->nullable();
        $this->foreignId('updated_by')->constrained($this->tableUser)->nullable();
    }

    /**
     * Add a "deleted at" timestamp for the table.
     *
     * @param string $column
     * @param int    $precision
     *
     * @return \Illuminate\Database\Schema\ColumnDefinition
     */
    public function softDeletes($column = 'deleted_at', $precision = 0)
    {
        $this->timestamp($column, $precision)->nullable();
        $this->foreignId('deleted_by')->constrained($this->tableUser)->nullable();
        $this->timestamp('restore_at', $precision)->nullable();
        $this->foreignId('restore_by')->constrained($this->tableUser)->nullable();
    }

    /**
     * @param $fields
     * @param null $connection
     *
     * @return \Illuminate\Database\Schema\ColumnDefinition
     */
    public function setFields($fields, $connection = null)
    {
        if (is_array($fields) && count($fields) > 0) {
            foreach ($fields as $field => $attr) {
                if (!Schema::connection($connection)->hasColumn($this->getTable(), $field)) {
                    $type = preg_replace('/\s+/i', '', $attr['type']) ?: 'string';

                    if (strpos($type, ':', 1)) {
                        $epl = explode(':', $type);
                        $type = $epl[0];
                        $length = $epl[1];

                        $t = $this->{$type}($field, ...explode(',', $length));
                    } else {
                        $t = $this->{$type}($field);
                    }

                    if (isset($attr['null'])) {
                        $t->nullable((bool) $attr['null']);
                    }
                    if (isset($attr['default'])) {
                        $t->default($attr['default']);
                    }
                    if (isset($attr['comment'])) {
                        $t->comment($attr['comment']);
                    }
                }
            }
        }
    }

    /**
     * @param $foreigns
     * @param null $connection
     *
     * @return \Illuminate\Database\Schema\ColumnDefinition
     */
    public function setForeigns($foreigns, $connection = null)
    {
        if (is_array($foreigns) && count($foreigns) > 0) {
            $currentTable = $this->getTable();
            $fullTable = config('database.current_schema').'.'.$this->getTable();
            $fullTable = Str::forceSnake($fullTable);

            $sm = Schema::connection($connection)->getConnection()->getDoctrineSchemaManager();
            $fkeys = array_map(function ($a) {
                return $a->getName();
            }, $sm->listTableForeignKeys($this->getTable()));

            foreach ($foreigns as $tbl => $tblAttr) {
                foreach ($tblAttr as $key => $ref) {
                    if (!is_array($ref)) {
                        $vals['reference'] = $ref;
                    } else {
                        $vals = $ref;
                    }

                    if (!in_array("{$currentTable}_{$key}_foreign", $fkeys) && !in_array("{$fullTable}_{$key}_foreign", $fkeys)) {
                        $this->foreign($key)->references($vals['reference'])->on($tbl)
                            ->onUpdate(isset($vals['onUpdate']) ? $vals['onUpdate'] : 'cascade')
                            ->onDelete(isset($vals['onDelete']) ? $vals['onDelete'] : 'restrict');
                    }
                }
            }
        }
    }
}
