<?php

namespace backend\models\forms;

use backend\models\User;
use Yii;
use yii\base\Model;


/**
 * Class LoginForm
 * @package backend\models\forms
 */
class LoginForm extends Model
{
    /**
     * @var string
     */
    public $username;

    /**
     * @var string
     */
    public $password;

    /**
     * @var bool
     */
    public $rememberMe = true;

    /**
     * @var User
     */
    private $_user;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'password'], 'required'],
            ['rememberMe', 'boolean'],
            ['password', 'validatePassword'],
        ];
    }

    /**
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     * @throws \yii\base\InvalidParamException
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {

            $user = $this->getUser();

            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }

    /**
     * @return bool whether the user is logged in successfully
     * @throws \yii\base\InvalidParamException
     */
    public function login()
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
        }

        return false;
    }

    /**
     * @return User|null
     */
    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = User::findByUsername($this->username);
        }

        return $this->_user;
    }
}
