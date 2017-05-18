<?php

namespace PhpSql;

/**
 * Class Joiner
 * @package PhpSql
 * Here we gonna make join in PHP, not in mysql
 * trait is designed to use together with Doctrine\ORM\EntityRepository
 * TODO: to use with $this->getEntityManager()->createQueryBuilder() please
 *
 */
trait Joiner
{

    /**
     * complexity O(N^2)
     * @param array $parts - assoc array in format [join_key => sql or dataset for each part to be joined].
     * @param $on - lists of fields to be compared
     * @param $params - values to be set in sql
     * @param string $type - type of join. for now (left|right)-only supported
     * @throws \Exception
     * @return array
     */
    public function join($parts, $on, $params = [], $type = 'left')
    {
        //$parts and $on should be syncronized
        //probably we can move them to separate class Reflection e.g.
        //but there is nothing to code there for now
        if (count($parts) != count($on)) {
            throw new \Exception('Parts and on should have the same length');
        }

        foreach ($parts as $i => &$part) {
            if (is_string($parts[$i])) {
                $part = $this->fetchAll($part, $params);
            }

            //after the following we'll get joinable indexes there, ...
            $part = (new Collection($part))->index([$on[$i]]);
        }

        //... join them and then return underlying collection
        $ret = [];
        foreach ((new Index\Joint($parts))->values() as $v) {
            $ret[] = $v;
        }
        return $ret;
    }
}
