<html lang="de">
<head>
    <title>meisterrechner</title>

    <script
            src="https://code.jquery.com/jquery-3.6.3.min.js"
            integrity="sha256-pvPw+upLPUjgMXY0G+8O0xUf+/Im1MZjXxxgOcBQBXU="
            crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/lodash@4.17.21/lodash.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/knockout@3.5.1/build/output/knockout-latest.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/ko-reactor@1.4.2/dist/ko-reactor.min.js"></script>
    <script src="meisterrechner.js"></script>

    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="layout.css"/>

    <?php

    use MSTRRCHNR\MSTRRCHNR;

    require '../vendor/autoload.php';

    $games = __DIR__ . "/games.csv";
    $standings = __DIR__ . "/standings.csv";
    $mstrrchnr = new MSTRRCHNR($games, $standings);
    $second = $mstrrchnr->second();
    $third = $mstrrchnr->third();
    ?>

    <script>
        let standings = <?= json_encode($mstrrchnr->standings()) ?>;
        let games = <?= json_encode($mstrrchnr->games_json()) ?>;
        let mapping = <?= json_encode($mstrrchnr->mapping_teams()) ?>;
        let unmapping = <?= json_encode($mstrrchnr->unmapping_teams()) ?>;
    </script>
</head>
<body>

<nav class="navbar navbar-expand-lg bg-body-tertiary">
    <div class="container-fluid">
        <span class="navbar-brand"><strong>YB-Meisterrechner</strong> 2024</span>
    </div>
</nav>

<div class="container pt-4">
    <div class="card p-4" style="text-align: center;" data-bind="class: meisterClass">
        <h2 class="card-title" data-bind="text: meisterTitle"></h2>
        <strong data-bind="text: meisterText"></strong>
    </div>
    <div class="row">
        <div class="col-lg-4 col-sm-12">
            <div class="card mt-2 mb-2">
                <div class="card-body">
                    <h4 class="card-title m-1">YB</h4>
                    <div class="row">
                        <?php
                        foreach ($mstrrchnr->games() as $game) {
                            $team = $game->teamA();
                            $icon = "directions_bus";
                            if ($team == "YB") {
                                $icon = "home";
                                $team = $game->teamB();
                            }

                            echo "<div data-bind=\"click: notify.bind(this, 'BSC Young Boys','" . $game->spielnummer . "','" . $game->standing("BSC Young Boys") . "','" . $icon . "')\" class=\"card game m-1 " . $game->standing("BSC Young Boys") . "\" data-team=\"BSC Young Boys\" data-spielnummer=\"" . $game->spielnummer . "\" data-opponent=\"" . (($icon == "home") ? $game->teamA : $game->teamB) . "\" style=\"width: 23%;\">";
                            echo '<div class="card-body d-flex flex-column align-items-center">';
                            echo '<small>' . $game->spielrunde . '</small>';
                            echo '<span class="material-symbols-outlined">' . $icon . '</span>';
                            echo '<h6 class="card-title">' . $team . '</h6>';
                            echo $game->spieldatum();
                            echo "</div>";
                            echo "</div>";
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-sm-12 ">
            <div class="card mt-2 mb-2">
                <div class="card-body">
                    <h4 class="card-title m-1"><?= MSTRRCHNR::team($second) ?></h4>
                    <div class="row">
                        <?php
                        foreach ($mstrrchnr->games($second) as $game) {
                            $team = $game->teamA();
                            $icon = "directions_bus";
                            if ($team == MSTRRCHNR::team($second)) {
                                $icon = "home";
                                $team = $game->teamB();
                            }

                            echo "<div data-bind=\"click: notify.bind(this, '" . $team . "','" . $game->spielnummer . "','" . $game->standing($second) . "','" . $icon . "')\" class=\"card game m-1 " . $game->standing($second) . "\" data-team=\"" . $team . "\" data-spielnummer=\"" . $game->spielnummer . "\" style=\"width: 23%;\">";
                            echo '<div class="card-body d-flex flex-column align-items-center">';
                            echo '<small>' . $game->spielrunde . '</small>';
                            echo '<span class="material-symbols-outlined">' . $icon . '</span>';
                            echo '<h6 class="card-title">' . $team . '</h6>';
                            echo $game->spieldatum();
                            echo "</div>";
                            echo "</div>";
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-sm-12 ">
            <div class="card mt-2 mb-2">
                <div class="card-body">
                    <h4 class="card-title m-1"><?= MSTRRCHNR::team($third) ?></h4>
                    <div class="row">
                        <?php
                        foreach ($mstrrchnr->games($third) as $game) {
                            $team = $game->teamA();
                            $icon = "directions_bus";
                            if ($team == MSTRRCHNR::team($third)) {
                                $icon = "home";
                                $team = $game->teamB();
                            }

                            echo "<div data-bind=\"click: notify.bind(this, '" . $team . "','" . $game->spielnummer . "','" . $game->standing($third) . "','" . $icon . "')\" class=\"card game m-1 " . $game->standing($third) . "\" data-team=\"" . $team . "\" data-spielnummer=\"" . $game->spielnummer . "\" style=\"width: 23%;\">";
                            echo '<div class="card-body d-flex flex-column align-items-center">';
                            echo '<small>' . $game->spielrunde . '</small>';
                            echo '<span class="material-symbols-outlined">' . $icon . '</span>';
                            echo '<h6 class="card-title">' . $team . '</h6>';
                            echo $game->spieldatum();
                            echo "</div>";
                            echo "</div>";
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <hr/>

    <div class="row">
        <div class="col-lg-6 col-sm-12">
            <div class="card mt-2 mb-2">
                <div class="card-body">
                    <h4 class="card-title m-1">Tabelle</h4>
                    <table class="table">
                        <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Team</th>
                            <th scope="col">Punkte</th>
                            <th scope="col">Spiele</th>
                        </tr>
                        </thead>
                        <tbody data-bind="foreach: table">
                        <tr>
                            <td data-bind="text: $root.rank($index)"></td>
                            <td data-bind="text: team"></td>
                            <td data-bind="text: points"></td>
                            <td data-bind="text: played"></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-sm-12">
            <div class="card mt-2 mb-2">
                <div class="card-body">
                    <h4 class="card-title m-1">Spiele</h4>
                    <table class="table">
                        <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Datum</th>
                            <th scope="col" colspan="2">Partie</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        foreach ($mstrrchnr->schedule() as $schedule) {
                            echo '<tr>' .
                                '<th scope="row">' . $schedule->spielrunde . '</th>' .
                                '<td>' . date("d.m.y H:i", $schedule->spieldatum) . '</td>' .
                                '<td>' . $schedule->teamA . '</td>' .
                                '<td>' . $schedule->teamB . '</td>' .
                                '</tr>';
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>