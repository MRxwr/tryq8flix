<!-- Bug Smasher CSS -->
<style>
    .smasher-container {
        max-width: 400px;
        margin: 0 auto;
        background: #000;
        border-radius: 20px;
        overflow: hidden;
        position: relative;
        box-shadow: 0 0 30px rgba(255, 204, 0, 0.2);
        border: 2px solid #222;
        padding-bottom: 20px;
        aspect-ratio: 2/3;
    }

    .smasher-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 15px;
        padding: 20px;
        padding-top: 80px;
    }

    .smasher-port {
        aspect-ratio: 1/1;
        background: radial-gradient(circle at center, #111, #000);
        border: 2px solid #222;
        border-radius: 50%;
        position: relative;
        overflow: hidden;
    }

    .smasher-bug {
        position: absolute;
        width: 80%;
        height: 80%;
        left: 10%;
        bottom: -100%;
        font-size: 3rem;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: bottom 0.1s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        cursor: pointer;
    }

    .smasher-bug.active {
        bottom: 10%;
    }

    .smasher-bug.smashed {
        transform: scale(1.5);
        opacity: 0;
    }

    .smasher-ui {
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

    .smasher-score {
        font-size: 1.5rem;
        color: #ffcc00;
        font-weight: bold;
    }

    .smasher-time {
        font-size: 1.2rem;
        color: #fff;
    }
</style>

<!-- Bug Smasher Overlays -->
<div id="smasher-start-overlay" class="defender-overlay" style="display: none;">
    <div class="text-center p-4">
        <div class="mb-3" style="font-size: 4rem;">🐜</div>
        <h2 class="text-white mb-2 font-weight-bold">BUG SMASHER</h2>
        <p class="text-white-50 small mb-4">Eliminate the glitches in the server ports.<br>Tap the bugs before they vanish.</p>

        <div class="mb-4">
            <button class="flap-difficulty-btn active" onclick="setSmasherDiff('Maintenance', this)">MAINTENANCE</button>
            <button class="flap-difficulty-btn" onclick="setSmasherDiff('Critical', this)">CRITICAL</button>
        </div>

        <button class="btn btn-netflix btn-lg px-5 shadow-lg" onclick="startSmasherGame()">START DEBUGGING</button>
    </div>
</div>

<div id="smasher-gameover-overlay" class="defender-overlay" style="display:none;">
    <h2 class="text-danger mb-2 fw-bold">SYSTEM OVERLOAD</h2>
    <div class="small text-white-50 mb-1">BUGS DELETED</div>
    <div id="smasher-final-score" class="text-white h1 mb-4 fw-bold">0</div>
    <div class="d-grid gap-2 w-75">
        <button class="btn btn-netflix py-3" onclick="startSmasherGame()">RUN FIX</button>
        <button class="btn btn-outline-light" onclick="showGamesHome();">EXIT ROOM</button>
    </div>
</div>

<audio id="smasherHitSnd" src="https://assets.mixkit.co/active_storage/sfx/2571/2571-preview.mp3" preload="auto"></audio>

<script>
    let smasherScoreVal = 0;
    let smasherTimeLeft = 30;
    let smasherTimer;
    let smasherBugTimer;
    let smasherDiff = 'Maintenance';
    const bugs = ['🐜', '🐛', '🦗', '🕷️', '🦟'];

    function initSmasher() {
        const html = `
            <div class="smasher-container">
                <div class="smasher-ui">
                    <span id="smasher-time-display" class="smasher-time">30s</span>
                    <span id="smasher-score-display" class="smasher-score">0</span>
                </div>
                <div id="smasher-grid" class="smasher-grid">
                    ${Array(9).fill(0).map((_, i) => `<div class="smasher-port"><div class="smasher-bug" id="bug-${i}" onclick="smashBug(${i})">🐛</div></div>`).join('')}
                </div>
            </div>
        `;
        $('#game-container').html(html);
        $('#smasher-start-overlay').show();
    }

    function setSmasherDiff(d, btn) {
        smasherDiff = d;
        $('.flap-difficulty-btn').removeClass('active');
        $(btn).addClass('active');
    }

    function startSmasherGame() {
        $('#smasher-start-overlay').hide();
        $('#smasher-gameover-overlay').hide();
        smasherScoreVal = 0;
        smasherTimeLeft = 30;
        $('#smasher-score-display').text('0');
        $('#smasher-time-display').text('30s');

        $('.smasher-bug').removeClass('active smashed');

        clearInterval(smasherTimer);
        clearInterval(smasherBugTimer);

        smasherTimer = setInterval(() => {
            smasherTimeLeft--;
            $('#smasher-time-display').text(smasherTimeLeft + 's');
            if (smasherTimeLeft <= 0) gameOverSmasher();
        }, 1000);

        popBug();
    }

    function popBug() {
        if (smasherTimeLeft <= 0) return;

        let idx = Math.floor(Math.random() * 9);
        let bugEl = $(`#bug-${idx}`);
        let bugType = bugs[Math.floor(Math.random() * bugs.length)];

        bugEl.text(bugType).removeClass('smashed').addClass('active');

        let showTime = (smasherDiff === 'Critical' ? 600 : 1000) - (smasherScoreVal * 2);
        showTime = Math.max(400, showTime);

        setTimeout(() => {
            bugEl.removeClass('active');
            if (smasherTimeLeft > 0) {
                let delay = (smasherDiff === 'Critical' ? 400 : 800) - (smasherScoreVal);
                delay = Math.max(200, delay);
                setTimeout(popBug, delay);
            }
        }, showTime);
    }

    function smashBug(idx) {
        let bugEl = $(`#bug-${idx}`);
        if (!bugEl.hasClass('active') || bugEl.hasClass('smashed')) return;

        bugEl.addClass('smashed');
        smasherScoreVal += 10;
        $('#smasher-score-display').text(smasherScoreVal);

        let s = document.getElementById('smasherHitSnd');
        s.currentTime = 0;
        s.play().catch(() => {});

        // Particle effect (flash)
        bugEl.parent().css('border-color', '#ffcc00');
        setTimeout(() => bugEl.parent().css('border-color', '#222'), 100);
    }

    function gameOverSmasher() {
        clearInterval(smasherTimer);
        clearInterval(smasherBugTimer);
        $('.smasher-bug').removeClass('active');
        $('#smasher-final-score').text(smasherScoreVal);
        $('#smasher-gameover-overlay').fadeIn();
    }
</script>