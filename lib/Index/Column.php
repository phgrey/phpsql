<?php

namespace PhpSql\Index;

use \PhpSql\Collection;
use PhpSql\Errors\NotImplemented;
use PhpSql\Store;

class Column extends Item
{
    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var [string]
     */
    protected $fields;


    protected $built = false;

    public function __construct(Store\Readable $collection, $options = [])
    {
        $options += [
            'type' => 'hash',
            'unique' => false
        ];

        if (empty($options['fields']) && empty($options['function'])) {
            throw new \Exception('Either fields or function parameter must be given');
        }

        $this->collection = $collection;
        $this->fields = $options['fields'];

        if ($options['type'] == 'hash' && !$options['unique']) {
            $this->indexer = new Store\AssocArray();
        } else {
            throw new NotImplemented('Only non-unique hash indexes available for now');
        }

        //TODO: make a realy lazy loading here
        $this->build();
    }


    protected function build()
    {
        if (count($this->fields) != 1) {
            throw new NotImplemented('For now only 1 field can be indexed, got: ' . json_encode($this->fields));
        }

        $field = reset($this->fields);

        foreach ($this->collection->values() as $pk => $item) {
            $this->indexer->add([$item[$field]], [$pk]);
        }

        $this->built = $pk;
    }

    public function collection()
    {
        return $this->collection;
    }

    public function values($ids = [])
    {
        return $this->collection->values($ids);
    }


//    public function serialize($keys = [])
//    {
//        if (! $this->built) {
//            $this->build();
//        }
//
//        $keys = $keys ? $keys : $this->indexer->keys();
//
//        return array_reduce($keys, function ($memo, $key) {
//            $ids = $this->indexer->values($key);
//            return $memo + [$key => $this->collection()->serialize($ids)];
//        }, []);
//    }

    public function name()
    {
        $fields = $this->fields;
        asort($fields);
        $fields = array_unique($fields);
        return implode(',', $fields);
    }

//    abstract protected static function merge($hashes, $type);
}
