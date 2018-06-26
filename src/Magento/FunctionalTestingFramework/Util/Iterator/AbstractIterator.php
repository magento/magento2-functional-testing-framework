<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Util\Iterator;

/**
 * Class AbstractIterator
 *
 * @api
 */
abstract class AbstractIterator implements \Iterator, \Countable
{
    /**
     * Data
     *
     * @var array
     */
    protected $data = [];

    // @codingStandardsIgnoreStart
    /**
     * Current data element
     *
     * @var mixed
     */
    protected $current;

    /**
     * Get current element
     *
     * @return mixed
     */
    abstract public function current();
    // @codingStandardsIgnoreEnd

    /**
     * Key associated with the current row data
     *
     * @var int|string
     */
    protected $key;

    /**
     * Check if current element is valid
     *
     * @return boolean
     */
    abstract protected function isValid();

    /**
     * Initialize Data Array
     *
     * @return void
     */
    public function rewind()
    {
        reset($this->data);
        if (!$this->isValid()) {
            $this->next();
        }
    }

    /**
     * Seek to next valid row
     *
     * @return void
     */
    public function next()
    {
        $this->current = next($this->data);

        if ($this->current !== false) {
            if (!$this->isValid()) {
                $this->next();
            }
        } else {
            $this->key = null;
        }
    }

    /**
     * Check if current position is valid
     *
     * @return boolean
     */
    public function valid()
    {
        $current = current($this->data);
        if ($current === false || $current === null) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Get data key of the current data element
     *
     * @return integer|string
     */
    public function key()
    {
        return key($this->data);
    }

    /**
     * To make iterator countable
     *
     * @return integer
     */
    public function count()
    {
        return count($this->data);
    }

    /**
     * Initialize first element
     *
     * @return void
     */
    protected function initFirstElement()
    {
        if ($this->data) {
            $this->current = reset($this->data);
            if (!$this->isValid()) {
                $this->next();
            }
        }
    }
}
