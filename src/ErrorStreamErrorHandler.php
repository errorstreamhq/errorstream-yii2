<?php

namespace ErrorStream\ErrorStream;

use Yii;
use yii\base\InvalidConfigException;
use yii\web\Application;
use ErrorStream\ErrorStreamClient\ErrorStreamClient;
use ErrorStream\ErrorStreamClient\ErrorStreamReport;


class ErrorStreamErrorHandler extends \yii\web\ErrorHandler
{

    public $clientId = 'errorstream';


    public function init()
    {
        Yii::$app->on(Application::EVENT_BEFORE_REQUEST, [$this, 'onShutdown']);
    }

    public function onShutdown()
    {
        $error = error_get_last();
        if ($error !== null) {

            //Stop if we're not active.
            if($this->getClient()->active !== true) return;

            $errors = array(
                E_ERROR,
                E_PARSE,
                E_CORE_ERROR,
                E_CORE_WARNING,
                E_COMPILE_ERROR,
                E_COMPILE_WARNING,
                E_STRICT
            );

            if (in_array($error['type'], $errors)) {
                $this->getClient()->reportException($this->createErrorException($error['message'], $error['type'], $error['file'], $error['line']));
            }
        }
    }

    public function handleError($code, $message, $file, $line)
    {
        if (error_reporting() & $code && $this->getClient()->active == true) {
            $this->getClient()->reportException($this->createErrorException($message, $code, $file, $line));
        }

        parent::handleError($code, $message, $file, $line);
    }

    public function handleException($exception)
    {
        if($this->getClient()->active == true)
            $this->getClient()->reportException($exception);
        parent::handleException($exception);
    }

    protected function createErrorException($message, $code, $file, $line)
    {
        return new \ErrorException($message, $code, 0/* will be resolved */, $file, $line);
    }

    public function getClient()
    {
        if (!Yii::$app->has($this->clientId)) {
            throw new InvalidConfigException(sprintf('ErrorStream.componentID "%s" is invalid.', $this->clientId));
        }
        return Yii::$app->get($this->clientId);
    }
}