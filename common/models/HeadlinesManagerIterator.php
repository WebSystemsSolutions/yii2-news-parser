<?php
/**
 * Created by PhpStorm.
 * User: kastiel
 * Date: 16.04.2017
 * Time: 12:29
 */

namespace common\models;

use yii\db\BatchQueryResult;

/**
 * Class HeadlinesManagerIterator
 * @package common\models
 */
class HeadlinesManagerIterator implements \Iterator
{
    /**
     * @var BatchQueryResult
     */
    private $batch;

    /**
     * @var callable
     */
    private $prepareFunc;

    /**
     * HeadlinesManagerIterator constructor.
     * @param BatchQueryResult $batch
     * @param callable $prepareFunc
     */
    public function __construct(BatchQueryResult $batch, callable $prepareFunc)
    {
        $this->batch = $batch;
        $this->prepareFunc = $prepareFunc;
    }

    /**
     * @return Headline[]
     */
    public function current()
    {
        return call_user_func($this->prepareFunc, $this->batch->current());
    }

    /**
     * @return void
     */
    public function rewind()
    {
        $this->batch->rewind();
    }

    /**
     * @return integer
     */
    public function key()
    {
        return $this->batch->key();
    }

    /**
     * @return void
     */
    public function next()
    {
        $this->batch->next();
    }

    /**
     * @return bool
     */
    public function valid()
    {
        return $this->batch->valid();
    }
}