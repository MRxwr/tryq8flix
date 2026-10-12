<?php $v2RootBaseFoot = isset($rootBase) ? $rootBase : "../"; ?>
    <footer class="site-foot">
        <div class="site-foot__grid">
            <a href="?v=Home"><i class="fa-solid fa-house"></i> Home</a>
            <a href="?v=Search"><i class="fa-solid fa-magnifying-glass"></i> Search</a>
            <a href="?v=Games"><i class="fa-solid fa-gamepad"></i> Games</a>
            <a href="?v=LiveMatchesList"><i class="fa-solid fa-futbol"></i> Live Matches</a>
            <a href="?v=Download"><i class="fa-solid fa-cloud-arrow-down"></i> Downloader</a>
            <a href="?v=Favorites"><i class="fa-solid fa-heart"></i> Favorites</a>
            <a href="?v=History"><i class="fa-solid fa-clock-rotate-left"></i> History</a>
            <a href="?v=About"><i class="fa-solid fa-circle-info"></i> About</a>
            <a href="?v=Terms"><i class="fa-solid fa-file-contract"></i> Terms</a>
            <a href="?v=Policy"><i class="fa-solid fa-shield-halved"></i> Privacy</a>
            <a href="?v=Settings"><i class="fa-solid fa-gear"></i> Settings</a>
        </div>
        <div class="site-foot__copy">
            <span class="brand__mark" style="width:24px;height:24px;font-size:.6rem;"><i class="fa-solid fa-play"></i></span>
            <span>&copy; <?= date("Y") ?> TryQ8Flix · Stream responsibly.</span>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="assets/js/core.js"></script>
</body>
</html>
