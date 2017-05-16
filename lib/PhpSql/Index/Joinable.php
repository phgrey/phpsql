<?php
namespace PhpSql\Index;

interface Joinable
{
    public function join(Joinable $right, $type);
}
