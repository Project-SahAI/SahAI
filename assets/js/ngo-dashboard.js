document.addEventListener('DOMContentLoaded', function() {
    // Sidebar Toggle
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const sidebar = document.getElementById('sidebar');
    
    sidebarToggle.addEventListener('click', function() {
        sidebar.classList.toggle('show');
    });

    // Navigation Active State
    const navLinks = document.querySelectorAll('.nav-link');
    const sections = document.querySelectorAll('.section');
    const pageTitle = document.getElementById('page-title');

    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            // Remove active from all links
            navLinks.forEach(l => l.classList.remove('active'));
            this.classList.add('active');

            // Update page title
            pageTitle.textContent = this.textContent.trim();

            // Show corresponding section
            const sectionId = this.getAttribute('data-section');
            sections.forEach(section => {
                section.classList.remove('active-section');
                if (section.id === sectionId) {
                    section.classList.add('active-section');
                }
            });
        });
    });

    // Theme Toggle
    const themeSwitch = document.getElementById('theme-switch');
    
    themeSwitch.addEventListener('change', function() {
        document.body.classList.toggle('dark-theme', this.checked);
    });

    // Sample Charts (using Chart.js)
    const projectChart = new Chart(document.getElementById('projectChart'), {
        type: 'bar',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Projects',
                data: [12, 19, 3, 5, 2, 3],
                backgroundColor: 'rgba(14, 162, 189, 0.6)'
            }]
        }
    });

    const volunteerChart = new Chart(document.getElementById('volunteerChart'), {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Volunteers',
                data: [65, 59, 80, 81, 56, 55],
                borderColor: 'rgba(78, 166, 133, 1)',
                tension: 0.1
            }]
        }
    });
}); 