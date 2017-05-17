<?php
namespace PhpSql\Store;

interface Joinable extends Indexer
{
    /**
     * @param $store
     * @param $type - join type. Engine-cpecific, but hash supports only left|right
     * @return Joinable
     */
    public function join(Joinable $store, $type);
}
