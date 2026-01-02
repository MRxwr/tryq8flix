<!-- Cyber Runner CSS -->
<style>
    .runner-container {
        max-width: 400px;
        margin: 0 auto;
        background: #000;
        border-radius: 20px;
        overflow: hidden;
        position: relative;
        box-shadow: 0 0 30px rgba(229, 9, 20, 0.2);
        border: 2px solid #222;
        user-select: none;
    }

    #runner-canvas {
        background: #050505;
        display: block;
        width: 100%;
    }

    .runner-ui {
        position: absolute;
        top: 20px;
        width: 100%;
        padding: 0 20px;
        display: flex;
        justify-content: space-between;
        pointer-events: none;
    }

    .runner-stats {
        text-align: left;
        font-family: 'Courier New', Courier, monospace;
    }

    .runner-label {
        color: #888;
        font-size: 0.7rem;
        text-transform: uppercase;
    }

    .runner-value {
        color: #e50914;
        font-weight: bold;
        font-size: 1.2rem;
        display: block;
    }

    .runner-overlay-modal {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.8);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        z-index: 10;
        backdrop-filter: blur(5px);
    }
</style>

<!-- Runner Modals/Overlay -->
<div id="runner-start-overlay" class="runner-overlay-modal">
    <h2 class="text-white mb-4">CYBER RUNNER</h2>
    <p class="text-white-50 text-center px-4 mb-4 small">Dodge obstacles by swiping or using arrow keys.<br>Space to Jump, Down to Slide.</p>
    <button class="btn btn-netflix px-5" onclick="startRunnerGame()">START MISSION</button>
</div>

<div id="runner-gameover-overlay" class="runner-overlay-modal" style="display:none;">
    <h2 class="text-danger mb-2">CRASHED</h2>
    <div class="text-white h1 mb-4" id="runner-final-score">0</div>
    <button class="btn btn-netflix px-5 mb-2" onclick="startRunnerGame()">RETRY</button>
    <button class="btn btn-outline-light px-5" onclick="showGamesHome()">EXIT</button>
</div>

<!-- Runner Sounds -->
<audio id="runnerJumpSound" src="https://assets.mixkit.co/active_storage/sfx/2571/2571-preview.mp3" preload="auto"></audio>
<audio id="runnerCrashSound" src="https://assets.mixkit.co/active_storage/sfx/21/21-preview.mp3" preload="auto"></audio>

