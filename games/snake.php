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

<script>
    let snake, food, direction, nextDirection, score, gameRunning, snakeTimerInterval, snakeGameLoop;
    let canvas, ctx;
    let box = 20;
    let snakeSpeed = 100;

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
        canvas = document.getElementById('snake-canvas');
        ctx = canvas.getContext('2d');

        // Modal for difficulty
        const modal = new bootstrap.Modal(document.getElementById('snakeDifficultyModal'));
        modal.show();
    }

    function setSnakeDifficulty(level) {
        if (level === 'easy') snakeSpeed = 150;
        else if (level === 'medium') snakeSpeed = 100;
        else snakeSpeed = 60;

        const modal = bootstrap.Modal.getInstance(document.getElementById('snakeDifficultyModal'));
        modal.hide();
        startSnakeGame();
    }

    function startSnakeGame() {
        snake = [{
            x: 9 * box,
            y: 10 * box
        }];
        food = {
            x: Math.floor(Math.random() * 19 + 1) * box,
            y: Math.floor(Math.random() * 19 + 1) * box
        };
        score = 0;
        direction = 'RIGHT';
        nextDirection = 'RIGHT';
        gameRunning = true;

        $('#snake-score').text('0');
        startSnakeTimer();

        if (snakeGameLoop) clearInterval(snakeGameLoop);
        snakeGameLoop = setInterval(drawSnake, snakeSpeed);

        document.addEventListener('keydown', handleSnakeKey);
    }

    function handleSnakeKey(e) {
        if (e.keyCode == 37 && direction != 'RIGHT') nextDirection = 'LEFT';
        else if (e.keyCode == 38 && direction != 'DOWN') nextDirection = 'UP';
        else if (e.keyCode == 39 && direction != 'LEFT') nextDirection = 'RIGHT';
        else if (e.keyCode == 40 && direction != 'UP') nextDirection = 'DOWN';
    }

    function handleSnakeControl(dir) {
        if (dir == 'LEFT' && direction != 'RIGHT') nextDirection = 'LEFT';
        else if (dir == 'UP' && direction != 'DOWN') nextDirection = 'UP';
        else if (dir == 'RIGHT' && direction != 'LEFT') nextDirection = 'RIGHT';
        else if (dir == 'DOWN' && direction != 'UP') nextDirection = 'DOWN';
    }

    function drawSnake() {
        if (!gameRunning) return;

        ctx.fillStyle = '#0a0a0a';
        ctx.fillRect(0, 0, canvas.width, canvas.height);

        for (let i = 0; i < snake.length; i++) {
            ctx.fillStyle = (i == 0) ? "#e50914" : "#ffffff";
            ctx.shadowBlur = (i == 0) ? 10 : 0;
            ctx.shadowColor = "#e50914";
            ctx.fillRect(snake[i].x, snake[i].y, box, box);
            ctx.strokeStyle = "#0a0a0a";
            ctx.strokeRect(snake[i].x, snake[i].y, box, box);
        }

        ctx.shadowBlur = 15;
        ctx.shadowColor = "#00ff00";
        ctx.fillStyle = "#00ff00";
        ctx.fillRect(food.x, food.y, box, box);
        ctx.shadowBlur = 0;

        direction = nextDirection;
        let snakeX = snake[0].x;
        let snakeY = snake[0].y;

        if (direction == "LEFT") snakeX -= box;
        if (direction == "UP") snakeY -= box;
        if (direction == "RIGHT") snakeX += box;
        if (direction == "DOWN") snakeY += box;

        if (snakeX == food.x && snakeY == food.y) {
            score++;
            $('#snake-score').text(score);
            food = {
                x: Math.floor(Math.random() * 19 + 1) * box,
                y: Math.floor(Math.random() * 19 + 1) * box
            };
        } else {
            snake.pop();
        }

        let newHead = {
            x: snakeX,
            y: snakeY
        };

        if (snakeX < 0 || snakeX >= canvas.width || snakeY < 0 || snakeY >= canvas.height || collision(newHead, snake)) {
            gameOverSnake();
            return;
        }

        snake.unshift(newHead);
    }

    function collision(head, array) {
        for (let i = 0; i < array.length; i++) {
            if (head.x == array[i].x && head.y == array[i].y) return true;
        }
        return false;
    }

    function gameOverSnake() {
        gameRunning = false;
        clearInterval(snakeGameLoop);
        clearInterval(snakeTimerInterval);
        document.removeEventListener('keydown', handleSnakeKey);

        $('#final-snake-score').text(score);
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