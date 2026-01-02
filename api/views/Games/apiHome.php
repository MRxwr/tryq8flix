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
            ),
            array(
                "id" => "runner",
                "title" => "Cyber Runner",
                "description" => "A high-speed endless runner. Dodge obstacles and survive the neon grid.",
                "image" => "https://img.freepik.com/premium-vector/online-game-banner-neon-runner_23-2148530665.jpg",
                "tag" => "Action"
            ),
            array(
                "id" => "dino",
                "title" => "Cyber Dino",
                "description" => "Escape the digital extinction. Jump and duck through the neon desert.",
                "image" => "https://img.freepik.com/premium-vector/t-rex-dinosaur-pixel-art-vector-illustration_611584-30.jpg",
                "tag" => "Arcade"
            ),
            array(
                "id" => "jumper",
                "title" => "Cyber Jumper",
                "description" => "Reach for the neon stars. A gravity-defying vertical adventure.",
                "image" => "https://img.freepik.com/premium-vector/rocket-space-minimalist-pixel-art_611584-142.jpg",
                "tag" => "Arcade"
            ),
            array(
                "id" => "flap",
                "title" => "Cyber Flap",
                "description" => "Navigate the data pipes. A neon reimagining of a classic bird's flight.",
                "image" => "https://img.freepik.com/free-vector/pixel-art-yellow-bird-flying_611584-3.jpg",
                "tag" => "Arcade"
            ),
            array(
                "id" => "drive",
                "title" => "Neon Highway",
                "description" => "Drive through the digital congestion. Dodge dumpsters and broken code cars.",
                "image" => "https://img.freepik.com/free-vector/pixel-art-racing-car-on-neon-background_611584-1.jpg",
                "tag" => "Racing"
            )
        );
        echo dataOutput(array("games" => $games));
        die();
    }
} else {
    echo dataError(array("msg" => "Action is required"));
    die();
}
