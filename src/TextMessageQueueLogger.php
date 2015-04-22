<?php

namespace Smart\TextMessageQueue;

use Cocur\Slugify\Slugify;
use Psr\Log\LoggerInterface;

class TextMessageQueueLogger implements LoggerInterface
{
    /**
     * @var string
     */
    private $logDir;

    /**
     * @var string
     */
    private $logFile;

    /**
     * @param string $logFile
     * @param string $logDir
     */
    public function __construct($logFile, $logDir)
    {
        $this->logFile = $logFile;
        $this->logDir = $logDir;
    }

    /**
     * @param string $message
     * @param array  $context
     */
    private function logTextMessage($message, array $context = [])
    {
        if ($this->logDir === null) {
            return;
        }

        if (!is_dir($this->logDir)) {
            mkdir($this->logDir);
        }

        $toNumber = isset($context['to'][0]) ? $context['to'][0] : null;
        $mediaUrls = isset($context['mediaUrls']) ? $context['mediaUrls']
            : null;

        $slugify = new Slugify;

        $file = date('Y-m-d H-i-s ') .
            substr($slugify->slugify($toNumber), 0, 28) . ' ' .
            substr($slugify->slugify($message), 0, 28);

        $file = $this->logDir . DIRECTORY_SEPARATOR . $file;

        $originFile = $file;
        $count = 1;
        while (file_exists($file . '.txt')) {
            $file = $originFile . $count;
            $count++;
            if ($count >= 100) {
                break;
            }
        }

        $body = '';
        $body .= 'Date: ' . date('Y-m-d H:i:s') . PHP_EOL;
        $body .= 'To: ' . $toNumber . PHP_EOL . PHP_EOL;
        $body .= 'Message: ' . PHP_EOL;
        $body .= '-------------------------------' . PHP_EOL . PHP_EOL;
        $body .= $message . PHP_EOL . PHP_EOL;

        if (!empty($mediaUrls)) {
            $body .= 'Media Urls: ' . PHP_EOL;
            $body .= '-------------------------------' . PHP_EOL . PHP_EOL;

            foreach ($mediaUrls as $mediaUrl) {
                $body .= $mediaUrl . PHP_EOL;
            }
        }

        file_put_contents($file . '.txt', $body);
    }

    /**
     * @param string $level
     * @param string $message
     */
    private function writeLog($level, $message)
    {
        if (!empty($message)) {
            if ($this->logFile === null) {
                return;
            }
            $content
                =
                date('Y-m-d H:i:s') . ' ' . $level . ': ' . $message . PHP_EOL;
            file_put_contents($this->logFile, $content, FILE_APPEND);
        }
    }

    /**
     * System is unusable.
     *
     * @param string $message
     * @param array  $context
     *
     * @return null
     */
    public function emergency($message, array $context = [])
    {
        $this->writeLog('Emergency', $message);
    }

    /**
     * Action must be taken immediately.
     *
     * @param string $message
     * @param array  $context
     *
     * @return null
     */
    public function alert($message, array $context = [])
    {
        $this->writeLog('Alert', $message);
    }

    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string $message
     * @param array  $context
     *
     * @return null
     */
    public function critical($message, array $context = [])
    {
        $this->writeLog('Critical', $message);
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message
     * @param array  $context
     *
     * @return null
     */
    public function error($message, array $context = [])
    {
        if (isset($context['to'][0])) {
            $this->writeLog('Error',
                "Error sending text message to \"{$context['to'][0]}\": {$message}");
        } else {
            $this->writeLog('Error', "Error sending text message: {$message}");
        }
    }

    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param string $message
     * @param array  $context
     *
     * @return null
     */
    public function warning($message, array $context = [])
    {
        $this->writeLog('Warning', $message);
    }

    /**
     * Normal but significant events.
     *
     * @param string $message
     * @param array  $context
     *
     * @return null
     */
    public function notice($message, array $context = [])
    {
        $this->writeLog('Notice', $message);
    }

    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param string $message
     * @param array  $context
     *
     * @return null
     */
    public function info($message, array $context = [])
    {
        if (empty($context)) {
            $this->writeLog('Info', $message);

            return;
        }

        if (isset($context['to'][0])) {
            $this->writeLog('Info',
                "Sent text message to \"{$context['to'][0]}\" : \"{$message}\"");
            $this->logTextMessage($message, $context);
        } else {
            $this->error("No recipient phone number provided", $context);
        }
    }

    /**
     * Detailed debug information.
     *
     * @param string $message
     * @param array  $context
     *
     * @return null
     */
    public function debug($message, array $context = [])
    {
        $this->writeLog('Debug', $message);
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed  $level
     * @param string $message
     * @param array  $context
     *
     * @return null
     */
    public function log($level, $message, array $context = [])
    {
        $this->writeLog('Log', $message);
    }
}
