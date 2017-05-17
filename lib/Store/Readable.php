<?php
namespace PhpSql\Store;

interface Readable extends \IteratorAggregate
{
    public function values($ids);
}
