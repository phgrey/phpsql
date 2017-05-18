<?php

namespace PhpSql\Index;

use PhpSql\Store\Readable;

/**
 * Interface Item - this interface describes what should be able
 * to do each item of our scene
 * @package PhpSql\Index
 */
abstract class Item implements Readable
{
    /**
     * @var Back\Indexer
     */
    protected $indexer;


    public function join(Item $another, $type)
    {
        return new Joint([$this->indexer, $another->indexer], $type);
    }


    public function chain(Item $another)
    {
        return new Chain([$this->indexer, $another->indexer]);
    }



    abstract public function values($ids = []);

    public function collection()
    {
        return new \PhpSql\Collection($this->values());
    }
}
