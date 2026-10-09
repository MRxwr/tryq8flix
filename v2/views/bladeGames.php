<?php include 'header.php'; ?>

<style>
    .game-card {
        background: #181818;
        border-radius: 8px;
        overflow: hidden;
        transition: all 0.3s ease;
        cursor: pointer;
        border: 1px solid #333;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .game-card:hover {
        transform: translateY(-10px);
        border-color: #e50914;
        box-shadow: 0 10px 20px rgba(229, 9, 20, 0.2);
    }

    .game-image {
        width: 100%;
        height: 180px;
        object-fit: cover;
    }

    .game-content {
        padding: 1.5rem;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    .game-title {
        font-size: 1.25rem;
        font-weight: bold;
        margin-bottom: 0.5rem;
        color: #fff;
    }

    .game-desc {
        font-size: 0.9rem;
        color: #b3b3b3;
        margin-bottom: 1.5rem;
        flex-grow: 1;
    }

    .game-tag {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        background: rgba(229, 9, 20, 0.1);
        color: #e50914;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: bold;
        text-transform: uppercase;
    }
</style>

<div class="container" style="margin-top: 100px; min-height: 80vh;">
    <!-- Games Home Grid -->
    <div id="games-home-view">
        <div class="d-flex justify-content-between align-items-center mb-4 px-4">
            <h2 class="section-title m-0 p-0">Games Center</h2>
            <div class="text-white-50">Choose a game to play</div>
        </div>
        <div id="games-grid" class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4 px-4">
            <div class="col text-center py-5 w-100">
                <div class="spinner-border text-danger" role="status"></div>
            </div>
        </div>
    </div>

    <!-- Game Viewport -->
    <div id="game-play-view" style="display: none;">
        <button class="btn btn-link text-white-50 text-decoration-none mb-4" onclick="showGamesHome()">
            <i class="fas fa-arrow-left me-2"></i> Back to Games
        </button>
        <div id="game-container">
            <!-- Game logic will inject content here -->
        </div>
    </div>
</div>

<!-- Load Game Modules -->
<?php
// Automatically include all game files from the games directory
$gamesPath = dirname(__DIR__) . '/games/';
if (is_dir($gamesPath)) {
    foreach (glob($gamesPath . "*.php") as $filename) {
        include $filename;
    }
}
?>

<?php include 'footer.php'; ?>

<script>
    $(document).ready(function() {
        loadGames();
    });

    function loadGames() {
        $.getJSON('api/index.php?endpoint=Games/Home&action=list', function(response) {
            if (response.ok && response.data && response.data.games) {
                let html = '';
                response.data.games.forEach(game => {
                    html += `
                    <div class="col">
                        <div class="game-card" onclick="playGame('${game.id}')">
                            <img src="${game.image}" class="game-image" alt="${game.title}">
                            <div class="game-content">
                                <div class="mb-2">
                                    <span class="game-tag">${game.tag}</span>
                                </div>
                                <h3 class="game-title">${game.title}</h3>
                                <p class="game-desc">${game.description}</p>
                                <button class="btn btn-netflix w-100">Play Now</button>
                            </div>
                        </div>
                    </div>
                `;
                });
                $('#games-grid').html(html);
            } else {
                const errorMsg = (response.data && response.data.msg) ? response.data.msg : 'Failed to load games.';
                $('#games-grid').html(`<div class="col-12 text-center py-5"><p class="text-white-50">${errorMsg}</p></div>`);
            }
        });
    }

    function showGamesHome() {
        $('#game-play-view').hide();
        $('.runner-overlay-modal').hide(); // Hide any active runner overlays
        $('.dino-overlay').hide(); // Hide any active dino overlays
        $('.jumper-overlay').hide(); // Hide any active jumper overlays
        $('.flap-overlay').hide(); // Hide any active flap overlays
        $('.drive-overlay').hide(); // Hide any active drive overlays
        $('.bricks-overlay').hide();
        $('.defender-overlay').hide();
        $('.memory-overlay').hide();
        $('.merge-overlay').hide();
        $('.smasher-overlay').hide();
        $('.code-overlay').hide();
        $('#games-home-view').fadeIn();
        $('#game-container').empty();
        // Stop any running game timers/intervals if they exist globally
        if (typeof clearInterval === 'function' && typeof timerInterval !== 'undefined') {
            clearInterval(timerInterval);
        }
    }

    function playGame(gameId) {
        $('#games-home-view').hide();
        $('#game-play-view').fadeIn();

        // Dynamically call the init function for the selected game
        // Convention: init[GameId] capitalize first letter
        const initFuncName = 'init' + gameId.charAt(0).toUpperCase() + gameId.slice(1);
        if (typeof window[initFuncName] === 'function') {
            window[initFuncName]();
        } else {
            $('#game-container').html('<div class="text-center py-5">Game module not found.</div>');
        }
    }
</script>