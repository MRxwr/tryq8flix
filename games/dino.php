<!-- Cyber Dino CSS -->
<style>
    .dino-container {
        max-width: 400px;
        margin: 0 auto;
        background: #000;
        border-radius: 20px;
        overflow: hidden;
        position: relative;
        box-shadow: 0 0 30px rgba(0, 255, 65, 0.2);
        border: 2px solid #222;
        user-select: none;
        touch-action: none;
        aspect-ratio: 2/3;
    }

    #dino-canvas {
        background: #050505;
        display: block;
        width: 100%;
        height: 100%;
    }

    .dino-ui {
        position: absolute;
        top: 20px;
        width: 100%;
        padding: 0 20px;
        display: flex;
        justify-content: space-between;
        font-family: 'Courier New', Courier, monospace;
        color: #00ff41;
        pointer-events: none;
        z-index: 5;
    }

    .dino-score {
        font-size: 1.2rem;
        font-weight: bold;
    }

    .dino-hi-score {
        font-size: 0.8rem;
        color: #008f11;
    }

    .dino-overlay {
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
        z-index: 10;
        backdrop-filter: blur(8px);
    }
</style>

<!-- Dino Overlays -->
<div id="dino-start-overlay" class="dino-overlay" style="display: none;">
    <div class="text-center p-4">
        <div class="mb-3" style="font-size: 4rem;">🦖</div>
        <h2 class="text-white mb-2">CYBER DINO</h2>
        <p class="text-white-50 small mb-4">
            Dodge digital obstacles in the neon void.<br>
            <span class="text-success">↑ JUMP</span> | <span class="text-info">↓ DUCK</span><br>
            Space / Arrows / Tap
        </p>
        <button class="btn btn-netflix btn-lg px-5 shadow-lg" onclick="startDinoGame()">START MISSION</button>
    </div>
</div>

<div id="dino-gameover-overlay" class="dino-overlay" style="display:none;">
    <h2 class="text-danger mb-2">SYSTEM FAILURE</h2>
    <div class="small text-white-50 mb-1">SCORE ACHIEVED</div>
    <div id="dino-final-score" class="text-white h1 mb-4 fw-bold">00000</div>
    <div class="d-grid gap-2 w-75">
        <button class="btn btn-netflix py-3" onclick="startDinoGame()">RE-INITIALIZE</button>
        <button class="btn btn-outline-light" onclick="dinoActive = false; showGamesHome();">QUIT TERMINAL</button>
    </div>
</div>

<!-- Dino Sounds -->
<audio id="dinoJumpSound" src="https://assets.mixkit.co/active_storage/sfx/2571/2571-preview.mp3" preload="auto"></audio>
<audio id="dinoHitSound" src="https://assets.mixkit.co/active_storage/sfx/21/21-preview.mp3" preload="auto"></audio>
<audio id="dinoPointSound" src="https://assets.mixkit.co/active_storage/sfx/2019/2019-preview.mp3" preload="auto"></audio>

