<?php include 'header.php'; ?>

<div class="container" style="margin-top: 100px;">
    <h2 class="section-title mb-4">Privacy Policy</h2>
    <div class="card bg-dark text-white">
        <div class="card-body">
            <div id="content-area">
                <div class="text-center"><div class="spinner-border text-danger"></div></div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

<script>
$(document).ready(function() {
    $.getJSON('api/index.php?endpoint=Settings', function(response) {
        if(response.ok && response.data.length > 0) {
            $('#content-area').html(response.data[0].policy || '<p>No information available.</p>');
        } else {
            $('#content-area').html('<p class="text-danger">Failed to load content.</p>');
        }
    });
});
</script>
