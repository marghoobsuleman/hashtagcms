<?php
namespace MarghoobSuleman\HashtagCms\Core\Traits\Admin;

use Monolog\Handler\ElasticSearchHandler;
use Monolog\Formatter\ElasticaFormatter;
use Monolog\Logger;
use Elastica\Client;


trait LogManager {


    /**
     * @todo: Revisit this trait
     * Log as info
     * @param $message
     */
    public function infoLog($message) {

        $client = new Client(array("connectTimeout"=>1));
        $options = array(
            'index' => 'monolog',
            'type' => 'record',
            'ignore_error' => true,
        );
        $handler = new ElasticSearchHandler($client, $options);
        $handler->setFormatter(new ElasticaFormatter('monolog','record'));
        $log = new Logger('application_logs');
        $log->pushHandler($handler);

        $log->info(
            json_encode(array(
                    "info_message" => $message
                )
            ),[url()->current()]
        );
    }

    /**
     * Desc: Log as warning
     * @param $message
     */
    public function warnLog($message) {
        $client = new Client(array("connectTimeout"=>1));
        $options = array(
            'index' => 'monolog',
            'type' => 'record',
            'ignore_error' => true,
        );
        $handler = new ElasticSearchHandler($client, $options);
        $handler->setFormatter(new ElasticaFormatter('monolog','record'));
        $log = new Logger('application_logs');
        $log->pushHandler($handler);
        $log->warn(
            json_encode(array(
                    "warn_message" => $message
                )
            ),[url()->current()]
        );
    }

    /**
     * Desc: Log as error
     * @param $message
     */
    public function errorLog($message) {
        $client = new Client(array("connectTimeout"=>1));
        $options = array(
            'index' => 'monolog',
            'type' => 'record',
            'ignore_error' => true,
        );
        $handler = new ElasticSearchHandler($client, $options);
        $handler->setFormatter(new ElasticaFormatter('monolog','record'));
        $log = new Logger('error_logs');
        $log->pushHandler($handler);
        $log->error(
            json_encode(array(
                    "error_message" => $message
                )
            ),[url()->current()]
        );
    }
}
