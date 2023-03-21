$(document).ready(function () {
    function Team(team, played, points) {
        var self = this;

        self.team = ko.observable(team);
        self.played = ko.observable(played);
        self.points = ko.observable(points);
    }

    function AppViewModel() {
        const self = this;

        self.points = ko.pureComputed(function () {
            /*
            let predictions2 = self.predictions2();
            let teams = Object.keys(predictions2);

            if (teams.length>0) {
                let test = "test";
                $.each(teams, function (index, key) {
                    let games = Object.keys(predictions2[key]);
                    console.log("teams", key, predictions2[key]);
                    let standings = predictions2[key];
                    $.each(games, function (index, key) {
                        console.log("games", key, standings[key]);
                    });
                });
                return test;
            }
             */
            return "none";
        }, self);

        self.standings = ko.observableArray(standings.map(team => {
            return new Team(team['team'],team['played'],team['points']);
        }));

        self.games = ko.observableArray(games);

        self.predictions = ko.observableArray([]);
        self.predictions.subscribe(function (predictions) {
            let g = self.games();

            let games = Object.keys(predictions);
            $.each(games, function (index, key) {
                let spielnummer = key.replace('spielnummer', '');
                let test = g.findIndex(o => {
                    return o['spielnummer'].toString()===spielnummer
                });
                let test2 = g[test];
                test2['resultat']=predictions[key];
                console.log("game", spielnummer, predictions[key], g[test], test2);
            });
        });

        self.notify = function (team, spielnummer, standing, homeAway) {
            if (standing === undefined || standing === "") {
                let predictions = self.predictions();
                let prediction = predictions['spielnummer'+spielnummer];
                standing = "win";
                if (prediction !== undefined) {
                    standing = self.predictionIs(homeAway, prediction);
                }

                // css
                $(".game[data-spielnummer='" + spielnummer + "']").each(function () {
                    if ($(this).data("team") === team) {
                        $(this).removeClass(["win", "draw", "loss"]);
                        $(this).addClass(standing);
                    } else {
                        $(this).removeClass(["win", "draw", "loss"]);
                        $(this).addClass(self.oppositeStanding(standing));
                    }
                });
                // prediction
                predictions['spielnummer'+spielnummer] = self.prediction(homeAway, standing);
                self.predictions(predictions);
            }
        }

        self.oppositeStanding = function (standing) {
            if (standing === "win") {
                return "loss";
            } else if (standing === "loss") {
                return "win";
            }
            return standing;
        }

        self.prediction = function (homeAway, standing) {
            if (standing === "draw") {
                return 0;
            } else {
                if (homeAway === "home") {
                    return ((standing === "win") ? 1 : 2);
                } else {
                    return ((standing === "win") ? 2 : 1);
                }
            }
        }

        self.predictionIs = function (homeAway, prediction) {
            if (homeAway === "home") {
                if (prediction === 1) {
                    return "draw";
                } else if (prediction === 0) {
                    return "loss";
                } else {
                    return "win";
                }
            } else {
                if (prediction === 2) {
                    return "draw";
                } else if (prediction === 0) {
                    return "loss";
                } else {
                    return "win";
                }
            }
        }
    }

    // Activates knockout.js
    ko.applyBindings(new AppViewModel());
});