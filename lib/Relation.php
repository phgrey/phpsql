<?php

namespace PhpSql;

use \PhpSql\Store\Indexer;

/**
 * Class Index
 * @package PhpSql
 * In-memory data, indexed and ready to be joined using PHP instead of MySQL
 * @see Joiner::join for usage examples
 *
 */
class Relation
{
    protected $data = [];

    /**
     * @var [Store\Joinable]
     */
    protected $indexers = [];


    public function join($type = 'left')
    {
        foreach ($this->keys() as $key) {
            $row = $this->values($key);
            if ($this->checkJoin($row, $type)) {
                yield $key => $row;
            }
        }
    }

    protected function checkJoin($row, $type)
    {
        if ($type == 'left') {
            return count($row[0]) > 0;
        } elseif ($type == 'right') {
            return count($row[count($row) - 1]) > 0;
        } elseif ($type == 'inner') {
            return count(array_filter($row)) == count($row);
        } elseif ($type == 'outer') {
            return count(array_filter($row)) > 0;
        }
        throw new \Exception('Do not know how to build join type: '.$type);
    }

    public static function multiple($row)
    {
        $count = 1;
        foreach ($row as &$cell) {
            $cell = $cell || [null];
            $count *= count($cell);
        }

        $ret = [];
        for ($i = 0; $i < $count; $i++) {
            $ret[] = array_map(function ($cell) use ($i) {
                return $cell[$i % count($cell)];
            }, $row);
        }
        return $ret;
    }

    protected function keys()
    {
        $list = array_reduce($this->indexers, function ($memo, Indexer $index) {
            return array_merge($memo, $index->keys());
        }, []);
        return array_unique($list);
    }

    protected function values($key)
    {
        return array_map(function (Indexer $ind) use ($key) {
            return $ind->values($key);
        }, $this->indexers);
    }

    public function __construct($indexers)
    {
        $this->indexers = $indexers;
    }


}
