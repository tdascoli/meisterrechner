<?php

namespace MSTRRCHNR\Helpers;

use CSVDB\Converter;
use MSTRRCHNR\Data\Team;

class TeamConverter implements Converter
{

    public function convert(iterable $records): array
    {
        $results = [];
        foreach ($records as $record) {
            $results[] = Team::of($record);
        }
        return $results;
    }
}