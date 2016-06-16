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

    /**
     *  0 => message
     *  1 => level
     *  2 => category
     *  3 => timestamp
     */
    protected function processLogs($logs)
    {
        foreach ($logs as $log) {
            $array = explode("\n", $log[0]);
            $message = implode('<br>', $array);


            $report = new ErrorStreamReport();
            $report->error_group = $message;
            $report->line_number = 0;
            $report->file_name = 'N/A';
            $report->message = $message;
            $report->stack_trace = $message;
            $report->severity =  $this->getSeverity($log);

            
            $this->getClient()->reportException($report);
        }
    }

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
        $client = $this->getClient();
        foreach($this->messages as $message) {

            list($text, $level, $category, $timestamp) = $message;

            $report = new ErrorStreamReport();
            $report->error_group = $message;
            $report->line_number = 0;
            $report->file_name = 'N/A';
            $report->message = $text;
            $report->stack_trace = $text;
            $report->severity =  $this->getSeverity($level);

            $client->reportException($report);
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