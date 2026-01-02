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
        aspect-ratio: 2/3;
    }

    #runner-canvas {
        background: #050505;
        display: block;
        width: 100%;
        height: 100%;
    }

    .runner-ui {
        position: absolute;
        top: 20px;
        width: 100%;
        padding: 0 20px;
        display: flex;
        justify-content: space-between;
        pointer-events: none;
        z-index: 5;
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
        background: rgba(0, 0, 0, 0.85);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        z-index: 20;
        backdrop-filter: blur(8px);
    }
</style>

<!-- Runner Modals/Overlay -->
<div id="runner-start-overlay" class="runner-overlay-modal" style="display: none;">
    <div class="text-center p-4">
        <i class="fas fa-bolt fa-3x text-danger mb-3"></i>
        <h2 class="text-white mb-2">CYBER RUNNER 3D</h2>
        <p class="text-white-50 small mb-4">
            Dodge obstacles on the neon grid.<br>
            <span class="text-danger">↑ JUMP</span> | <span class="text-info">↓ SLIDE</span><br>
            Use Arrows or Swipe to Move
        </p>
        <button class="btn btn-netflix btn-lg px-5 shadow-lg" onclick="startRunnerGame()">INITIATE RUN</button>
    </div>
</div>

<div id="runner-gameover-overlay" class="runner-overlay-modal" style="display:none;">
    <h2 class="text-danger mb-2">SYSTEM CRITICAL</h2>
    <div class="small text-white-50 mb-1">DISTANCE REACHED</div>
    <div class="text-white h1 mb-4 fw-bold" id="runner-final-score">0</div>
    <div class="d-grid gap-2 w-75">
        <button class="btn btn-netflix py-3" onclick="startRunnerGame()">RE-INITIALIZE</button>
        <button class="btn btn-outline-light" onclick="runnerActive = false; showGamesHome();">QUIT TERMINAL</button>
    </div>
</div>

<!-- Runner Sounds -->
<audio id="runnerJumpSound" src="https://assets.mixkit.co/active_storage/sfx/2571/2571-preview.mp3" preload="auto"></audio>
<audio id="runnerCrashSound" src="https://assets.mixkit.co/active_storage/sfx/21/21-preview.mp3" preload="auto"></audio>

