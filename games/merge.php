<!-- Cyber Merge CSS -->
<style>
    .merge-container {
        max-width: 400px;
        margin: 0 auto;
        background: #000;
        border-radius: 20px;
        overflow: hidden;
        position: relative;
        box-shadow: 0 0 30px rgba(163, 51, 255, 0.2);
        border: 2px solid #222;
        padding: 20px;
        aspect-ratio: 2/3;
    }

    .merge-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 10px;
        background: #111;
        padding: 10px;
        border-radius: 12px;
        margin-top: 60px;
    }

    .merge-tile {
        aspect-ratio: 1/1;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 1.2rem;
        transition: all 0.1s;
        transform: scale(0);
        animation: tileAppear 0.2s forwards;
    }

    @keyframes tileAppear {
        to {
            transform: scale(1);
        }
    }

    .tile-2 {
        background: #1a1a1a;
        color: #888;
    }

    .tile-4 {
        background: #222;
        color: #aaa;
    }

    .tile-8 {
        background: #a333ff;
        color: #fff;
        box-shadow: 0 0 10px rgba(163, 51, 255, 0.4);
    }

    .tile-16 {
        background: #8a2be2;
        color: #fff;
        box-shadow: 0 0 15px rgba(138, 43, 226, 0.5);
    }

    .tile-32 {
        background: #ff00ff;
        color: #fff;
        box-shadow: 0 0 20px rgba(255, 0, 255, 0.6);
    }

    .tile-64 {
        background: #ff007f;
        color: #fff;
    }

    .tile-128 {
        background: #ff3366;
        color: #fff;
        font-size: 1rem;
    }

    .tile-256 {
        background: #00f6ff;
        color: #000;
        font-size: 1rem;
    }

    .tile-512 {
        background: #00ff41;
        color: #000;
        font-size: 1rem;
    }

    .tile-1024 {
        background: #ffcc00;
        color: #000;
        font-size: 0.8rem;
    }

    .tile-2048 {
        background: #fff;
        color: #000;
        font-size: 0.8rem;
        box-shadow: 0 0 30px #fff;
    }

    .merge-ui {
        position: absolute;
        width: 100%;
        top: 20px;
        left: 0;
        padding: 0 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        z-index: 5;
        font-family: 'Outfit', sans-serif;
    }

    .merge-score {
        font-size: 1.5rem;
        color: #a333ff;
        font-weight: bold;
    }
</style>

<!-- Merge Overlays -->
<div id="merge-start-overlay" class="defender-overlay" style="display: none;">
    <div class="text-center p-4">
        <div class="mb-3" style="font-size: 4rem;">🧩</div>
        <h2 class="text-white mb-2 font-weight-bold">CYBER MERGE</h2>
        <p class="text-white-50 small mb-4">Fuse data blocks to reach 2048.<br>Use Arrows or Swipe.</p>
        <button class="btn btn-netflix btn-lg px-5 shadow-lg" onclick="startMergeGame()">INITIALIZE GRID</button>
    </div>
</div>

<div id="merge-gameover-overlay" class="defender-overlay" style="display:none;">
    <h2 class="text-danger mb-2 fw-bold">GRID LOCKED</h2>
    <div class="small text-white-50 mb-1">TOTAL DATA ENERGY</div>
    <div id="merge-final-score" class="text-white h1 mb-4 fw-bold">0</div>
    <div class="d-grid gap-2 w-75">
        <button class="btn btn-netflix py-3" onclick="startMergeGame()">RE-INIT</button>
        <button class="btn btn-outline-light" onclick="showGamesHome();">LOGOUT</button>
    </div>
</div>

<audio id="mergeSnd" src="https://assets.mixkit.co/active_storage/sfx/2571/2571-preview.mp3" preload="auto"></audio>

