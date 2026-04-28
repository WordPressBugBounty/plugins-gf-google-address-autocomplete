; (function ($) {

    document.addEventListener('DOMContentLoaded', function () {
        const menuLinks = document.querySelectorAll('.gfgaa_menu_item li a');
        const tabs = document.querySelectorAll('.tab-content');

        // Show tab by URL parameter on page load
        const url = new URL(window.location);
        const currentTab = url.searchParams.get('tab') || 'intro';
        showTab(currentTab);

        // On menu click
        menuLinks.forEach(link => {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                const tabName = this.getAttribute('data-tab');
                showTab(tabName);
                // Change URL parameter without reloading
                url.searchParams.set('tab', tabName);
                window.history.pushState({}, '', url);
            });
        });

        function showTab(tabName) {
            // Hide all tabs
            tabs.forEach(t => t.style.display = 'none');

            // Remove active class from all menu links
            menuLinks.forEach(l => l.classList.remove('active'));
            if (!document.getElementById(tabName)) {
                tabName = 'intro';
            }
            // Show current tab and set active menu item
            document.getElementById(tabName).style.display = 'block';
            document.querySelector(`[data-tab="${tabName}"]`)?.classList.add('active');
        }
    });

})(jQuery);