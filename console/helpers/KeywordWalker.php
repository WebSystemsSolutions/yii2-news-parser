<?php
/**
 * Created by PhpStorm.
 * User: kastiel
 * Date: 8/16/17
 * Time: 10:12 AM
 */

namespace console\helpers;

/**
 * Class KeywordWalker
 * @package console\helpers
 */
class KeywordWalker
{
    /**
     * @var \SplQueue|null
     */
    private static $queue;

    /**
     * @param array $keywords
     */
    private static function init(array $keywords)
    {
        self::$queue = new \SplQueue();

        foreach ($keywords as $keyword) {
            self::$queue->push($keyword);
        }

        self::$queue->rewind();
    }

    /**
     * @param array $keywords
     * @param bool $stopped
     *
     * @return bool|mixed
     */
    public static function process(array $keywords, $stopped = false)
    {
        if (!self::$queue) {
            self::init($keywords);
        } else {

            if ($stopped) {
                self::$queue->push(self::$queue->shift());
            } else {
                self::$queue->shift();
            }

            self::$queue->next();
        }

        return self::$queue->valid() ? self::$queue->current() : false;
    }
}