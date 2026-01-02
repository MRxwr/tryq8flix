<!-- Cyber Bricks CSS -->
<style>
    .bricks-container {
        max-width: 400px;
        margin: 0 auto;
        background: #000;
        border-radius: 20px;
        overflow: hidden;
        position: relative;
        box-shadow: 0 0 30px rgba(255, 0, 255, 0.2);
        border: 2px solid #222;
        user-select: none;
        touch-action: none;
        aspect-ratio: 2/3;
    }

    #bricks-canvas {
        background: #050005;
        display: block;
        width: 100%;
        height: 100%;
    }

    .bricks-ui {
        position: absolute;
        top: 20px;
        width: 100%;
        padding: 0 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-family: 'Outfit', sans-serif;
        color: #fff;
        pointer-events: none;
        z-index: 5;
    }

    .bricks-score {
        font-size: 1.5rem;
        font-weight: 800;
        color: #ff00ff;
        text-shadow: 0 0 10px rgba(255, 0, 255, 0.5);
    }

    .bricks-lives {
        font-size: 1.2rem;
        color: #ff3366;
    }

    .bricks-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.9);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        z-index: 10;
        backdrop-filter: blur(10px);
    }
</style>

<!-- Bricks Overlays -->
<div id="bricks-start-overlay" class="bricks-overlay" style="display: none;">
    <div class="text-center p-4">
        <div class="mb-3" style="font-size: 4rem;">🧱</div>
        <h2 class="text-white mb-2 font-weight-bold">CYBER BRICKS</h2>
        <p class="text-white-50 small mb-4">Break the firewall.<br>Move with Mouse/Touch.</p>

        <div class="mb-4">
            <button class="flap-difficulty-btn active" onclick="setBricksDiff('Normal', this)">NORMAL</button>
            <button class="flap-difficulty-btn" onclick="setBricksDiff('Overdrive', this)">OVERDRIVE</button>
        </div>

        <button class="btn btn-netflix btn-lg px-5 shadow-lg" onclick="startBricksGame()">ESTABLISH LINK</button>
    </div>
</div>

<div id="bricks-gameover-overlay" class="bricks-overlay" style="display:none;">
    <h2 id="bricks-status-text" class="text-danger mb-2 fw-bold">LINK SEVERED</h2>
    <div class="small text-white-50 mb-1">SCORE ACHIEVED</div>
    <div id="bricks-final-score" class="text-white h1 mb-4 fw-bold">0</div>
    <div class="d-grid gap-2 w-75">
        <button class="btn btn-netflix py-3" onclick="startBricksGame()">RE-INITIATE</button>
        <button class="btn btn-outline-light" onclick="bricksActive = false; showGamesHome();">CLEAN EXIT</button>
    </div>
</div>

<!-- Bricks Sounds -->
<audio id="bricksHitSnd" src="https://assets.mixkit.co/active_storage/sfx/2571/2571-preview.mp3" preload="auto"></audio>
<audio id="bricksPowerSnd" src="https://assets.mixkit.co/active_storage/sfx/2019/2019-preview.mp3" preload="auto"></audio>
<audio id="bricksDieSnd" src="https://assets.mixkit.co/active_storage/sfx/21/21-preview.mp3" preload="auto"></audio>

