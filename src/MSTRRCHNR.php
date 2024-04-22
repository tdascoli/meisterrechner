<?php

namespace MSTRRCHNR;

use CSVDB\CSVDB;
use CSVDB\Helpers\CSVConfig;
use MSTRRCHNR\Helpers\GameConverter;
use MSTRRCHNR\Helpers\StandingConverter;
use MSTRRCHNR\Helpers\TeamConverter;

class MSTRRCHNR
{
    private CSVDB $games;
    private CSVDB $standings;

    /**
     * @param CSVDB $repository
     */
    public function __construct(string $games, string $standings)
    {
        $this->games = new CSVDB($games, new CSVConfig(1, "UTF-8", ";", true, true, false));
        $this->standings = new CSVDB($standings, new CSVConfig(0, "UTF-8", ";", true, true, false));
    }

    public function games(string $team = "BSC Young Boys"): array
    {
        return $this->games->select()->where([["Teamname A" => $team], ["Teamname B" => $team]], CSVDB::OR)->orderBy("Spielrunde")->get(new GameConverter());
    }

    public function games_json(): array
    {
        return $this->games->select()->orderBy("Spielrunde")->get(new StandingConverter());
    }

    public function schedule(): array
    {
        $test = $this->games->select(["Spielrunde", "Spieldatum"])->orderBy("Spielrunde")->get();
        $spielrunde = array();
        foreach ($test as $schedule) {
            if (strtotime($schedule['Spieldatum']) > time()) {
                $spielrunde[] = $schedule['Spielrunde'];
            }
        }
        return $this->games->select()->where(["Spielrunde" => $spielrunde[0]])->orderBy("Spielnummer")->get(new StandingConverter());
    }

    public function second(): string
    {
        $standings = $this->standings();
        $standing = $standings[1];
        $team = $standing->team;

        if ($team == self::team($team)) {
            $team = self::unmap_team($team);
        }

        return $team;
    }

    public function standings(): array
    {
        return $this->standings->select()->orderBy(["Points" => CSVDB::DESC])->get(new TeamConverter());
    }

    public static function team(string $team): string
    {
        $teams = self::mapping_teams();
        return $teams[$team];
    }

    public static function mapping_teams(): array
    {
        return [
            "BSC Young Boys" => "YB",
            "FC Basel 1893" => "Basel",
            "Servette FC" => "Servette",
            "Grasshopper Club Zürich" => "GC",
            "FC St. Gallen 1879" => "St.Gallen",
            "FC Luzern" => "Luzern",
            "FC Sion" => "Sion",
            "FC Winterthur" => "Winti",
            "FC Lugano" => "Lugano",
            "FC Zürich" => "FCZ",

            "YB" => "YB",
            "Lugano" => "Lugano",
            "Servette" => "Servette",
            "St. Gallen 1879" => "St.Gallen",
            "Winterthur" => "Winti",
            "Zürich" => "FCZ"
        ];
    }

    public static function unmap_team(string $team): string
    {
        $teams = self::unmapping_teams();
        return $teams[$team];
    }

    public static function unmapping_teams(): array
    {
        return [
            "YB" => "BSC Young Boys",
            "Basel" => "FC Basel 1893",
            "Servette" => "Servette FC",
            "GC" => "Grasshopper Club Zürich",
            "St.Gallen" => "FC St. Gallen 1879",
            "Luzern" => "FC Luzern",
            "Sion" => "FC Sion",
            "Winti" => "FC Winterthur",
            "Lugano" => "FC Lugano",
            "FCZ" => "FC Zürich",
            "Winterthur" => "FC Winterthur",
            "Zürich" => "FC Zürich",
        ];
    }
}