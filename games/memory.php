<!-- Neural Link CSS -->
<style>
    .memory-container {
        max-width: 400px;
        margin: 0 auto;
        background: #000;
        border-radius: 20px;
        overflow: hidden;
        position: relative;
        box-shadow: 0 0 30px rgba(0, 100, 255, 0.2);
        border: 2px solid #222;
        padding-bottom: 20px;
        aspect-ratio: 2/3;
    }

    .memory-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 10px;
        padding: 20px;
        padding-top: 80px;
    }

    .memory-card {
        aspect-ratio: 1/1;
        background: #111;
        border: 1px solid #333;
        border-radius: 12px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: transparent;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        transform-style: preserve-3d;
        position: relative;
    }

    .memory-card.flipped {
        transform: rotateY(180deg);
        background: rgba(0, 100, 255, 0.1);
        border-color: #0064ff;
        color: #fff;
        text-shadow: 0 0 10px rgba(0, 100, 255, 0.5);
    }

    .memory-card.matched {
        background: rgba(0, 255, 65, 0.1);
        border-color: #00ff41;
        color: #00ff41;
        pointer-events: none;
    }

    .memory-card::after {
        content: '🔒';
        position: absolute;
        color: #333;
        font-size: 1rem;
        backface-visibility: hidden;
    }

    .memory-card.flipped::after {
        display: none;
    }

    .memory-ui {
        position: absolute;
        width: 100%;
        top: 20px;
        padding: 0 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        z-index: 5;
        font-family: 'Outfit', sans-serif;
    }

    .memory-timer {
        font-size: 1.2rem;
        color: #0064ff;
        font-weight: bold;
    }

    .memory-moves {
        font-size: 0.9rem;
        color: #888;
    }
</style>

<!-- Neural Link Overlays -->
<div id="memory-start-overlay" class="defender-overlay" style="display: none;">
    <div class="text-center p-4">
        <div class="mb-3" style="font-size: 4rem;">🧠</div>
        <h2 class="text-white mb-2 font-weight-bold">NEURAL LINK</h2>
        <p class="text-white-50 small mb-4">Sync your neural pathways.<br>Match all pairs before the backup fails.</p>

        <div class="mb-4">
            <button class="flap-difficulty-btn active" onclick="setMemoryDiff('Direct', this)">DIRECT LINK</button>
            <button class="flap-difficulty-btn" onclick="setMemoryDiff('Deep', this)">DEEP SYNC</button>
        </div>

        <button class="btn btn-netflix btn-lg px-5 shadow-lg" onclick="startMemoryGame()">ENTER TERMINAL</button>
    </div>
</div>

<div id="memory-gameover-overlay" class="defender-overlay" style="display:none;">
    <h2 id="memory-status-text" class="mb-2 fw-bold">SYNC FAILURE</h2>
    <div class="small text-white-50 mb-1">FINAL SCORE</div>
    <div id="memory-final-score" class="text-white h1 mb-4 fw-bold">0</div>
    <div class="d-grid gap-2 w-75">
        <button class="btn btn-netflix py-3" onclick="startMemoryGame()">RE-SYNC</button>
        <button class="btn btn-outline-light" onclick="showGamesHome();">LOGOUT</button>
    </div>
</div>

<audio id="memoryFlipSnd" src="https://assets.mixkit.co/active_storage/sfx/2571/2571-preview.mp3" preload="auto"></audio>
<audio id="memoryMatchSnd" src="https://assets.mixkit.co/active_storage/sfx/2019/2019-preview.mp3" preload="auto"></audio>

