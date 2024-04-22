$(document).ready(function () {
    function Team(team, played, points, parent) {
        var self = this;

        self.team = ko.observable(team);
        self.played = ko.observable(played);
        self.initialPoints = ko.observable(points);
        self.points = ko.pureComputed(function () {
            let points = self.initialPoints();
            // games
            let games = _.filter(parent.currentGames(), function (o) {
                return (o.teamA === team || o.teamB === team) && o.resultat !== null;
            });

            $.each(games, function (index, value) {
                if (value.resultat === 0) {
                    points += 1;
                } else {
                    if (value.resultat === 1 && value.teamA === team || value.resultat === 2 && value.teamB === team) {
                        points += 3;
                    }
                }
            });

            return points;
        });
        self.predictablePoints = function (amount) {
            let points = self.initialPoints();
            // games
            let games = _.filter(parent.currentGames(), function (o) {
                return (o.teamA === team || o.teamB === team) && o.resultat !== null;
            });

            $.each(games, function (_, game) {
                if (game.spielrunde <= amount) {
                    if (game.resultat === 0) {
                        points += 1;
                    } else {
                        if (game.resultat === 1 && game.teamA === team || game.resultat === 2 && game.teamB === team) {
                            points += 3;
                        }
                    }
                }
            });
            return points;
        }
    }

    function AppViewModel() {
        const self = this;

        self.points = function (team_name) {
            return ko.pureComputed(function () {
                let team = _.find(self.standings(), function (o) {
                    return o.team() === team_name;
                });

                return team.points();
            });
        }

        self.diff = function (date) {
            let ts = Math.round((new Date()).getTime() / 1000);
            let diff = date - ts;
            return Math.round(diff / (3600 * 24));
        }

        self.meisterTitle = ko.pureComputed(function () {
            let meister = self.meister();
            let message = "⏳ weiw! ⏳";
            if (meister.champ === "nope") {
                message = "🤬 Nope! 🤬";
            } else if (meister.champ === "champ") {
                message = "🎉 YB wird in " + self.diff(meister.game.spieldatum) + " Tagen Meister! 🎉";
            } else if (meister.champ === "maybe") {
                message = "🤘 YB wird in " + self.diff(meister.game.spieldatum) + " Tagen Meister! 🤘";
            }
            return message;
        });
        self.meisterText = ko.pureComputed(function () {
            let meister = self.meister();
            let message = "witer, eifach immer witer";
            if (meister.champ === "nope") {
                message = "leider nein!";
            } else if (meister.champ === "champ") {
                let homeAway = (meister.game.teamA === "BSC Young Boys") ? "zuhause" : "auswärts";
                let opponent = (meister.game.teamA === "BSC Young Boys") ? meister.game.teamB : meister.game.teamA;
                let date = new Date(meister.game.spieldatum * 1000);
                let dateText = date.getDate() + "." + (date.getMonth() + 1) + ".";
                message = "in der " + meister.game.spielrunde + ". Runde, am " + dateText + ", " + homeAway + " gegen " + mapping[opponent];
            } else if (meister.champ === "maybe") {
                let homeAway = (meister.game.teamA === "BSC Young Boys") ? "zuhause" : "auswärts";
                let opponent = (meister.game.teamA === "BSC Young Boys") ? meister.game.teamB : meister.game.teamA;
                message = "am letzten Spieltag " + homeAway + " gegen " + mapping[opponent];
            }
            return message;
        });
        self.meisterClass = ko.pureComputed(function () {
            let meister = self.meister();
            return meister.champ;
        });

        self.meister = ko.pureComputed(function () {
            // first
            let first = self.standings()[0];

            // second
            let second = self.standings()[1];

            // games
            let totalGames = 38;
            let gamesLeft = first.played();
            let games = _.filter(self.currentGames(), function (o) {
                return o.teamA === first.team() || o.teamB === first.team();
            });

            let meister = _.find(games, function (game) {
                if (game.resultat !== null) {
                    gamesLeft++;
                    let abstand = game.points - second.predictablePoints(gamesLeft);
                    let possiblePoints = (totalGames - gamesLeft) * 3;
                    return (possiblePoints < abstand)
                }
                return false;
            });
            let gl = _.filter(games, function (o) {
                return o.resultat !== null;
            });

            gamesLeft = first.played()+gl.length;
            if (meister === undefined) {
                if (totalGames > gamesLeft) {
                    meister = {champ: "weiw"};
                } else {
                    meister = {champ: "nope"};
                }
            } else {
                if (meister.spielrunde < totalGames) {
                    meister = {champ: "champ", game: meister};
                } else {
                    meister = {champ: "maybe", game: meister};
                }
            }
            return meister;
        });

        self.standings = ko.observableArray(standings.map(team => {
            return new Team(unmapping[team['team']], team['played'], team['points'], self);
        }));

        self.games = ko.observableArray(games);
        self.currentGames = ko.pureComputed(function () {
            let ts = Math.round((new Date()).getTime() / 1000);
            return _.filter(self.games(), function (o) {
                return (o.spieldatum >= ts);
            });
        });

        self.predictions = ko.observableArray([]);
        self.predictions.subscribe(function (predictions) {
            let first = self.standings()[0];
            let g = self.games();

            let games = Object.keys(predictions);
            let points = first.initialPoints();
            $.each(games, function (_, key) {
                let spielnummer = key.replace('spielnummer', '');
                let index = g.findIndex(o => {
                    return o['spielnummer'].toString() === spielnummer
                });
                let spiel = g[index];
                spiel['resultat'] = predictions[key];
                if (spiel.teamA === first.team() || spiel.teamB === first.team()) {
                    let homeAway = spiel.teamA === first.team();
                    points = self.gamePoints(points, homeAway, predictions[key]);
                    spiel['points'] = points;
                }
            });
            self.games(g)
        });

        self.notify = function (team, spielnummer, standing, homeAway) {
            if (standing === undefined || standing === "") {
                let predictions = self.predictions();
                let prediction = predictions['spielnummer' + spielnummer];
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
                predictions['spielnummer' + spielnummer] = self.prediction(homeAway, standing);
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

        self.gamePoints = function (points, homeAway, standing) {
            if (standing === 0) {
                points += 1;
            } else {
                if (standing === 1 && homeAway || standing === 2 && !homeAway) {
                    points += 3;
                }
            }
            return points;
        }
    }

    // Activates knockout.js
    ko.applyBindings(new AppViewModel());
});