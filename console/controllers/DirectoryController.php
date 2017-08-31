<?php

namespace console\controllers;

use yii\console\Controller;
use yii\helpers\FileHelper;
use Yii;

/**
 * Class DirectoryController
 * @package console\controllers
 */
class DirectoryController extends Controller
{
    /**
     * @var array
     */
    private static $directories = [
        '@console/runtime/headline-files',
        '@console/runtime/price-files',
        '@backend/runtime/archives',
        '@backend/runtime/import-files',
    ];

    /**
     * @return string
     */
    public function actionCreate()
    {
        $exist = false;

        foreach (self::$directories as $alias) {

            $directory = Yii::getAlias($alias);

            if(!file_exists($directory)) {

                $exist = true;

                if (!FileHelper::createDirectory($directory, 0777)) {
                    $this->stdout("Failed to create directory: '$directory'\n");
                } else {
                    $this->stdout("The directory was created successfully: '$directory'\n");
                }
            }
        }

        if (!$exist) {
            $this->stdout("No new directories found.\n");
        }

        return self::EXIT_CODE_NORMAL;
    }
}
