<?php

namespace PhpSql\Index;

class Joint extends Item
{
    /**
     * @var [Column]
     */
    protected $columns = [];

    public function __construct($columns, $type = 'left')
    {
        $this->columns = $columns;
        $indexers = array_map($columns, function (Column $c) {
            return $c->indexer;
        });
        $collections = array_map($columns, function (Column $c) {
            return $c->values();
        });
        $this->indexer = new Back\Relation($indexers, $collections);
    }

    public function join(Item $another)
    {
        $this->indexer->add([$another->indexer], [$another->values()]);
        return $this;
    }


    public function values($ids = [])
    {
        foreach ($this->indexer->values($ids) as $row) {
            foreach ($row as $i => &$cell) {
                $cell = $this->columns[$i]->values($cell);
            }
            $data = Back\Relation::multiple($row);
            foreach ($data as $record) {
                $record = call_user_func_array('array_merge', $record);
                yield $record;
            }
        }
    }


}
