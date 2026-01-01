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

    /* Sudoku Specific Styles */
    .sudoku-container {
        max-width: 500px;
        margin: 0 auto;
        user-select: none;
        background: rgba(255, 255, 255, 0.03);
        padding: 2rem;
        border-radius: 16px;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .sudoku-grid {
        display: grid;
        grid-template-columns: repeat(9, 1fr);
        border: 2px solid #fff;
        background: #fff;
        gap: 1.5px;
        border-radius: 4px;
        overflow: hidden;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
    }

    .sudoku-cell {
        aspect-ratio: 1;
        background: #141414;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: clamp(1rem, 4vw, 1.5rem);
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        color: #fff;
    }

    .sudoku-cell:hover {
        background: #252525;
    }

    .sudoku-cell.fixed {
        background: #1a1a1a;
        color: #777;
        cursor: default;
    }

    .sudoku-cell.selected {
        background: #e50914 !important;
        color: white !important;
        transform: scale(1.05);
        z-index: 5;
        box-shadow: 0 0 15px rgba(229, 9, 20, 0.5);
    }

    .sudoku-cell.highlight {
        background: #2a2a2a;
    }

    .sudoku-cell.same-num {
        background: #333;
        color: #e50914;
        font-weight: bold;
    }

    .sudoku-cell.error {
        color: #ff3333 !important;
        background: rgba(255, 0, 0, 0.1);
    }

    /* Bold borders for 3x3 grids */
    .sudoku-cell:nth-child(3n) {
        border-right: 2.5px solid #fff;
    }

    .sudoku-cell:nth-child(9n) {
        border-right: none;
    }

    .sudoku-grid>div:nth-child(n+19):nth-child(-n+27),
    .sudoku-grid>div:nth-child(n+46):nth-child(-n+54) {
        border-bottom: 2.5px solid #fff;
    }

    .number-pod {
        display: flex;
        justify-content: center;
        gap: 8px;
        margin-top: 2rem;
        flex-wrap: wrap;
    }

    .num-btn {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 12px;
        color: white;
        font-size: 1.2rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }

    .num-btn:hover {
        background: #e50914;
        border-color: #e50914;
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(229, 9, 20, 0.3);
    }

    .num-btn.eraser-btn {
        background: rgba(255, 255, 255, 0.1);
        width: auto;
        padding: 0 20px;
    }

    #sudoku-timer {
        font-family: 'Courier New', Courier, monospace;
        font-size: 1.2rem;
        font-weight: bold;
        color: #e50914;
        letter-spacing: 2px;
    }
</style>

<div class="container" style="margin-top: 100px; min-height: 80vh;">
    <div id="games-home-view">
        <div class="d-flex justify-content-between align-items-center mb-4 px-4">
            <h2 class="section-title m-0 p-0">Games Center</h2>
            <div class="text-white-50">Choose a game to play</div>
        </div>
        <div id="games-grid" class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4 px-4">
            <!-- Games injected here -->
            <div class="col text-center py-5 w-100">
                <div class="spinner-border text-danger" role="status"></div>
            </div>
        </div>
    </div>

    <div id="game-play-view" style="display: none;">
        <button class="btn btn-link text-white-50 text-decoration-none mb-4" onclick="showGamesHome()">
            <i class="fas fa-arrow-left me-2"></i> Back to Games
        </button>
        <div id="game-container">
            <!-- Specific game UI injected here -->
        </div>
    </div>
</div>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-white">
            <div class="modal-body text-center p-5">
                <div class="mb-4">
                    <i class="fas fa-trophy fa-4x text-warning"></i>
                </div>
                <h2 class="mb-3">Victory!</h2>
                <p class="text-white-50 mb-4">Amazing job! You've successfully solved the puzzle.</p>

                <div class="stats-box d-flex justify-content-around">
                    <div>
                        <div class="text-white-50 small">Difficulty</div>
                        <div id="modal-diff" class="fw-bold text-capitalize">Easy</div>
                    </div>
                    <div>
                        <div class="text-white-50 small">Time</div>
                        <div id="modal-time" class="fw-bold">00:00</div>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button class="btn btn-netflix" onclick="restartSudoku()">Play Again</button>
                    <button class="btn btn-outline-light" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sound Effect -->
