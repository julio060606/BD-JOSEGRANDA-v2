document.addEventListener('DOMContentLoaded', () => {
    // Elementos del DOM
    const sidebar = document.querySelector('.sidebar');
    const sidebarToggle = document.querySelector('.sidebar-toggle');
    const sidebarOverlay = document.querySelector('.sidebar-overlay');
    const content = document.querySelector('.content');
    
    // Elementos para animaciones
    const targets = document.querySelectorAll('.perfil-card, .card, .clima-card, .progress, .carousel, .logo-small, .resumen-card');

    // Toggle del sidebar
    function toggleSidebar() {
        sidebar.classList.toggle('active');
        sidebarOverlay.classList.toggle('active');
        content.classList.toggle('sidebar-active');
    }

    // Event listeners
    sidebarToggle.addEventListener('click', toggleSidebar);
    sidebarOverlay.addEventListener('click', toggleSidebar);

    // Cerrar sidebar al hacer clic en un enlace (en móviles)
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

    // Animación en cascada
    targets.forEach((target, index) => {
        target.style.transitionDelay = (index * 0.15) + "s";
    });

    // Intersection Observer para animaciones
    const io = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if(entry.isIntersecting){
                entry.target.classList.add('visible');
                if(entry.target.classList.contains('progress')){
                    const bar = entry.target.querySelector('.progress-bar');
                    const width = bar.style.width;
                    bar.style.width = '0';
                    setTimeout(() => { bar.style.width = width; }, 200);
                }
                io.unobserve(entry.target);
            }
        });
    }, { threshold: 0.2 });

    targets.forEach(t => io.observe(t));

    // Actualizar hora actual en la tabla de horarios
    function actualizarHoraActual() {
        const ahora = new Date();
        const horaActual = ahora.getHours() + ':' + (ahora.getMinutes() < 10 ? '0' : '') + ahora.getMinutes();
        
        // Remover clase de hora actual de todas las filas
        document.querySelectorAll('.horario-table .hora-actual').forEach(row => {
            row.classList.remove('hora-actual');
        });
        
        // Aquí podrías implementar lógica más sofisticada para determinar
        // qué hora de la tabla corresponde a la hora actual
        console.log('Hora actual:', horaActual);
    }

    // Actualizar cada minuto
    setInterval(actualizarHoraActual, 60000);
    actualizarHoraActual(); // Ejecutar al cargar

    // Manejo del resize de la ventana
    window.addEventListener('resize', () => {
        if (window.innerWidth >= 992) {
            sidebar.classList.remove('active');
            sidebarOverlay.classList.remove('active');
            content.classList.remove('sidebar-active');
        }
    });
});