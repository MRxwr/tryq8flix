<?php
/**
 * Game scores — the storage behind the shared Games Center scoreboard.
 *
 * One row per user and game, holding their best score for that game. The board
 * is shared in the sense that every game frame reports into the same table and
 * the same read renders every game at once; each user still only ever sees and
 * writes their own row.
 *
 *   endpoint=Scores&action=list            -> my scores + the site best per game
 *   endpoint=Scores&action=save (POST)     -> game, score   (keeps the higher)
 *
 * Requires the `gamescores` table, which is created on first use below. The same
 * DDL is repeated in a comment so it can be run by hand on a host where the
 * application user has no CREATE right.
 */

// Token validation
if ( empty($token) ) {
    echo dataError(array("msg" => "Unauthorized")); die();
}
$user = selectDB("users", "`keepalive` = '{$token}'");
if ( !$user ) {
    echo dataError(array("msg" => "Invalid token")); die();
}
$userId = (int) $user[0]['id'];

$scoresTable = "gamescores";

/**
 * CREATE TABLE IF NOT EXISTS is a no-op once the table is there, so the board
 * works on a fresh database without an install step. A host that forbids CREATE
 * fails here silently and then fails the read, which is the same shape of error
 * every other endpoint produces.
 *
 *   CREATE TABLE `gamescores` (
 *     `id` INT NOT NULL AUTO_INCREMENT,
 *     `userId` INT NOT NULL,
 *     `game` VARCHAR(32) NOT NULL,
 *     `score` INT NOT NULL DEFAULT 0,
 *     `date` DATETIME NOT NULL,
 *     PRIMARY KEY (`id`),
 *     UNIQUE KEY `userGame` (`userId`,`game`),
 *     KEY `gameScore` (`game`,`score`)
 *   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
 */
function scoresEnsureTable($table)
{
    GLOBAL $dbconnect;
    $sql = "CREATE TABLE IF NOT EXISTS `{$table}` ("
        . "`id` INT NOT NULL AUTO_INCREMENT,"
        . "`userId` INT NOT NULL,"
        . "`game` VARCHAR(32) NOT NULL,"
        . "`score` INT NOT NULL DEFAULT 0,"
        . "`date` DATETIME NOT NULL,"
        . "PRIMARY KEY (`id`),"
        . "UNIQUE KEY `userGame` (`userId`,`game`),"
        . "KEY `gameScore` (`game`,`score`)"
        . ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    return $dbconnect->query($sql);
}

/** A game id is only accepted if it names a module that actually exists. */
function scoresGameSlug($value)
{
    if ( !is_string($value) || !preg_match('/^[a-z0-9]{1,32}$/', $value) ) {
        return "";
    }
    if ( !is_file(dirname(__DIR__, 2) . "/games/" . $value . ".php") ) {
        return "";
    }
    return $value;
}

scoresEnsureTable($scoresTable);

$action = isset($_GET["action"]) ? $_GET["action"] : "";

if ( $action == "list" ) {

    $scores = selectDBNew($scoresTable, [$userId], "`userId` = ?", "");
    $mine = array();
    if ( $scores ) {
        foreach ( $scores as $row ) {
            $mine[] = array(
                "game" => $row["game"],
                "score" => (int) $row["score"],
                "date" => $row["date"]
            );
        }
    }

    // The site record for each game. Scores only — never whose they are.
    $records = array();
    $result = $dbconnect->query("SELECT `game`, MAX(`score`) AS `score` FROM `{$scoresTable}` GROUP BY `game`");
    if ( $result ) {
        while ( $row = $result->fetch_assoc() ) {
            $records[$row["game"]] = (int) $row["score"];
        }
    }

    echo dataOutput(array(
        "scores" => $mine,
        "records" => $records
    ));
    die();
}

if ( $action == "save" ) {

    $game = scoresGameSlug(isset($_POST["game"]) ? $_POST["game"] : "");
    if ( $game === "" ) {
        echo dataError(array("msg" => "Unknown game")); die();
    }
    if ( !isset($_POST["score"]) || !is_numeric($_POST["score"]) ) {
        echo dataError(array("msg" => "Score is required")); die();
    }

    $score = (int) $_POST["score"];
    if ( $score < 0 || $score > 1000000 ) {
        echo dataError(array("msg" => "Score out of range")); die();
    }

    $existing = selectDBNew($scoresTable, [$userId, $game], "`userId` = ? AND `game` = ?", "");

    if ( $existing ) {
        $previous = (int) $existing[0]["score"];
        if ( $score > $previous ) {
            $updated = updateDB(
                $scoresTable,
                array("score" => $score, "date" => $date),
                "`userId` = '{$userId}' AND `game` = '{$game}'"
            );
            if ( !$updated ) {
                echo dataError(array("msg" => "Could not save score")); die();
            }
            $best = $score;
        } else {
            // A lower run never counts, and the stored best stands.
            $best = $previous;
        }
        echo dataOutput(array(
            "game" => $game,
            "score" => $score,
            "best" => $best,
            "previous" => $previous,
            "isRecord" => $score > $previous
        ));
        die();
    }

    $inserted = insertDB($scoresTable, array(
        "userId" => $userId,
        "game" => $game,
        "score" => $score,
        "date" => $date
    ));
    if ( !$inserted ) {
        echo dataError(array("msg" => "Could not save score")); die();
    }

    echo dataOutput(array(
        "game" => $game,
        "score" => $score,
        "best" => $score,
        "previous" => 0,
        "isRecord" => true
    ));
    die();
}

echo dataError(array("msg" => "Action is required"));
