<?php

namespace Yusronarif\Core\Database\Eloquent\Concerns;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps as BaseHasTimestamps;

trait HasTimestamps
{
    use BaseHasTimestamps;

    protected $who = 'By System';

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
            $this->who = auth()->user()->name;
        }

        $this->{static::CREATED_AT} = $value;
        $this->{static::CREATED_BY} = $this->who;

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
            $this->who = auth()->user()->name;
        }

        $this->{static::UPDATED_AT} = $value;
        $this->{static::UPDATED_BY} = $this->who;

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
