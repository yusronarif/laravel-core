<?php

namespace Yusronarif\Core\Database\Eloquent;

use Illuminate\Database\Eloquent\Model as ParentModel;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use Yusronarif\Core\Database\Eloquent\Concerns\HasTimestamps;
use Yusronarif\Core\Database\Eloquent\Scopes\GeneralScope;

class Model extends ParentModel
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
     * @param array $attributes
     *
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $schema = DB::getDatabaseName();

        $this->fullnameTable['self'] = "{$schema}.{$this->table}";
    }

    /**
     * Perform a model insert operation.
     *
     * @param  \Yusronarif\Core\Database\Eloquent\Builder  $query
     * @return bool
     */
    protected function performInsert(Builder $query)
    {
        if (in_array(strtolower($this->getKeyType()), ['string', 'uuid'])) {
            $this->setIncrementing(false);
            $this->setAttribute($this->getKeyName(), Uuid::uuid4()->getHex());
        }

        return parent::performInsert($query);
    }

    /**
     * Set the keys for a save update query.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    /*protected function setKeysForSaveQuery(Builder $query)
    {
        $keys = $this->getKeyName();
        if (!is_array($keys)) {
            return parent::setKeysForSaveQuery($query);
        }

        foreach ($keys as $keyName) {
            $query->where($keyName, '=', $this->getKeyForSaveQuery($keyName));
        }

        return $query;
    }*/

    /**
     * Get the primary key value for a save query.
     *
     * @return mixed
     */
    /*protected function getKeyForSaveQuery($keyName = null)
    {
        if (is_null($keyName)) {
            $keyName = $this->getKeyName();
        }

        if (isset($this->original[$keyName])) {
            return $this->original[$keyName];
        }

        return $this->getAttribute($keyName);
    }*/
}
