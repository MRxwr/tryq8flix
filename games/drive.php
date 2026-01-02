<!-- Neon Highway CSS -->
<style>
    .drive-container {
        max-width: 400px;
        margin: 0 auto;
        background: #000;
        border-radius: 20px;
        overflow: hidden;
        position: relative;
        box-shadow: 0 0 30px rgba(0, 246, 255, 0.2);
        border: 2px solid #222;
        user-select: none;
        touch-action: none;
        aspect-ratio: 2/3;
    }

    #drive-canvas {
        background: #050510;
        display: block;
        width: 100%;
        height: 100%;
    }

    .drive-ui {
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

    .drive-score {
        font-size: 1.8rem;
        font-weight: 800;
        color: #00f6ff;
        text-shadow: 0 0 10px rgba(0, 246, 255, 0.5);
    }

    .drive-timer {
        font-size: 0.9rem;
        color: #888;
    }

    .drive-overlay {
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

<!-- Drive Overlays -->
<div id="drive-start-overlay" class="drive-overlay" style="display: none;">
    <div class="text-center p-4">
        <div class="mb-3" style="font-size: 4rem;">🏎️</div>
        <h2 class="text-white mb-2 font-weight-bold">NEON HIGHWAY</h2>
        <p class="text-white-50 small mb-4">Dodge the junk on the 3-lane data road.<br>Arrows or Tap lanes to drive.</p>

        <div class="mb-4">
            <button class="flap-difficulty-btn active" onclick="setDriveDiff('Easy', this)">CRUISE</button>
            <button class="flap-difficulty-btn" onclick="setDriveDiff('Turbo', this)">TURBO</button>
        </div>

        <button class="btn btn-netflix btn-lg px-5 shadow-lg" onclick="startDriveGame()">IGNITION</button>
    </div>
</div>

<div id="drive-gameover-overlay" class="drive-overlay" style="display:none;">
    <h2 class="text-danger mb-2 fw-bold">TOTAL WRECK</h2>
    <div class="small text-white-50 mb-1">DISTANCE / TIME</div>
    <div class="d-flex align-items-center mb-4">
        <span id="drive-final-score" class="text-white h1 mb-0 fw-bold me-3">0km</span>
        <span id="drive-final-time" class="text-white-50 h4 mb-0">0s</span>
    </div>
    <div class="d-grid gap-2 w-75">
        <button class="btn btn-netflix py-3" onclick="startDriveGame()">RE-BUILD</button>
        <button class="btn btn-outline-light" onclick="driveActive = false; showGamesHome();">GARAGE EXIT</button>
    </div>
</div>

<audio id="driveSnd" src="https://assets.mixkit.co/active_storage/sfx/2571/2571-preview.mp3" preload="auto"></audio>
<audio id="driveCrash" src="https://assets.mixkit.co/active_storage/sfx/21/21-preview.mp3" preload="auto"></audio>

<script>
    let driveCanvas, driveCtx;
    let driveActive = false;
    let driveScore = 0;
    let driveTime = 0;
    let driveDiff = 'Easy';
    let driveStartTime;

    // Road Config
    const R_WIDTH = 400;
    const R_HEIGHT = 600;
    const LANE_WIDTH = R_WIDTH / 3;
    let R_SPEED = 5;

    let car = {
        lane: 1,
        x: LANE_WIDTH + LANE_WIDTH / 2,
        y: 500,
        w: 50,
        h: 80,
        targetX: LANE_WIDTH + LANE_WIDTH / 2,
        emotion: 'happy'
    };
    let driveObstacles = [];
    let driveFrameCount = 0;

    function initDrive() {
        const html = `
            <div class="drive-container">
                <div class="drive-ui">
                    <span id="drive-timer-val" class="drive-timer">00:00</span>
                    <span id="drive-score-val" class="drive-score">0km</span>
                </div>
                <canvas id="drive-canvas" width="400" height="600"></canvas>
            </div>
        `;
        $('#game-container').html(html);
        driveCanvas = document.getElementById('drive-canvas');
        driveCtx = driveCanvas.getContext('2d');

        $('#drive-start-overlay').show();
        setupDriveControls();
    }

    function setDriveDiff(d, btn) {
        driveDiff = d;
        $('.flap-difficulty-btn').removeClass('active');
        $(btn).addClass('active');
        R_SPEED = (d === 'Turbo') ? 8 : 5;
    }

    function setupDriveControls() {
        document.addEventListener('keydown', e => {
            if (!driveActive) return;
            if (e.code === 'ArrowLeft' && car.lane > 0) moveCar(car.lane - 1);
            if (e.code === 'ArrowRight' && car.lane < 2) moveCar(car.lane + 1);
        });
        driveCanvas.addEventListener('touchstart', e => {
            if (!driveActive) return;
            let tx = e.touches[0].clientX - driveCanvas.getBoundingClientRect().left;
            let l = Math.floor(tx / (driveCanvas.clientWidth / 3));
            moveCar(l);
        });
    }

    function moveCar(l) {
        car.lane = l;
        car.targetX = l * LANE_WIDTH + LANE_WIDTH / 2;
        let s = document.getElementById('driveSnd');
        s.currentTime = 0;
        s.play().catch(() => {});
    }

    function startDriveGame() {
        $('#drive-start-overlay').hide();
        $('#drive-gameover-overlay').hide();
        driveActive = true;
        driveScore = 0;
        driveTime = 0;
        driveFrameCount = 0;
        driveStartTime = Date.now();
        car.lane = 1;
        car.x = LANE_WIDTH + LANE_WIDTH / 2;
        car.targetX = car.x;
        driveObstacles = [];
        updateDrive();
    }

    function updateDrive() {
        if (!driveActive) return;
        driveFrameCount++;
        driveCtx.clearRect(0, 0, R_WIDTH, R_HEIGHT);

        // Timer
        let elapsed = Math.floor((Date.now() - driveStartTime) / 1000);
        $('#drive-timer-val').text(String(Math.floor(elapsed / 60)).padStart(2, '0') + ':' + String(elapsed % 60).padStart(2, '0'));
        driveTime = elapsed;

        // Draw Road Marks
        driveCtx.strokeStyle = 'rgba(255,255,255,0.1)';
        driveCtx.setLineDash([20, 20]);
        driveCtx.lineDashOffset = -driveFrameCount * R_SPEED;
        driveCtx.beginPath();
        driveCtx.moveTo(LANE_WIDTH, 0);
        driveCtx.lineTo(LANE_WIDTH, R_HEIGHT);
        driveCtx.moveTo(LANE_WIDTH * 2, 0);
        driveCtx.lineTo(LANE_WIDTH * 2, R_HEIGHT);
        driveCtx.stroke();
        driveCtx.setLineDash([]);

        // Car Movement Smooth
        car.x += (car.targetX - car.x) * 0.2;

        // Obstacles
        if (driveFrameCount % (driveDiff === 'Turbo' ? 40 : 60) === 0) {
            let l = Math.floor(Math.random() * 3);
            let types = ['trash', 'dumpster', 'broken_car'];
            let type = types[Math.floor(Math.random() * types.length)];
            driveObstacles.push({
                x: l * LANE_WIDTH + LANE_WIDTH / 2,
                y: -100,
                type,
                w: 60,
                h: type === 'broken_car' ? 90 : 50
            });
        }

        for (let i = driveObstacles.length - 1; i >= 0; i--) {
            let o = driveObstacles[i];
            o.y += R_SPEED;

            drawObstacle(o);

            // Collision
            if (Math.abs(car.x - o.x) < 40 && Math.abs(car.y - o.y) < 60) {
                gameOverDrive();
            }

            if (o.y > R_HEIGHT + 100) driveObstacles.splice(i, 1);
        }

        // Scoring
        driveScore += R_SPEED * 0.01;
        $('#drive-score-val').text(driveScore.toFixed(1) + 'km');

        // Emotion
        let dangerFound = driveObstacles.some(o => Math.abs(o.y - car.y) < 150 && Math.abs(o.x - car.x) < 40);
        car.emotion = dangerFound ? 'shocked' : 'focused';

        drawCar(car.x, car.y, car.emotion);
        requestAnimationFrame(updateDrive);
    }

    function drawObstacle(o) {
        driveCtx.save();
        driveCtx.translate(o.x, o.y);
        if (o.type === 'broken_car') {
            driveCtx.fillStyle = '#444';
            driveCtx.fillRect(-20, -40, 40, 80);
            driveCtx.fillStyle = '#ff0000'; // Hazards
            if (driveFrameCount % 20 < 10) driveCtx.fillRect(-18, 30, 8, 5);
        } else if (o.type === 'trash') {
            driveCtx.fillStyle = '#1a1a1a';
            driveCtx.fillRect(-15, -15, 30, 30);
            driveCtx.strokeStyle = '#555';
            driveCtx.strokeRect(-15, -15, 30, 30);
        } else {
            driveCtx.fillStyle = '#0a2a0a';
            driveCtx.fillRect(-25, -20, 50, 40);
            driveCtx.strokeStyle = '#00ff00';
            driveCtx.strokeRect(-25, -20, 50, 40);
        }
        driveCtx.restore();
    }

    function drawCar(x, y, emotion) {
        driveCtx.save();
        driveCtx.translate(x, y);

        // Body (Cyber Car)
        driveCtx.fillStyle = '#00f6ff';
        driveCtx.shadowBlur = 15;
        driveCtx.shadowColor = '#00f6ff';
        driveCtx.fillRect(-25, -40, 50, 80);

        // Roof
        driveCtx.fillStyle = '#003366';
        driveCtx.fillRect(-18, -15, 36, 40);

        // Lights
        driveCtx.fillStyle = '#fff';
        driveCtx.fillRect(-20, -38, 10, 5);
        driveCtx.fillRect(10, -38, 10, 5);

        // Emotions on windshield
        driveCtx.fillStyle = '#fff';
        driveCtx.shadowBlur = 0;
        if (emotion === 'shocked') {
            driveCtx.beginPath();
            driveCtx.arc(-8, 5, 3, 0, Math.PI * 2);
            driveCtx.arc(8, 5, 3, 0, Math.PI * 2);
            driveCtx.fill();
        } else {
            driveCtx.fillRect(-10, 5, 6, 2);
            driveCtx.fillRect(4, 5, 6, 2);
        }

        driveCtx.restore();
    }

    function gameOverDrive() {
        driveActive = false;
        document.getElementById('driveCrash').play().catch(() => {});
        $('#drive-final-score').text(driveScore.toFixed(1) + 'km');
        $('#drive-final-time').text(driveTime + 's');
        $('#drive-gameover-overlay').fadeIn();
    }
</script>