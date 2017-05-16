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
     * @param $options
     * @return array - data indexed
     * @throws Errors\NotImplemented
     */
    public function getIndexed($fields, $options = [])
    {

        $ind = new Index\Hash($this, $fields);

        $index_name = $ind->name();

        if (empty($this->indexes[$index_name])) {
            $this->indexes[$index_name] = $ind;
        }

        return $this->indexes[$index_name];
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
