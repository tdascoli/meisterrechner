<html lang="de">
<head>
    <title>meisterrechner</title>

    <script
            src="https://code.jquery.com/jquery-3.6.3.min.js"
            integrity="sha256-pvPw+upLPUjgMXY0G+8O0xUf+/Im1MZjXxxgOcBQBXU="
            crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/lodash@4.17.21/lodash.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/knockout@3.5.1/build/output/knockout-latest.min.js"></script>
    <script src="meisterrechner.js"></script>

    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <link rel="stylesheet" href="layout.css"/>

    <?php

    use MSTRRCHNR\MSTRRCHNR;

    require '../vendor/autoload.php';

    $games = __DIR__ . "\games.csv";
    $standings = __DIR__ . "\standings.csv";
    $mstrrchnr = new MSTRRCHNR($games, $standings);
    $second = $mstrrchnr->second();
    ?>

    <script>
        let standings = <?= json_encode($mstrrchnr->standings()) ?>;
        let games = <?= json_encode($mstrrchnr->games_json()) ?>;

        $(document).ready(function () {
            //console.log(standings[0]);
            //console.log(standings[1]);
        });
    </script>
</head>
<body>
<div class="container pt-4">
    <div class="d-flex">
        <div class="card m-1 w-50">
            <div class="card-body">
                <h4 class="card-title m-1">YB</h4>
                <div class="d-flex flex-wrap">
                    <?php
                    foreach ($mstrrchnr->games() as $game) {
                        $team = $game->teamA();
                        $icon = "directions_bus";
                        if ($team == "YB") {
                            $icon = "home";
                            $team = $game->teamB();
                        }

                        echo "<div data-bind=\"click: notify.bind(this, 'BSC Young Boys','" . $game->spielnummer . "','" . $game->standing("BSC Young Boys") . "','" . $icon . "')\" class=\"card game m-1 " . $game->standing("BSC Young Boys") . "\" data-team=\"BSC Young Boys\" data-spielnummer=\"" . $game->spielnummer . "\" style=\"width: 23%;\">";
                        echo '<div class="card-body d-flex flex-column align-items-center">';
                        echo '<small>' . $game->spielrunde . '</small>';
                        echo '<span class="material-symbols-outlined">' . $icon . '</span>';
                        echo '<h6 class="card-title">' . $team . '</h6>';
                        echo $game->spieldatum;
                        echo "</div>";
                        echo "</div>";
                    }
                    ?>
                </div>
            </div>
        </div>

        <div class="card m-1 w-50">
            <div class="card-body">
                <h4 class="card-title m-1"><?= MSTRRCHNR::team($second) ?></h4>
                <div class="d-flex flex-wrap">
                    <?php
                    foreach ($mstrrchnr->games($second) as $game) {
                        $team = $game->teamA();
                        $icon = "directions_bus";
                        if ($team == MSTRRCHNR::team($second)) {
                            $icon = "home";
                            $team = $game->teamB();
                        }

                        echo "<div data-bind=\"click: notify.bind(this, '" . $team . "','" . $game->spielnummer . "','" . $game->standing($team) . "','" . $icon . "')\" class=\"card game m-1 " . $game->standing($team) . "\" data-team=\"" . $team . "\" data-spielnummer=\"" . $game->spielnummer . "\" style=\"width: 23%;\">";
                        echo '<div class="card-body d-flex flex-column align-items-center">';
                        echo '<small>' . $game->spielrunde . '</small>';
                        echo '<span class="material-symbols-outlined">' . $icon . '</span>';
                        echo '<h6 class="card-title">' . $team . '</h6>';
                        echo $game->spieldatum;
                        echo "</div>";
                        echo "</div>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
<input data-bind="value: points" />
<pre>
    <?php
    var_dump($mstrrchnr->standings());
    ?>
</pre>
</body>
</html>