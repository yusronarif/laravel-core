<?php

namespace Yusronarif\Core\Database\Eloquent\Concerns;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps as BaseHasTimestamps;

trait HasTimestamps
{
    use BaseHasTimestamps;

    /**
     * Set the value of the "created at" attribute.
     *
     * @param mixed $value
     *
     * @return $this
     */
    public function setCreatedAt($value)
    {
        if (auth()->check()) {
            $this->unknownPerformer = auth()->user()->name;
        }

        $this->{static::CREATED_AT} = $value;
        $this->{static::CREATED_BY} = $this->unknownPerformer;

        return $this;
    }

    /**
     * Set the value of the "updated at" attribute.
     *
     * @param mixed $value
     *
     * @return $this
     */
    public function setUpdatedAt($value)
    {
        if (auth()->check()) {
            $this->unknownPerformer = auth()->user()->name;
        }

        $this->{static::UPDATED_AT} = $value;
        $this->{static::UPDATED_BY} = $this->unknownPerformer;

        return $this;
    }

    /**
     * Get the name of the "created by" column.
     *
     * @return string
     */
    public function getCreatedByColumn()
    {
        return static::CREATED_BY;
    }

    /**
     * Get the name of the "updated by" column.
     *
     * @return string
     */
    public function getUpdatedByColumn()
    {
        return static::UPDATED_BY;
    }
}
