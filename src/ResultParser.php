<?php

namespace MSTRRCHNR;

use CSVDB\CSVDB;
use CSVDB\Helpers\CSVConfig;
use Goutte\Client;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Log\LoggerInterface;

class ResultParser
{
    private CSVDB $csvdb;
    public LoggerInterface $logger;
    public Client $client;

    public function __construct(string $dir = __DIR__, string $file = "games.csv")
    {
        $this->logger = $this->logger($dir);
        $this->client = new Client();
        $this->csvdb = new CSVDB($dir . "/" . $file, new CSVConfig(0, "UTF - 8", ";", true, false, false));
    }

    public function parse(string $url): void
    {
        $games = $this->csvdb->select()->where([["Resultat" => [0, CSVDB::NEG]], ["Resultat" => [1, CSVDB::NEG]], ["Resultat" => [2, CSVDB::NEG]]])->orderBy("Spielrunde")->limit(5)->get();
        $spielnummern = [];
        foreach ($games as $game) {
            $spielnummern[$game["Spielnummer"]] = $game;
        }

        $crawler = $this->client->request('GET', $url);
        $crawler->filter('div[id$="tbResultate"]')->each(function ($node) use ($spielnummern) {
            $node->filter('.list-group-item > .spiel')->each(function ($item) use ($spielnummern) {
                $spielInfo = $item->filter(".spielInfo")->text();
                $spielnummer = str_replace("Spielnummer ", "", $spielInfo);
                if (array_key_exists($spielnummer, $spielnummern)) {
                    if ($item->filter('.torA')->count() > 0) {
                        $torA = (int)$item->filter('.torA')->text();
                        $torB = (int)$item->filter('.torB')->text();
                        $resultat = 0;
                        if ($torA > $torB) {
                            $resultat = 1;
                        } elseif ($torA < $torB) {
                            $resultat = 2;
                        }
                        $record = [
                            "Resultat" => $resultat
                        ];
                        try {
                            $this->csvdb->update($record, ["Spielnummer" => $spielnummer]);
                        } catch (\Exception $e) {
                            $this->logger->warning($e);
                        }
                    }
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