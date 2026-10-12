<?php
declare(strict_types=1);

/**
 * Games Center — the arcade catalog and the per-account best score behind the
 * shared board.
 *
 * The modules in /games/ are the legacy arcade ports. Each ships its own styles,
 * full-page overlays and globals, so game.php hosts exactly one of them per
 * document and nothing here asks a module to cooperate beyond the final readout
 * it already renders on screen (assets/js/scoreboard.js reads that).
 *
 * One row per account and game holds that account's best, so a lower run never
 * replaces a higher one. A score is always read and written for the signed-in
 * account only; the site record per game is an aggregate that never names anyone.
 */

function gameCatalog(): array
{
    $games = [
        ['id'=>'sudoku','title'=>'Sudoku','tag'=>'Strategy','description'=>'A classic logic-based, combinatorial number-placement puzzle.','image'=>'https://img.freepik.com/premium-vector/sudoku-game-concept-illustration_23-2148606411.jpg'],
        ['id'=>'snake','title'=>'Neon Snake','tag'=>'Arcade','description'=>'Classic neon snake game with obstacles and power-ups.','image'=>'https://img.freepik.com/premium-vector/snake-game-neon-style_23-2148606410.jpg'],
        ['id'=>'tictactoe','title'=>'Tic Tac Toe','tag'=>'Classic','description'=>'Play the ultimate X vs O battle against a friend or our AI.','image'=>'https://img.freepik.com/premium-vector/tic-tac-toe-game-concept-illustration_23-2148606412.jpg'],
        ['id'=>'runner','title'=>'Cyber Runner','tag'=>'Action','description'=>'A high-speed endless runner. Dodge obstacles and survive the neon grid.','image'=>'https://img.freepik.com/premium-vector/online-game-banner-neon-runner_23-2148530665.jpg'],
        ['id'=>'dino','title'=>'Cyber Dino','tag'=>'Arcade','description'=>'Escape the digital extinction. Jump and duck through the neon desert.','image'=>'https://img.freepik.com/premium-vector/t-rex-dinosaur-pixel-art-vector-illustration_611584-30.jpg'],
        ['id'=>'jumper','title'=>'Cyber Jumper','tag'=>'Arcade','description'=>'Reach for the neon stars. A gravity-defying vertical adventure.','image'=>'https://img.freepik.com/premium-vector/rocket-space-minimalist-pixel-art_611584-142.jpg'],
        ['id'=>'flap','title'=>'Cyber Flap','tag'=>'Arcade','description'=>"Navigate the data pipes. A neon reimagining of a classic bird's flight.",'image'=>'https://img.freepik.com/free-vector/pixel-art-yellow-bird-flying_611584-3.jpg'],
        ['id'=>'drive','title'=>'Neon Highway','tag'=>'Racing','description'=>'Drive through the digital congestion. Dodge dumpsters and broken code cars.','image'=>'https://img.freepik.com/free-vector/pixel-art-racing-car-on-neon-background_611584-1.jpg'],
        ['id'=>'bricks','title'=>'Cyber Bricks','tag'=>'Arcade','description'=>'Shatter the digital firewall. A glowing breakout experience with power-ups.','image'=>'https://img.freepik.com/free-vector/pixel-art-background-with-bricks_23-2148117768.jpg'],
        ['id'=>'defender','title'=>'Data Defender','tag'=>'Action','description'=>'Protect the core from viral incursions. Modern retro space combat.','image'=>'https://img.freepik.com/free-vector/pixel-art-space-invaders-background_23-2148117769.jpg'],
        ['id'=>'memory','title'=>'Neural Link','tag'=>'Puzzle','description'=>'Sync your neural pathways. A high-speed memory match terminal.','image'=>'https://img.freepik.com/free-vector/pixel-art-brain-concept_23-2148117770.jpg'],
        ['id'=>'merge','title'=>'Cyber Merge','tag'=>'Puzzle','description'=>'Fuse data blocks to reach the master node. Addictive 2048 logic.','image'=>'https://img.freepik.com/free-vector/pixel-art-number-blocks_23-2148117771.jpg'],
        ['id'=>'smasher','title'=>'Bug Smasher','tag'=>'Arcade','description'=>'Eliminate the glitches popping up in the server room. Fast-paced tapping.','image'=>'https://img.freepik.com/free-vector/pixel-art-bug-character_23-2148117772.jpg'],
        ['id'=>'code','title'=>'Code Breaker','tag'=>'Strategy','description'=>'Decrypt the daily security keyword in 6 attempts. Strategy meets hacking.','image'=>'https://img.freepik.com/free-vector/pixel-art-terminal-concept_23-2148117773.jpg'],
    ];
    // An entry is only offered while its module is really on disk.
    return array_values(array_filter($games, static fn(array $game): bool => gameSlug($game['id']) !== ''));
}

/** A game id is accepted only if it names a module that actually exists. */
function gameSlug(mixed $value): string
{
    if (!is_string($value) || !preg_match('/^[a-z0-9]{1,32}$/D', $value)) return '';
    return is_file(V2_ROOT . '/../games/' . $value . '.php') ? $value : '';
}

/**
 * The catalog with this account's best per game and the site record beside it.
 * A null account still gets the catalog — playing needs no account, only saving
 * does — with every score left empty.
 */
function gameBoard(?int $userId): array
{
    $games = gameCatalog();
    $mine = [];
    if ($userId !== null) {
        foreach (query('SELECT game,score FROM game_scores WHERE user_id=?', [$userId])->fetchAll() as $row) $mine[$row['game']] = (int) $row['score'];
    }
    $records = [];
    foreach (query('SELECT game,MAX(score) AS score FROM game_scores GROUP BY game')->fetchAll() as $row) $records[$row['game']] = (int) $row['score'];
    $played = 0;
    foreach ($games as $index => $game) {
        $games[$index]['mine'] = $mine[$game['id']] ?? null;
        $games[$index]['record'] = $records[$game['id']] ?? null;
        if ($games[$index]['mine'] !== null) $played++;
    }
    return ['games'=>$games, 'played'=>$played, 'total'=>count($games), 'signed_in'=>$userId !== null];
}

/** Keep the higher of the stored best and this run. A lower run never counts. */
function saveGameScore(int $userId, mixed $game, mixed $score): array
{
    $slug = gameSlug($game);
    if ($slug === '') throw new AppError('unknown_game', 'That game is not available.', 404);
    if (!is_numeric($score) || (float) $score !== (float) (int) $score || (int) $score < 0 || (int) $score > 1000000) {
        throw new AppError('invalid_score', 'A score must be a whole number between 0 and 1000000.');
    }
    $score = (int) $score;
    $previous = (int) (query('SELECT score FROM game_scores WHERE user_id=? AND game=?', [$userId, $slug])->fetchColumn() ?: 0);
    if ($score > $previous) {
        query('INSERT INTO game_scores(user_id,game,score,updated_at) VALUES(?,?,?,UTC_TIMESTAMP()) ON DUPLICATE KEY UPDATE score=VALUES(score),updated_at=UTC_TIMESTAMP()', [$userId, $slug, $score]);
    }
    return ['game'=>$slug, 'score'=>$score, 'best'=>max($previous, $score), 'previous'=>$previous, 'is_record'=>$score > $previous];
}
