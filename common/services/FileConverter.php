<?php
/**
 * Created by PhpStorm.
 * User: kastiel
 * Date: 4/19/17
 * Time: 1:40 PM
 */

namespace common\services;

use common\models\config\GeneratorConfig;
use common\models\Headline;
use common\models\HeadlinePrice;
use yii\validators\CompareValidator;
use yii\validators\DateValidator;

/**
 * Class FileConverter
 * @package common\services
 */
class FileConverter implements FileConverterInterface
{
    /**
     * @var ManagerInterface
     */
    private $manager;

    /**
     * @var FileGeneratorInterface
     */
    private $fileGenerator;

    /**
     * @var GeneratorConfig
     */
    private $config;

    /**
     * @param FileGeneratorInterface $file_generator
     */
    public function __construct(FileGeneratorInterface $file_generator)
    {
        $this->fileGenerator = $file_generator;
    }

    /**
     * @param string $date_from
     * @param string $date_to
     * @param callable|null $callback_log a function is used to show information
     * @return bool
     */
    public function begin($date_from, $date_to, callable $callback_log = null)
    {
        if (!$this->validateDateRange($date_from, $date_to, $error)) {

            $this->callbackLog($callback_log, $error, true);
            return false;
        }

        foreach ($this->manager->iterateRange($this->config->keyword, $date_from, $date_to) as $objects) {

            /**@var Headline[]|HeadlinePrice[] $objects*/
            foreach ($objects as $object) {

                if ($this->fileGenerator->generate($object)) {

                    $this->callbackLog($callback_log, "{$object->id} - generated");

                } else {

                    $this->callbackLog($callback_log, $this->fileGenerator->getError(), true);
                    return false;
                }
            }

        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function getDateRange()
    {
       return $this->manager->getDateRange($this->config->keyword);
    }

    /**
     * @inheritdoc
     */
    public function setConfig(GeneratorConfig $config)
    {
        $this->config = $config;
    }

    /**
     * @inheritdoc
     */
    public function setManager(ManagerInterface $manager)
    {
        $this->manager = $manager;

        if ($this->manager instanceof HeadlinesManagerInterface) {
            $this->fileGenerator->setPath($this->config->headlineFilesPath);
        }

        if ($this->manager instanceof HeadlinesPricesManagerInterface) {
            $this->fileGenerator->setPath($this->config->priceFilesPath);
        }
    }

    /**
     * @param callable|null $callback_log a function is used to show information
     * @return bool
     */
    public function clear(callable $callback_log = null)
    {
        if (!$this->fileGenerator->clear()) {

            $this->callbackLog($callback_log, $this->fileGenerator->getError(), true);
            return false;
        }

        return true;
    }

    /**
     * @param callable|null $callback_log
     * @param string $message
     * @param bool $error
     */
    private function callbackLog(callable $callback_log = null, $message = '', $error = false)
    {
        if ($callback_log) {
            $callback_log($message, $error);
        }
    }

    /**
     * @param string $date_from
     * @param string $date_to
     * @param string|null $error
     * @return bool
     */
    private function validateDateRange($date_from, $date_to, &$error = null)
    {
        $validator = new DateValidator([
            'format'  => 'php:'.($format = 'Y-m-d'),
        ]);

        if (!$validator->validate($date_from) || !$validator->validate($date_to)) {

            $error = "Date format has to be: $format";
            return false;
        }

        $validator = new CompareValidator([
            'operator'     => '>=',
            'compareValue' => $date_from,
        ]);

        if (!$validator->validate($date_to, $error)) {

            $error = "Date '$date_from' has to be less or equal '$date_to'";
            return false;
        }

        return true;
    }
}