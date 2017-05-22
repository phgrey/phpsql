<?php
namespace PhpSql\Store;

use \PhpSql\Errors\NotImplemented;
use PhpSql\Index\Back\Indexer;


class AssocArray implements Readable, Indexer
{

    protected $hash = [];

    public function add($keys, $values = [])
    {
        $this->hash += self::build($keys, $values);
        return $this;
    }

    public function keys()
    {
        return array_keys($this->hash);
    }

    public function values($keys = [])
    {
        return array_reduce($keys, function ($memo, $key) {
            return isset($this->hash[$key])
                ? array_merge($this->hash[$key], $memo)
                : $memo
            ;
        }, []);
    }


    public function serialize()
    {
        return $this->hash;
    }


    public static function build($keys, $values)
    {
        if (count($keys) != count($values)) {
            throw new \Exception('Keys and values should have the same length');
        }

        $hash = [];

        foreach ($keys as $i => $key) {
            //here what's will be different in unique
            if (!isset($hash[$key])) {
                $hash[$key] = [];
            }

            $hash[$key][] = $values[$i];
        }
        return $hash;
    }


    protected static function merge($hashes, $type = 'left')
    {


        if ($type == 'right') {
            $hashes = array_reverse($hashes);
            $type = 'left';
        }


        $base = array_shift($hashes);
        foreach ($hashes as $hash) {
            foreach ($hash as $id => $items_new) {
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
        }
        return $base;
    }

}