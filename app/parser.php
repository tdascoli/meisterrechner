<?php

use MSTRRCHNR\StandingsParser;

require '../vendor/autoload.php';

$standings = new StandingsParser(__DIR__);
$standings->parse("https://matchcenter.fvbj-afbj.ch/default.aspx?oid=6&lng=1&v=777842&t=52469&ls=21031&sg=60114&a=trr");

$results = new \MSTRRCHNR\ResultParser(__DIR__);
$results->parse("https://matchcenter.fvbj-afbj.ch/default.aspx?oid=6&lng=1&v=777842&t=52469&ls=21031&sg=60114&a=sp");
