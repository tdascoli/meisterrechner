<?php

namespace MSTRRCHNR;

use CSVDB\CSVDB;
use CSVDB\Helpers\CSVConfig;
use Goutte\Client;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Log\LoggerInterface;

class StandingsParser
{
    private CSVDB $csvdb;
    public LoggerInterface $logger;
    public Client $client;

    public function __construct(string $dir = __DIR__, string $file = "standings.csv")
    {
        $this->logger = $this->logger($dir);
        $this->client = new Client();
        $this->csvdb = new CSVDB($dir . "/" . $file, new CSVConfig(0, "UTF - 8", ";", true, false, false));
    }

    public function parse(string $url): void
    {
        $crawler = $this->client->request('GET', $url);
        $crawler->filter('table[id$="tbRangliste"]')->each(function ($node) {
            $node->filter('tr')->each(function ($tr) {
                $played = (int)$tr->filter('.ranCsp')->text();
                $team = $tr->filter('.ranCteam')->text();
                $points = (int)$tr->filter('.ranCpt')->text();
                $message = "played $played / team $team / points / $points";
                $this->logger->info($message);
                $record = [
                    "Team" => $team,
                    "Played" => $played,
                    "Points" => $points
                ];

                try {
                    $this->csvdb->upsert($record, ["Team" => $team]);
                } catch (\Exception $e) {
                    $this->logger->warning($e);
                }
            });
        });
    }

    public function logger(string $dir, string $name = self::class): LoggerInterface
    {
        $loggerSettings = array(
            "path" => $dir . '/logs/app.log',
            "level" => Logger::DEBUG);
        $logger = new Logger($name);

        $processor = new UidProcessor();
        $logger->pushProcessor($processor);

        $handler = new StreamHandler($loggerSettings['path'], $loggerSettings['level']);
        $logger->pushHandler($handler);

        return $logger;
    }
}