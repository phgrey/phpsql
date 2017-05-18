<?php

namespace PhpSql;

/**
 * Class Index
 * @package PhpSql
 * In-memory data, indexed and ready to be joined using PHP instead of MySQL
 * @see Joiner::join for usage examples
 *
 */
class Collection implements Store\Readable, \IteratorAggregate
{
    protected $data = [];

    protected $indexes = [];


    public function __construct($store)
    {
        $this->data = $store;
    }

    public function getIterator()
    {
        foreach ($this->values() as $key => $value) {
            yield $key => $value;
        }
    }

    /**
     * Returns data indexed, as a assoc array
     * @param $fields
     * @param $options
     * @return \PhpSql\Index\Column - data indexed
     * @throws Errors\NotImplemented
     */
    public function index($fields, $options = [])
    {
        return new Index\Column($this, $options + ['fields' => $fields]);
    }

    public function values($ids = [])
    {
        if (!$ids) {
            return $this->data;
        } else {
            return array_map(function ($id) {
                return $this->get($id);
            }, $ids);
        }
    }

    protected function get($id)
    {
        return $this->data[$id];
    }
}
