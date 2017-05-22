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
            foreach (Back\Relation::multiple($row) as $record) {
                $aggr = [];
                foreach ($record as $i => $id) {
                    $aggr += $this->columns[$i]->values([$id])[0];
                }
                yield $aggr;
            }
        }
    }


}