<script>
    let dinoCanvas, dinoCtx;
    let dinoActive = false;
    let dinoScore = 0;
    let dinoHighScore = localStorage.getItem('dinoHighScore') || 0;

    // Game Constants (Portrait)
    const D_WIDTH = 400;
    const D_HEIGHT = 600;
    const GROUND_Y = 530;
    const GRAVITY = 0.6;

    // Game State
    let dinoTimer = 0;
    let dinoSpeed = 6;
    let obstacles = [];
    let clouds = [];

    const dino = {
        x: 40,
        y: GROUND_Y,
        w: 40,
        h: 40,
        dy: 0,
        jumpForce: 13,
        isJumping: false,
        isDucking: false,
        frame: 0
    };

    function initDino() {
        const html = `
            <div class="dino-container">
                <div class="dino-ui">
                    <span class="dino-hi-score">HI <span id="hi-score-val">00000</span></span>
                    <span id="score-val" class="dino-score">00000</span>
                </div>
                <canvas id="dino-canvas" width="400" height="600"></canvas>
            </div>
        `;
        $('#game-container').html(html);
        dinoCanvas = document.getElementById('dino-canvas');
        dinoCtx = dinoCanvas.getContext('2d');
        $('#hi-score-val').text(String(dinoHighScore).padStart(5, '0'));

        $('#dino-start-overlay').show();
        setupDinoControls();
    }

    function setupDinoControls() {
        // Keyboard
        document.addEventListener('keydown', (e) => {
            if (!dinoActive) return;
            if ((e.code === 'Space' || e.code === 'ArrowUp') && !dino.isJumping) {
                dino.dy = -dino.jumpForce;
                dino.isJumping = true;
                playSound('dinoJumpSound');
            }
            if (e.code === 'ArrowDown') {
                dino.isDucking = true;
            }
        });

        document.addEventListener('keyup', (e) => {
            if (e.code === 'ArrowDown') dino.isDucking = false;
        });

        // Touch Swipe Logic
        let tsY;
        dinoCanvas.addEventListener('touchstart', e => {
            if (!dinoActive) return;
            e.preventDefault();
            tsY = e.touches[0].clientY;
            dino.isDucking = false; // Reset ducking on new touch
        }, {
            passive: false
        });

        dinoCanvas.addEventListener('touchmove', e => {
            if (!dinoActive) return;
            e.preventDefault();
        }, {
            passive: false
        });

        dinoCanvas.addEventListener('touchend', e => {
            if (!dinoActive) return;
            e.preventDefault();
            let teY = e.changedTouches[0].clientY;
            let dy = teY - tsY;

            if (dy < -30) { // Swipe Up
                if (!dino.isJumping) {
                    dino.dy = -dino.jumpForce;
                    dino.isJumping = true;
                    dino.isDucking = false;
                    playSound('dinoJumpSound');
                }
            } else if (dy > 30) { // Swipe Down
                dino.isDucking = true;
                setTimeout(() => {
                    dino.isDucking = false;
                }, 500); // Auto-rise after 0.5s for touch
            } else { // Simple Tap
                if (!dino.isJumping) {
                    dino.dy = -dino.jumpForce;
                    dino.isJumping = true;
                    dino.isDucking = false;
                    playSound('dinoJumpSound');
                }
            }
        }, {
            passive: false
        });
    }

    function playSound(id) {
        const s = document.getElementById(id);
        if (s) {
            s.currentTime = 0;
            s.play().catch(() => {});
        }
    }

    function startDinoGame() {
        $('#dino-start-overlay').hide();
        $('#dino-gameover-overlay').hide();
        dinoActive = true;
        dinoScore = 0;
        dinoSpeed = 6;
        obstacles = [];
        clouds = [];
        dino.y = GROUND_Y;
        dino.dy = 0;
        dino.isJumping = false;
        dino.isDucking = false;
        dinoTimer = 0;

        updateDino();
    }

    function updateDino() {
        if (!dinoActive) return;

        dinoCtx.clearRect(0, 0, D_WIDTH, D_HEIGHT);

        // Background Grid (Optional aesthetic)
        dinoCtx.strokeStyle = 'rgba(0, 255, 65, 0.05)';
        dinoCtx.lineWidth = 1;
        for (let i = 0; i < D_HEIGHT; i += 50) {
            dinoCtx.beginPath();
            dinoCtx.moveTo(0, i);
            dinoCtx.lineTo(D_WIDTH, i);
            dinoCtx.stroke();
        }

        // Ground Line
        dinoCtx.strokeStyle = '#333';
        dinoCtx.lineWidth = 2;
        dinoCtx.beginPath();
        dinoCtx.moveTo(0, GROUND_Y);
        dinoCtx.lineTo(D_WIDTH, GROUND_Y);
        dinoCtx.stroke();

        // Clouds / Bits
        if (Math.random() < 0.02) clouds.push({
            x: D_WIDTH,
            y: Math.random() * 300 + 50,
            v: Math.random() * 1 + 0.5,
            w: 20 + Math.random() * 40
        });
        clouds.forEach((c, i) => {
            c.x -= c.v;
            dinoCtx.fillStyle = 'rgba(0, 255, 65, 0.1)';
            dinoCtx.fillRect(c.x, c.y, c.w, 4);
            if (c.x < -100) clouds.splice(i, 1);
        });

        // Dino Physics
        if (dino.isJumping) {
            dino.y += dino.dy;
            dino.dy += GRAVITY;
            if (dino.y >= GROUND_Y) {
                dino.y = GROUND_Y;
                dino.isJumping = false;
                dino.dy = 0;
            }
        }

        dino.frame++;
        drawCyberDino(dino.x, dino.y, dino.isDucking);

        // Obstacles
        if (dinoTimer % (Math.floor(60 + Math.random() * 40)) === 0 && dinoTimer > 30) {
            let type = Math.random() > 0.3 ? 'cactus' : 'bird';
            if (type === 'cactus') {
                obstacles.push({
                    x: D_WIDTH,
                    y: GROUND_Y,
                    w: 15 + Math.random() * 25,
                    h: 30 + Math.random() * 30,
                    type: 'cactus'
                });
            } else {
                obstacles.push({
                    x: D_WIDTH,
                    y: GROUND_Y - 45 - (Math.random() * 60),
                    w: 30,
                    h: 20,
                    type: 'bird'
                });
            }
        }
        dinoTimer++;

        // Update & Draw Obstacles
        for (let i = obstacles.length - 1; i >= 0; i--) {
            const obs = obstacles[i];
            obs.x -= dinoSpeed;

            dinoCtx.save();
            if (obs.type === 'cactus') {
                // Neon Mountains
                dinoCtx.fillStyle = '#00ff41';
                dinoCtx.shadowBlur = 15;
                dinoCtx.shadowColor = '#00ff41';

                dinoCtx.beginPath();
                dinoCtx.moveTo(obs.x, obs.y);
                dinoCtx.lineTo(obs.x + obs.w / 2, obs.y - obs.h);
                dinoCtx.lineTo(obs.x + obs.w, obs.y);
                dinoCtx.closePath();
                dinoCtx.fill();

                // Shading for 3D look
                dinoCtx.fillStyle = 'rgba(0,0,0,0.3)';
                dinoCtx.beginPath();
                dinoCtx.moveTo(obs.x + obs.w / 2, obs.y - obs.h);
                dinoCtx.lineTo(obs.x + obs.w, obs.y);
                dinoCtx.lineTo(obs.x + obs.w / 2, obs.y);
                dinoCtx.fill();
            } else {
                // Neon Clouds (Flying)
                dinoCtx.fillStyle = '#00f6ff';
                dinoCtx.shadowBlur = 15;
                dinoCtx.shadowColor = '#00f6ff';

                let ox = obs.x;
                let oy = obs.y - obs.h;
                let ow = obs.w;
                let oh = obs.h;

                dinoCtx.beginPath();
                dinoCtx.arc(ox + ow * 0.25, oy + oh * 0.7, oh * 0.4, 0, Math.PI * 2);
                dinoCtx.arc(ox + ow * 0.5, oy + oh * 0.4, oh * 0.6, 0, Math.PI * 2);
                dinoCtx.arc(ox + ow * 0.75, oy + oh * 0.7, oh * 0.4, 0, Math.PI * 2);
                dinoCtx.fill();
            }
            dinoCtx.restore();

            // Collision Sensing
            let dw = dino.w - 15;
            let dh = dino.isDucking ? 20 : dino.h - 10;
            let dx = dino.x + 10;
            let dy = dino.isDucking ? dino.y - 20 : dino.y - dino.h + 5;

            if (dx < obs.x + obs.w && dx + dw > obs.x && dy < obs.y && dy + dh > obs.y - obs.h) {
                gameOverDino();
            }

            if (obs.x < -100) obstacles.splice(i, 1);
        }

        // Score update
        dinoScore += 0.15;
        let s = Math.floor(dinoScore);
        $('#score-val').text(String(s).padStart(5, '0'));
        if (s > 0 && s % 100 === 0 && dinoTimer % 60 === 0) playSound('dinoPointSound');

        dinoSpeed += 0.0015;
        requestAnimationFrame(updateDino);
    }

    function drawCyberDino(x, y, isDucking) {
        dinoCtx.save();

        let bounce = 0;
        if (!dino.isJumping && !isDucking) {
            bounce = Math.abs(Math.sin(dino.frame * 0.2)) * 3;
        }

        dinoCtx.translate(x, y - bounce);
        dinoCtx.fillStyle = '#00ff41';
        dinoCtx.shadowBlur = 15;
        dinoCtx.shadowColor = '#00ff41';

        if (isDucking) {
            // Ducking Dino Body
            dinoCtx.fillRect(0, -25, 45, 25);
            dinoCtx.fillRect(40, -25, 20, 15); // Stretched Head
            // Tail
            dinoCtx.beginPath();
            dinoCtx.moveTo(0, -10);
            dinoCtx.lineTo(-15, -5);
            dinoCtx.lineTo(0, 0);
            dinoCtx.fill();
            // Eye
            dinoCtx.fillStyle = '#000';
            dinoCtx.fillRect(52, -22, 4, 4);
        } else {
            // Standing/Running Dino
            // Tail
            dinoCtx.beginPath();
            dinoCtx.moveTo(0, -25);
            dinoCtx.lineTo(-18, -35);
            dinoCtx.lineTo(0, -10);
            dinoCtx.fill();

            // Body
            dinoCtx.fillRect(0, -42, 26, 32);
            // Neck
            dinoCtx.fillRect(16, -52, 12, 15);
            // Head
            dinoCtx.fillRect(18, -55, 24, 15);
            // Small Arm
            dinoCtx.fillRect(24, -30, 8, 4);

            // Legs Cycle
            let leg1Offset = Math.sin(dino.frame * 0.3) * 8;
            let leg2Offset = Math.sin(dino.frame * 0.3 + Math.PI) * 8;

            if (dino.isJumping) {
                leg1Offset = 0;
                leg2Offset = 5;
            }

            // Leg 1
            dinoCtx.fillRect(5, -12, 6, 8 + Math.max(0, -leg1Offset));
            // Leg 2
            dinoCtx.fillRect(16, -12, 6, 8 + Math.max(0, -leg2Offset));

            // Eye
            dinoCtx.fillStyle = '#111';
            dinoCtx.fillRect(35, -52, 4, 4);
        }
        dinoCtx.restore();
    }

    function gameOverDino() {
        dinoActive = false;
        playSound('dinoHitSound');
        if (dinoScore > dinoHighScore) {
            dinoHighScore = Math.floor(dinoScore);
            localStorage.setItem('dinoHighScore', dinoHighScore);
            $('#hi-score-val').text(String(dinoHighScore).padStart(5, '0'));
        }
        $('#dino-final-score').text(String(Math.floor(dinoScore)).padStart(5, '0'));
        $('#dino-gameover-overlay').fadeIn();
    }
</script>