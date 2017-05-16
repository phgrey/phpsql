<?php
/**
 * Created by IntelliJ IDEA.
 * User: user
 * Date: 15.05.17
 * Time: 11:57
 */

namespace PhpSql;

/**
 * Class Index
 * @package PhpSql
 * In-memory data, indexed and ready to be joined using PHP instead of MySQL
 * @see Joiner::join for usage examples
 *
 */
class Collection implements \IteratorAggregate
{
    protected $data = [];
    protected $indexes = [];


    public function getIterator()
    {
        foreach ($this->data as $key => $value) {
            yield $key => $value;
        }
    }

    /**
     * Returns data indexed, as a assoc array
     * @param $fields
     * @return array - data indexed
     * @throws Errors\NotImplemented
     */
    public function getIndexed($fields)
    {
        asort($fields);
        $fields = array_unique($fields);
        if (length($fields) != 1) {
            throw new Errors\NotImplemented('For now only 1 field can be indexed, got: ' . json_encode($fields));
        }
        $index_name = implode(',', $fields);

        $ind = new Index\Hash($this, $fields);

        if (empty($this->indexes[$index_name])) {
            $this->indexes[$index_name] = $ind;
        }

        return $this->indexes[$index_name];
    }

    /**
     * Creates simple non-unique hash-style index for data given
     * TODO: use stdlib for btree-like index when need
     * TODO: move to separate trait IndexedCollection
     * @param $data
     * @param $field
     * @return array - assoc array with keys - index values and values - array of records
     */
    protected static function hashIndex($data, $field)
    {
        $ret = array();

        foreach ($data as $item) {
            $key = $item[$field];
            if (!isset($ret[$key])) {
                $ret[$key] = [];
            }

            $arr[$key][] = $item;
        }

        return $ret;
    }

    /**
     * @param \Iterator $data
     * @return static
     */
    public static function fromIterator(\Iterator $data = null)
    {
        $ret = new static();
        if (!is_null($data)) {
            $ret->data = $data;
        }
        return $ret;
    }


}
