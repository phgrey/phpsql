<?php
namespace PhpSql\Index;

interface Joinable
{
    /**
     * @param Joinable $right
     * @param $type - join type. Engine-cpecific, but hash supports only left|right
     * @return Joinable
     */
    public function join(Joinable $right, $type);
}