<script>
    let runnerCanvas, runnerCtx;
    let runnerActive = false;
    let runnerScore = 0;
    let runnerSpeed = 0.015;
    let runnerTargetLane = 0; // -1: Left, 0: Mid, 1: Right
    let runnerCurrentX = 0;
    let runnerIsJumping = false;
    let runnerIsSliding = false;
    let runnerJumpY = 0;
    let runnerJumpV = 0;
    let runnerSlideTimer = 0;
    let runnerObstacles = [];
    let runnerFrame = 0;
    let runnerLoop;

    const CANVAS_W = 400;
    const CANVAS_H = 600;
    const HORIZON_Y = 150; // Moved up for semi-top view
    const PLANE_W = 1200; // Wider base for better ground visibility

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

        $('#runner-start-overlay').show();
        setupRunnerControls();
    }

    function setupRunnerControls() {
        // Keyboard
        document.addEventListener('keydown', (e) => {
            if (!runnerActive) return;
            if (e.key === 'ArrowLeft' && runnerTargetLane > -1) runnerTargetLane--;
            if (e.key === 'ArrowRight' && runnerTargetLane < 1) runnerTargetLane++;
            if ((e.key === 'ArrowUp' || e.key === ' ') && !runnerIsJumping && !runnerIsSliding) triggerRunnerJump();
            if (e.key === 'ArrowDown' && !runnerIsJumping) triggerRunnerSlide();
        });

        // Touch Swipe
        let tsX, tsY;
        document.addEventListener('touchstart', e => {
            tsX = e.touches[0].clientX;
            tsY = e.touches[0].clientY;
            if (runnerActive) e.preventDefault();
        }, {
            passive: false
        });

        document.addEventListener('touchend', e => {
            if (!runnerActive) return;
            let teX = e.changedTouches[0].clientX;
            let teY = e.changedTouches[0].clientY;
            let dx = teX - tsX;
            let dy = teY - tsY;

            if (Math.abs(dx) > Math.abs(dy)) {
                if (dx > 40 && runnerTargetLane < 1) runnerTargetLane++;
                else if (dx < -40 && runnerTargetLane > -1) runnerTargetLane--;
            } else {
                if (dy < -40 && !runnerIsJumping && !runnerIsSliding) triggerRunnerJump();
                else if (dy > 40 && !runnerIsJumping) triggerRunnerSlide();
            }
        });
    }

    function triggerRunnerJump() {
        runnerIsJumping = true;
        runnerJumpV = 12;
        const snd = document.getElementById('runnerJumpSound');
        if (snd) {
            snd.currentTime = 0;
            snd.play().catch(e => {});
        }
    }

    function triggerRunnerSlide() {
        runnerIsSliding = true;
        runnerSlideTimer = 45;
    }

    function startRunnerGame() {
        $('#runner-start-overlay').hide();
        $('#runner-gameover-overlay').hide();
        runnerActive = true;
        runnerScore = 0;
        runnerSpeed = 0.015;
        runnerTargetLane = 0;
        runnerCurrentX = 0;
        runnerIsJumping = false;
        runnerIsSliding = false;
        runnerJumpY = 0;
        runnerObstacles = [];
        runnerFrame = 0;

        if (runnerLoop) cancelAnimationFrame(runnerLoop);
        drawRunnerLoop();
    }

    function project(x, y, z) {
        // z: 0 to 1 (0 is far/horizon, 1 is camera)
        // Non-linear perspective for a semi-top-down "bird's eye" view
        const perspective = Math.pow(z, 1.4);
        const pX = (CANVAS_W / 2) + (x * perspective * PLANE_W / 2);
        const pY = HORIZON_Y + (perspective * (CANVAS_H - HORIZON_Y));
        const scale = 0.1 + (perspective * 0.9);
        return {
            x: pX,
            y: pY,
            scale
        };
    }

    function drawRunnerLoop() {
        if (!runnerActive) return;

        // Clear Background (Gradient)
        let grad = runnerCtx.createLinearGradient(0, 0, 0, CANVAS_H);
        grad.addColorStop(0, '#050010');
        grad.addColorStop(0.4, '#100020');
        grad.addColorStop(1, '#000000');
        runnerCtx.fillStyle = grad;
        runnerCtx.fillRect(0, 0, CANVAS_W, CANVAS_H);

        // Draw Road (Perspective Lanes)
        runnerCtx.strokeStyle = 'cyan';
        runnerCtx.lineWidth = 2;
        runnerCtx.shadowBlur = 10;
        runnerCtx.shadowColor = 'cyan';

        const lanePositions = [-1.5, -0.5, 0.5, 1.5];
        lanePositions.forEach(lx => {
            let pFar = project(lx / 1.5, 0, 0);
            let pNear = project(lx / 1.5, 0, 1);
            runnerCtx.beginPath();
            runnerCtx.moveTo(pFar.x, pFar.y);
            runnerCtx.lineTo(pNear.x, pNear.y);
            runnerCtx.stroke();
        });

        // Moving Horizontal Lines
        runnerCtx.shadowBlur = 0;
        runnerCtx.strokeStyle = 'rgba(0, 255, 255, 0.2)';
        for (let i = 0; i < 15; i++) {
            let z = ((runnerFrame * runnerSpeed) % 0.1) + (i * 0.1);
            if (z > 1) z -= 1;
            let pL = project(-1, 0, z);
            let pR = project(1, 0, z);
            runnerCtx.beginPath();
            runnerCtx.moveTo(pL.x, pL.y);
            runnerCtx.lineTo(pR.x, pR.y);
            runnerCtx.stroke();
        }

        // Update Obstacles
        runnerFrame++;
        if (runnerFrame % 50 === 0) {
            let lane = Math.floor(Math.random() * 3) - 1; // -1, 0, 1
            let type = Math.random() > 0.5 ? 'jump' : 'slide';
            runnerObstacles.push({
                lane,
                type,
                z: 0
            });
        }

        // Draw Obstacles (Back to Front)
        runnerObstacles.sort((a, b) => a.z - b.z);

        for (let i = runnerObstacles.length - 1; i >= 0; i--) {
            let obs = runnerObstacles[i];
            obs.z += runnerSpeed;

            let p = project(obs.lane * 0.66, 0, obs.z);
            let size = p.scale * 80;

            runnerCtx.save();
            runnerCtx.translate(p.x, p.y);

            if (obs.type === 'jump') {
                // Ground Barrier
                runnerCtx.fillStyle = '#ff00ff';
                runnerCtx.shadowBlur = 15;
                runnerCtx.shadowColor = '#ff00ff';
                runnerCtx.fillRect(-size / 2, -size / 3, size, size / 3);
            } else {
                // Floating Barrier
                runnerCtx.fillStyle = '#00ffff';
                runnerCtx.shadowBlur = 15;
                runnerCtx.shadowColor = '#00ffff';
                runnerCtx.fillRect(-size / 1.5, -size * 1.2, size * 1.33, size / 4);
            }
            runnerCtx.restore();

            // Collision (z is near 0.85 to 0.95)
            if (obs.z > 0.8 && obs.z < 0.95 && obs.lane === runnerTargetLane) {
                if (obs.type === 'jump' && runnerJumpY < 40) runnerGameOver();
                if (obs.type === 'slide' && !runnerIsSliding) runnerGameOver();
            }

            if (obs.z > 1.2) runnerObstacles.splice(i, 1);
        }

        // Update Player
        let targetX = runnerTargetLane * 0.66;
        runnerCurrentX += (targetX - runnerCurrentX) * 0.15;

        if (runnerIsJumping) {
            runnerJumpY += runnerJumpV;
            runnerJumpV -= 0.6;
            if (runnerJumpY <= 0) {
                runnerJumpY = 0;
                runnerIsJumping = false;
            }
        }

        if (runnerIsSliding) {
            runnerSlideTimer--;
            if (runnerSlideTimer <= 0) runnerIsSliding = false;
        }

        // Project Player
        let playerP = project(runnerCurrentX, 0, 0.9);
        let pSize = 60;

        runnerCtx.save();
        runnerCtx.translate(playerP.x, playerP.y - runnerJumpY);
        runnerCtx.shadowBlur = 20;
        runnerCtx.shadowColor = '#e50914';
        runnerCtx.fillStyle = '#e50914';

        let py = runnerIsSliding ? -15 : -50;
        let ph = runnerIsSliding ? 15 : 50;

        runnerCtx.fillRect(-15, py, 30, ph);
        runnerCtx.strokeStyle = '#fff';
        runnerCtx.lineWidth = 2;
        runnerCtx.strokeRect(-15, py, 30, ph);
        runnerCtx.restore();

        // Update Score
        runnerScore += runnerSpeed * 100;
        $('#runner-score-val').text(Math.floor(runnerScore) + 'm');
        runnerSpeed += 0.00001;

        runnerLoop = requestAnimationFrame(drawRunnerLoop);
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