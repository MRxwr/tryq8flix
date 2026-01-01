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

    function initSudoku() {
        const html = `
        <div class="sudoku-container text-center">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="m-0">Sudoku</h3>
                <div>
                    <button class="btn btn-sm btn-outline-light me-2" onclick="generateSudoku('easy')">New Game</button>
                    <span id="sudoku-timer">00:00</span>
                </div>
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
        generateSudoku('easy');
    }

    let timerInterval;

    function startTimer() {
        let seconds = 0;
        clearInterval(timerInterval);
        timerInterval = setInterval(() => {
            seconds++;
            const mins = Math.floor(seconds / 60).toString().padStart(2, '0');
            const secs = (seconds % 60).toString().padStart(2, '0');
            $('#sudoku-timer').text(`${mins}:${secs}`);
        }, 1000);
    }

    function generateSudoku(difficulty) {
        // Basic Sudoku Generator (Simplified for demonstration)
        // In a real app, you'd want a more robust algorithm
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

        // Shuffle rows/cols within blocks to create variety
        solvedBoard = JSON.parse(JSON.stringify(base));
        sudokuBoard = JSON.parse(JSON.stringify(solvedBoard));

        // Randomly remove numbers based on difficulty
        let removeCount = 40; // easy
        while (removeCount > 0) {
            let r = Math.floor(Math.random() * 9);
            let c = Math.floor(Math.random() * 9);
            if (sudokuBoard[r][c] !== 0) {
                sudokuBoard[r][c] = 0;
                removeCount--;
            }
        }

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
        alert('Congratulations! You solved it!');
    }
</script>