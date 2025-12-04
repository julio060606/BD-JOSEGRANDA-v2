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

    // ===================== FUNCIONALIDADES DEL HORARIO =====================
    const selectorSemana = document.getElementById('selectorSemana');
    const filtroGrado = document.getElementById('filtroGrado');
    const btnSemanaActual = document.getElementById('btnSemanaActual');
    const btnImprimir = document.getElementById('btnImprimir');
    const horasTotales = document.getElementById('horasTotales');

    // Establecer semana actual por defecto
    function establecerSemanaActual() {
        const hoy = new Date();
        const primerDia = new Date(hoy.setDate(hoy.getDate() - hoy.getDay() + 1)); // Lunes
        const ultimoDia = new Date(hoy.setDate(hoy.getDate() + 4)); // Viernes
        
        const formatoFecha = (fecha) => {
            return fecha.toLocaleDateString('es-ES', { 
                day: '2-digit', 
                month: '2-digit' 
            });
        };
        
        document.querySelector('.card-header h5').textContent = 
            `Horario de Clases - Semana del ${formatoFecha(primerDia)} al ${formatoFecha(ultimoDia)}`;
    }

    // Filtrar por grado
    if (filtroGrado) {
        filtroGrado.addEventListener('change', function() {
            const gradoSeleccionado = this.value;
            const clases = document.querySelectorAll('.clase-item');
            
            clases.forEach(clase => {
                const textoClase = clase.querySelector('strong').textContent;
                if (gradoSeleccionado === 'all' || textoClase.includes(`${gradoSeleccionado}°`)) {
                    clase.parentElement.style.display = '';
                } else {
                    clase.parentElement.style.display = 'none';
                }
            });
            
            // Actualizar horas totales
            actualizarHorasTotales();
        });
    }

    // Semana actual
    if (btnSemanaActual) {
        btnSemanaActual.addEventListener('click', function() {
            establecerSemanaActual();
            if (selectorSemana) {
                const hoy = new Date();
                const year = hoy.getFullYear();
                const week = getWeekNumber(hoy);
                selectorSemana.value = `${year}-W${week.toString().padStart(2, '0')}`;
            }
        });
    }

    // Imprimir horario
    if (btnImprimir) {
        btnImprimir.addEventListener('click', function() {
            window.print();
        });
    }

    // Cambiar semana
    if (selectorSemana) {
        selectorSemana.addEventListener('change', function() {
            const [year, week] = this.value.split('-W');
            const fecha = getDateFromWeek(year, week);
            const primerDia = new Date(fecha);
            const ultimoDia = new Date(fecha);
            ultimoDia.setDate(ultimoDia.getDate() + 4);
            
            const formatoFecha = (fecha) => {
                return fecha.toLocaleDateString('es-ES', { 
                    day: '2-digit', 
                    month: '2-digit' 
                });
            };
            
            document.querySelector('.card-header h5').textContent = 
                `Horario de Clases - Semana del ${formatoFecha(primerDia)} al ${formatoFecha(ultimoDia)}`;
        });
    }

    // Actualizar horas totales
    function actualizarHorasTotales() {
        const clasesVisibles = document.querySelectorAll('.clase-item:not([style*="display: none"])');
        const horas = clasesVisibles.length * 0.75; // 45 minutos por clase
        if (horasTotales) {
            horasTotales.textContent = `${horas} horas semanales`;
        }
    }

    // Resaltar hora actual
    function resaltarHoraActual() {
        const ahora = new Date();
        const hora = ahora.getHours();
        const minutos = ahora.getMinutes();
        const horaActual = hora + minutos / 60;
        
        // Remover resaltado anterior
        document.querySelectorAll('.hora-actual').forEach(fila => {
            fila.classList.remove('hora-actual');
        });
        
        // Determinar fila actual basada en la hora
        let filaIndex = -1;
        if (horaActual >= 7 && horaActual < 7.75) filaIndex = 0;
        else if (horaActual >= 7.75 && horaActual < 8.5) filaIndex = 1;
        else if (horaActual >= 8.5 && horaActual < 9.25) filaIndex = 2;
        else if (horaActual >= 9.25 && horaActual < 10) filaIndex = 3;
        else if (horaActual >= 10 && horaActual < 10.75) filaIndex = 4;
        else if (horaActual >= 10.75 && horaActual < 11.5) filaIndex = 5;
        else if (horaActual >= 11.5 && horaActual < 12.25) filaIndex = 6;
        else if (horaActual >= 12.25 && horaActual < 13) filaIndex = 7;
        
        if (filaIndex >= 0) {
            const filas = document.querySelectorAll('.horario-table tbody tr');
            if (filas[filaIndex]) {
                filas[filaIndex].classList.add('hora-actual');
            }
        }
    }

    // Funciones auxiliares para manejo de semanas
    function getWeekNumber(date) {
        const firstDayOfYear = new Date(date.getFullYear(), 0, 1);
        const pastDaysOfYear = (date - firstDayOfYear) / 86400000;
        return Math.ceil((pastDaysOfYear + firstDayOfYear.getDay() + 1) / 7);
    }

    function getDateFromWeek(year, week) {
        const firstDayOfYear = new Date(year, 0, 1);
        const days = (week - 1) * 7;
        return new Date(year, 0, firstDayOfYear.getDate() + days);
    }

    // Inicializar
    establecerSemanaActual();
    resaltarHoraActual();
    actualizarHorasTotales();

    // Actualizar hora actual cada minuto
    setInterval(resaltarHoraActual, 60000);

    // Manejo del resize de la ventana
    window.addEventListener('resize', () => {
        if (window.innerWidth >= 992) {
            sidebar.classList.remove('active');
            sidebarOverlay.classList.remove('active');
            content.classList.remove('sidebar-active');
        }
    });
});