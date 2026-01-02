<!-- Cyber Dino CSS -->
<style>
    .dino-container {
        max-width: 600px;
        margin: 0 auto;
        background: #0a0a0a;
        border-radius: 20px;
        overflow: hidden;
        position: relative;
        box-shadow: 0 0 40px rgba(0, 255, 65, 0.1);
        border: 2px solid #1a1a1a;
        user-select: none;
        touch-action: none;
    }

    #dino-canvas {
        background: #050505;
        display: block;
        width: 100%;
        height: auto;
    }

    .dino-ui {
        position: absolute;
        top: 15px;
        right: 20px;
        font-family: 'Courier New', Courier, monospace;
        color: #00ff41;
        text-align: right;
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
        margin-right: 10px;
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
    <div class="text-center">
        <div class="mb-3" style="font-size: 4rem;">🦖</div>
        <h2 class="text-white mb-2">CYBER DINO</h2>
        <p class="text-white-50 small mb-4">
            Avoid obstacles in the neon desert.<br>
            <span class="text-success">↑ JUMP</span> | <span class="text-info">↓ DUCK</span><br>
            Space / Arrows / Tap
        </p>
        <button class="btn btn-netflix btn-lg px-5 shadow-lg" onclick="startDinoGame()">START RUN</button>
    </div>
</div>

<div id="dino-gameover-overlay" class="dino-overlay" style="display:none;">
    <h2 class="text-danger mb-2">EXTINCT</h2>
    <div id="dino-final-score" class="text-white h1 mb-4 fw-bold">00000</div>
    <div class="d-grid gap-2 w-75">
        <button class="btn btn-netflix py-3" onclick="startDinoGame()">RE-REGENERATE</button>
        <button class="btn btn-outline-light" onclick="dinoActive = false; showGamesHome();">EXIT SYSTEM</button>
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

    // Game Constants
    const D_WIDTH = 800;
    const D_HEIGHT = 200;
    const GROUND_Y = 170;
    const GRAVITY = 0.6;

    // Game State
    let dinoTimer = 0;
    let dinoSpeed = 7;
    let obstacles = [];
    let cloudFrame = 0;
    let clouds = [];

    const dino = {
        x: 50,
        y: GROUND_Y,
        w: 44,
        h: 44,
        dy: 0,
        jumpForce: 12,
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
                <canvas id="dino-canvas" width="800" height="200"></canvas>
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

        // Touch/Click
        dinoCanvas.addEventListener('touchstart', e => {
            if (!dinoActive) return;
            e.preventDefault();
            if (!dino.isJumping) {
                dino.dy = -dino.jumpForce;
                dino.isJumping = true;
                playSound('dinoJumpSound');
            }
        }, {
            passive: false
        });

        // Long press for ducking on touch? Let's use a simple tap-to-jump for now.
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
        dinoSpeed = 7;
        obstacles = [];
        clouds = [];
        dino.y = GROUND_Y;
        dino.dy = 0;
        dino.isJumping = false;
        dino.isDucking = false;

        requestAnimationFrame(updateDino);
    }

    function updateDino() {
        if (!dinoActive) return;

        dinoCtx.clearRect(0, 0, D_WIDTH, D_HEIGHT);

        // Ground
        dinoCtx.strokeStyle = '#333';
        dinoCtx.beginPath();
        dinoCtx.moveTo(0, GROUND_Y + 2);
        dinoCtx.lineTo(D_WIDTH, GROUND_Y + 2);
        dinoCtx.stroke();

        // Clouds (Decorative)
        if (Math.random() < 0.01) clouds.push({
            x: D_WIDTH,
            y: Math.random() * 80 + 20,
            v: Math.random() * 0.5 + 0.1
        });
        clouds.forEach((c, i) => {
            c.x -= c.v;
            dinoCtx.fillStyle = 'rgba(255,255,255,0.05)';
            dinoCtx.fillRect(c.x, c.y, 40, 10);
            if (c.x < -50) clouds.splice(i, 1);
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

        // Animated Dino
        dino.frame++;
        drawCyberDino(dino.x, dino.y, dino.isDucking);

        // Obstacles spawning
        if (dinoTimer % 100 === 0) {
            let type = Math.random() > 0.3 ? 'cactus' : 'bird';
            if (type === 'cactus') {
                obstacles.push({
                    x: D_WIDTH,
                    y: GROUND_Y,
                    w: 20 + Math.random() * 20,
                    h: 30 + Math.random() * 20,
                    type: 'cactus'
                });
            } else {
                obstacles.push({
                    x: D_WIDTH,
                    y: GROUND_Y - 50 - (Math.random() * 40),
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

            // Draw Obstacle
            if (obs.type === 'cactus') {
                dinoCtx.fillStyle = '#00ff41';
                dinoCtx.shadowBlur = 10;
                dinoCtx.shadowColor = '#00ff41';
                dinoCtx.fillRect(obs.x, obs.y - obs.h, obs.w, obs.h);
            } else {
                dinoCtx.fillStyle = '#00f6ff';
                dinoCtx.shadowBlur = 10;
                dinoCtx.shadowColor = '#00f6ff';
                // Flapping bird
                let flap = Math.sin(dinoTimer * 0.2) * 5;
                dinoCtx.fillRect(obs.x, obs.y - flap, obs.w, obs.h);
            }
            dinoCtx.shadowBlur = 0;

            // Collision
            let dx = dino.x;
            let dy = dino.isDucking ? dino.y - 20 : dino.y - dino.h;
            let dw = dino.w;
            let dh = dino.isDucking ? 20 : dino.h;

            if (dx < obs.x + obs.w && dx + dw > obs.x && dy < obs.y && dy + dh > obs.y - obs.h) {
                gameOverDino();
            }

            if (obs.x < -100) obstacles.splice(i, 1);
        }

        // Score update
        dinoScore += 0.1;
        let s = Math.floor(dinoScore);
        $('#score-val').text(String(s).padStart(5, '0'));
        if (s > 0 && s % 100 === 0 && dinoTimer % 1 === 0) {
            // Milestone sound
            if (dinoTimer % 50 === 0) playSound('dinoPointSound');
        }

        dinoSpeed += 0.001;
        requestAnimationFrame(updateDino);
    }

    function drawCyberDino(x, y, isDucking) {
        dinoCtx.save();
        dinoCtx.translate(x, y);

        dinoCtx.fillStyle = '#00ff41';
        dinoCtx.shadowBlur = 15;
        dinoCtx.shadowColor = '#00ff41';

        if (isDucking) {
            // Ducking Dino
            dinoCtx.fillRect(0, -25, 60, 25);
            dinoCtx.fillStyle = '#000';
            dinoCtx.fillRect(45, -20, 5, 5); // Eye
        } else {
            // Standing/Running Dino
            dinoCtx.fillRect(0, -44, 30, 44); // Body
            dinoCtx.fillRect(20, -44, 24, 15); // Head

            // Legs
            let foot = Math.sin(dino.frame * 0.2) > 0 ? 5 : 0;
            if (dino.isJumping) foot = 0;
            dinoCtx.fillRect(5, -5 + foot, 8, 5);
            dinoCtx.fillRect(17, -5 - foot, 8, 5);

            // Eye
            dinoCtx.fillStyle = '#000';
            dinoCtx.fillRect(36, -40, 4, 4);
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