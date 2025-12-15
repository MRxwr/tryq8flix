<?php include 'header.php'; ?>

<div class="container" style="margin-top: 100px;">
    <h2 class="section-title">Live Matches</h2>
    <div id="matches-list">
        <div class="text-center"><div class="spinner-border text-danger" role="status"></div></div>
    </div>
</div>

<?php include 'footer.php'; ?>

<script>
$(document).ready(function() {
    $.getJSON('api/index.php?endpoint=Live&action=live', function(response) {
        $('#matches-list').empty();
        if(response.ok && response.data.length > 0) {
            response.data.forEach(match => {
                if(match.rightTeamName) {
                    let html = `
                        <div class="live-match-card">
                            <div class="d-flex align-items-center" style="width: 40%;">
                                <img src="${match.rightTeamLogo}" class="team-logo me-3">
                                <span>${match.rightTeamName}</span>
                            </div>
                            <div class="text-center" style="width: 20%;">
                                <div class="match-time">${match.matchTime}</div>
                                <div class="small text-muted">${match.result || 'VS'}</div>
                                <div class="badge bg-danger">${match.liveStatus}</div>
                            </div>
                            <div class="d-flex align-items-center justify-content-end" style="width: 40%;">
                                <span>${match.leftTeamName}</span>
                                <img src="${match.leftTeamLogo}" class="team-logo ms-3">
                            </div>
                            <a href="?v=Servers&link=${encodeURIComponent(match.href)}&type=live" class="stretched-link"></a>
                        </div>
                    `;
                    $('#matches-list').append(html);
                }
            });
        } else {
            $('#matches-list').html('<p class="text-center">No live matches found at the moment.</p>');
        }
    });
});
</script>
