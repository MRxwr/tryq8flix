<?php include 'header.php'; ?>

<div class="container" style="margin-top: 100px;">
    <div id="details-container">
        <div class="text-center"><div class="spinner-border text-danger"></div></div>
    </div>
</div>

<?php include 'footer.php'; ?>

<script>
$(document).ready(function() {
    const urlParams = new URLSearchParams(window.location.search);
    const href = decryptLink(urlParams.get('href'));
    const server = urlParams.get('server');
    
    if(href && server) {
        $.getJSON(`api/index.php?endpoint=More&action=list&server=${server}&href=${encodeURIComponent(href)}`, function(response) {
            if(response.ok) {
                const data = response.data;
                
                // If no seasons and no episodes, it's likely a movie -> go to servers
                if ((!data.seasons || data.seasons.length === 0) && (!data.episodes || data.episodes.length === 0)) {
                     window.location.replace(`?v=Servers&href=${encodeURIComponent(encryptLink(href))}&server=${server}`);
                     return;
                }

                let html = `
                    <div class="row">
                        <div class="col-md-12 mb-4">
                            <h2>${data.seasons && data.seasons.length > 0 ? 'Seasons' : 'Episodes'}</h2>
                        </div>
                    </div>
                `;
                
                if(data.seasons && data.seasons.length > 0) {
                    html += `<div class="row mb-5">`;
                    data.seasons.forEach(season => {
                        html += `
                            <div class="col-6 col-md-3 col-lg-2 mb-3">
                                <div class="card bg-dark text-white h-100" onclick="navigateTo('?v=More&href=${encodeURIComponent(encryptLink(season.link))}&server=${server}')" style="cursor:pointer;">
                                    <div class="card-body text-center d-flex align-items-center justify-content-center">
                                        <h5 class="card-title">${season.title}</h5>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    html += `</div>`;
                }
                
                if(data.episodes && data.episodes.length > 0) {
                    html += `<h3>Episodes</h3><div class="row">`;
                    data.episodes.forEach(ep => {
                        html += `
                            <div class="col-6 col-md-3 col-lg-2 mb-3">
                                <div class="card bg-dark text-white h-100" onclick="navigateTo('?v=Servers&href=${encodeURIComponent(encryptLink(ep.link))}&server=${server}')" style="cursor:pointer;">
                                    <div class="card-body text-center d-flex align-items-center justify-content-center">
                                        <h6 class="card-title">${ep.title}</h6>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    html += `</div>`;
                }
                
                $('#details-container').html(html);
            }
        });
    }
});
</script>
