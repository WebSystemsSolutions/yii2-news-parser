<?php

/**
 * Created by PhpStorm.
 * User: kastiel
 * Date: 4/28/17
 * Time: 3:09 PM
 */

/**
 * Application requirement checker script.
 *
 * In order to run this script use the following console command:
 * php requirements.php
 *
 * In order to run this script from the web, you should copy it to the web root.
 * If you are using Linux you can create a hard link instead, using the following command:
 * ln requirements.php ../requirements.php
 */

// you may need to adjust this path to the correct Yii framework path
$frameworkPath = dirname(__FILE__) . '/vendor/yiisoft/yii2';

if (!is_dir($frameworkPath)) {

    echo '<h1>Error</h1>';
    echo '<p><strong>The path to yii framework seems to be incorrect.</strong></p>';
    echo '<p>You need to install Yii framework via composer or adjust the framework path in file <abbr title="' . __FILE__ . '">' . basename(__FILE__) . '</abbr>.</p>';
    echo '<p>Please refer to the <abbr title="' . dirname(__FILE__) . '/README.md">README</abbr> on how to install App.</p>';
}

require_once($frameworkPath . '/requirements/YiiRequirementChecker.php');
$requirementsChecker = new YiiRequirementChecker();

/**
 * Adjust requirements according to your application specifics.
 */
$requirements = [
    [
        'name'      => 'PDO extension',
        'mandatory' => true,
        'condition' => extension_loaded('pdo'),
        'by'        => 'All DB-related classes',
    ],
    [
        'name'      => 'PDO MySQL extension',
        'mandatory' => false,
        'condition' => extension_loaded('pdo_mysql'),
        'by'        => 'All DB-related classes',
        'memo'      => 'Required for MySQL database.',
    ],
    'phpExposePhp'  => [
        'name'      => 'Expose PHP',
        'mandatory' => false,
        'condition' => $requirementsChecker->checkPhpIniOff("expose_php"),
        'by'        => 'Security reasons',
        'memo'      => '"expose_php" should be disabled at php.ini',
    ],
    'phpAllowUrlInclude' => [
        'name'      => 'PHP allow url include',
        'mandatory' => false,
        'condition' => $requirementsChecker->checkPhpIniOff("allow_url_include"),
        'by'        => 'Security reasons',
        'memo'      => '"allow_url_include" should be disabled at php.ini',
    ],
];

$requirementsChecker->checkYii()->check($requirements)->render();