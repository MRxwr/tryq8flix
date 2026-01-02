<!-- Data Defender CSS -->
<style>
    .defender-container {
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

    #defender-canvas {
        background: #050a05;
        display: block;
        width: 100%;
        height: 100%;
    }

    .defender-ui {
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

    .defender-score {
        font-size: 1.5rem;
        font-weight: 800;
        color: #00ff41;
        text-shadow: 0 0 10px rgba(0, 255, 65, 0.5);
    }

    .defender-waves {
        font-size: 0.9rem;
        color: #888;
    }

    .defender-overlay {
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

<!-- Defender Overlays -->
<div id="defender-start-overlay" class="defender-overlay" style="display: none;">
    <div class="text-center p-4">
        <div class="mb-3" style="font-size: 4rem;">🛡️</div>
        <h2 class="text-white mb-2 font-weight-bold">DATA DEFENDER</h2>
        <p class="text-white-50 small mb-4">Protect the core from viral incursions.<br>Tap/Arrows to slide, Tap/Space to fire.</p>

        <div class="mb-4">
            <button class="flap-difficulty-btn active" onclick="setDefenderDiff('Safe', this)">SAFE MODE</button>
            <button class="flap-difficulty-btn" onclick="setDefenderDiff('Infected', this)">INFECTED</button>
        </div>

        <button class="btn btn-netflix btn-lg px-5 shadow-lg" onclick="startDefenderGame()">BOOT DEFENSES</button>
    </div>
</div>

<div id="defender-gameover-overlay" class="defender-overlay" style="display:none;">
    <h2 class="text-danger mb-2 fw-bold">CORE COMPROMISED</h2>
    <div class="small text-white-50 mb-1">TOTAL DELETIONS</div>
    <div id="defender-final-score" class="text-white h1 mb-4 fw-bold">0</div>
    <div class="d-grid gap-2 w-75">
        <button class="btn btn-netflix py-3" onclick="startDefenderGame()">RE-BOOT</button>
        <button class="btn btn-outline-light" onclick="defenderActive = false; showGamesHome();">SHUTDOWN</button>
    </div>
</div>

<!-- Defender Sounds -->
<audio id="defenderShootSnd" src="https://assets.mixkit.co/active_storage/sfx/2571/2571-preview.mp3" preload="auto"></audio>
<audio id="defenderExplodeSnd" src="https://assets.mixkit.co/active_storage/sfx/21/21-preview.mp3" preload="auto"></audio>

<script>
    let defenderCanvas, defenderCtx;
    let defenderActive = false;
    let defenderScoreVal = 0;
    let defenderWave = 1;
    let defenderDiff = 'Safe';

    // Game Configuration
    const DF_WIDTH = 400;
    const DF_HEIGHT = 600;

    let defenderShip = {
        x: 180,
        y: 540,
        w: 40,
        h: 30,
        targetX: 180
    };
    let defenderBullets = [];
    let defenderEnemies = [];
    let defenderEnemyBullets = [];
    let defenderFrame = 0;
    let defenderEnemyDir = 1;
    let defenderEnemyStepY = false;

    function initDefender() {
        const html = `
            <div class="defender-container">
                <div class="defender-ui">
                    <span id="defender-wave-display" class="defender-waves">WAVE 01</span>
                    <span id="defender-score-display" class="defender-score">0</span>
                </div>
                <canvas id="defender-canvas" width="400" height="600"></canvas>
            </div>
        `;
        $('#game-container').html(html);
        defenderCanvas = document.getElementById('defender-canvas');
        defenderCtx = defenderCanvas.getContext('2d');

        $('#defender-start-overlay').show();
        setupDefenderControls();
    }

    function setDefenderDiff(d, btn) {
        defenderDiff = d;
        $('.flap-difficulty-btn').removeClass('active');
        $(btn).addClass('active');
    }

    function setupDefenderControls() {
        const fire = () => {
            if (!defenderActive || defenderBullets.length > 5) return;
            defenderBullets.push({
                x: defenderShip.x + defenderShip.w / 2 - 2,
                y: defenderShip.y,
                w: 4,
                h: 12
            });
            let s = document.getElementById('defenderShootSnd');
            s.currentTime = 0;
            s.play().catch(() => {});
        };

        document.addEventListener('keydown', e => {
            if (e.code === 'ArrowLeft') defenderShip.targetX -= 25;
            if (e.code === 'ArrowRight') defenderShip.targetX += 25;
            if (e.code === 'Space') fire();
        });

        defenderCanvas.addEventListener('touchstart', e => {
            e.preventDefault();
            let tx = e.touches[0].clientX - defenderCanvas.getBoundingClientRect().left;
            defenderShip.targetX = tx - defenderShip.w / 2;
            fire();
        }, {
            passive: false
        });
    }

    function spawnDefenderEnemies() {
        defenderEnemies = [];
        let rows = 4 + Math.min(defenderWave, 3);
        let cols = 6;
        for (let r = 0; r < rows; r++) {
            for (let c = 0; c < cols; c++) {
                defenderEnemies.push({
                    x: 50 + c * 50,
                    y: 80 + r * 40,
                    w: 30,
                    h: 24,
                    type: r < 2 ? 'cmd' : (r < 4 ? 'virus' : 'glitch')
                });
            }
        }
    }

    function startDefenderGame() {
        $('#defender-start-overlay').hide();
        $('#defender-gameover-overlay').hide();
        defenderActive = true;
        defenderScoreVal = 0;
        defenderWave = 1;
        defenderBullets = [];
        defenderEnemyBullets = [];
        defenderShip.x = 180;
        defenderShip.targetX = 180;
        $('#defender-wave-display').text('WAVE 01');
        spawnDefenderEnemies();
        updateDefender();
    }

    function updateDefender() {
        if (!defenderActive) return;
        defenderFrame++;
        defenderCtx.clearRect(0, 0, DF_WIDTH, DF_HEIGHT);

        // Background matrix-like fall
        defenderCtx.fillStyle = 'rgba(0, 255, 65, 0.05)';
        for (let i = 0; i < 10; i++) {
            defenderCtx.fillRect(Math.random() * DF_WIDTH, Math.random() * DF_HEIGHT, 2, 10);
        }

        // Ship Movement
        defenderShip.x += (defenderShip.targetX - defenderShip.x) * 0.2;
        defenderShip.x = Math.max(0, Math.min(DF_WIDTH - defenderShip.w, defenderShip.x));

        // Draw Ship
        defenderCtx.save();
        defenderCtx.fillStyle = '#00ff41';
        defenderCtx.shadowBlur = 15;
        defenderCtx.shadowColor = '#00ff41';
        defenderCtx.beginPath();
        defenderCtx.moveTo(defenderShip.x + defenderShip.w / 2, defenderShip.y);
        defenderCtx.lineTo(defenderShip.x, defenderShip.y + defenderShip.h);
        defenderCtx.lineTo(defenderShip.x + defenderShip.w, defenderShip.y + defenderShip.h);
        defenderCtx.fill();
        defenderCtx.restore();

        // Player Bullets
        for (let i = defenderBullets.length - 1; i >= 0; i--) {
            let b = defenderBullets[i];
            b.y -= 7;
            defenderCtx.fillStyle = '#fff';
            defenderCtx.fillRect(b.x, b.y, b.w, b.h);

            if (b.y < 0) defenderBullets.splice(i, 1);
        }

        // Enemies
        let mostRight = 0;
        let mostLeft = DF_WIDTH;
        let reachedBottom = false;

        defenderEnemies.forEach(e => {
            if (e.x + e.w > mostRight) mostRight = e.x + e.w;
            if (e.x < mostLeft) mostLeft = e.x;
            if (e.y + e.h > defenderShip.y) reachedBottom = true;
        });

        if (mostRight > DF_WIDTH - 20 || mostLeft < 20) {
            defenderEnemyDir *= -1;
            defenderEnemyStepY = true;
        }

        let speed = 0.5 + (defenderWave * 0.2) + (defenderDiff === 'Infected' ? 0.5 : 0);

        for (let i = defenderEnemies.length - 1; i >= 0; i--) {
            let e = defenderEnemies[i];
            e.x += defenderEnemyDir * speed;
            if (defenderEnemyStepY) e.y += 10;

            // Draw Enemy
            drawDefenderEnemy(e);

            // Collision with bullet
            for (let j = defenderBullets.length - 1; j >= 0; j--) {
                let b = defenderBullets[j];
                if (b.x > e.x && b.x < e.x + e.w && b.y > e.y && b.y < e.y + e.h) {
                    defenderEnemies.splice(i, 1);
                    defenderBullets.splice(j, 1);
                    defenderScoreVal += 50;
                    $('#defender-score-display').text(defenderScoreVal);
                    document.getElementById('defenderExplodeSnd').play().catch(() => {});
                    break;
                }
            }

            // Enemy Shooting
            if (Math.random() < 0.001 * defenderWave) {
                defenderEnemyBullets.push({
                    x: e.x + e.w / 2,
                    y: e.y + e.h,
                    w: 3,
                    h: 10
                });
            }
        }
        defenderEnemyStepY = false;

        // Enemy Bullets
        for (let i = defenderEnemyBullets.length - 1; i >= 0; i--) {
            let b = defenderEnemyBullets[i];
            b.y += 4;
            defenderCtx.fillStyle = '#ff3366';
            defenderCtx.fillRect(b.x, b.y, b.w, b.h);

            if (b.x > defenderShip.x && b.x < defenderShip.x + defenderShip.w && b.y > defenderShip.y && b.y < defenderShip.y + defenderShip.h) {
                gameOverDefender();
                return;
            }
            if (b.y > DF_HEIGHT) defenderEnemyBullets.splice(i, 1);
        }

        if (reachedBottom) {
            gameOverDefender();
            return;
        }
        if (defenderEnemies.length === 0) {
            defenderWave++;
            $('#defender-wave-display').text('WAVE ' + String(defenderWave).padStart(2, '0'));
            spawnDefenderEnemies();
        }

        requestAnimationFrame(updateDefender);
    }

    function drawDefenderEnemy(e) {
        defenderCtx.save();
        defenderCtx.translate(e.x + e.w / 2, e.y + e.h / 2);
        let pulse = Math.sin(defenderFrame * 0.1) * 2;

        defenderCtx.fillStyle = e.type === 'cmd' ? '#ffcc00' : (e.type === 'virus' ? '#ff3366' : '#00f6ff');
        defenderCtx.shadowBlur = 10;
        defenderCtx.shadowColor = defenderCtx.fillStyle;

        // Retro Alien Shape
        defenderCtx.fillRect(-12 - pulse, -8, 24 + pulse * 2, 16);
        defenderCtx.fillRect(-15, 0, 30, 8); // Tentacles

        // Eyes
        defenderCtx.fillStyle = '#000';
        defenderCtx.shadowBlur = 0;
        defenderCtx.fillRect(-8, -4, 4, 4);
        defenderCtx.fillRect(4, -4, 4, 4);

        defenderCtx.restore();
    }

    function gameOverDefender() {
        defenderActive = false;
        $('#defender-final-score').text(defenderScoreVal);
        $('#defender-gameover-overlay').fadeIn();
    }
</script>