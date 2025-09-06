document.addEventListener('DOMContentLoaded', function () {
    const burger = document.getElementById('burger-btn');
    const sidebar = document.getElementById('sidebar');
    const closeBtn = document.getElementById('close-sidebar');

    burger.addEventListener('click', function () {
        sidebar.classList.remove('hidden');
    });

    closeBtn.addEventListener('click', function () {
        sidebar.classList.add('hidden');
    });

    sidebar.addEventListener('click', function (e) {
        if (e.target === sidebar) {
            sidebar.classList.add('hidden');
        }
    });
});