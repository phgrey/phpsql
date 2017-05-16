<?php

namespace PhpSql\Index;

use \PhpSql\Collection;

abstract class Base
{
    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var [string]
     */
    protected $fields;


    public function __construct(Collection $collection, $fields)
    {
        $this->collection = $collection;


        $this->fields = $fields;

        $this->hash = static::build($collection, $fields);
    }


    public function collection()
    {
        return $this->collection;
    }

    abstract public function serialize();

    abstract public function name();
}