<!-- Tic Tac Toe CSS -->
<style>
    .ttt-container {
        max-width: 450px;
        margin: 0 auto;
        background: rgba(255, 255, 255, 0.03);
        padding: 2rem;
        border-radius: 24px;
        backdrop-filter: blur(15px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        text-align: center;
        user-select: none;
    }

    .ttt-board {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 15px;
        margin: 2rem 0;
    }

    .ttt-cell {
        aspect-ratio: 1;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 16px;
        border: 1px solid rgba(255, 255, 255, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        font-weight: 900;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    .ttt-cell:hover:not(.occupied) {
        background: rgba(255, 255, 255, 0.1);
        transform: translateY(-5px);
    }

    .ttt-cell.occupied {
        cursor: default;
    }

    .ttt-cell.x {
        color: #e50914;
        text-shadow: 0 0 15px rgba(229, 9, 20, 0.5);
    }

    .ttt-cell.o {
        color: #ffffff;
        text-shadow: 0 0 15px rgba(255, 255, 255, 0.5);
    }

    .ttt-status {
        font-size: 1.2rem;
        font-weight: bold;
        color: #fff;
        margin-bottom: 1rem;
        min-height: 1.8rem;
    }

    .ttt-scoreboard {
        display: flex;
        justify-content: space-around;
        background: rgba(0, 0, 0, 0.3);
        padding: 1rem;
        border-radius: 12px;
        margin-bottom: 1.5rem;
    }

    .score-item {
        display: flex;
        flex-direction: column;
    }

    .score-label {
        color: #888;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .score-value {
        font-size: 1.5rem;
        font-weight: bold;
    }

    .score-value.player-x {
        color: #e50914;
    }

    .score-value.player-o {
        color: #fff;
    }

    .winning-cell {
        animation: pulseWin 1s infinite alternate;
    }

    @keyframes pulseWin {
        from {
            transform: scale(1);
            box-shadow: 0 0 0px transparent;
        }

        to {
            transform: scale(1.05);
            box-shadow: 0 0 20px rgba(229, 9, 20, 0.4);
        }
    }
</style>

<!-- Tic Tac Toe Modals -->
<div class="modal fade" id="tttModeModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-white" style="background: #181818; border: 1px solid #333;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title w-100 text-center mt-3">Game Mode</h5>
            </div>
            <div class="modal-body text-center p-4">
                <p class="text-white-50 mb-4">Choose how you want to play</p>
                <div class="d-grid gap-3">
                    <button class="btn btn-netflix py-3" onclick="setTTTMode('ai')">
                        <i class="fas fa-robot me-2"></i> Vs Computer
                    </button>
                    <button class="btn btn-outline-light py-3" onclick="setTTTMode('pvp')">
                        <i class="fas fa-user-friends me-2"></i> 2 Players
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- TTT Sounds -->
<audio id="tttMoveSound" src="https://assets.mixkit.co/active_storage/sfx/2571/2571-preview.mp3" preload="auto"></audio>
<audio id="tttWinSound" src="https://assets.mixkit.co/active_storage/sfx/2013/2013-preview.mp3" preload="auto"></audio>

<script>
    let tttBoard = ['', '', '', '', '', '', '', '', ''];
    let currentPlayer = 'X';
    let tttGameActive = false;
    let tttMode = 'ai'; // ai or pvp
    let tttScores = {
        X: 0,
        O: 0,
        Ties: 0
    };

    function initTictactoe() {
        const html = `
            <div class="ttt-container">
                <div class="ttt-scoreboard">
                    <div class="score-item">
                        <span class="score-label">Player (X)</span>
                        <span id="score-X" class="score-value player-x">0</span>
                    </div>
                    <div class="score-item">
                        <span class="score-label">Ties</span>
                        <span id="score-Ties" class="score-value">0</span>
                    </div>
                    <div class="score-item">
                        <span class="score-label" id="score-label-o">${tttMode === 'ai' ? 'CPU' : 'Player'} (O)</span>
                        <span id="score-O" class="score-value player-o">0</span>
                    </div>
                </div>
                
                <div class="ttt-status" id="ttt-status">Your turn (X)</div>
                
                <div class="ttt-board" id="ttt-board">
                    ${[0,1,2,3,4,5,6,7,8].map(i => `<div class="ttt-cell" onclick="handleTTTClick(${i})" data-index="${i}"></div>`).join('')}
                </div>
                
                <div class="d-grid gap-2">
                    <button class="btn btn-netflix" onclick="resetTTTBoard()">Restart Game</button>
                    <button class="btn btn-outline-light btn-sm mt-2" onclick="promptTTTMode()">Change Mode</button>
                </div>
            </div>
        `;
        $('#game-container').html(html);
        promptTTTMode();
    }

    function promptTTTMode() {
        const modal = new bootstrap.Modal(document.getElementById('tttModeModal'));
        modal.show();
    }

    function setTTTMode(mode) {
        tttMode = mode;
        tttScores = {
            X: 0,
            O: 0,
            Ties: 0
        };
        updateTTTScores();
        // Update labels
        $('#score-label-o').text(mode === 'ai' ? 'CPU (O)' : 'Player (O)');

        const modal = bootstrap.Modal.getInstance(document.getElementById('tttModeModal'));
        if (modal) modal.hide();
        resetTTTBoard();
    }

    function handleTTTClick(index) {
        if (!tttGameActive || tttBoard[index] !== '') return;

        makeTTTMove(index, currentPlayer);

        if (tttGameActive && tttMode === 'ai' && currentPlayer === 'O') {
            setTimeout(makeCPUMove, 500);
        }
    }

    function makeTTTMove(index, player) {
        tttBoard[index] = player;
        const cell = $(`.ttt-cell[data-index="${index}"]`);
        cell.addClass(player.toLowerCase() + ' occupied').text(player);

        // Sound
        const snd = document.getElementById('tttMoveSound');
        if (snd) {
            snd.currentTime = 0;
            snd.play().catch(e => {});
        }

        checkTTTResult();

        if (tttGameActive) {
            currentPlayer = currentPlayer === 'X' ? 'O' : 'X';
            $('#ttt-status').text(`${currentPlayer === 'X' ? 'Player (X)' : (tttMode === 'ai' ? 'CPU (O)' : 'Player (O)')}'s turn`);
        }
    }

    function checkTTTResult() {
        const winConditions = [
            [0, 1, 2],
            [3, 4, 5],
            [6, 7, 8], // Rows
            [0, 3, 6],
            [1, 4, 7],
            [2, 5, 8], // Cols
            [0, 4, 8],
            [2, 4, 6] // Diagonals
        ];

        let roundWon = false;
        for (let condition of winConditions) {
            const [a, b, c] = condition;
            if (tttBoard[a] && tttBoard[a] === tttBoard[b] && tttBoard[a] === tttBoard[c]) {
                roundWon = true;
                highlightWinningCells(condition);
                break;
            }
        }

        if (roundWon) {
            const winner = currentPlayer;
            $('#ttt-status').addClass('text-success').text(`${winner} Wins!`);
            tttScores[winner]++;
            updateTTTScores();
            tttGameActive = false;

            // Win Sound + Confetti
            const winSnd = document.getElementById('tttWinSound');
            if (winSnd) winSnd.play().catch(e => {});
            confetti({
                particleCount: 150,
                spread: 70,
                origin: {
                    y: 0.6
                },
                colors: ['#e50914', '#ffffff']
            });
            return;
        }

        if (!tttBoard.includes('')) {
            $('#ttt-status').text("It's a Tie!");
            tttScores.Ties++;
            updateTTTScores();
            tttGameActive = false;
            return;
        }
    }

    function highlightWinningCells(combination) {
        combination.forEach(index => {
            $(`.ttt-cell[data-index="${index}"]`).addClass('winning-cell');
        });
    }

    function updateTTTScores() {
        $('#score-X').text(tttScores.X);
        $('#score-O').text(tttScores.O);
        $('#score-Ties').text(tttScores.Ties);
    }

    function resetTTTBoard() {
        tttBoard = ['', '', '', '', '', '', '', '', ''];
        tttGameActive = true;
        currentPlayer = 'X';
        $('#ttt-status').removeClass('text-success').text("Your turn (X)");
        $('.ttt-cell').removeClass('x o occupied winning-cell').text('');
    }

    // --- AI Logic (Minimax) ---
    function makeCPUMove() {
        if (!tttGameActive) return;
        const bestMove = minimax(tttBoard, 'O').index;
        makeTTTMove(bestMove, 'O');
    }

    function minimax(newBoard, player) {
        const availSpots = newBoard.map((v, i) => v === '' ? i : null).filter(v => v !== null);

        if (checkWinAI(newBoard, 'X')) return {
            score: -10
        };
        if (checkWinAI(newBoard, 'O')) return {
            score: 10
        };
        if (availSpots.length === 0) return {
            score: 0
        };

        const moves = [];
        for (let i = 0; i < availSpots.length; i++) {
            const move = {};
            move.index = availSpots[i];
            newBoard[availSpots[i]] = player;

            if (player === 'O') move.score = minimax(newBoard, 'X').score;
            else move.score = minimax(newBoard, 'O').score;

            newBoard[availSpots[i]] = '';
            moves.push(move);
        }

        let bestMove;
        if (player === 'O') {
            let bestScore = -10000;
            for (let i = 0; i < moves.length; i++) {
                if (moves[i].score > bestScore) {
                    bestScore = moves[i].score;
                    bestMove = i;
                }
            }
        } else {
            let bestScore = 10000;
            for (let i = 0; i < moves.length; i++) {
                if (moves[i].score < bestScore) {
                    bestScore = moves[i].score;
                    bestMove = i;
                }
            }
        }
        return moves[bestMove];
    }

    function checkWinAI(board, player) {
        const winConditions = [
            [0, 1, 2],
            [3, 4, 5],
            [6, 7, 8],
            [0, 3, 6],
            [1, 4, 7],
            [2, 5, 8],
            [0, 4, 8],
            [2, 4, 6]
        ];
        return winConditions.some(c => board[c[0]] === player && board[c[1]] === player && board[c[2]] === player);
    }
</script>