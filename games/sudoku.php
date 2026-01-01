<!-- Sudoku CSS -->
<style>
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

    #sudoku-timer {
        font-family: 'Courier New', Courier, monospace;
        font-size: 1.2rem;
        font-weight: bold;
        color: #e50914;
        letter-spacing: 2px;
    }
</style>

<!-- Sudoku Modals -->
<div class="modal fade" id="difficultyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-white" style="background: #181818; border: 1px solid #333;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title w-100 text-center mt-3">Select Difficulty</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-4">
                <p class="text-white-50 mb-4">Choose your challenge level to start a new game.</p>
                <div class="d-grid gap-3">
                    <button class="btn btn-outline-success py-3" onclick="setDifficultyAndStart('easy')">
                        <i class="fas fa-leaf me-2"></i> Easy
                        <div class="small opacity-50">Great for beginners</div>
                    </button>
                    <button class="btn btn-outline-warning py-3" onclick="setDifficultyAndStart('medium')">
                        <i class="fas fa-mountain me-2"></i> Medium
                        <div class="small opacity-50">A balanced challenge</div>
                    </button>
                    <button class="btn btn-outline-danger py-3" onclick="setDifficultyAndStart('hard')">
                        <i class="fas fa-fire me-2"></i> Hard
                        <div class="small opacity-50">For Sudoku masters</div>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-white" style="background: #181818; border: 1px solid #e50914; border-radius: 16px;">
            <div class="modal-body text-center p-5">
                <div class="mb-4">
                    <i class="fas fa-trophy fa-4x text-warning"></i>
                </div>
                <h2 class="mb-3">Victory!</h2>
                <p class="text-white-50 mb-4">Amazing job! You've successfully solved the puzzle.</p>
                <div class="stats-box d-flex justify-content-around mb-4 p-3 rounded" style="background: rgba(255,255,255,0.05);">
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

<!-- Victory Sound -->
<audio id="victorySound" src="https://assets.mixkit.co/active_storage/sfx/2013/2013-preview.mp3" preload="auto"></audio>

<script>
    let sudokuBoard = [];
    let solvedBoard = [];
    let selectedCell = null;
    let currentDifficulty = 'easy';
    let timerInterval;

    function initSudoku() {
        const html = `
        <div class="sudoku-container text-center">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="m-0">Sudoku</h3>
                <div id="sudoku-timer">00:00</div>
            </div>
            <div class="mb-4 d-flex justify-content-center">
                <button class="btn btn-netflix px-4" onclick="promptNewGame()"><i class="fas fa-sync-alt me-2"></i> New Game</button>
            </div>
            <div class="sudoku-grid" id="sudoku-grid"></div>
            <div class="number-pod">
                ${[1,2,3,4,5,6,7,8,9].map(n => `<button class="num-btn" onclick="inputNumber(${n})">${n}</button>`).join('')}
                <button class="num-btn eraser-btn" onclick="inputNumber(0)"><i class="fas fa-eraser me-2"></i> Erase</button>
            </div>
        </div>
    `;
        $('#game-container').html(html);
        promptNewGame();
    }

    function promptNewGame() {
        const modal = new bootstrap.Modal(document.getElementById('difficultyModal'));
        modal.show();
    }

    function setDifficultyAndStart(level) {
        currentDifficulty = level;
        const modalEl = document.getElementById('difficultyModal');
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();
        generateSudoku();
    }

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

        function shuffleBoard(arr) {
            let board = JSON.parse(JSON.stringify(arr));
            for (let i = 0; i < 3; i++) {
                let r1 = i * 3 + Math.floor(Math.random() * 3);
                let r2 = i * 3 + Math.floor(Math.random() * 3);
                [board[r1], board[r2]] = [board[r2], board[r1]];
            }
            return board;
        }

        solvedBoard = shuffleBoard(base);
        sudokuBoard = JSON.parse(JSON.stringify(solvedBoard));

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
        startTimer();
        renderBoard();
    }

    function renderBoard() {
        let html = '';
        for (let r = 0; r < 9; r++) {
            for (let c = 0; c < 9; c++) {
                const val = sudokuBoard[r][c];
                html += `<div class="sudoku-cell ${val !== 0 ? 'fixed' : ''}" data-r="${r}" data-c="${c}" onclick="selectCell(${r}, ${c})">${val !== 0 ? val : ''}</div>`;
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
        $(`.sudoku-cell[data-r="${r}"]`).addClass('highlight');
        $(`.sudoku-cell[data-c="${c}"]`).addClass('highlight');
        const startR = Math.floor(r / 3) * 3;
        const startC = Math.floor(c / 3) * 3;
        for (let i = startR; i < startR + 3; i++) {
            for (let j = startC; j < startC + 3; j++) {
                $(`.sudoku-cell[data-r="${i}"][data-c="${j}"]`).addClass('highlight');
            }
        }
        if (val !== 0) {
            $('.sudoku-cell').each(function() {
                const row = $(this).data('r');
                const col = $(this).data('c');
                if (sudokuBoard[row][col] === val && (row !== r || col !== c)) $(this).addClass('same-num');
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
        if (num !== 0 && num !== solvedBoard[r][c]) cell.addClass('error');
        else cell.removeClass('error');
        checkWin();
    }

    function checkWin() {
        for (let r = 0; r < 9; r++) {
            for (let c = 0; c < 9; c++) {
                if (sudokuBoard[r][c] !== solvedBoard[r][c]) return false;
            }
        }
        clearInterval(timerInterval);
        const sound = document.getElementById('victorySound');
        if (sound) {
            sound.currentTime = 0;
            sound.play().catch(e => {});
        }
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
            if (Date.now() < end) requestAnimationFrame(frame);
        }());
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
        promptNewGame();
    }
</script>