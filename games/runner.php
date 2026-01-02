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

            let pFront = project(obs.lane * 0.72, 0, obs.z);
            let pBack = project(obs.lane * 0.72, 0, Math.max(0, obs.z - 0.08)); // Depth points

            let fSize = pFront.scale * 100;
            let bSize = pBack.scale * 100;
            let fH = fSize * (obs.type === 'jump' ? 0.6 : 1.5);
            let bH = bSize * (obs.type === 'jump' ? 0.6 : 1.5);

            if (obs.type === 'jump') {
                // 3D Wooden Crate
                // Left Side
                runnerCtx.fillStyle = '#5D2E0A';
                runnerCtx.beginPath();
                runnerCtx.moveTo(pFront.x - fSize / 2, pFront.y);
                runnerCtx.lineTo(pBack.x - bSize / 2, pBack.y);
                runnerCtx.lineTo(pBack.x - bSize / 2, pBack.y - bH);
                runnerCtx.lineTo(pFront.x - fSize / 2, pFront.y - fH);
                runnerCtx.fill();

                // Right Side
                runnerCtx.fillStyle = '#4D2608';
                runnerCtx.beginPath();
                runnerCtx.moveTo(pFront.x + fSize / 2, pFront.y);
                runnerCtx.lineTo(pBack.x + bSize / 2, pBack.y);
                runnerCtx.lineTo(pBack.x + bSize / 2, pBack.y - bH);
                runnerCtx.lineTo(pFront.x + fSize / 2, pFront.y - fH);
                runnerCtx.fill();

                // Top Face
                runnerCtx.fillStyle = '#A0522D';
                runnerCtx.beginPath();
                runnerCtx.moveTo(pFront.x - fSize / 2, pFront.y - fH);
                runnerCtx.lineTo(pBack.x - bSize / 2, pBack.y - bH);
                runnerCtx.lineTo(pBack.x + bSize / 2, pBack.y - bH);
                runnerCtx.lineTo(pFront.x + fSize / 2, pFront.y - fH);
                runnerCtx.fill();

                // Front Face
                runnerCtx.fillStyle = '#8B4513';
                runnerCtx.fillRect(pFront.x - fSize / 2, pFront.y - fH, fSize, fH);
                runnerCtx.strokeStyle = '#5D2E0A';
                runnerCtx.lineWidth = Math.max(1, fSize / 15);
                runnerCtx.strokeRect(pFront.x - fSize / 2, pFront.y - fH, fSize, fH);

                // Front Detail (X)
                runnerCtx.beginPath();
                runnerCtx.moveTo(pFront.x - fSize / 2, pFront.y - fH);
                runnerCtx.lineTo(pFront.x + fSize / 2, pFront.y);
                runnerCtx.moveTo(pFront.x + fSize / 2, pFront.y - fH);
                runnerCtx.lineTo(pFront.x - fSize / 2, pFront.y);
                runnerCtx.stroke();
            } else {
                // 3D Wooden Table (Slide)
                let fW = fSize * 1.33;
                let bW = bSize * 1.33;
                let legW = fSize * 0.1;
                let bLegW = bSize * 0.1;

                // Back Legs (drawn first)
                runnerCtx.fillStyle = '#3D1F06';
                runnerCtx.fillRect(pBack.x - bW / 2, pBack.y - bH, bLegW, bH);
                runnerCtx.fillRect(pBack.x + bW / 2 - bLegW, pBack.y - bH, bLegW, bH);

                // Table Top slab (Side panels)
                let thick = fSize * 0.15;
                let bThick = bSize * 0.15;

                runnerCtx.fillStyle = '#5D2E0A'; // Slab Sides
                runnerCtx.beginPath();
                runnerCtx.moveTo(pFront.x - fW / 2, pFront.y - fH);
                runnerCtx.lineTo(pBack.x - bW / 2, pBack.y - bH);
                runnerCtx.lineTo(pBack.x - bW / 2, pBack.y - bH + bThick);
                runnerCtx.lineTo(pFront.x - fW / 2, pFront.y - fH + thick);
                runnerCtx.fill();

                runnerCtx.beginPath();
                runnerCtx.moveTo(pFront.x + fW / 2, pFront.y - fH);
                runnerCtx.lineTo(pBack.x + bW / 2, pBack.y - bH);
                runnerCtx.lineTo(pBack.x + bW / 2, pBack.y - bH + bThick);
                runnerCtx.lineTo(pFront.x + fW / 2, pFront.y - fH + thick);
                runnerCtx.fill();

                // Slab Top
                runnerCtx.fillStyle = '#A0522D';
                runnerCtx.beginPath();
                runnerCtx.moveTo(pFront.x - fW / 2, pFront.y - fH);
                runnerCtx.lineTo(pBack.x - bW / 2, pBack.y - bH);
                runnerCtx.lineTo(pBack.x + bW / 2, pBack.y - bH);
                runnerCtx.lineTo(pFront.x + fW / 2, pFront.y - fH);
                runnerCtx.fill();

                // Front Legs
                runnerCtx.fillStyle = '#4D2608';
                runnerCtx.fillRect(pFront.x - fW / 2, pFront.y - fH, legW, fH);
                runnerCtx.fillRect(pFront.x + fW / 2 - legW, pFront.y - fH, legW, fH);

                // Front Edge of Top
                runnerCtx.fillStyle = '#8B4513';
                runnerCtx.fillRect(pFront.x - fW / 2, pFront.y - fH, fW, thick);
                runnerCtx.strokeStyle = '#5D2E0A';
                runnerCtx.strokeRect(pFront.x - fW / 2, pFront.y - fH, fW, thick);
            }

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
        drawRunnerCharacter(playerP.x, playerP.y, playerP.scale);

        // Update Score
        runnerScore += runnerSpeed * 100;
        $('#runner-score-val').text(Math.floor(runnerScore) + 'm');
        runnerSpeed += 0.00001;

        runnerLoop = requestAnimationFrame(drawRunnerLoop);
    }

    function drawRunnerCharacter(x, y, scale) {
        runnerCtx.save();
        runnerCtx.translate(x, y);

        let s = scale * 1.5;
        let color = '#fff';
        let neonColor = '#e50914';

        runnerCtx.strokeStyle = color;
        runnerCtx.lineWidth = 3 * s;
        runnerCtx.lineCap = 'round';

        // Draw Skateboard
        runnerCtx.fillStyle = '#222';
        runnerCtx.fillRect(-20 * s, -5 * s, 40 * s, 4 * s);

        // Wheels (Neon)
        runnerCtx.fillStyle = neonColor;
        runnerCtx.beginPath();
        runnerCtx.arc(-15 * s, -1 * s, 2 * s, 0, Math.PI * 2);
        runnerCtx.arc(15 * s, -1 * s, 2 * s, 0, Math.PI * 2);
        runnerCtx.fill();

        if (runnerIsSliding) {
            // Crouched Skater
            runnerCtx.beginPath();
            // Torso (Low)
            runnerCtx.moveTo(0, -5 * s);
            runnerCtx.lineTo(-10 * s, -20 * s);
            // Arms (Tucked)
            runnerCtx.moveTo(-10 * s, -20 * s);
            runnerCtx.lineTo(5 * s, -15 * s);
            runnerCtx.stroke();
            // Head (Low)
            runnerCtx.beginPath();
            runnerCtx.arc(-12 * s, -27 * s, 6 * s, 0, Math.PI * 2);
            runnerCtx.fillStyle = color;
            runnerCtx.fill();
        } else {
            // Standing Skater
            let hipY = -5 * s;
            let shoulderY = -45 * s;

            // Standing Pose (one leg slightly bent)
            runnerCtx.beginPath();
            runnerCtx.moveTo(-5 * s, 0);
            runnerCtx.lineTo(0, hipY); // Back leg
            runnerCtx.moveTo(5 * s, 0);
            runnerCtx.lineTo(0, hipY); // Front leg
            runnerCtx.lineTo(-5 * s, shoulderY); // Torso (leaning)

            // Arms (for balance)
            runnerCtx.moveTo(-5 * s, shoulderY);
            runnerCtx.lineTo(-20 * s, -35 * s);
            runnerCtx.moveTo(-5 * s, shoulderY);
            runnerCtx.lineTo(15 * s, -40 * s);
            runnerCtx.stroke();

            // Head
            runnerCtx.beginPath();
            runnerCtx.arc(-8 * s, -55 * s, 7 * s, 0, Math.PI * 2);
            runnerCtx.fillStyle = color;
            runnerCtx.fill();
        }

        runnerCtx.restore();
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