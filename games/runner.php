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
        touch-action: none;
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
        <button class="btn btn-netflix btn-lg px-5 shadow-lg" onclick="startRunnerGame()">START MISSION</button>
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
    const HORIZON_Y = 220; // Lower horizon for "behind the player" chase view
    const PLANE_W = 600; // Conceptual width at bottom plane

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
            if (!runnerActive) return;
            tsX = e.touches[0].clientX;
            tsY = e.touches[0].clientY;
        }, {
            passive: false
        });

        document.addEventListener('touchmove', e => {
            if (!runnerActive) return;
            e.preventDefault();
        }, {
            passive: false
        });

        document.addEventListener('touchend', e => {
            if (!runnerActive) return;
            e.preventDefault();
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
        }, {
            passive: false
        });
    }

    function triggerRunnerJump() {
        runnerIsJumping = true;
        runnerJumpV = 10;
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

    function project(x, alt, z) {
        // z: 0 (horizon) to 1 (camera)
        const perspective = Math.pow(z, 1.2);
        const pX = (CANVAS_W / 2) + (x * perspective * PLANE_W / 2);
        const pY = HORIZON_Y + (perspective * (CANVAS_H - HORIZON_Y)) - (alt * perspective);
        const scale = 0.05 + (perspective * 0.95);
        return {
            x: pX,
            y: pY,
            scale
        };
    }

    function drawRunnerLoop() {
        if (!runnerActive) return;

        // Draw Sky
        let skyGrad = runnerCtx.createLinearGradient(0, 0, 0, HORIZON_Y);
        skyGrad.addColorStop(0, '#000');
        skyGrad.addColorStop(1, '#050015');
        runnerCtx.fillStyle = skyGrad;
        runnerCtx.fillRect(0, 0, CANVAS_W, HORIZON_Y);

        // Draw Ground Surface
        runnerCtx.fillStyle = '#050505';
        runnerCtx.fillRect(0, HORIZON_Y, CANVAS_W, CANVAS_H - HORIZON_Y);

        // Draw Road Base
        runnerCtx.fillStyle = '#111';
        let pL1 = project(-1.1, 0, 0);
        let pR1 = project(1.1, 0, 0);
        let pR2 = project(1.1, 0, 1.2);
        let pL2 = project(-1.1, 0, 1.2);
        runnerCtx.beginPath();
        runnerCtx.moveTo(pL1.x, pL1.y);
        runnerCtx.lineTo(pR1.x, pR1.y);
        runnerCtx.lineTo(pR2.x, pR2.y);
        runnerCtx.lineTo(pL2.x, pL2.y);
        runnerCtx.fill();

        // Draw Lane Borders
        runnerCtx.strokeStyle = 'rgba(0, 255, 255, 0.4)';
        runnerCtx.lineWidth = 2;
        const laneX = [-1.1, -0.36, 0.36, 1.1];
        laneX.forEach(lx => {
            let start = project(lx, 0, 0);
            let end = project(lx, 0, 1.2);
            runnerCtx.beginPath();
            runnerCtx.moveTo(start.x, start.y);
            runnerCtx.lineTo(end.x, end.y);
            runnerCtx.stroke();
        });

        // Moving Street Lines (Horizontal)
        runnerCtx.strokeStyle = 'rgba(0, 255, 255, 0.1)';
        for (let i = 0; i < 12; i++) {
            let z = ((runnerFrame * runnerSpeed) % 0.1) + (i * 0.1);
            if (z > 1.2) z -= 1.2;
            let start = project(-1.1, 0, z);
            let end = project(1.1, 0, z);
            runnerCtx.beginPath();
            runnerCtx.moveTo(start.x, start.y);
            runnerCtx.lineTo(end.x, end.y);
            runnerCtx.stroke();
        }

        // Update Obstacles
        runnerFrame++;
        if (runnerFrame % 60 === 0) {
            let lane = Math.floor(Math.random() * 3) - 1; // -1, 0, 1
            let type = Math.random() > 0.5 ? 'jump' : 'slide';
            runnerObstacles.push({
                lane,
                type,
                z: 0
            });
        }

        // Draw Obstacles (sort by Z for depth)
        runnerObstacles.sort((a, b) => a.z - b.z);
        for (let i = runnerObstacles.length - 1; i >= 0; i--) {
            let obs = runnerObstacles[i];
            obs.z += runnerSpeed;

            let p = project(obs.lane * 0.72, 0, obs.z);
            let size = p.scale * 100;

            runnerCtx.save();
            runnerCtx.translate(p.x, p.y);

            if (obs.type === 'jump') {
                // Wooden Crate (Jump)
                runnerCtx.fillStyle = '#8B4513'; // Saddle Brown
                runnerCtx.shadowBlur = 5;
                runnerCtx.shadowColor = 'rgba(0,0,0,0.5)';
                runnerCtx.fillRect(-size / 2, -size / 1.8, size, size / 1.8);

                // Crate Detail (Cross)
                runnerCtx.strokeStyle = '#5D2E0A';
                runnerCtx.lineWidth = Math.max(1, size / 15);
                runnerCtx.strokeRect(-size / 2, -size / 1.8, size, size / 1.8);
                runnerCtx.beginPath();
                runnerCtx.moveTo(-size / 2, -size / 1.8);
                runnerCtx.lineTo(size / 2, 0);
                runnerCtx.moveTo(size / 2, -size / 1.8);
                runnerCtx.lineTo(-size / 2, 0);
                runnerCtx.stroke();
            } else {
                // Wooden Table (Slide)
                runnerCtx.fillStyle = '#5D2E0A'; // Dark Brown for legs
                let legW = size * 0.1;
                runnerCtx.fillRect(-size / 1.5, -size * 1.5, legW, size * 1.5); // Left Leg
                runnerCtx.fillRect(size / 1.5 - legW, -size * 1.5, legW, size * 1.5); // Right Leg

                runnerCtx.fillStyle = '#8B4513'; // Saddle Brown for top
                runnerCtx.fillRect(-size / 1.5, -size * 1.6, size * 1.33, size * 0.2); // Table Top

                // Box on Table
                runnerCtx.fillStyle = '#A0522D'; // Sienna
                runnerCtx.fillRect(-size / 3, -size * 2.1, size / 1.5, size * 0.5);
                runnerCtx.strokeStyle = '#5D2E0A';
                runnerCtx.strokeRect(-size / 3, -size * 2.1, size / 1.5, size * 0.5);
            }
            runnerCtx.restore();

            // Collision Sensing (Player is around z=0.75)
            if (obs.z > 0.7 && obs.z < 0.85 && obs.lane === runnerTargetLane) {
                if (obs.type === 'jump' && runnerJumpY < 40) runnerGameOver();
                if (obs.type === 'slide' && !runnerIsSliding) runnerGameOver();
            }

            if (obs.z > 1.3) runnerObstacles.splice(i, 1);
        }

        // Update Player Position
        let targetX = runnerTargetLane * 0.72;
        runnerCurrentX += (targetX - runnerCurrentX) * 0.12;

        if (runnerIsJumping) {
            runnerJumpY += runnerJumpV;
            runnerJumpV -= 0.5;
            if (runnerJumpY <= 0) {
                runnerJumpY = 0;
                runnerIsJumping = false;
            }
        }

        if (runnerIsSliding) {
            runnerSlideTimer--;
            if (runnerSlideTimer <= 0) runnerIsSliding = false;
        }

        // Draw Player (Behind the obstacles if z > playerZ)
        let playerP = project(runnerCurrentX, runnerJumpY, 0.75); // Player at z=0.75
        runnerCtx.save();
        runnerCtx.translate(playerP.x, playerP.y);
        runnerCtx.shadowBlur = 20;
        runnerCtx.shadowColor = '#e50914';
        runnerCtx.fillStyle = '#e50914';

        let pW = playerP.scale * 40;
        let pH = runnerIsSliding ? (playerP.scale * 20) : (playerP.scale * 60);
        let pOffset = runnerIsSliding ? -pH : -pH;

        runnerCtx.fillRect(-pW / 2, pOffset, pW, pH);
        runnerCtx.strokeStyle = '#fff';
        runnerCtx.lineWidth = 2;
        runnerCtx.strokeRect(-pW / 2, pOffset, pW, pH);
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