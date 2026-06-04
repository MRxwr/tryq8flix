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
                    const qParams = {
                        v: 'Servers',
                        link: match.href,
                        type: 'live',
                        title: `${match.leftTeamName} VS ${match.rightTeamName}`,
                        image: match.leftTeamLogo,
                        leftLogo: match.leftTeamLogo,
                        rightLogo: match.rightTeamLogo,
                        leftName: match.leftTeamName,
                        rightName: match.rightTeamName
                    };
                    const queryString = Object.keys(qParams).map(key => key + '=' + encodeURIComponent(qParams[key])).join('&');
                    const encryptedUrl = '?q=' + encodeURIComponent(encryptLink(queryString));

                    let html = `
                        <div class="live-match-card">
                            <div class="match-main">
                                <div class="team-box">
                                    <span class="team-name text-white me-3 d-none d-md-inline">${match.leftTeamName}</span>
                                    <img src="${match.leftTeamLogo}" class="team-logo" alt="${match.leftTeamName}">
                                    <span class="team-name text-white ms-2 d-inline d-md-none">${match.leftTeamName}</span>
                                </div>
                                
                                <div class="match-center">
                                    <span class="match-time">${match.matchTime}</span>
                                    <div class="match-result">${match.result || '0 - 0'}</div>
                                    <span class="badge-live">${match.liveStatus}</span>
                                </div>
                                
                                <div class="team-box left">
                                    <span class="team-name text-white me-2 d-inline d-md-none">${match.rightTeamName}</span>
                                    <img src="${match.rightTeamLogo}" class="team-logo" alt="${match.rightTeamName}">
                                    <span class="team-name text-white ms-3 d-none d-md-inline">${match.rightTeamName}</span>
                                </div>
                            </div>
                            
                            <div class="match-footer">
                                <div class="footer-item">
                                    <i class="fas fa-trophy"></i>
                                    <span>${match.league || 'مباراة'}</span>
                                </div>
                                <div class="footer-item">
                                    <i class="fas fa-microphone"></i>
                                    <span>${match.commentator || 'غير معروف'}</span>
                                </div>
                                <div class="footer-item">
                                    <i class="fas fa-tv"></i>
                                    <span>${match.channel || 'غير معروف'}</span>
                                </div>
                            </div>
                            
                            <a href="${encryptedUrl}" class="stretched-link"></a>
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
