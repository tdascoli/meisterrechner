<?php

namespace MSTRRCHNR\Helpers;

use CSVDB\Converter;
use MSTRRCHNR\Data\Game;

class GameConverter implements Converter
{

    public function convert(iterable $records): array
    {
        $results = [];
        foreach ($records as $record) {
            $results[] = Game::of($record);
        }
        return $results;
    }
}