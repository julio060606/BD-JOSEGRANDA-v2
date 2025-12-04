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

    // ===================== FUNCIONALIDADES DE CURSOS =====================
    
    // Función para abrir gestión de calificaciones
    window.abrirCalificaciones = function(curso) {
        // En una implementación real, esto redirigiría a la página de calificaciones
        // Por ahora mostramos un alert
        alert(`Abriendo gestión de calificaciones para: ${curso}`);
        
        // Ejemplo de redirección (descomentar cuando existan las páginas):
        // window.location.href = `calificaciones.html?curso=${encodeURIComponent(curso)}`;
    };

    // Función para abrir gestión de asistencia
    window.abrirAsistencia = function(curso) {
        // En una implementación real, esto redirigiría a la página de asistencia
        // Por ahora mostramos un alert
        alert(`Abriendo gestión de asistencia para: ${curso}`);
        
        // Ejemplo de redirección (descomentar cuando existan las páginas):
        // window.location.href = `asistencia.html?curso=${encodeURIComponent(curso)}`;
    };

    // Manejo del resize de la ventana
    window.addEventListener('resize', () => {
        if (window.innerWidth >= 992) {
            sidebar.classList.remove('active');
            sidebarOverlay.classList.remove('active');
            content.classList.remove('sidebar-active');
        }
    });

    // Asegurar que todo el contenido sea visible
    setTimeout(() => {
        const cards = document.querySelectorAll('.curso-card');
        cards.forEach(card => {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        });
    }, 100);
});