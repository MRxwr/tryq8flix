<?php
if (isset($_GET["action"]) && !empty($_GET["action"])) {
    if ($_GET["action"] == "list") {
        $games = array(
            array(
                "id" => "sudoku",
                "title" => "Sudoku",
                "description" => "A classic logic-based, combinatorial number-placement puzzle.",
                "image" => "https://img.freepik.com/premium-vector/sudoku-game-concept-illustration_23-2148606411.jpg",
                "tag" => "Strategy"
            ),
            array(
                "id" => "snake",
                "title" => "Neon Snake",
                "description" => "Classic neon snake game with obstacles and power-ups.",
                "image" => "https://img.freepik.com/premium-vector/snake-game-neon-style_23-2148606410.jpg",
                "tag" => "Arcade"
            ),
            array(
                "id" => "tictactoe",
                "title" => "Tic Tac Toe",
                "description" => "Play the ultimate X vs O battle against a friend or our AI.",
                "image" => "https://img.freepik.com/premium-vector/tic-tac-toe-game-concept-illustration_23-2148606412.jpg",
                "tag" => "Classic"
            )
        );
        echo dataOutput(array("games" => $games));
        die();
    }
} else {
    echo dataError(array("msg" => "Action is required"));
    die();
}
