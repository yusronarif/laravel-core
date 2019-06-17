<?php

namespace ArKID\Perbanas\Core\Database\Eloquent;

use ArKID\Perbanas\Core\Database\Eloquent\Concerns\HasTimestamps;
use ArKID\Perbanas\Core\Database\Eloquent\Scopes\GeneralScope;
use Illuminate\Database\Eloquent\Model as BaseModel;

class Model extends BaseModel
{
    use HasTimestamps, GeneralScope;

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
    }
}
