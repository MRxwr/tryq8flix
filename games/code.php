<!-- Code Breaker CSS -->
<style>
    .code-container {
        max-width: 400px;
        margin: 0 auto;
        background: #000;
        border-radius: 20px;
        overflow: hidden;
        position: relative;
        box-shadow: 0 0 30px rgba(0, 255, 65, 0.2);
        border: 2px solid #222;
        padding: 20px;
        aspect-ratio: 2/3;
        display: flex;
        flex-direction: column;
    }

    .code-grid {
        display: grid;
        grid-template-rows: repeat(6, 1fr);
        gap: 8px;
        margin-top: 60px;
        flex-grow: 1;
    }

    .code-row {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 8px;
    }

    .code-tile {
        aspect-ratio: 1/1;
        border: 2px solid #333;
        background: #111;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        font-weight: 800;
        color: #fff;
        text-transform: uppercase;
        border-radius: 8px;
        transition: all 0.3s;
    }

    .code-tile.active {
        border-color: #00ff41;
    }

    .code-tile.correct {
        background: #00ff41;
        border-color: #00ff41;
        color: #000;
    }

    .code-tile.present {
        background: #ffcc00;
        border-color: #ffcc00;
        color: #000;
    }

    .code-tile.absent {
        background: #333;
        border-color: #333;
        color: #888;
    }

    .code-keyboard {
        display: grid;
        grid-template-columns: repeat(10, 1fr);
        gap: 4px;
        margin-top: 20px;
    }

    .key {
        background: #222;
        color: #fff;
        border: none;
        border-radius: 4px;
        padding: 10px 0;
        font-size: 0.8rem;
        cursor: pointer;
        text-transform: uppercase;
    }

    .key.wide {
        grid-column: span 1.5;
        font-size: 0.6rem;
    }

    .key:active {
        background: #444;
    }

    .key.correct {
        background: #00ff41;
        color: #000;
    }

    .key.present {
        background: #ffcc00;
        color: #000;
    }

    .key.absent {
        background: #111;
        color: #555;
    }
</style>

<!-- Code Breaker Overlays -->
<div id="code-start-overlay" class="defender-overlay" style="display: none;">
    <div class="text-center p-4">
        <div class="mb-3" style="font-size: 4rem;">🔓</div>
        <h2 class="text-white mb-2 font-weight-bold">CODE BREAKER</h2>
        <p class="text-white-50 small mb-4">Decrypt the 5-letter security keyword.<br>6 attempts to bypass the terminal.</p>
        <button class="btn btn-netflix btn-lg px-5 shadow-lg" onclick="startCodeGame()">INITIALIZE BYPASS</button>
    </div>
</div>

<div id="code-gameover-overlay" class="defender-overlay" style="display:none;">
    <h2 id="code-status-text" class="text-danger mb-2 fw-bold">ACCESS DENIED</h2>
    <div class="small text-white-50 mb-1">SECURITY KEYWORD</div>
    <div id="code-final-word" class="text-white h2 mb-4 fw-bold">*****</div>
    <div class="d-grid gap-2 w-75">
        <button class="btn btn-netflix py-3" onclick="startCodeGame()">NEW ATTEMPT</button>
        <button class="btn btn-outline-light" onclick="showGamesHome();">SUSPEND SESSION</button>
    </div>
</div>

