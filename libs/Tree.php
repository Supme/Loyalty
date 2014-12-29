<?php

class Tree {

    /**
     *
     * @var array
     */
    protected $_array = array();

    /**
     *
     * @var array
     */
    protected $_three = array();

    /**
     *
     * @param array $array
     */
    public function __construct(array $array)
    {
        $this->_array = $array;
    }

    /**
     *
     * @return array
     */
    public function get()
    {
        return $this->_three;
    }

    /**
     *
     * @return \Tree
     */
    public function each()
    {
        $this->_clear();

        $rebuild_array = array();
        foreach ($this->_array as &$row)
        {
            $rebuild_array[$row['pid']][] = &$row;
        }

        foreach ($this->_array as & $row)
        {
            if(isset($rebuild_array[$row['id']]))
            {
                $row['childs'] = $rebuild_array[$row['id']];
            }
        }

        $this->_three = reset($rebuild_array);

        return $this;
    }

    /**
     *
     * @return \Tree
     */
    public function recurse()
    {
        $this->_clear();

        $rebuild_array = array();
        foreach ($this->_array as $row)
        {
            $rebuild_array[$row['pid']][] = $row;
        }

        ksort($rebuild_array);

        $this->_three = $this->_build_recurse($rebuild_array, reset($rebuild_array));

        return $this;
    }

    /**
     *
     * @param array $array
     * @param array $parent
     * @return array
     */
    protected function _build_recurse( & $array, $parent)
    {
        foreach ($parent as $pid => $row)
        {
            if(isset($array[$row['id']]))
            {
                $row['childs'] = $this->_build_recurse($array, $array[$row['id']]);
            }

            $three[] = $row;
        }

        return $three;
    }

    protected function _clear()
    {
        $this->_three = array();
    }
}