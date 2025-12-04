document.addEventListener('DOMContentLoaded', function() {
    // ===================== NAVEGACIÓN SIDEBAR =====================
    const sidebar = document.querySelector('.sidebar');
    const sidebarToggle = document.querySelector('.sidebar-toggle');
    const sidebarOverlay = document.querySelector('.sidebar-overlay');
    const content = document.querySelector('.content');
    
    function toggleSidebar() {
        sidebar.classList.toggle('active');
        sidebarOverlay.classList.toggle('active');
        content.classList.toggle('sidebar-active');
    }

    sidebarToggle.addEventListener('click', toggleSidebar);
    sidebarOverlay.addEventListener('click', toggleSidebar);

    if (window.innerWidth < 992) {
        const sidebarLinks = document.querySelectorAll('.sidebar .nav-link');
        sidebarLinks.forEach(link => {
            link.addEventListener('click', () => {
                if (sidebar.classList.contains('active')) {
                    toggleSidebar();
                }
            });
        });
    }

    // ===================== ANIMACIONES SIMPLES =====================
    // Asegurar que todo el contenido sea visible
    setTimeout(() => {
        const cards = document.querySelectorAll('.card');
        cards.forEach(card => {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        });
    }, 100);

    // ===================== MANEJO DEL IFRAME =====================
    const calendarIframe = document.querySelector('iframe');
    if (calendarIframe) {
        calendarIframe.addEventListener('load', function() {
            console.log('Calendario cargado correctamente');
        });
    }

    // Manejo del resize de la ventana
    window.addEventListener('resize', () => {
        if (window.innerWidth >= 992) {
            sidebar.classList.remove('active');
            sidebarOverlay.classList.remove('active');
            content.classList.remove('sidebar-active');
        }
        
        // Ajustar altura del iframe en móviles
        if (calendarIframe && window.innerWidth < 768) {
            calendarIframe.style.height = '400px';
        }
    });

    // Ajustar altura inicial en móviles
    if (calendarIframe && window.innerWidth < 768) {
        calendarIframe.style.height = '400px';
    }
});