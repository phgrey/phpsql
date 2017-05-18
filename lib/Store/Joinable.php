<?php
namespace PhpSql\Store;

use PhpSql\Index\Back\Indexer;

interface Joinable extends Indexer
{
    /**
     * @param $store
     * @param $type - join type. Engine-specific, but hash supports only left|right
     * @return Joinable
     */
    public function join(Joinable $store, $type);
}
