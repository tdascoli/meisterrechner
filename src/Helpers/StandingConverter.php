<?php

namespace MSTRRCHNR\Helpers;

use CSVDB\Converter;
use MSTRRCHNR\Data\Standing;

class StandingConverter implements Converter
{

    public function convert(iterable $records): array
    {
        $results = [];
        foreach ($records as $record) {
            $results[] = Standing::of($record);
        }
        return $results;
    }
}