<script>
    const codeWords = ['LOGIC', 'CYBER', 'MATCH', 'POWER', 'BLOCK', 'CHIPS', 'BREAK', 'START', 'SHIFT', 'INPUT', 'FIELD', 'GUARD', 'ROBOT', 'ALARM', 'INDEX', 'PIXEL', 'PROXY', 'CACHE', 'CLICK', 'LEVEL'];
    let targetWord = '';
    let currentGuess = '';
    let guesses = [];
    let codeGameOver = false;

    function initCode() {
        const html = `
            <div class="code-container">
                <div class="code-grid" id="code-grid">
                    ${Array(6).fill(0).map((_, r) => `
                        <div class="code-row">
                            ${Array(5).fill(0).map((_, c) => `<div class="code-tile" id="tile-${r}-${c}"></div>`).join('')}
                        </div>
                    `).join('')}
                </div>
                <div class="code-keyboard" id="code-kb"></div>
            </div>
        `;
        $('#game-container').html(html);
        renderKeyboard();
        $('#code-start-overlay').show();
        setupCodeControls();
    }

    function renderKeyboard() {
        const layout = [
            'QWERTYUIOP',
            'ASDFGHJKL',
            'ZXCVBNM'
        ];
        let html = '';
        layout.forEach((row, r) => {
            let rowHtml = '<div style="display:flex; justify-content:center; gap:4px; margin-bottom:4px; width:100%;">';
            if (r === 2) rowHtml += '<button class="key wide" onclick="handleCodeKey(\'ENTER\')">ENTER</button>';
            row.split('').forEach(char => {
                html += `<button class="key" id="key-${char}" onclick="handleCodeKey('${char}')">${char}</button>`;
                rowHtml += `<button class="key" id="key-${char}" onclick="handleCodeKey('${char}')">${char}</button>`;
            });
            if (r === 2) rowHtml += '<button class="key wide" onclick="handleCodeKey(\'BACKSPACE\')">DEL</button>';
            rowHtml += '</div>';
            html += rowHtml; // Use the rowHtml for better spacing
        });
        $('#code-kb').html(html).css('display', 'block');
        // Actually lets do a simpler grid for the kb
        let fullHtml = '';
        ['Q', 'W', 'E', 'R', 'T', 'Y', 'U', 'I', 'O', 'P', 'A', 'S', 'D', 'F', 'G', 'H', 'J', 'K', 'L', 'ENTER', 'Z', 'X', 'C', 'V', 'B', 'N', 'M', 'BACK'].forEach(k => {
            fullHtml += `<button class="key ${k.length>1?'wide':''}" id="key-${k}" onclick="handleCodeKey('${k}')">${k}</button>`;
        });
        $('#code-kb').html(fullHtml);
    }

    function setupCodeControls() {
        document.addEventListener('keydown', e => {
            if (codeGameOver) return;
            let key = e.key.toUpperCase();
            if (key === 'ENTER') handleCodeKey('ENTER');
            else if (key === 'BACKSPACE') handleCodeKey('BACK');
            else if (/^[A-Z]$/.test(key)) handleCodeKey(key);
        });
    }

    function startCodeGame() {
        $('#code-start-overlay').hide();
        $('#code-gameover-overlay').hide();
        targetWord = codeWords[Math.floor(Math.random() * codeWords.length)];
        currentGuess = '';
        guesses = [];
        codeGameOver = false;
        $('.code-tile').text('').removeClass('correct present absent active');
        $('.key').removeClass('correct present absent');
        updateCodeDisplay();
    }

    function handleCodeKey(key) {
        if (codeGameOver) return;
        if (key === 'BACK') {
            currentGuess = currentGuess.slice(0, -1);
        } else if (key === 'ENTER') {
            if (currentGuess.length === 5) submitGuess();
        } else if (currentGuess.length < 5) {
            currentGuess += key;
        }
        updateCodeDisplay();
    }

    function updateCodeDisplay() {
        let rowIdx = guesses.length;
        if (rowIdx >= 6) return;

        for (let i = 0; i < 5; i++) {
            let tile = $(`#tile-${rowIdx}-${i}`);
            tile.text(currentGuess[i] || '').toggleClass('active', !!currentGuess[i]);
        }
    }

    function submitGuess() {
        let guess = currentGuess;
        let result = [];
        let targetArr = targetWord.split('');
        let guessArr = guess.split('');

        // Mark correct
        guessArr.forEach((char, i) => {
            if (char === targetArr[i]) {
                result[i] = 'correct';
                targetArr[i] = null;
            }
        });

        // Mark present/absent
        guessArr.forEach((char, i) => {
            if (!result[i]) {
                let idx = targetArr.indexOf(char);
                if (idx !== -1) {
                    result[i] = 'present';
                    targetArr[idx] = null;
                } else {
                    result[i] = 'absent';
                }
            }
        });

        // Animate tiles
        let rowIdx = guesses.length;
        result.forEach((res, i) => {
            setTimeout(() => {
                let tile = $(`#tile-${rowIdx}-${i}`);
                tile.addClass(res);
                let key = $(`#key-${guessArr[i]}`);
                if (!key.hasClass('correct')) key.addClass(res);
            }, i * 100);
        });

        guesses.push(guess);
        currentGuess = '';

        if (guess === targetWord) {
            setTimeout(() => gameOverCode(true), 1000);
        } else if (guesses.length === 6) {
            setTimeout(() => gameOverCode(false), 1000);
        }
    }

    function gameOverCode(win) {
        codeGameOver = true;
        $('#code-status-text').text(win ? "ACCESS GRANTED" : "ACCESS DENIED").toggleClass('text-success', win).toggleClass('text-danger', !win);
        $('#code-final-word').text(targetWord);
        $('#code-gameover-overlay').fadeIn();
    }
</script>