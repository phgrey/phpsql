<?php

namespace PhpSql\Index\Back;

use PhpSql\Collection;
use PhpSql\Index\Back\Indexer;

/**
 * Class Relation is designed to implement mysql's standard join behaviour
 * for different join types and rows multiple
 * @package PhpSql\Index\Back
 */
class Relation implements Indexer
{
    protected $data = [];

    /**
     * @var [Store\Joinable]
     */
    protected $indexers = [];

    /**
     * @var [Collection]
     */
    protected $collections = [];


    protected $join_type;


    public function __construct($indexers, $collections, $type)
    {
        $this->indexers = $indexers;
        $this->collections = $collections;
        $this->join_type = $type;
    }

    public function keys()
    {
        $list = array_reduce($this->indexers, function ($memo, Indexer $index) {
            return array_merge($memo, $index->keys());
        }, []);
        return array_unique($list);
    }

    public function row($key)
    {
        return array_map(function (Indexer $ind) use ($key) {
            return $ind->values([$key]);
        }, $this->indexers);
    }

    /**
     * @param $indexers
     * @param array|Collection $collections - will be here if needed later
     */
    public function add($indexers, $collections = [])
    {
        $this->indexers = array_merge($this->indexers, $indexers);
        $this->collections = array_merge($this->collections, $collections);
    }

    public function values($keys = [])
    {
        $keys = $keys ? $keys : $this->keys();
        foreach ($keys as $key) {
            $row = $this->row($key);
            if ($this->checkJoin($row)) {
                yield $key => $row;
            }
        }
    }

    protected function checkJoin($row)
    {

        $type = $this->join_type;
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
            $cell = $cell ? $cell : [null];
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
}
