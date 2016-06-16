<?php

namespace ErrorStream\ErrorStream;

use Yii;
use yii\base\InvalidConfigException;
use yii\log\Logger;
use yii\log\Target;
use ErrorStream\ErrorStreamClient\ErrorStreamClient;
use ErrorStream\ErrorStreamClient\ErrorStreamReport;


class ErrorStreamLogger extends Target
{

    public $clientId = 'errorstream';

    protected function getSeverity($log){
        $severity = 3;
        $name = Logger::getLevelName($log[1]);
        if($name == 'trace') $severity = 1;
        if($name == 'info') $severity = 1;
        if($name == 'profile') $severity = 2;
        if($name == 'warning') $severity = 2;
        if($name == 'error') $severity = 3;
        return $severity;
    }

    public function export()
    {
        //Stop if we're not active.
        if($this->getClient()->active !== true) return;

        $client = $this->getClient();
        foreach($this->messages as $message) {

            list($text, $level, $category, $timestamp) = $message;

            $report = new ErrorStreamReport();
            $report->error_group = $level . $text;
            $report->line_number = 0;
            $report->file_name = 'N/A';
            $report->message = $text;
            $report->stack_trace = $text;
            $report->severity = ($level < 4) ? $level : 3; // No higher than 3

            $client->report($report);
        }
    }

    public function getClient()
    {
        if (!Yii::$app->has($this->clientId)) {
            throw new InvalidConfigException(sprintf('ErrorStream.componentID "%s" is invalid.', $this->clientId));
        }
        return Yii::$app->get($this->clientId);
    }
}