<?php

namespace MSTRRCHNR\Data;

use Selective\ArrayReader\ArrayReader;

class Standing
{
    public int $spielrunde;
    public int $spielnummer;
    public int $spieldatum;
    public string $teamA;
    public string $teamB;
    public ?int $resultat;
    public ?int $points = null;

    public function __construct(array $data = [])
    {

        $reader = new ArrayReader($data);

        $this->spielrunde = $reader->findInt('Spielrunde');
        $this->spielnummer = $reader->findInt('Spielnummer');
        $this->teamA = $reader->findString('Teamname A');
        $this->teamB = $reader->findString('Teamname B');
        $this->resultat = $reader->findInt('Resultat');

        $spieldatum = $reader->findString('Spieldatum');
        $spielzeit = $reader->findString('Spielzeit');
        $this->spieldatum = strtotime($spieldatum . " " . $spielzeit);
    }

    public static function of(array $data = []): Standing
    {
        return new Standing($data);
    }
}