<script>
    let mergeGrid = [];
    let mergeScoreVal = 0;

    function initMerge() {
        const html = `
            <div class="merge-container">
                <div class="merge-ui">
                    <span id="merge-score-display" class="merge-score">0</span>
                    <span class="text-white-50 small">GOAL: 2048</span>
                </div>
                <div id="merge-grid" class="merge-grid"></div>
            </div>
        `;
        $('#game-container').html(html);
        $('#merge-start-overlay').show();
        setupMergeControls();
    }

    function setupMergeControls() {
        document.addEventListener('keydown', e => {
            if (!$('#merge-grid').length) return;
            if (e.key === 'ArrowUp') mergeMove('up');
            if (e.key === 'ArrowDown') mergeMove('down');
            if (e.key === 'ArrowLeft') mergeMove('left');
            if (e.key === 'ArrowRight') mergeMove('right');
        });

        let tsX, tsY;
        document.getElementById('game-container').addEventListener('touchstart', e => {
            tsX = e.touches[0].clientX;
            tsY = e.touches[0].clientY;
        });
        document.getElementById('game-container').addEventListener('touchend', e => {
            let dx = e.changedTouches[0].clientX - tsX;
            let dy = e.changedTouches[0].clientY - tsY;
            if (Math.abs(dx) > Math.abs(dy)) {
                if (dx > 30) mergeMove('right');
                else if (dx < -30) mergeMove('left');
            } else {
                if (dy > 30) mergeMove('down');
                else if (dy < -30) mergeMove('up');
            }
        });
    }

    function startMergeGame() {
        $('#merge-start-overlay').hide();
        $('#merge-gameover-overlay').hide();
        mergeScoreVal = 0;
        $('#merge-score-display').text('0');
        mergeGrid = Array(16).fill(0);
        addMergeTile();
        addMergeTile();
        renderMergeGrid();
    }

    function addMergeTile() {
        let empty = mergeGrid.map((v, i) => v === 0 ? i : null).filter(v => v !== null);
        if (empty.length > 0) {
            let idx = empty[Math.floor(Math.random() * empty.length)];
            mergeGrid[idx] = Math.random() < 0.9 ? 2 : 4;
        }
    }

    function renderMergeGrid() {
        let html = '';
        mergeGrid.forEach(v => {
            html += `<div class="merge-tile ${v ? 'tile-'+v : ''}">${v || ''}</div>`;
        });
        $('#merge-grid').html(html);
    }

    function mergeMove(dir) {
        let moved = false;
        let grid = [...mergeGrid];

        for (let i = 0; i < 4; i++) {
            let line = [];
            for (let j = 0; j < 4; j++) {
                let idx;
                if (dir === 'left') idx = i * 4 + j;
                else if (dir === 'right') idx = i * 4 + (3 - j);
                else if (dir === 'up') idx = j * 4 + i;
                else idx = (3 - j) * 4 + i;
                if (grid[idx] !== 0) line.push(grid[idx]);
            }

            let mergedLine = [];
            for (let j = 0; j < line.length; j++) {
                if (line[j] === line[j + 1]) {
                    mergedLine.push(line[j] * 2);
                    mergeScoreVal += line[j] * 2;
                    j++;
                    moved = true;
                } else mergedLine.push(line[j]);
            }
            while (mergedLine.length < 4) mergedLine.push(0);

            for (let j = 0; j < 4; j++) {
                let idx;
                if (dir === 'left') idx = i * 4 + j;
                else if (dir === 'right') idx = i * 4 + (3 - j);
                else if (dir === 'up') idx = j * 4 + i;
                else idx = (3 - j) * 4 + i;
                if (grid[idx] !== mergedLine[j]) moved = true;
                grid[idx] = mergedLine[j];
            }
        }

        if (moved) {
            mergeGrid = grid;
            addMergeTile();
            renderMergeGrid();
            $('#merge-score-display').text(mergeScoreVal);
            document.getElementById('mergeSnd').play().catch(() => {});

            if (!canMergeMove()) gameOverMerge();
        }
    }

    function canMergeMove() {
        if (mergeGrid.includes(0)) return true;
        for (let i = 0; i < 4; i++) {
            for (let j = 0; j < 4; j++) {
                let v = mergeGrid[i * 4 + j];
                if (j < 3 && v === mergeGrid[i * 4 + j + 1]) return true;
                if (i < 3 && v === mergeGrid[(i + 1) * 4 + j]) return true;
            }
        }
        return false;
    }

    function gameOverMerge() {
        $('#merge-final-score').text(mergeScoreVal);
        $('#merge-gameover-overlay').fadeIn();
    }
</script>