<script>
    let bricksCanvas, bricksCtx;
    let bricksActive = false;
    let bricksScoreVal = 0;
    let bricksLivesLeft = 3;
    let bricksDiffLevel = 'Normal';

    // Config
    const B_WIDTH = 400;
    const B_HEIGHT = 600;
    let B_BALL_SPEED = 4;

    let bricksBall = {
        x: 200,
        y: 500,
        dx: 4,
        dy: -4,
        radius: 8
    };
    let bricksPaddle = {
        w: 80,
        h: 12,
        x: 160
    };
    let bricksArr = [];
    let bricksPowerups = [];

    const B_COLS = 6;
    const B_ROWS = 8;
    const B_PADDING = 10;
    const B_OFFSET_TOP = 80;
    const B_OFFSET_LEFT = 20;
    const B_BRICK_W = 55;
    const B_BRICK_H = 20;

    function initBricks() {
        const html = `
            <div class="bricks-container">
                <div class="bricks-ui">
                    <div id="bricks-lives-display" class="bricks-lives">❤❤❤</div>
                    <span id="bricks-score-display" class="bricks-score">0</span>
                </div>
                <canvas id="bricks-canvas" width="400" height="600"></canvas>
            </div>
        `;
        $('#game-container').html(html);
        bricksCanvas = document.getElementById('bricks-canvas');
        bricksCtx = bricksCanvas.getContext('2d');

        $('#bricks-start-overlay').show();
        setupBricksControls();
    }

    function setBricksDiff(d, btn) {
        bricksDiffLevel = d;
        $('.flap-difficulty-btn').removeClass('active');
        $(btn).addClass('active');
        B_BALL_SPEED = (d === 'Overdrive') ? 6 : 4;
    }

    function setupBricksControls() {
        const movePaddle = (x) => {
            if (!bricksActive) return;
            let relX = x - bricksCanvas.getBoundingClientRect().left;
            bricksPaddle.x = Math.max(0, Math.min(B_WIDTH - bricksPaddle.w, relX - bricksPaddle.w / 2));
        };

        bricksCanvas.addEventListener('mousemove', e => movePaddle(e.clientX));
        bricksCanvas.addEventListener('touchmove', e => {
            e.preventDefault();
            movePaddle(e.touches[0].clientX);
        }, {
            passive: false
        });
    }

    function startBricksGame() {
        $('#bricks-start-overlay').hide();
        $('#bricks-gameover-overlay').hide();
        bricksActive = true;
        bricksScoreVal = 0;
        bricksLivesLeft = 3;
        updateBricksLives();

        bricksBall.x = B_WIDTH / 2;
        bricksBall.y = B_HEIGHT - 100;
        bricksBall.dx = B_BALL_SPEED;
        bricksBall.dy = -B_BALL_SPEED;

        bricksPaddle.x = (B_WIDTH - bricksPaddle.w) / 2;
        bricksPaddle.w = 80;

        bricksArr = [];
        for (let c = 0; c < B_COLS; c++) {
            bricksArr[c] = [];
            for (let r = 0; r < B_ROWS; r++) {
                bricksArr[c][r] = {
                    x: 0,
                    y: 0,
                    status: 1,
                    type: Math.random() > 0.9 ? 'power' : 'normal'
                };
            }
        }

        bricksPowerups = [];
        updateBricks();
    }

    function updateBricksLives() {
        $('#bricks-lives-display').text('❤'.repeat(bricksLivesLeft));
    }

    function updateBricks() {
        if (!bricksActive) return;
        bricksCtx.clearRect(0, 0, B_WIDTH, B_HEIGHT);

        // Background Grid
        bricksCtx.strokeStyle = 'rgba(255,0,255,0.05)';
        for (let i = 0; i < B_HEIGHT; i += 50) {
            bricksCtx.beginPath();
            bricksCtx.moveTo(0, i);
            bricksCtx.lineTo(B_WIDTH, i);
            bricksCtx.stroke();
        }

        // Draw Bricks
        let allClear = true;
        for (let c = 0; c < B_COLS; c++) {
            for (let r = 0; r < B_ROWS; r++) {
                let b = bricksArr[c][r];
                if (b.status === 1) {
                    allClear = false;
                    b.x = c * (B_BRICK_W + B_PADDING) + B_OFFSET_LEFT;
                    b.y = r * (B_BRICK_H + B_PADDING) + B_OFFSET_TOP;

                    bricksCtx.save();
                    bricksCtx.fillStyle = b.type === 'power' ? '#00f6ff' : '#ff00ff';
                    bricksCtx.shadowBlur = 10;
                    bricksCtx.shadowColor = bricksCtx.fillStyle;
                    bricksCtx.fillRect(b.x, b.y, B_BRICK_W, B_BRICK_H);
                    bricksCtx.restore();

                    // Ball Collision
                    if (bricksBall.x > b.x && bricksBall.x < b.x + B_BRICK_W && bricksBall.y > b.y && bricksBall.y < b.y + B_BRICK_H) {
                        bricksBall.dy *= -1;
                        b.status = 0;
                        bricksScoreVal += 10;
                        $('#bricks-score-display').text(bricksScoreVal);

                        let s = document.getElementById('bricksHitSnd');
                        s.currentTime = 0;
                        s.play().catch(() => {});

                        if (b.type === 'power') {
                            bricksPowerups.push({
                                x: b.x + B_BRICK_W / 2,
                                y: b.y,
                                type: 'wide'
                            });
                        }
                    }
                }
            }
        }

        if (allClear) {
            gameOverBricks(true);
            return;
        }

        // Ball Movement
        bricksBall.x += bricksBall.dx;
        bricksBall.y += bricksBall.dy;

        if (bricksBall.x + bricksBall.dx > B_WIDTH - bricksBall.radius || bricksBall.x + bricksBall.dx < bricksBall.radius) bricksBall.dx *= -1;
        if (bricksBall.y + bricksBall.dy < bricksBall.radius) bricksBall.dy *= -1;
        else if (bricksBall.y + bricksBall.dy > B_HEIGHT - bricksBall.radius) {
            if (bricksBall.x > bricksPaddle.x && bricksBall.x < bricksPaddle.x + bricksPaddle.w) {
                // Change angle based on hit point
                let hitPos = (bricksBall.x - (bricksPaddle.x + bricksPaddle.w / 2)) / (bricksPaddle.w / 2);
                bricksBall.dx = hitPos * B_BALL_SPEED;
                bricksBall.dy = -Math.sqrt(Math.pow(B_BALL_SPEED * 1.4, 2) - Math.pow(bricksBall.dx, 2));

                let s = document.getElementById('bricksHitSnd');
                s.currentTime = 0;
                s.play().catch(() => {});
            } else {
                bricksLivesLeft--;
                updateBricksLives();
                document.getElementById('bricksDieSnd').play().catch(() => {});
                if (bricksLivesLeft === 0) {
                    gameOverBricks(false);
                    return;
                } else {
                    bricksBall.x = B_WIDTH / 2;
                    bricksBall.y = B_HEIGHT - 100;
                    bricksBall.dx = B_BALL_SPEED;
                    bricksBall.dy = -B_BALL_SPEED;
                }
            }
        }

        // Draw Ball
        bricksCtx.save();
        bricksCtx.beginPath();
        bricksCtx.arc(bricksBall.x, bricksBall.y, bricksBall.radius, 0, Math.PI * 2);
        bricksCtx.fillStyle = "#fff";
        bricksCtx.shadowBlur = 15;
        bricksCtx.shadowColor = "#fff";
        bricksCtx.fill();
        bricksCtx.closePath();
        bricksCtx.restore();

        // Draw Paddle
        bricksCtx.save();
        bricksCtx.fillStyle = "#ff00ff";
        bricksCtx.shadowBlur = 15;
        bricksCtx.shadowColor = "#ff00ff";
        bricksCtx.fillRect(bricksPaddle.x, B_HEIGHT - 30, bricksPaddle.w, bricksPaddle.h);
        bricksCtx.restore();

        // Powerups
        for (let i = bricksPowerups.length - 1; i >= 0; i--) {
            let p = bricksPowerups[i];
            p.y += 2;

            bricksCtx.fillStyle = '#00f6ff';
            bricksCtx.beginPath();
            bricksCtx.arc(p.x, p.y, 8, 0, Math.PI * 2);
            bricksCtx.fill();

            if (p.y > B_HEIGHT - 40 && p.x > bricksPaddle.x && p.x < bricksPaddle.x + bricksPaddle.w) {
                bricksPaddle.w = 120;
                setTimeout(() => {
                    bricksPaddle.w = 80;
                }, 8000);
                document.getElementById('bricksPowerSnd').play().catch(() => {});
                bricksPowerups.splice(i, 1);
            } else if (p.y > B_HEIGHT) bricksPowerups.splice(i, 1);
        }

        requestAnimationFrame(updateBricks);
    }

    function gameOverBricks(win) {
        bricksActive = false;
        $('#bricks-status-text').text(win ? "SYSTEM BREACHED" : "LINK SEVERED").toggleClass('text-success', win).toggleClass('text-danger', !win);
        $('#bricks-final-score').text(bricksScoreVal);
        $('#bricks-gameover-overlay').fadeIn();
    }
</script>