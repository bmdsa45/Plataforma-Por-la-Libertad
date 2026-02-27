document.addEventListener('DOMContentLoaded', () => {
    const menuToggle = document.querySelector('.menu-toggle');
    const navUl = document.querySelector('nav ul');

    if (menuToggle && navUl) {
        menuToggle.addEventListener('click', () => {
            menuToggle.classList.toggle('active');
            navUl.classList.toggle('active');
        });

        // Close menu when a link is clicked
        const navLinks = document.querySelectorAll('nav ul li a');
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                menuToggle.classList.remove('active');
                navUl.classList.remove('active');
            });
        });

        // Close menu when clicking outside
        document.addEventListener('click', (event) => {
            const isClickInsideMenu = navUl.contains(event.target);
            const isClickInsideToggle = menuToggle.contains(event.target);

            if (!isClickInsideMenu && !isClickInsideToggle && navUl.classList.contains('active')) {
                menuToggle.classList.remove('active');
                navUl.classList.remove('active');
            }
        });
    }
});