<audio id="victorySound" src="https://assets.mixkit.co/active_storage/sfx/2013/2013-preview.mp3" preload="auto"></audio>

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
        $('#games-home-view').fadeIn();
        $('#game-container').empty();
    }

    function playGame(gameId) {
        $('#games-home-view').hide();
        $('#game-play-view').fadeIn();

        if (gameId === 'sudoku') {
            initSudoku();
        }
    }

    // --- Sudoku Game Logic ---
    let sudokuBoard = [];
    let solvedBoard = [];
    let selectedCell = null;
    let currentDifficulty = 'easy';

    function initSudoku() {
        const html = `
        <div class="sudoku-container text-center">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="m-0">Sudoku</h3>
                <div id="sudoku-timer">00:00</div>
            </div>
            
            <div class="mb-4 d-flex justify-content-center gap-2">
                <button class="btn btn-sm btn-outline-success diff-btn active" data-level="easy" onclick="setDifficulty('easy')">Easy</button>
                <button class="btn btn-sm btn-outline-warning diff-btn" data-level="medium" onclick="setDifficulty('medium')">Medium</button>
                <button class="btn btn-sm btn-outline-danger diff-btn" data-level="hard" onclick="setDifficulty('hard')">Hard</button>
                <button class="btn btn-sm btn-netflix ms-2" onclick="generateSudoku()"><i class="fas fa-sync-alt"></i> New Game</button>
            </div>

            <div class="sudoku-grid" id="sudoku-grid"></div>
            <div class="number-pod">
                ${[1,2,3,4,5,6,7,8,9].map(n => `<button class="num-btn" onclick="inputNumber(${n})">${n}</button>`).join('')}
                <button class="num-btn eraser-btn" onclick="inputNumber(0)"><i class="fas fa-eraser me-2"></i> Erase</button>
            </div>
        </div>
    `;
        $('#game-container').html(html);
        startTimer();
        generateSudoku();
    }

    function setDifficulty(level) {
        currentDifficulty = level;
        $('.diff-btn').removeClass('active');
        $(`.diff-btn[data-level="${level}"]`).addClass('active');
        generateSudoku();
    }

    let timerInterval;

    function startTimer() {
        let seconds = 0;
        $('#sudoku-timer').text('00:00');
        clearInterval(timerInterval);
        timerInterval = setInterval(() => {
            seconds++;
            const mins = Math.floor(seconds / 60).toString().padStart(2, '0');
            const secs = (seconds % 60).toString().padStart(2, '0');
            $('#sudoku-timer').text(`${mins}:${secs}`);
        }, 1000);
    }

    function generateSudoku() {
        // Basic Sudoku Generator
        const base = [
            [5, 3, 4, 6, 7, 8, 9, 1, 2],
            [6, 7, 2, 1, 9, 5, 3, 4, 8],
            [1, 9, 8, 3, 4, 2, 5, 6, 7],
            [8, 5, 9, 7, 6, 1, 4, 2, 3],
            [4, 2, 6, 8, 5, 3, 7, 9, 1],
            [7, 1, 3, 9, 2, 4, 8, 5, 6],
            [9, 6, 1, 5, 3, 7, 2, 8, 4],
            [2, 8, 7, 4, 1, 9, 6, 3, 5],
            [3, 4, 5, 2, 8, 6, 1, 7, 9]
        ];

        // Shift the base board to create variety
        function shuffleBoard(arr) {
            let board = JSON.parse(JSON.stringify(arr));
            // Shuffle blocks of 3 rows
            for (let i = 0; i < 3; i++) {
                let r1 = i * 3 + Math.floor(Math.random() * 3);
                let r2 = i * 3 + Math.floor(Math.random() * 3);
                [board[r1], board[r2]] = [board[r2], board[r1]];
            }
            return board;
        }

        solvedBoard = shuffleBoard(base);
        sudokuBoard = JSON.parse(JSON.stringify(solvedBoard));

        // Difficulty Settings: Number of cells to remove
        const difficultyMap = {
            'easy': 30,
            'medium': 45,
            'hard': 55
        };

        let removeCount = difficultyMap[currentDifficulty] || 35;

        while (removeCount > 0) {
            let r = Math.floor(Math.random() * 9);
            let c = Math.floor(Math.random() * 9);
            if (sudokuBoard[r][c] !== 0) {
                sudokuBoard[r][c] = 0;
                removeCount--;
            }
        }

        startTimer(); // Reset timer for new game
        renderBoard();
    }

    function renderBoard() {
        let html = '';
        for (let r = 0; r < 9; r++) {
            for (let c = 0; c < 9; c++) {
                const val = sudokuBoard[r][c];
                const isFixed = val !== 0; // Simplified check for now
                html += `<div class="sudoku-cell ${val !== 0 ? 'fixed' : ''}" 
                        data-r="${r}" data-c="${c}" 
                        onclick="selectCell(${r}, ${c})">
                        ${val !== 0 ? val : ''}
                    </div>`;
            }
        }
        $('#sudoku-grid').html(html);
    }

    function selectCell(r, c) {
        $('.sudoku-cell').removeClass('selected highlight same-num');
        selectedCell = {
            r,
            c
        };
        const cell = $(`.sudoku-cell[data-r="${r}"][data-c="${c}"]`);
        const val = sudokuBoard[r][c];

        cell.addClass('selected');

        // Highlight same row and column
        $(`.sudoku-cell[data-r="${r}"]`).addClass('highlight');
        $(`.sudoku-cell[data-c="${c}"]`).addClass('highlight');

        // Highlight same 3x3 grid
        const startR = Math.floor(r / 3) * 3;
        const startC = Math.floor(c / 3) * 3;
        for (let i = startR; i < startR + 3; i++) {
            for (let j = startC; j < startC + 3; j++) {
                $(`.sudoku-cell[data-r="${i}"][data-c="${j}"]`).addClass('highlight');
            }
        }

        // Highlight cells with same number
        if (val !== 0) {
            $('.sudoku-cell').each(function() {
                const row = $(this).data('r');
                const col = $(this).data('c');
                if (sudokuBoard[row][col] === val && (row !== r || col !== c)) {
                    $(this).addClass('same-num');
                }
            });
        }
    }

    function inputNumber(num) {
        if (!selectedCell) return;
        const {
            r,
            c
        } = selectedCell;
        const cell = $(`.sudoku-cell[data-r="${r}"][data-c="${c}"]`);

        if (cell.hasClass('fixed')) return;

        sudokuBoard[r][c] = num;
        cell.text(num === 0 ? '' : num);

        if (num !== 0 && num !== solvedBoard[r][c]) {
            cell.addClass('error');
        } else {
            cell.removeClass('error');
        }

        checkWin();
    }

    function checkWin() {
        for (let r = 0; r < 9; r++) {
            for (let c = 0; c < 9; c++) {
                if (sudokuBoard[r][c] !== solvedBoard[r][c]) return false;
            }
        }

        // Stop Timer
        clearInterval(timerInterval);

        // Play sound
        const sound = document.getElementById('victorySound');
        if (sound) {
            sound.currentTime = 0;
            sound.play().catch(e => console.log("Sound could not play"));
        }

        // Fire Confetti
        const duration = 3 * 1000;
        const end = Date.now() + duration;

        (function frame() {
            confetti({
                particleCount: 5,
                angle: 60,
                spread: 55,
                origin: {
                    x: 0
                },
                colors: ['#e50914', '#ffffff']
            });
            confetti({
                particleCount: 5,
                angle: 120,
                spread: 55,
                origin: {
                    x: 1
                },
                colors: ['#e50914', '#ffffff']
            });

            if (Date.now() < end) {
                requestAnimationFrame(frame);
            }
        }());

        // Show Modal
        $('#modal-diff').text(currentDifficulty);
        $('#modal-time').text($('#sudoku-timer').text());
        const modalEl = document.getElementById('successModal');
        if (modalEl) {
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
        }
    }

    function restartSudoku() {
        const modalEl = document.getElementById('successModal');
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();
        generateSudoku();
    }
</script>