<!-- Snake CSS -->
<style>
    .snake-game-container {
        max-width: 500px;
        margin: 0 auto;
        background: rgba(255, 255, 255, 0.03);
        padding: 1.5rem;
        border-radius: 20px;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        text-align: center;
        user-select: none;
    }

    #snake-canvas {
        background: #0a0a0a;
        border: 2px solid #333;
        border-radius: 8px;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
        max-width: 100%;
        display: block;
        margin: 0 auto;
    }

    .snake-stats {
        display: flex;
        justify-content: space-between;
        margin-bottom: 1rem;
        font-family: 'Courier New', Courier, monospace;
    }

    .stat-label {
        color: #888;
        font-size: 0.8rem;
        text-transform: uppercase;
    }

    .stat-value {
        color: #e50914;
        font-weight: bold;
        font-size: 1.2rem;
    }

    .snake-controls {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
        max-width: 180px;
        margin: 1.5rem auto 0;
    }

    .control-btn {
        width: 50px;
        height: 50px;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 12px;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        cursor: pointer;
        transition: all 0.2s;
    }

    .control-btn:active {
        background: #e50914;
        transform: scale(0.9);
    }
</style>

<!-- Snake Modals -->
<div class="modal fade" id="snakeDifficultyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-white" style="background: #181818; border: 1px solid #333;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title w-100 text-center mt-3">Select Speed</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-4">
                <div class="d-grid gap-3">
                    <button class="btn btn-outline-success py-3" onclick="setSnakeDifficulty('easy')">Sluggish (Easy)</button>
                    <button class="btn btn-outline-warning py-3" onclick="setSnakeDifficulty('medium')">Normal (Medium)</button>
                    <button class="btn btn-outline-danger py-3" onclick="setSnakeDifficulty('hard')">Hyper (Hard)</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="snakeGameOverModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-white" style="background: #181818; border: 1px solid #e50914;">
            <div class="modal-body text-center p-5">
                <h2 class="text-danger mb-4">CRASHED!</h2>
                <div class="stats-box d-flex justify-content-around mb-4 p-3 rounded" style="background: rgba(255,255,255,0.05);">
                    <div>
                        <div class="small text-white-50">Score</div>
                        <div id="final-snake-score" class="fw-bold">0</div>
                    </div>
                    <div>
                        <div class="small text-white-50">Time</div>
                        <div id="final-snake-time" class="fw-bold">00:00</div>
                    </div>
                </div>
                <div class="d-grid gap-2">
                    <button class="btn btn-netflix" onclick="restartSnake()">Try Again</button>
                    <button class="btn btn-outline-light" data-bs-dismiss="modal" onclick="showGamesHome()">Exit</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sound Effects -->
<audio id="snakeEatSound" src="https://assets.mixkit.co/active_storage/sfx/212/212-preview.mp3" preload="auto"></audio>
<audio id="snakeCrashSound" src="https://assets.mixkit.co/active_storage/sfx/21/21-preview.mp3" preload="auto"></audio>

