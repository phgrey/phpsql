<?php
namespace PhpSql\Store;

//use PhpSql\Index\Back\Indexer;

abstract class Indexable implements Readable, \IteratorAggregate
{

    protected $data = [];

    /**
     * @var array - assoc array of null record, used for joins
     */
    protected $null = [];

    public function __construct($store)
    {
        $this->data = $store;
    }

    public function null()
    {
        if (! $this->null) {
            $this->null = array_fill_keys(array_keys($this->data[0]), null);
        }

        return $this->null;
    }

    public function getIterator()
    {
        foreach ($this->values() as $key => $value) {
            yield $key => $value;
        }
    }

    /**
     * Returns data indexed, as a assoc array
     * @param $fields
     * @param $options
     * @return \PhpSql\Index\Column - data indexed
     * @throws \PhpSql\Errors\NotImplemented
     */
    public function index($fields, $options = [])
    {
        return new \PhpSql\Index\Column($this, $options + ['fields' => $fields]);
    }

    abstract public function values($ids = []);

    abstract protected function get($id);
}
