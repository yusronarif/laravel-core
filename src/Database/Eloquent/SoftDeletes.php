<?php

namespace ArKID\Perbanas\Core\Database\Eloquent;

use Illuminate\Database\Eloquent\SoftDeletes as BaseSoftDeletes;

trait SoftDeletes
{
    use BaseSoftDeletes;

    protected $who = 'By System';

    /**
     * Perform the actual delete query on this model instance.
     *
     * @return void
     */
    protected function runSoftDelete()
    {
        $query = $this->setKeysForSaveQuery($this->newModelQuery());

        if (auth()->check()) {
            $this->who = auth()->user()->name;
        }

        $time = $this->freshTimestamp();

        $columns = [
            $this->getDeletedAtColumn() => $this->fromDateTime($time),
            $this->getDeletedByColumn() => $this->who
        ];

        $this->{$this->getDeletedAtColumn()} = $time;
        $this->{$this->getDeletedByColumn()} = $this->who;

        if ($this->timestamps && ! is_null($this->getUpdatedAtColumn())) {
            $this->{$this->getUpdatedAtColumn()} = $time;
            $this->{$this->getUpdatedByColumn()} = $this->who;

            $columns[$this->getUpdatedAtColumn()] = $this->fromDateTime($time);
            $columns[$this->getUpdatedByColumn()] = $this->who;
        }

        $query->update($columns);
    }

    /**
     * Restore a soft-deleted model instance.
     *
     * @return bool|null
     */
    public function restore()
    {
        // If the restoring event does not return false, we will proceed with this
        // restore operation. Otherwise, we bail out so the developer will stop
        // the restore totally. We will clear the deleted timestamp and save.
        if ($this->fireModelEvent('restoring') === false) {
            return false;
        }

        if (auth()->check()) {
            $this->who = auth()->user()->name;
        }

        $this->{$this->getDeletedAtColumn()} = null;
        $this->{$this->getDeletedByColumn()} = null;
        $this->{$this->getRestoreAtColumn()} = $this->freshTimestamp();
        $this->{$this->getRestoreByColumn()} = $this->who;

        // Once we have saved the model, we will fire the "restored" event so this
        // developer will do anything they need to after a restore operation is
        // totally finished. Then we will return the result of the save call.
        $this->exists = true;

        $result = $this->save();

        $this->fireModelEvent('restored', false);

        return $result;
    }

    /**
     * Get the name of the "deleted by" column.
     *
     * @return string
     */
    public function getDeletedByColumn()
    {
        return defined('static::DELETED_BY') ? static::DELETED_BY : 'deleted_by';
    }

    /**
     * Get the name of the "deleted by" column.
     *
     * @return string
     */
    public function getRestoreAtColumn()
    {
        return defined('static::RESTORE_AT') ? static::RESTORE_AT: 'restore_at';
    }

    /**
     * Get the name of the "deleted by" column.
     *
     * @return string
     */
    public function getRestoreByColumn()
    {
        return defined('static::RESTORE_BY') ? static::RESTORE_BY : 'restore_by';
    }

    /**
     * Get the fully qualified "deleted by" column.
     *
     * @return string
     */
    public function getQualifiedDeletedByColumn()
    {
        return $this->qualifyColumn($this->getDeletedByColumn());
    }

    /**
     * Get the fully qualified "restore at" column.
     *
     * @return string
     */
    public function getQualifiedRestoreAtColumn()
    {
        return $this->qualifyColumn($this->getRestoreAtColumn());
    }

    /**
     * Get the fully qualified "restore by" column.
     *
     * @return string
     */
    public function getQualifiedRestoreByColumn()
    {
        return $this->qualifyColumn($this->getRestoreByColumn());
    }
}
