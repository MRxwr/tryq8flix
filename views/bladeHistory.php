<?php include 'header.php'; ?>

<div class="container" style="margin-top: 100px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="section-title m-0">Watch History</h2>
        <button class="btn btn-sm btn-outline-danger" onclick="clearHistory()">
            <i class="fas fa-trash-alt me-1"></i> Clear History
        </button>
    </div>
    
    <div id="historyResults" class="row"></div>
    <div id="loadingIndicator" class="text-center mt-4">
        <div class="spinner-border text-danger"></div>
    </div>
    <div id="emptyState" class="text-center mt-5" style="display: none;">
        <i class="fas fa-history fa-4x mb-3 text-white-50"></i>
        <h3>No history yet</h3>
        <p class="text-white-50">Movies and TV shows you watch will appear here.</p>
        <a href="?v=Home" class="btn btn-netflix mt-3">Browse Content</a>
    </div>
</div>

<?php include 'footer.php'; ?>

<script>
$(document).ready(function() {
    loadHistory();
});

function loadHistory() {
    $('#loadingIndicator').show();
    $('#historyResults').empty();
    $('#emptyState').hide();

    $.getJSON('api/index.php?endpoint=History&action=list', function(response) {
        $('#loadingIndicator').hide();
        
        if(response.ok && response.data.history && response.data.history.length > 0) {
            response.data.history.forEach(item => {
                const encHref = encryptLink(item.link);
                const encImage = encryptLink(item.poster);
                const encTitle = encryptLink(item.title);
                const safeTitle = item.title.replace(/'/g, "&#39;").replace(/"/g, "&quot;");
                
                // Format date
                const date = new Date(item.date);
                const dateStr = date.toLocaleDateString() + ' ' + date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});

                let html = `
                    <div class="col-6 col-md-3 col-lg-2 mb-4">
                        <div class="movie-card w-100" onclick="navigateToEncrypted({v: 'Servers', href: '${encHref}', server: '${item.server}', image: '${encImage}', title: '${encTitle}'})">
                            <img src="${item.poster}" alt="${safeTitle}" onerror="this.src='https://via.placeholder.com/200x300?text=No+Image'">
                            <div class="mt-2 text-center small text-truncate">${safeTitle}</div>
                            <div class="text-center text-muted" style="font-size: 0.7rem;">${dateStr}</div>
                        </div>
                    </div>
                `;
                $('#historyResults').append(html);
            });
        } else {
            $('#emptyState').show();
        }
    }).fail(function() {
        $('#loadingIndicator').hide();
        $('#historyResults').html('<p class="text-center text-danger">Failed to load history.</p>');
    });
}

function clearHistory() {
    if(confirm('Are you sure you want to clear your entire watch history?')) {
        $.post('api/index.php?endpoint=History&action=clear', function(response) {
            if(response.ok) {
                loadHistory();
            } else {
                alert('Failed to clear history');
            }
        }, 'json');
    }
}
</script>