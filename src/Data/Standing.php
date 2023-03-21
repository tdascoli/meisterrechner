<?php

namespace MSTRRCHNR\Data;

use Selective\ArrayReader\ArrayReader;

class Standing
{
    public int $spielnummer;
    public string $teamA;
    public string $teamB;
    public ?int $resultat;

    public function __construct(array $data = [])
    {

        $reader = new ArrayReader($data);

        $this->spielnummer = $reader->findInt('Spielnummer');
        $this->teamA = $reader->findString('Teamname A');
        $this->teamB = $reader->findString('Teamname B');
        $this->resultat = $reader->findInt('Resultat');
    }

    public static function of(array $data = []): Standing
    {
        return new Standing($data);
    }
}