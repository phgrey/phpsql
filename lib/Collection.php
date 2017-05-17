<?php

namespace PhpSql;
use PhpSql\Errors\NotImplemented;

/**
 * Class Index
 * @package PhpSql
 * In-memory data, indexed and ready to be joined using PHP instead of MySQL
 * @see Joiner::join for usage examples
 *
 */
class Collection implements Store\Readable
{
    protected $data = [];

    protected $indexes = [];


    public function getIterator()
    {
        foreach ($this->data as $key => $value) {
            yield $key => $value;
        }
    }

    public function __construct($store)
    {
        $this->data = $store;
    }

    /**
     * Returns data indexed, as a assoc array
     * @param $fields
     * @param $options
     * @return \PhpSql\Index - data indexed
     * @throws Errors\NotImplemented
     */
    public function getIndex($fields, $options = [])
    {
        return new Index($this, $options + ['fields' => $fields]);
    }




    public function values($ids = [])
    {
        if (!$ids) {
            return $this;
        } else {
            return new static(array_map(function ($id) {
                return $this->data[$id];
            }, $ids));
        }
    }
}
