<?php

namespace PhpSql;

/**
 * Class Index
 * @package PhpSql
 * In-memory data, indexed and ready to be joined using PHP instead of MySQL
 * @see Joiner::join for usage examples
 *
 */
class Collection extends Store\Indexable
{

    public function values($ids = [])
    {
        if (!$ids) {
            return $this->data;
        } else {
            return array_map(function ($id) {
                return is_null($id) ? $this->null() : $this->get($id);
            }, $ids);
        }
    }

    protected function get($id)
    {
        return $this->data[$id];
    }
}
