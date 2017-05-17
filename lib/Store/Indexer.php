<?php
namespace PhpSql\Store;

interface Indexer
{
    public function add($keys, $values);
    public function keys();
//    public function sorted($asc);
    public function values($key);
//    public function build($keys, $values);
//    public function serialize();
}
