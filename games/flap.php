<!-- Cyber Flap CSS -->
<style>
    .flap-container {
        max-width: 400px;
        margin: 0 auto;
        background: #000;
        border-radius: 20px;
        overflow: hidden;
        position: relative;
        box-shadow: 0 0 30px rgba(255, 100, 0, 0.2);
        border: 2px solid #222;
        user-select: none;
        touch-action: none;
        aspect-ratio: 2/3;
    }

    #flap-canvas {
        background: #050510;
        display: block;
        width: 100%;
        height: 100%;
    }

    .flap-ui {
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

    .flap-score {
        font-size: 2rem;
        font-weight: 800;
        color: #ff6400;
        text-shadow: 0 0 10px rgba(255, 100, 0, 0.5);
    }

    .flap-timer {
        font-size: 0.9rem;
        color: #888;
    }

    .flap-overlay {
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

    .flap-difficulty-btn {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        color: #fff;
        padding: 10px 20px;
        border-radius: 12px;
        margin: 5px;
        transition: all 0.3s;
    }

    .flap-difficulty-btn.active {
        background: #ff6400;
        border-color: #ff6400;
        box-shadow: 0 0 15px rgba(255, 100, 0, 0.4);
    }
</style>

<!-- Flap Overlays -->
<div id="flap-start-overlay" class="flap-overlay" style="display: none;">
    <div class="text-center p-4">
        <div class="mb-3" style="font-size: 4rem;">🐦</div>
        <h2 class="text-white mb-2 font-weight-bold">CYBER FLAP</h2>
        <p class="text-white-50 small mb-4">Fly through the server pipes.<br>Space / Mirror Tap to flap.</p>

        <div class="mb-4">
            <button class="flap-difficulty-btn active" onclick="setFlapDiff('Easy', this)">EASY</button>
            <button class="flap-difficulty-btn" onclick="setFlapDiff('Pro', this)">PRO</button>
        </div>

        <button class="btn btn-netflix btn-lg px-5 shadow-lg" onclick="startFlapGame()">NITRO START</button>
    </div>
</div>

<div id="flap-gameover-overlay" class="flap-overlay" style="display:none;">
    <h2 class="text-danger mb-2 fw-bold">WAVE COLLAPSE</h2>
    <div class="small text-white-50 mb-1">SCORE / TIME</div>
    <div class="d-flex align-items-center mb-4">
        <span id="flap-final-score" class="text-white h1 mb-0 fw-bold me-3">0</span>
        <span id="flap-final-time" class="text-white-50 h4 mb-0">0s</span>
    </div>
    <div class="d-grid gap-2 w-75">
        <button class="btn btn-netflix py-3" onclick="startFlapGame()">RE-SYNC</button>
        <button class="btn btn-outline-light" onclick="flapActive = false; showGamesHome();">BACK TO HUB</button>
    </div>
</div>

<audio id="flapSnd" src="https://assets.mixkit.co/active_storage/sfx/2571/2571-preview.mp3" preload="auto"></audio>
<audio id="flapPoint" src="https://assets.mixkit.co/active_storage/sfx/2019/2019-preview.mp3" preload="auto"></audio>
<audio id="flapDie" src="https://assets.mixkit.co/active_storage/sfx/21/21-preview.mp3" preload="auto"></audio>

<script>
    let flapCanvas, flapCtx;
    let flapActive = false;
    let flapScore = 0;
    let flapTime = 0;
    let flapDiff = 'Easy';
    let flapStartTime;

    // Game Physics
    const F_WIDTH = 400;
    const F_HEIGHT = 600;
    let F_GRAVITY = 0.25;
    let F_LIFT = -6;
    let F_SPEED = 2.5;
    let F_GAP = 170;

    let bird = {
        x: 50,
        y: 300,
        v: 0,
        w: 34,
        h: 24,
        emotion: 'neutral'
    };
    let pipes = [];
    let flapFrame = 0;

    function initFlap() {
        const html = `
            <div class="flap-container">
                <div class="flap-ui">
                    <span id="flap-timer-val" class="flap-timer">00:00</span>
                    <span id="flap-score-val" class="flap-score">0</span>
                </div>
                <canvas id="flap-canvas" width="400" height="600"></canvas>
            </div>
        `;
        $('#game-container').html(html);
        flapCanvas = document.getElementById('flap-canvas');
        flapCtx = flapCanvas.getContext('2d');

        $('#flap-start-overlay').show();
        setupFlapControls();
    }

    function setFlapDiff(d, btn) {
        flapDiff = d;
        $('.flap-difficulty-btn').removeClass('active');
        $(btn).addClass('active');
        if (d === 'Pro') {
            F_SPEED = 3.5;
            F_GAP = 130;
            F_GRAVITY = 0.35;
        } else {
            F_SPEED = 2.5;
            F_GAP = 170;
            F_GRAVITY = 0.25;
        }
    }

    function setupFlapControls() {
        const trigger = () => {
            if (!flapActive) return;
            bird.v = F_LIFT;
            bird.emotion = 'focused';
            let s = document.getElementById('flapSnd');
            s.currentTime = 0;
            s.play().catch(() => {});
        };
        document.addEventListener('keydown', e => {
            if (e.code === 'Space') trigger();
        });
        flapCanvas.addEventListener('touchstart', e => {
            e.preventDefault();
            trigger();
        }, {
            passive: false
        });
    }

    function startFlapGame() {
        $('#flap-start-overlay').hide();
        $('#flap-gameover-overlay').hide();
        flapActive = true;
        flapScore = 0;
        flapTime = 0;
        flapStartTime = Date.now();
        flapFrame = 0;
        bird.y = 300;
        bird.v = 0;
        pipes = [];
        updateFlap();
    }

    function updateFlap() {
        if (!flapActive) return;
        flapFrame++;
        flapCtx.clearRect(0, 0, F_WIDTH, F_HEIGHT);

        // Timer
        let elapsed = Math.floor((Date.now() - flapStartTime) / 1000);
        let m = String(Math.floor(elapsed / 60)).padStart(2, '0');
        let s = String(elapsed % 60).padStart(2, '0');
        $('#flap-timer-val').text(`${m}:${s}`);
        flapTime = elapsed;

        // Background (Neon Grid)
        flapCtx.strokeStyle = 'rgba(255,100,0,0.1)';
        for (let i = 0; i < F_WIDTH; i += 40) {
            flapCtx.beginPath();
            flapCtx.moveTo(i - (flapFrame % 40), 0);
            flapCtx.lineTo(i - (flapFrame % 40), F_HEIGHT);
            flapCtx.stroke();
        }

        // Bird Logic
        bird.v += F_GRAVITY;
        bird.y += bird.v;

        if (bird.v > 3) bird.emotion = 'shocked';
        else if (bird.v < -1) bird.emotion = 'focused';
        else bird.emotion = 'neutral';

        // Pipes
        if (flapFrame % 100 === 0) {
            let space = F_GAP;
            let top = 50 + Math.random() * (F_HEIGHT - space - 100);
            pipes.push({
                x: F_WIDTH,
                top,
                bottom: top + space,
                w: 50,
                passed: false
            });
        }

        for (let i = pipes.length - 1; i >= 0; i--) {
            let p = pipes[i];
            p.x -= F_SPEED;

            // Draw Pipe
            let grad = flapCtx.createLinearGradient(p.x, 0, p.x + p.w, 0);
            grad.addColorStop(0, '#ff6400');
            grad.addColorStop(1, '#993300');
            flapCtx.fillStyle = grad;
            flapCtx.shadowBlur = 15;
            flapCtx.shadowColor = '#ff6400';
            flapCtx.fillRect(p.x, 0, p.w, p.top);
            flapCtx.fillRect(p.x, p.bottom, p.w, F_HEIGHT - p.bottom);
            flapCtx.shadowBlur = 0;

            // Collision
            if (bird.x + bird.w > p.x && bird.x < p.x + p.w) {
                if (bird.y < p.top || bird.y + bird.h > p.bottom) gameOverFlap();
            }

            // Score
            if (!p.passed && bird.x > p.x + p.w) {
                p.passed = true;
                flapScore++;
                $('#flap-score-val').text(flapScore);
                document.getElementById('flapPoint').play().catch(() => {});
            }

            if (p.x < -100) pipes.splice(i, 1);
        }

        if (bird.y > F_HEIGHT || bird.y < 0) gameOverFlap();

        drawFlappy(bird.x, bird.y, bird.emotion);
        requestAnimationFrame(updateFlap);
    }

    function drawFlappy(x, y, emotion) {
        flapCtx.save();
        flapCtx.translate(x + 17, y + 12);
        let angle = Math.min(Math.PI / 4, Math.max(-Math.PI / 4, bird.v * 0.1));
        flapCtx.rotate(angle);

        // Body
        flapCtx.fillStyle = '#ffcc00';
        flapCtx.shadowBlur = 10;
        flapCtx.shadowColor = '#ffcc00';
        flapCtx.fillRect(-17, -12, 34, 24);

        // Wing
        flapCtx.fillStyle = '#ff9900';
        let flapY = Math.sin(flapFrame * 0.3) * 5;
        flapCtx.fillRect(-10, -2 + flapY, 12, 8);

        // Face
        flapCtx.fillStyle = '#111';
        flapCtx.shadowBlur = 0;
        if (emotion === 'neutral') {
            flapCtx.fillRect(8, -6, 4, 4);
        } else if (emotion === 'focused') {
            flapCtx.beginPath();
            flapCtx.moveTo(6, -8);
            flapCtx.lineTo(14, -6);
            flapCtx.stroke();
            flapCtx.fillRect(8, -5, 4, 4);
        } else if (emotion === 'shocked') {
            flapCtx.beginPath();
            flapCtx.arc(10, -5, 3, 0, Math.PI * 2);
            flapCtx.fill();
            flapCtx.beginPath();
            flapCtx.arc(10, 5, 3, 0, Math.PI * 2);
            flapCtx.fill(); // Open beak/mouth
        }

        // Beak
        flapCtx.fillStyle = '#ff3300';
        flapCtx.fillRect(17, -2, 6, 8);

        flapCtx.restore();
    }

    function gameOverFlap() {
        flapActive = false;
        document.getElementById('flapDie').play().catch(() => {});
        $('#flap-final-score').text(flapScore);
        $('#flap-final-time').text(flapTime + 's');
        $('#flap-gameover-overlay').fadeIn();
    }
</script>