<?php
namespace PhpSql\Index;

use \PhpSql;
use \PhpSql\Errors\NotImplemented;

class Hash implements Joinable
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
     * @var  array - assoc array for Hash imlementing
     */
    protected $hash = [];


    public function __construct(Collection $collection, $fields)
    {
        $this->collection = $collection;


        $this->fields = $fields;

        $this->hash = static::build($collection, $fields);
    }

    /**
     * Hash-specific index build mechanism
     * @param Collection $collection
     * @param $fields
     */
    public static function build(Collection $collection, $fields)
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

        $ret = array();
    }



}