<script>
    let memoryCards = [];
    let memoryFlipped = [];
    let memoryMatchedCount = 0;
    let memoryMoves = 0;
    let memoryTimeLeft = 60;
    let memoryTimer;
    let memoryDiff = 'Direct';
    const memoryIcons = ['📡', '🛡️', '🛰️', '🔋', '💾', '💻', '💡', '🔒', '🔑', '🕹️'];

    function initMemory() {
        const html = `
            <div class="memory-container">
                <div class="memory-ui">
                    <span id="memory-timer-display" class="memory-timer">00s</span>
                    <span id="memory-moves-display" class="memory-moves">MOVES: 0</span>
                </div>
                <div id="memory-grid" class="memory-grid"></div>
            </div>
        `;
        $('#game-container').html(html);
        $('#memory-start-overlay').show();
    }

    function setMemoryDiff(d, btn) {
        memoryDiff = d;
        $('.flap-difficulty-btn').removeClass('active');
        $(btn).addClass('active');
        memoryTimeLeft = (d === 'Deep') ? 45 : 60;
    }

    function startMemoryGame() {
        $('#memory-start-overlay').hide();
        $('#memory-gameover-overlay').hide();
        memoryMoves = 0;
        memoryMatchedCount = 0;
        memoryFlipped = [];
        $('#memory-moves-display').text('MOVES: 0');

        let count = memoryDiff === 'Deep' ? 16 : 12;
        let selectedIcons = memoryIcons.slice(0, count / 2);
        let deck = [...selectedIcons, ...selectedIcons].sort(() => Math.random() - 0.5);

        let html = '';
        deck.forEach((icon, i) => {
            html += `<div class="memory-card" data-icon="${icon}" data-index="${i}" onclick="flipMemoryCard(this)">${icon}</div>`;
        });
        $('#memory-grid').html(html).css('grid-template-columns', memoryDiff === 'Deep' ? 'repeat(4, 1fr)' : 'repeat(3, 1fr)');
        if (memoryDiff === 'Direct') $('#memory-grid').css('grid-template-columns', 'repeat(3, 1fr)');
        else $('#memory-grid').css('grid-template-columns', 'repeat(4, 1fr)');

        startMemoryTimer();
    }

    function startMemoryTimer() {
        clearInterval(memoryTimer);
        let duration = memoryDiff === 'Deep' ? 45 : 60;
        memoryTimer = setInterval(() => {
            duration--;
            $('#memory-timer-display').text(duration + 's');
            if (duration <= 0) {
                clearInterval(memoryTimer);
                gameOverMemory(false);
            }
        }, 1000);
    }

    function flipMemoryCard(card) {
        if ($(card).hasClass('flipped') || $(card).hasClass('matched') || memoryFlipped.length >= 2) return;

        const snd = document.getElementById('memoryFlipSnd');
        snd.currentTime = 0;
        snd.play().catch(() => {});

        $(card).addClass('flipped');
        memoryFlipped.push(card);

        if (memoryFlipped.length === 2) {
            memoryMoves++;
            $('#memory-moves-display').text('MOVES: ' + memoryMoves);

            setTimeout(checkMemoryMatch, 800);
        }
    }

    function checkMemoryMatch() {
        let [c1, c2] = memoryFlipped;
        let icon1 = $(c1).data('icon');
        let icon2 = $(c2).data('icon');

        if (icon1 === icon2) {
            $(c1).addClass('matched');
            $(c2).addClass('matched');
            memoryMatchedCount += 2;
            document.getElementById('memoryMatchSnd').play().catch(() => {});

            let total = memoryDiff === 'Deep' ? 16 : 12;
            if (memoryMatchedCount === total) {
                gameOverMemory(true);
            }
        } else {
            $(c1).removeClass('flipped');
            $(c2).removeClass('flipped');
        }
        memoryFlipped = [];
    }

    function gameOverMemory(win) {
        clearInterval(memoryTimer);
        $('#memory-status-text').text(win ? "SYNC COMPLETE" : "SYNC FAILURE").toggleClass('text-success', win).toggleClass('text-danger', !win);
        $('#memory-final-score').text(win ? (1000 - memoryMoves * 10) : memoryMatchedCount * 50);
        $('#memory-gameover-overlay').fadeIn();
    }
</script>