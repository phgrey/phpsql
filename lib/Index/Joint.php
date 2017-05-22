<?php

namespace PhpSql\Index;

class Joint extends Item
{
    /**
     * @var [Column]
     */
    protected $columns = [];

    /**
     * @var string
     */
    protected $type = '';

    public function __construct($columns, $type = 'left')
    {
        $this->columns = $columns;
        $indexers = array_map(function (Column $c) {
            return $c->indexer;
        }, $columns);
        $collections = array_map(function (Column $c) {
            return $c->collection();
        }, $columns);

        $this->indexer = new Back\Relation($indexers, $collections, $type);

        $this->type = $type;
    }

    public function join(Item $another, $type = 'left')
    {
        if ($type != $this->type) {
            return parent::join($another, $type);
        }

        $this->indexer->add([$another->indexer], [$another->collection()]);
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
