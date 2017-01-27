<?php

namespace LaravelX\Logger;

use Psr\Log\LoggerInterface;
use Monolog\Logger as MonoLogger;
use Monolog\Handler\StreamHandler;
use Gelf\Transport\UdpTransport;
use Gelf\Publisher;
use Monolog\Handler\GelfHandler;

class Logger
{
    const App = 'App';

    const SsEmail = 'ss-email';

    const TypeGrayLog = "graylog";

    /**
     * @param $name
     * @param $path
     * @param int $level
     *
     * @return MonoLogger
     */
    public static function newLogger($name, $path, $level = MonoLogger::DEBUG)
    {
        // @todo
        if (config('log.handler') == self::TypeGrayLog ) {
            return self::newGraylog($name);
        }

        // create a log channel
        $log = new MonoLogger($name);
        $log->pushHandler(new StreamHandler($path, $level));

        return $log;
    }

    /**
     * @return Publisher
     */
    public static function newGrayPublisher()
    {
        $transport = new UdpTransport(config('log.graylog.addr'), config('log.graylog.port'), UdpTransport::CHUNK_SIZE_LAN);
        $publisher = new Publisher();
        $publisher->addTransport($transport);
        return $publisher;
    }

    /**
     * @return GelfHandler
     */
    public static  function newGelfHandler(){
        return new GelfHandler(self::newGrayPublisher());
    }

    public static function newGraylog($name = 'ShadowX')
    {
        $log = new MonoLogger($name);
        $log->pushHandler(self::newGelfHandler());
        return $log;
    }

    /**
     * @param $name
     *
     * @return string
     */
    protected static function genPath($name)
    {
        return storage_path(sprintf('logs/%s.log', $name));
    }

    /**
     * @return LoggerInterface
     */
    public static function newAppLogger()
    {
        return self::newLogger(self::App, self::genPath(self::App));
    }

    /**
     * @return LoggerInterface
     */
    public static function getLogger(){
        return app('app-log');
    }


}