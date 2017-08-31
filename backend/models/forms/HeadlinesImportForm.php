<?php
/**
 * Created by PhpStorm.
 * User: kastiel
 * Date: 5/10/17
 * Time: 11:31 AM
 */

namespace backend\models\forms;

use yii\base\Model;
use yii\web\UploadedFile;
use Yii;

/**
 * Class ArchiveForm
 * @package backend\models\forms
 */
class HeadlinesImportForm extends Model
{
    /**
     * @var UploadedFile
     */
    public $file;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            ['file', 'file', 'skipOnEmpty' => false, 'extensions' => 'csv', 'checkExtensionByMimeType' => false],
        ];
    }

    /**
     * @return bool
     * @throws \yii\base\InvalidParamException
     */
    public function upload()
    {
        if(Yii::$app->request->isPost && !Yii::$app->request->isAjax) {

            $this->file = UploadedFile::getInstance($this, 'file');

            if ($this->validate()) {
                return true;
            }
        }

        return false;
    }
}