<script>
    let snakeArr, snakeFood, snakeDirection, snakeNextDirection, snakeScoreVal, snakeGameActive, snakeTimerInterval, snakeGameLoop;
    let snakeCanvas, snakeCtx;
    let snakeBox = 20;
    let snakeGameSpeed = 100;

    function initSnake() {
        const html = `
            <div class="snake-game-container">
                <div class="snake-stats">
                    <div><div class="stat-label">Score</div><div id="snake-score" class="stat-value">0</div></div>
                    <div><div class="stat-label">Time</div><div id="snake-time-display" class="stat-value">00:00</div></div>
                </div>
                <canvas id="snake-canvas" width="400" height="400"></canvas>
                <div class="snake-controls d-md-none">
                    <div></div><div class="control-btn" onclick="handleSnakeControl('UP')"><i class="fas fa-chevron-up"></i></div><div></div>
                    <div class="control-btn" onclick="handleSnakeControl('LEFT')"><i class="fas fa-chevron-left"></i></div>
                    <div class="control-btn" onclick="handleSnakeControl('DOWN')"><i class="fas fa-chevron-down"></i></div>
                    <div class="control-btn" onclick="handleSnakeControl('RIGHT')"><i class="fas fa-chevron-right"></i></div>
                </div>
                <div class="mt-3 small text-white-50 d-none d-md-block">Use Arrow Keys to Move</div>
            </div>
        `;
        $('#game-container').html(html);
        snakeCanvas = document.getElementById('snake-canvas');
        snakeCtx = snakeCanvas.getContext('2d');

        // Modal for difficulty
        const modal = new bootstrap.Modal(document.getElementById('snakeDifficultyModal'));
        modal.show();

        // Touch Controls
        let touchStartX = 0;
        let touchStartY = 0;

        snakeCanvas.addEventListener('touchstart', function(e) {
            touchStartX = e.touches[0].clientX;
            touchStartY = e.touches[0].clientY;
            e.preventDefault();
        }, {
            passive: false
        });

        snakeCanvas.addEventListener('touchmove', function(e) {
            if (!snakeGameActive) return;
            e.preventDefault();

            let touchEndX = e.touches[0].clientX;
            let touchEndY = e.touches[0].clientY;

            let dx = touchEndX - touchStartX;
            let dy = touchEndY - touchStartY;

            // Minimum swipe distance
            if (Math.abs(dx) > 30 || Math.abs(dy) > 30) {
                if (Math.abs(dx) > Math.abs(dy)) {
                    // Horizontal swipe
                    if (dx > 0 && snakeDirection != 'LEFT') snakeNextDirection = 'RIGHT';
                    else if (dx < 0 && snakeDirection != 'RIGHT') snakeNextDirection = 'LEFT';
                } else {
                    // Vertical swipe
                    if (dy > 0 && snakeDirection != 'UP') snakeNextDirection = 'DOWN';
                    else if (dy < 0 && snakeDirection != 'DOWN') snakeNextDirection = 'UP';
                }
                // Reset starts to prevent multiple direction changes in one swipe
                touchStartX = touchEndX;
                touchStartY = touchEndY;
            }
        }, {
            passive: false
        });
    }

    function setSnakeDifficulty(level) {
        if (level === 'easy') snakeGameSpeed = 150;
        else if (level === 'medium') snakeGameSpeed = 100;
        else snakeGameSpeed = 60;

        // Unlock audio for mobile
        const s1 = document.getElementById('snakeEatSound');
        const s2 = document.getElementById('snakeCrashSound');
        if (s1) {
            s1.play().then(() => {
                s1.pause();
                s1.currentTime = 0;
            }).catch(e => {});
        }
        if (s2) {
            s2.play().then(() => {
                s2.pause();
                s2.currentTime = 0;
            }).catch(e => {});
        }

        const modal = bootstrap.Modal.getInstance(document.getElementById('snakeDifficultyModal'));
        if (modal) modal.hide();
        startSnakeGame(level);
    }

    function startSnakeGame(difficulty) {
        clearInterval(snakeTimerInterval);
        clearInterval(snakeGameLoop);

        const diffSettings = {
            'easy': 150,
            'medium': 100,
            'hard': 70
        };
        snakeGameSpeed = diffSettings[difficulty] || 100;

        snakeArr = [{
            x: 10 * snakeBox,
            y: 10 * snakeBox
        }];
        snakeFood = spawnSnakeFood();
        snakeDirection = "RIGHT";
        snakeNextDirection = "RIGHT";
        snakeScoreVal = 0;
        snakeGameActive = true;

        $('#snake-score').text('0');
        startSnakeTimer();

        snakeGameLoop = setInterval(drawSnake, snakeGameSpeed);

        document.addEventListener('keydown', handleSnakeKey);
    }

    function spawnSnakeFood() {
        return {
            x: Math.floor(Math.random() * 19 + 1) * snakeBox,
            y: Math.floor(Math.random() * 19 + 1) * snakeBox
        };
    }

    function handleSnakeKey(e) {
        if (e.keyCode == 37 && snakeDirection != 'RIGHT') snakeNextDirection = 'LEFT';
        else if (e.keyCode == 38 && snakeDirection != 'DOWN') snakeNextDirection = 'UP';
        else if (e.keyCode == 39 && snakeDirection != 'LEFT') snakeNextDirection = 'RIGHT';
        else if (e.keyCode == 40 && snakeDirection != 'UP') snakeNextDirection = 'DOWN';
    }

    function handleSnakeControl(dir) {
        if (dir == 'LEFT' && snakeDirection != 'RIGHT') snakeNextDirection = 'LEFT';
        else if (dir == 'UP' && snakeDirection != 'DOWN') snakeNextDirection = 'UP';
        else if (dir == 'RIGHT' && snakeDirection != 'LEFT') snakeNextDirection = 'RIGHT';
        else if (dir == 'DOWN' && snakeDirection != 'UP') snakeNextDirection = 'DOWN';
    }

    function drawSnake() {
        if (!snakeGameActive) return;

        snakeCtx.fillStyle = '#0a0a0a';
        snakeCtx.fillRect(0, 0, snakeCanvas.width, snakeCanvas.height);

        for (let i = 0; i < snakeArr.length; i++) {
            snakeCtx.fillStyle = (i == 0) ? "#e50914" : "#ffffff";
            snakeCtx.shadowBlur = (i == 0) ? 10 : 0;
            snakeCtx.shadowColor = "#e50914";
            snakeCtx.fillRect(snakeArr[i].x, snakeArr[i].y, snakeBox, snakeBox);
            snakeCtx.strokeStyle = "#0a0a0a";
            snakeCtx.strokeRect(snakeArr[i].x, snakeArr[i].y, snakeBox, snakeBox);
        }

        snakeCtx.shadowBlur = 15;
        snakeCtx.shadowColor = "#00ff00";
        snakeCtx.fillStyle = "#00ff00";
        snakeCtx.fillRect(snakeFood.x, snakeFood.y, snakeBox, snakeBox);
        snakeCtx.shadowBlur = 0;

        snakeDirection = snakeNextDirection;
        let snakeX = snakeArr[0].x;
        let snakeY = snakeArr[0].y;

        if (snakeDirection == "LEFT") snakeX -= snakeBox;
        if (snakeDirection == "UP") snakeY -= snakeBox;
        if (snakeDirection == "RIGHT") snakeX += snakeBox;
        if (snakeDirection == "DOWN") snakeY += snakeBox;

        if (snakeX == snakeFood.x && snakeY == snakeFood.y) {
            snakeScoreVal++;
            $('#snake-score').text(snakeScoreVal);

            // Play Eat Sound (Cloned for overlap)
            const eatSound = document.getElementById('snakeEatSound');
            if (eatSound) {
                const playClone = eatSound.cloneNode(true);
                playClone.play().catch(e => {});
                // Cleanup clone after playing
                playClone.onended = () => playClone.remove();
            }

            snakeFood = spawnSnakeFood();
        } else {
            snakeArr.pop();
        }

        let newHead = {
            x: snakeX,
            y: snakeY
        };

        if (snakeX < 0 || snakeX >= snakeCanvas.width || snakeY < 0 || snakeY >= snakeCanvas.height || snakeCollision(newHead, snakeArr)) {
            gameOverSnake();
            return;
        }

        snakeArr.unshift(newHead);
    }

    function snakeCollision(head, array) {
        for (let i = 0; i < array.length; i++) {
            if (head.x == array[i].x && head.y == array[i].y) return true;
        }
        return false;
    }

    function gameOverSnake() {
        snakeGameActive = false;
        clearInterval(snakeGameLoop);
        clearInterval(snakeTimerInterval);
        document.removeEventListener('keydown', handleSnakeKey);

        // Play Crash Sound
        const crashSound = document.getElementById('snakeCrashSound');
        if (crashSound) {
            crashSound.currentTime = 0;
            crashSound.play().catch(e => {});
        }

        $('#final-snake-score').text(snakeScoreVal);
        $('#final-snake-time').text($('#snake-time-display').text());
        const modal = new bootstrap.Modal(document.getElementById('snakeGameOverModal'));
        modal.show();
    }

    function restartSnake() {
        const modal = bootstrap.Modal.getInstance(document.getElementById('snakeGameOverModal'));
        if (modal) modal.hide();
        initSnake();
    }

    function startSnakeTimer() {
        let seconds = 0;
        $('#snake-time-display').text('00:00');
        clearInterval(snakeTimerInterval);
        snakeTimerInterval = setInterval(() => {
            seconds++;
            const mins = Math.floor(seconds / 60).toString().padStart(2, '0');
            const secs = (seconds % 60).toString().padStart(2, '0');
            $('#snake-time-display').text(`${mins}:${secs}`);
        }, 1000);
    }
</script>