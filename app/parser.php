<?php

use MSTRRCHNR\ResultParser;
use MSTRRCHNR\StandingsParser;

require '../vendor/autoload.php';

$standings = new StandingsParser(__DIR__);
$standings->parse("https://matchcenter-sfl.football.ch/default.aspx?oid=2&lng=1&s=2024&ln=11011&ls=23028&sg=64771&a=mrr");

$results = new ResultParser(__DIR__);
//$results->parse("https://matchcenter.fvbj-afbj.ch/default.aspx?oid=6&lng=1&v=777842&t=52469&ls=22224&sg=62864&a=sp");
