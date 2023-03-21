<?php

namespace MSTRRCHNR\Data;

use MSTRRCHNR\MSTRRCHNR;
use Selective\ArrayReader\ArrayReader;

class Game
{

    public int $spielrunde;
    public int $spielnummer;
    public string $spieldatum;
    public string $spielzeit;
    public string $teamA;
    public string $teamB;
    public string $spielort;
    public ?int $resultat;

    public function __construct(array $data = [])
    {

        $reader = new ArrayReader($data);

        $this->spielrunde = $reader->findInt('Spielrunde');
        $this->spielnummer = $reader->findInt('Spielnummer');
        $this->spieldatum = $reader->findString('Spieldatum');
        $this->spielzeit = $reader->findString('Spielzeit');
        $this->teamA = $reader->findString('Teamname A');
        $this->teamB = $reader->findString('Teamname B');
        $this->spielort = $reader->findString('Spielort');
        $this->resultat = $reader->findInt('Resultat');
    }

    public static function of(array $data = []): Game
    {
        return new Game($data);
    }

    public function teamA(): string
    {
        return MSTRRCHNR::team($this->teamA);
    }

    public function teamB(): string
    {
        return MSTRRCHNR::team($this->teamB);
    }

    public function standing(string $team): string
    {
        if (isset($this->resultat)) {
            $resultat = $this->resultat;
            if ($resultat == 0) {
                return "draw";
            }
            if ($team == $this->teamA) {
                return ($resultat == 1) ? "win" : "loss";
            } else {
                return ($resultat == 2) ? "win" : "loss";
            }
        }
        return "";
    }
}