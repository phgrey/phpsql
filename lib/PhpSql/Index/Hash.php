<?php
namespace PhpSql\Index;

use \PhpSql\Collection;
use \PhpSql\Errors\NotImplemented;

class Hash extends Base implements Joinable
{
    /**
     * @var  array - assoc array for Hash imlementing
     */
    protected $hash = [];

    /**
     * @param Collection $collection
     * @param $fields
     * @return array
     * @throws NotImplemented
     */
    protected static function build(Collection $collection, $fields)
    {
        $ret = [];

        if (count($fields) != 1) {
            throw new NotImplemented('For now only 1 field can be indexed, got: ' . json_encode($fields));
        }

        $field = reset($fields);

        foreach ($collection as $item) {
            $key = $item[$field];
            if (!isset($ret[$key])) {
                $ret[$key] = [];
            }

            $ret[$key][] = $item;
        }

        return $ret;
    }

    public function join(Joinable $right, $type)
    {
        if (!($right instanceof static)) {
            throw new NotImplemented('for now we do support Hash indexes only');
        }

        $ret = new static();
        $ret->hash = static::joinHashes($this->hash, $right->hash, $type);

        return $ret;
    }


    protected static function joinHashes($base, $adds, $type = 'left')
    {
        if ($type == 'right') {
            $tmp = $adds;
            $adds = $base;
            $base = $tmp;
            $type = 'left';
        }

        foreach ($adds as $id => $items_new) {
            if ($type == 'left') {
                if (empty($base[$id])) {
                    continue;
                }
            } else {
                //here is good place for additional join types
                throw new NotImplemented('Only left|right join can be used for now');
            }
            $items_original = empty($base[$id]) ? [] : $base[$id];

            $base[$id] = [];

            foreach ($items_original as $item_original) {
                foreach ($items_new as $item_new) {
                    $base[$id][] = $item_original + $item_new;
                }
            }
        }

        return $base;
    }

    public function serialize()
    {
        return $this->hash;
    }

    public function name()
    {
        $fields = $this->fields;
        asort($fields);
        $fields = array_unique($fields);
        return implode(',', $fields);
    }
}