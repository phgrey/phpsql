<?php

namespace PhpSql;

use \PhpSql\Collection;

class Index
{
    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var [string]
     */
    protected $fields;

    /**
     * @var  Store\Indexable
     */
    protected $back = null;

    protected $built = false;

    public function __construct(Collection $collection, $options = [])
    {
        $options += [
            'type' => 'hash',
            'unique' => false
        ];

        if (empty($options['fields']) && empty($options['callable'])) {
            throw new \Exception('Either fields or callable parameter must be given');
        }

        $this->collection = $collection;
        $this->fields = $options['fields'];

        if ($options['type'] == 'hash' && !$options['unique']) {
            $this->back = new Store\AssocArray();
        } else {
            throw new NotImplemented('Only non-unique hash indexes available for now');
        }
    }


    protected function build()
    {
        if (count($this->fields) != 1) {
            throw new NotImplemented('For now only 1 field can be indexed, got: ' . json_encode($this->fields));
        }

        $field = reset($this->fields);

        foreach ($this->collection as $pk => $item) {
            $this->back->add([$item[$field]], [$pk]);
        }

        $this->built = $pk;
    }


    public function collection()
    {
        return $this->collection;
    }


    public function join(Index $right, $type)
    {
        $this->back = static::merge($this->serialize(), $right->serialize(), $type);
        return $this;
    }


    public function serialize($keys = [])
    {
        $this->built || $this->build();

        $keys = $keys || $this->back->keys();

        return array_reduce($keys, function ($memo, $key) {
            $ids = $this->back->values($key);
            return $memo + [$key => $this->collection()->serialize($ids)];
        }, []);
    }

    public function name()
    {
        $fields = $this->fields;
        asort($fields);
        $fields = array_unique($fields);
        return implode(',', $fields);
    }

//    abstract protected static function merge($hashes, $type);
}
