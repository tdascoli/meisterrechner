<?php

namespace MSTRRCHNR\Data;

use Selective\ArrayReader\ArrayReader;

class Team
{

    public string $team;
    public int $played;
    public int $points;

    /**
     * @param string $name
     */
    public function __construct(array $data = [])
    {
        $reader = new ArrayReader($data);

        $this->team = $reader->findString('Team');
        $this->played = $reader->findInt('Played');
        $this->points = $reader->findInt('Points');
    }

    public static function of(array $data = []): Team
    {
        return new Team ($data);
    }
}