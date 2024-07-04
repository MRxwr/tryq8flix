document.addEventListener('DOMContentLoaded', (event) => {
    // Add event listeners for theme switch
    const themeSwitcher = document.getElementById('theme-switcher');
    themeSwitcher.addEventListener('change', function() {
        if (this.checked) {
            document.body.classList.add('dark-theme');
            document.body.classList.remove('light-theme');
        } else {
            document.body.classList.add('light-theme');
            document.body.classList.remove('dark-theme');
        }
    });
});