<script>
    let runnerCanvas, runnerCtx;
    let runnerActive = false;
    let runnerScore = 0;
    let runnerSpeed = 5;
    let runnerLane = 1; // 0: Left, 1: Middle, 2: Right
    let runnerTargetLane = 1;
    let runnerPlayerX = 200;
    let runnerIsJumping = false;
    let runnerIsSliding = false;
    let runnerJumpY = 0;
    let runnerJumpV = 0;
    let runnerSlideTimer = 0;
    let runnerObstacles = [];
    let runnerFrame = 0;
    let runnerLoop;

    const RUNNER_WIDTH = 400;
    const RUNNER_HEIGHT = 600;
    const LANE_WIDTH = RUNNER_WIDTH / 3;

    function initRunner() {
        const html = `
            <div class="runner-container">
                <div class="runner-ui">
                    <div class="runner-stats">
                        <span class="runner-label">Distance</span>
                        <span id="runner-score-val" class="runner-value">0m</span>
                    </div>
                </div>
                <canvas id="runner-canvas" width="400" height="600"></canvas>
            </div>
        `;
        $('#game-container').html(html);
        runnerCanvas = document.getElementById('runner-canvas');
        runnerCtx = runnerCanvas.getContext('2d');

        setupRunnerControls();
    }

    function setupRunnerControls() {
        // Keyboard
        document.addEventListener('keydown', (e) => {
            if (!runnerActive) return;
            if (e.key === 'ArrowLeft' && runnerTargetLane > 0) runnerTargetLane--;
            if (e.key === 'ArrowRight' && runnerTargetLane < 2) runnerTargetLane++;
            if ((e.key === 'ArrowUp' || e.key === ' ') && !runnerIsJumping && !runnerIsSliding) triggerRunnerJump();
            if (e.key === 'ArrowDown' && !runnerIsJumping) triggerRunnerSlide();
        });

        // Touch Swipe
        let tsX, tsY;
        document.addEventListener('touchstart', e => {
            tsX = e.touches[0].clientX;
            tsY = e.touches[0].clientY;
        });
        document.addEventListener('touchend', e => {
            if (!runnerActive) return;
            let teX = e.changedTouches[0].clientX;
            let teY = e.changedTouches[0].clientY;
            let dx = teX - tsX;
            let dy = teY - tsY;

            if (Math.abs(dx) > Math.abs(dy)) {
                if (dx > 50 && runnerTargetLane < 2) runnerTargetLane++;
                else if (dx < -50 && runnerTargetLane > 0) runnerTargetLane--;
            } else {
                if (dy < -50 && !runnerIsJumping && !runnerIsSliding) triggerRunnerJump();
                else if (dy > 50 && !runnerIsJumping) triggerRunnerSlide();
            }
        });
    }

    function triggerRunnerJump() {
        runnerIsJumping = true;
        runnerJumpV = 15;
        const snd = document.getElementById('runnerJumpSound');
        if (snd) {
            snd.currentTime = 0;
            snd.play().catch(e => {});
        }
    }

    function triggerRunnerSlide() {
        runnerIsSliding = true;
        runnerSlideTimer = 40;
    }

    function startRunnerGame() {
        $('#runner-start-overlay').hide();
        $('#runner-gameover-overlay').hide();
        runnerActive = true;
        runnerScore = 0;
        runnerSpeed = 8;
        runnerTargetLane = 1;
        runnerPlayerX = RUNNER_WIDTH / 2;
        runnerIsJumping = false;
        runnerIsSliding = false;
        runnerJumpY = 0;
        runnerObstacles = [];
        runnerFrame = 0;

        if (runnerLoop) cancelAnimationFrame(runnerLoop);
        drawRunner();
    }

    function drawRunner() {
        if (!runnerActive) return;

        // Clear
        runnerCtx.fillStyle = '#111';
        runnerCtx.fillRect(0, 0, RUNNER_WIDTH, RUNNER_HEIGHT);

        // Draw Grid Lines (Pseudo 3D)
        runnerCtx.strokeStyle = '#222';
        runnerCtx.lineWidth = 2;
        for (let i = 0; i <= 3; i++) {
            let x = i * LANE_WIDTH;
            runnerCtx.beginPath();
            runnerCtx.moveTo(x, 0);
            runnerCtx.lineTo(x, RUNNER_HEIGHT);
            runnerCtx.stroke();
        }

        // Moving Horizontal Grid
        runnerCtx.strokeStyle = 'rgba(229, 9, 20, 0.1)';
        for (let i = 0; i < 10; i++) {
            let y = ((runnerFrame * runnerSpeed) % 100) + (i * 100);
            runnerCtx.beginPath();
            runnerCtx.moveTo(0, y);
            runnerCtx.lineTo(RUNNER_WIDTH, y);
            runnerCtx.stroke();
        }

        // Update Player Movement
        let targetX = (runnerTargetLane * LANE_WIDTH) + (LANE_WIDTH / 2);
        runnerPlayerX += (targetX - runnerPlayerX) * 0.2;

        if (runnerIsJumping) {
            runnerJumpY += runnerJumpV;
            runnerJumpV -= 0.8;
            if (runnerJumpY <= 0) {
                runnerJumpY = 0;
                runnerIsJumping = false;
            }
        }

        if (runnerIsSliding) {
            runnerSlideTimer--;
            if (runnerSlideTimer <= 0) runnerIsSliding = false;
        }

        // Draw Player
        runnerCtx.save();
        runnerCtx.translate(runnerPlayerX, RUNNER_HEIGHT - 100 - runnerJumpY);

        let scaleY = runnerIsSliding ? 0.5 : 1;
        runnerCtx.shadowBlur = 15;
        runnerCtx.shadowColor = '#e50914';
        runnerCtx.fillStyle = '#e50914';
        runnerCtx.fillRect(-20, -40 * scaleY, 40, 60 * scaleY);

        // Glow Effect
        runnerCtx.strokeStyle = '#fff';
        runnerCtx.lineWidth = 2;
        runnerCtx.strokeRect(-20, -40 * scaleY, 40, 60 * scaleY);
        runnerCtx.restore();

        // Spawn Obstacles
        runnerFrame++;
        if (runnerFrame % 60 === 0) {
            let lane = Math.floor(Math.random() * 3);
            let type = Math.random() > 0.5 ? 'low' : 'high';
            runnerObstacles.push({
                lane,
                type,
                y: -50
            });
        }

        // Update & Draw Obstacles
        for (let i = runnerObstacles.length - 1; i >= 0; i--) {
            let obs = runnerObstacles[i];
            obs.y += runnerSpeed;

            let ox = (obs.lane * LANE_WIDTH) + (LANE_WIDTH / 2);

            runnerCtx.save();
            runnerCtx.translate(ox, obs.y);
            runnerCtx.shadowBlur = 10;
            runnerCtx.shadowColor = obs.type === 'low' ? '#00ffff' : '#ff00ff';
            runnerCtx.fillStyle = obs.type === 'low' ? '#00ffff' : '#ff00ff';

            if (obs.type === 'low') {
                runnerCtx.fillRect(-40, -20, 80, 40); // Jump over
            } else {
                runnerCtx.fillRect(-50, -40, 100, 20); // Slide under (floating)
            }
            runnerCtx.restore();

            // Collision Detection
            let pLane = Math.round((runnerPlayerX - LANE_WIDTH / 2) / LANE_WIDTH);
            if (obs.lane === pLane && Math.abs(obs.y - (RUNNER_HEIGHT - 100)) < 40) {
                if (obs.type === 'low' && runnerJumpY < 30) runnerGameOver();
                if (obs.type === 'high' && !runnerIsSliding) runnerGameOver();
            }

            if (obs.y > RUNNER_HEIGHT + 100) runnerObstacles.splice(i, 1);
        }

        // Update Score
        runnerScore += runnerSpeed / 10;
        $('#runner-score-val').text(Math.floor(runnerScore) + 'm');
        runnerSpeed += 0.001;

        runnerLoop = requestAnimationFrame(drawRunner);
    }

    function runnerGameOver() {
        runnerActive = false;
        cancelAnimationFrame(runnerLoop);
        const snd = document.getElementById('runnerCrashSound');
        if (snd) {
            snd.currentTime = 0;
            snd.play().catch(e => {});
        }

        $('#runner-final-score').text(Math.floor(runnerScore) + 'm');
        $('#runner-gameover-overlay').fadeIn();
    }
</script>