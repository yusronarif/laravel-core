<?php

namespace Yusronarif\Core\Database\Eloquent;

use Illuminate\Database\Eloquent\Model as BaseModel;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use Yusronarif\Core\Database\Eloquent\Concerns\HasTimestamps;
use Yusronarif\Core\Database\Eloquent\Scopes\GeneralScope;

class Model extends BaseModel
{
    use HasTimestamps, GeneralScope;

    /*
     * The list of table wich include with schema
     */
    protected $fullnameTable = [];

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
     * Get the value indicating whether the IDs are incrementing.
     *
     * @return bool
     */
    public function getIncrementing()
    {
        if (in_array(strtolower($this->getKeyType()), ['string', 'uuid'])) {
            return false;
        }
        return $this->incrementing;
    }

    /**
     * Perform a model insert operation.
     *
     * @param  Builder  $query
     * @return bool
     */
    protected function performInsert(Builder $query)
    {
        if (in_array(strtolower($this->getKeyType()), ['string', 'uuid'])) {
            $this->setIncrementing(false);
            $this->setAttribute($this->getKeyName(), (string) Uuid::uuid4()->getHex());
        }

        return parent::performInsert($query);
    }

    /**
     * Set the keys for a save update query.
     *
     * @param Builder $query
     *
     * @return Builder
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
