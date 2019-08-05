<?php

namespace Yusronarif\Laravel\Database\Eloquent;

use Yusronarif\Laravel\Database\Eloquent\Concerns\HasTimestamps;
use Yusronarif\Laravel\Database\Eloquent\Scopes\GeneralScope;
use Illuminate\Database\Eloquent\Model as BaseModel;
use Illuminate\Support\Facades\DB;

class Model extends BaseModel
{
    use HasTimestamps, GeneralScope;

    /*
     * The list of table wich include with schema
     */
    protected $fullnameTable = [];

    /**
     * The name of the "created at" column.
     *
     * @var string
     */
    const CREATED_BY = 'created_by';

    /**
     * The name of the "updated at" column.
     *
     * @var string
     */
    const UPDATED_BY = 'updated_by';

    /**
     * Create a new Eloquent model instance.
     *
     * @param  array  $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $schema = DB::getDatabaseName();

        $this->fullnameTable['self'] = "{$schema}.{$this->table}";
    }
}
