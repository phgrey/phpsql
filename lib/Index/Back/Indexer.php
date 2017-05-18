<?php
namespace PhpSql\Index\Back;

use PhpSql\Store\Readable;

interface Indexer extends Readable
{
    public function add($keys, $values = []);
    public function keys();
//    public function values($keys);
}
