<!-- Cyber Jumper CSS -->
<style>
    .jumper-container {
        max-width: 400px;
        margin: 0 auto;
        background: #000;
        border-radius: 20px;
        overflow: hidden;
        position: relative;
        box-shadow: 0 0 30px rgba(138, 43, 226, 0.2);
        border: 2px solid #222;
        user-select: none;
        touch-action: none;
        aspect-ratio: 2/3;
    }

    #jumper-canvas {
        background: #050010;
        display: block;
        width: 100%;
        height: 100%;
    }

    .jumper-ui {
        position: absolute;
        top: 20px;
        width: 100%;
        padding: 0 20px;
        display: flex;
        justify-content: space-between;
        font-family: 'Outfit', sans-serif;
        color: #e0e0e0;
        pointer-events: none;
        z-index: 5;
    }

    .jumper-score {
        font-size: 1.5rem;
        font-weight: 800;
        color: #a333ff;
    }

    .jumper-hi-score {
        font-size: 0.9rem;
        color: #666;
    }

    .jumper-overlay {
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

<!-- Jumper Overlays -->
<div id="jumper-start-overlay" class="jumper-overlay" style="display: none;">
    <div class="text-center p-4">
        <div class="mb-3" style="font-size: 4rem;">👾</div>
        <h2 class="text-white mb-2">CYBER JUMPER</h2>
        <p class="text-white-50 small mb-4">
            Ascend the digital tower.<br>
            Don't look down.<br>
            <span class="text-primary">← Left</span> | <span class="text-primary">Right →</span>
        </p>
        <button class="btn btn-netflix btn-lg px-5 shadow-lg" onclick="startJumperGame()">INITIATE ASCENT</button>
    </div>
</div>

<div id="jumper-gameover-overlay" class="jumper-overlay" style="display:none;">
    <h2 class="text-danger mb-2">SIGNAL LOST</h2>
    <div class="small text-white-50 mb-1">ASCENT HEIGHT</div>
    <div id="jumper-final-score" class="text-white h1 mb-4 fw-bold">0m</div>
    <div class="d-grid gap-2 w-75">
        <button class="btn btn-netflix py-3" onclick="startJumperGame()">RE-INITIALIZE</button>
        <button class="btn btn-outline-light" onclick="jumperActive = false; showGamesHome();">QUIT MISSION</button>
    </div>
</div>

<!-- Jumper Sounds -->
<audio id="jumperBounce" src="https://assets.mixkit.co/active_storage/sfx/2571/2571-preview.mp3" preload="auto"></audio>
<audio id="jumperBreak" src="https://assets.mixkit.co/active_storage/sfx/21/21-preview.mp3" preload="auto"></audio>
<audio id="jumperFall" src="https://assets.mixkit.co/active_storage/sfx/2574/2574-preview.mp3" preload="auto"></audio>

<script>
    let jumperCanvas, jumperCtx;
    let jumperActive = false;
    let jumperScore = 0;
    let jumperMaxScore = 0;
    let jumperHighScore = localStorage.getItem('jumperHighScore') || 0;

    // Constants
    const J_WIDTH = 400;
    const J_HEIGHT = 600;
    const J_GRAVITY = 0.35;
    const J_BOUNCE = -11;

    // State
    let platforms = [];
    let hazards = [];
    let jumperCamY = 0;
    let jumperActiveKeys = {};
    let jumperTimer = 0;

    const jumperPlayer = {
        x: 200,
        y: 400,
        w: 30,
        h: 30,
        vx: 0,
        vy: 0,
        emotion: 'happy' // happy, shocked, focused
    };

    function initJumper() {
        const html = `
            <div class="jumper-container">
                <div class="jumper-ui">
                    <span class="jumper-hi-score">BEST <span id="jumper-hi-score-val">0</span>m</span>
                    <span id="jumper-score-val" class="jumper-score">0m</span>
                </div>
                <canvas id="jumper-canvas" width="400" height="600"></canvas>
            </div>
        `;
        $('#game-container').html(html);
        jumperCanvas = document.getElementById('jumper-canvas');
        jumperCtx = jumperCanvas.getContext('2d');
        $('#jumper-hi-score-val').text(jumperHighScore);

        $('#jumper-start-overlay').show();
        setupJumperControls();
    }

    function setupJumperControls() {
        document.addEventListener('keydown', e => {
            jumperActiveKeys[e.code] = true;
        });
        document.addEventListener('keyup', e => {
            jumperActiveKeys[e.code] = false;
        });

        jumperCanvas.addEventListener('touchstart', e => {
            const touchX = e.touches[0].clientX - jumperCanvas.getBoundingClientRect().left;
            const canvasWidth = jumperCanvas.clientWidth;
            if (touchX < canvasWidth / 2) jumperActiveKeys['ArrowLeft'] = true;
            else jumperActiveKeys['ArrowRight'] = true;
        });
        jumperCanvas.addEventListener('touchend', e => {
            jumperActiveKeys['ArrowLeft'] = false;
            jumperActiveKeys['ArrowRight'] = false;
        });
    }

    function spawnJumperPlatform(y, type = 'normal') {
        const x = Math.random() * (J_WIDTH - 60);
        return {
            x,
            y,
            w: 60,
            h: 12,
            type,
            v: Math.random() > 0.5 ? 2 : -2
        };
    }

    function spawnJumperHazard(y) {
        return {
            x: Math.random() * (J_WIDTH - 40),
            y: y,
            w: 40,
            h: 30,
            v: Math.random() > 0.5 ? 1.5 : -1.5,
            frame: Math.random() * 10
        };
    }

    function startJumperGame() {
        $('#jumper-start-overlay').hide();
        $('#jumper-gameover-overlay').hide();
        jumperActive = true;
        jumperScore = 0;
        jumperMaxScore = 0;
        jumperCamY = 0;
        jumperTimer = 0;

        jumperPlayer.x = 200;
        jumperPlayer.y = 400;
        jumperPlayer.vx = 0;
        jumperPlayer.vy = J_BOUNCE;

        platforms = [];
        hazards = [];
        // Starter platform
        platforms.push({
            x: 170,
            y: 500,
            w: 60,
            h: 10,
            type: 'normal'
        });

        // Initial platforms
        for (let i = 1; i < 15; i++) {
            platforms.push(spawnJumperPlatform(500 - (i * 60), Math.random() > 0.8 ? 'moving' : 'normal'));
        }

        updateJumper();
    }

    function updateJumper() {
        if (!jumperActive) return;
        jumperTimer++;
        jumperCtx.clearRect(0, 0, J_WIDTH, J_HEIGHT);

        // Background Detail
        jumperCtx.strokeStyle = 'rgba(163, 51, 255, 0.1)';
        for (let i = 0; i < J_HEIGHT; i += 100) {
            jumperCtx.beginPath();
            jumperCtx.moveTo(0, (i + jumperCamY) % 100);
            jumperCtx.lineTo(J_WIDTH, (i + jumperCamY) % 100);
            jumperCtx.stroke();
        }

        // Player Physics
        if (jumperActiveKeys['ArrowLeft']) jumperPlayer.vx = -6;
        else if (jumperActiveKeys['ArrowRight']) jumperPlayer.vx = 6;
        else jumperPlayer.vx *= 0.85;

        jumperPlayer.x += jumperPlayer.vx;
        jumperPlayer.vy += J_GRAVITY;
        jumperPlayer.y += jumperPlayer.vy;

        // Screen Wrap
        if (jumperPlayer.x < -20) jumperPlayer.x = J_WIDTH;
        if (jumperPlayer.x > J_WIDTH) jumperPlayer.x = -20;

        // Camera Follow
        if (jumperPlayer.y < J_HEIGHT * 0.45) {
            let diff = (J_HEIGHT * 0.45) - jumperPlayer.y;
            jumperPlayer.y += diff;
            jumperCamY += diff;
            platforms.forEach(p => p.y += diff);
            hazards.forEach(h => h.y += diff);
        }

        // Update Score
        jumperScore = Math.max(jumperScore, Math.floor(jumperCamY / 10));
        $('#jumper-score-val').text(jumperScore + 'm');

        // Emotional State Logic
        if (jumperPlayer.vy < -2) jumperPlayer.emotion = 'focused';
        else if (jumperPlayer.vy > 2) jumperPlayer.emotion = 'shocked';
        else jumperPlayer.emotion = 'happy';

        // Hazards Spawning & Collision
        if (jumperTimer % 300 === 0) {
            hazards.push(spawnJumperHazard(-50));
        }

        for (let i = hazards.length - 1; i >= 0; i--) {
            let h = hazards[i];
            h.x += h.v;
            if (h.x < 0 || h.x > J_WIDTH - h.w) h.v *= -1;

            drawJumperHazard(h);

            // Player vs Hazard Collision
            if (jumperPlayer.x + 25 > h.x && jumperPlayer.x + 5 < h.x + h.w &&
                jumperPlayer.y + 25 > h.y && jumperPlayer.y + 5 < h.y + h.h) {
                gameOverJumper();
            }

            if (h.y > J_HEIGHT) hazards.splice(i, 1);
        }

        // Draw Platforms & Collision
        platforms.forEach((p, i) => {
            if (p.type === 'moving') {
                p.x += p.v;
                if (p.x < 0 || p.x > J_WIDTH - p.w) p.v *= -1;
            }

            // Draw Platform
            jumperCtx.save();
            jumperCtx.fillStyle = p.type === 'moving' ? '#00f6ff' : '#a333ff';
            jumperCtx.shadowBlur = 10;
            jumperCtx.shadowColor = jumperCtx.fillStyle;
            jumperCtx.fillRect(p.x, p.y, p.w, p.h);
            jumperCtx.restore();

            // Collision (only when falling)
            if (jumperPlayer.vy > 0 &&
                jumperPlayer.x + jumperPlayer.w > p.x &&
                jumperPlayer.x < p.x + p.w &&
                jumperPlayer.y + jumperPlayer.h > p.y &&
                jumperPlayer.y + jumperPlayer.h < p.y + p.h + 10) {

                jumperPlayer.vy = J_BOUNCE;
                document.getElementById('jumperBounce').currentTime = 0;
                document.getElementById('jumperBounce').play().catch(() => {});
            }

            // Remove off-screen
            if (p.y > J_HEIGHT) {
                platforms.splice(i, 1);
                platforms.push(spawnJumperPlatform(platforms[platforms.length - 1].y - 50, Math.random() > 0.85 ? 'moving' : 'normal'));
            }
        });

        // Draw Player with Emotions
        drawJumperPlayer(jumperPlayer.x, jumperPlayer.y, jumperPlayer.emotion);

        // Game Over
        if (jumperPlayer.y > J_HEIGHT + 100) {
            gameOverJumper();
        }

        requestAnimationFrame(updateJumper);
    }

    function drawJumperPlayer(x, y, emotion) {
        jumperCtx.save();
        jumperCtx.translate(x + 15, y + 15);

        // Body (Glowy Cube)
        jumperCtx.fillStyle = '#fff';
        jumperCtx.shadowBlur = 20;
        jumperCtx.shadowColor = '#fff';
        jumperCtx.fillRect(-15, -15, 30, 30);

        // Face
        jumperCtx.shadowBlur = 0;
        jumperCtx.fillStyle = '#000';

        if (emotion === 'happy') {
            // Beaming eyes
            jumperCtx.fillRect(-8, -8, 4, 4);
            jumperCtx.fillRect(4, -8, 4, 4);
            // Smile
            jumperCtx.beginPath();
            jumperCtx.arc(0, 2, 6, 0, Math.PI);
            jumperCtx.stroke();
        } else if (emotion === 'focused') {
            // Angry/Concentrated eyes
            jumperCtx.beginPath();
            jumperCtx.moveTo(-10, -10);
            jumperCtx.lineTo(-4, -6);
            jumperCtx.moveTo(10, -10);
            jumperCtx.lineTo(4, -6);
            jumperCtx.lineWidth = 2;
            jumperCtx.stroke();
            jumperCtx.fillRect(-7, -5, 4, 4);
            jumperCtx.fillRect(3, -5, 4, 4);
        } else if (emotion === 'shocked') {
            // Wide eyes
            jumperCtx.beginPath();
            jumperCtx.arc(-7, -6, 3, 0, Math.PI * 2);
            jumperCtx.arc(7, -6, 3, 0, Math.PI * 2);
            jumperCtx.fill();
            // Open mouth
            jumperCtx.beginPath();
            jumperCtx.arc(0, 5, 4, 0, Math.PI * 2);
            jumperCtx.fill();
        }

        jumperCtx.restore();
    }

    function drawJumperHazard(h) {
        jumperCtx.save();
        jumperCtx.translate(h.x + h.w / 2, h.y + h.h / 2);

        let pulse = Math.sin(jumperTimer * 0.1) * 5;
        jumperCtx.fillStyle = '#ff0055';
        jumperCtx.shadowBlur = 15;
        jumperCtx.shadowColor = '#ff0055';

        // Glitchy Body
        jumperCtx.beginPath();
        jumperCtx.moveTo(-h.w / 2 - pulse, -h.h / 2);
        jumperCtx.lineTo(h.w / 2 + pulse, -h.h / 2);
        jumperCtx.lineTo(h.w / 2, h.h / 2);
        jumperCtx.lineTo(-h.w / 2, h.h / 2);
        jumperCtx.closePath();
        jumperCtx.fill();

        // Eyes
        jumperCtx.fillStyle = '#fff';
        jumperCtx.shadowBlur = 0;
        let eyePos = Math.sin(jumperTimer * 0.05) * 5;
        jumperCtx.fillRect(-10 + eyePos, -5, 4, 4);
        jumperCtx.fillRect(6 + eyePos, -5, 4, 4);

        jumperCtx.restore();
    }

    function gameOverJumper() {
        jumperActive = false;
        document.getElementById('jumperFall').play().catch(() => {});

        if (jumperScore > jumperHighScore) {
            jumperHighScore = jumperScore;
            localStorage.setItem('jumperHighScore', jumperHighScore);
            $('#jumper-hi-score-val').text(jumperHighScore);
        }

        $('#jumper-final-score').text(jumperScore + 'm');
        $('#jumper-gameover-overlay').fadeIn();
    }
</script>