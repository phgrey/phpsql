<?php
/**
 * Created by IntelliJ IDEA.
 * User: user
 * Date: 15.05.17
 * Time: 12:55
 */

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
     * @return Collection
     */
    public function join($parts, $on, $params = [], $type = 'left')
    {
        //$parts and $on should be syncronized
        //probably we can move them to separate class Reflection e.g.
        //but there is nothing to code there for now
        if (length($parts) != length($on)) {
            throw new \Exception('Parts and on should have the same length');
        }

        if ($type == 'right') {
            $parts = array_reverse($parts);
            $on = array_reverse($on);
            $type = 'left';
        }

        foreach ($parts as $i => &$part) {
            if (is_string($parts[$i])) {
                $part = $this->fetchAll($parts[$i], $params);
            } else {
                $part = $parts[$i];
            }

            $part = Collection::fromIterator($part)
                ->getIndexed($on[$i]);
        }

        $ret = reset($parts);

        for ($i = 1; $i < length($parts); $i++) {
            $ret = $this->joinResults($ret, $parts[$i], $type);
        }

        return Collection::fromIterator(array_values($ret));
    }

    /**
     * Here we gonna match multiple results each-to-each the way mysql does
     * here You can see why unique indexes are cheaper
     * @param array $base
     * @param array $adds
     * @param array $type
     * @return array
     * @throws Errors\NotImplemented
     */
    protected function joinResults($base, $adds, $type)
    {


        foreach ($adds as $id => $items_new) {
            if ($type == 'left') {
                if (empty($ret[$id])) {
                    continue;
                }
            } else {
                //here is good place for additional join types
                throw new Errors\NotImplemented('Only left|right join can be used for now');
            }
            $items_original = empty($base[$id]) ? [] : $base[$id];

            $base[$id] = [];

            foreach ($items_original as $item_original) {
                foreach ($items_new as $item_new) {
                    $base[$id][] = $item_original + $item_new;
                }
            }
        }

        return $ret;
    }

    protected function index($data, $field)
    {
        return Collection::fromIterator($data)
            ->getIndexed($field);
    }

    protected function unindex($index)
    {
        return 1;
    }
}
