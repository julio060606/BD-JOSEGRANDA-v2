// ===================== NAVEGACIÓN SIDEBAR =====================
document.addEventListener('DOMContentLoaded', () => {
    // Elementos del DOM para sidebar
    const sidebar = document.querySelector('.sidebar');
    const sidebarToggle = document.querySelector('.sidebar-toggle');
    const sidebarOverlay = document.querySelector('.sidebar-overlay');
    const content = document.querySelector('.content');
    
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

    // ===================== Animaciones cards =====================
    const targets = document.querySelectorAll('.perfil-card, .card, .logo-small');
    targets.forEach((target, index) => target.style.transitionDelay = (index * 0.1) + "s");
    
    const io = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if(entry.isIntersecting){ 
                entry.target.classList.add('visible'); 
                io.unobserve(entry.target); 
            }
        });
    }, { threshold: 0.2 });
    
    targets.forEach(t => io.observe(t));

    // ===================== Configuración de Perfil =====================
const btnCambiarContra = document.getElementById('btnCambiarContra');
const btnGuardarContra = document.getElementById('btnGuardarContra');
const btnCancelarContra = document.getElementById('btnCancelarContra');
const cambiarContraForm = document.getElementById('cambiarContraForm');
const mensajeContra = document.getElementById('mensajeContra');

const btnCambiarFoto = document.getElementById('btnCambiarFoto');
const btnGuardarFoto = document.getElementById('btnGuardarFoto');
const btnCancelarFoto = document.getElementById('btnCancelarFoto');
const cambiarFotoForm = document.getElementById('cambiarFotoForm');
const inputFoto = document.getElementById('inputFoto');
const fotoPerfil = document.getElementById('fotoPerfil');

// Función para resetear todos los formularios
function resetearFormularios() {
    // Resetear formulario de contraseña
    cambiarContraForm.style.display = 'none';
    document.getElementById('nuevaContra').value = '';
    document.getElementById('confirmarContra').value = '';
    
    // Resetear formulario de foto
    cambiarFotoForm.style.display = 'none';
    inputFoto.value = '';
    
    // Resetear botones de contraseña
    btnCambiarContra.style.display = 'inline-block';
    btnGuardarContra.style.display = 'none';
    btnCancelarContra.style.display = 'none';
    
    // Resetear botones de foto
    btnCambiarFoto.style.display = 'inline-block';
    btnGuardarFoto.style.display = 'none';
    btnCancelarFoto.style.display = 'none';
    
    // Limpiar mensajes
    mensajeContra.innerHTML = '';
}

// Cambiar contraseña
btnCambiarContra.addEventListener('click', () => {
    resetearFormularios();
    cambiarContraForm.style.display = 'block';
    btnCambiarContra.style.display = 'none';
    btnGuardarContra.style.display = 'inline-block';
    btnCancelarContra.style.display = 'inline-block';
    btnCambiarFoto.style.display = 'none';
});

btnGuardarContra.addEventListener('click', () => {
    const nueva = document.getElementById('nuevaContra').value;
    const confirmar = document.getElementById('confirmarContra').value;
    
    if(nueva === '' || confirmar === ''){
        mensajeContra.innerHTML = '<span class="text-danger">Todos los campos son obligatorios.</span>';
        return;
    }
    
    if(nueva !== confirmar){
        mensajeContra.innerHTML = '<span class="text-danger">Las contraseñas no coinciden. Intenta de nuevo.</span>';
        return;
    }
    
    if(nueva.length < 6){
        mensajeContra.innerHTML = '<span class="text-danger">La contraseña debe tener al menos 6 caracteres.</span>';
        return;
    }
    
    mensajeContra.innerHTML = '<span class="text-success">¡Contraseña cambiada correctamente!</span>';
    resetearFormularios();
});

btnCancelarContra.addEventListener('click', () => {
    resetearFormularios();
});

// Cambiar foto
btnCambiarFoto.addEventListener('click', () => {
    resetearFormularios();
    cambiarFotoForm.style.display = 'block';
    btnCambiarFoto.style.display = 'none';
    btnGuardarFoto.style.display = 'inline-block';
    btnCancelarFoto.style.display = 'inline-block';
    btnCambiarContra.style.display = 'none';
});

// Guardar foto
btnGuardarFoto.addEventListener('click', () => {
    const file = inputFoto.files[0];
    
    if(!file){
        mensajeContra.innerHTML = '<span class="text-danger">Debes seleccionar una imagen.</span>';
        return;
    }
    
    // Validar tipo de archivo
    if(!file.type.match('image.*')) {
        mensajeContra.innerHTML = '<span class="text-danger">Solo se permiten archivos de imagen.</span>';
        return;
    }
    
    // Validar tamaño (max 2MB)
    if(file.size > 2 * 1024 * 1024) {
        mensajeContra.innerHTML = '<span class="text-danger">La imagen no debe superar los 2MB.</span>';
        return;
    }
    
    const reader = new FileReader();
    reader.onload = e => {
        fotoPerfil.src = e.target.result;
        mensajeContra.innerHTML = '<span class="text-success">¡Foto actualizada correctamente!</span>';
        resetearFormularios();
    }
    reader.readAsDataURL(file);
});

// Cancelar cambio de foto
btnCancelarFoto.addEventListener('click', () => {
    resetearFormularios();
});

// Cambio inmediato de foto (opcional - para ver preview)
inputFoto.addEventListener('change', (event) => {
    const file = event.target.files[0];
    if(file){
        // Validar tipo de archivo
        if(!file.type.match('image.*')) {
            mensajeContra.innerHTML = '<span class="text-danger">Solo se permiten archivos de imagen.</span>';
            inputFoto.value = '';
            return;
        }
        
        // Validar tamaño (max 2MB)
        if(file.size > 2 * 1024 * 1024) {
            mensajeContra.innerHTML = '<span class="text-danger">La imagen no debe superar los 2MB.</span>';
            inputFoto.value = '';
            return;
        }
        
        // Mostrar preview inmediato
        const reader = new FileReader();
        reader.onload = e => {
            fotoPerfil.src = e.target.result;
        }
        reader.readAsDataURL(file);
    }
});

    // ===================== Editar Información de Contacto =====================
    const btnEditarContacto = document.getElementById('btnEditarContacto');
    const btnGuardarContacto = document.getElementById('btnGuardarContacto');
    const btnCancelarContacto = document.getElementById('btnCancelarContacto');
    const mensajeContacto = document.getElementById('mensajeContacto');

    const camposContacto = [
        'contactCorreo', 'contactCorreoPersonal', 'contactTelefono', 
        'contactTelefonoEmergencia', 'contactDireccion', 'contactDistrito',
        'contactInfoMedica'
    ];
    
    const valoresInicialesContacto = camposContacto.map(id => document.getElementById(id).value);

    btnEditarContacto.addEventListener('click', () => {
        camposContacto.forEach(id => document.getElementById(id).disabled = false);
        btnEditarContacto.style.display = 'none';
        btnGuardarContacto.style.display = 'inline-block';
        btnCancelarContacto.style.display = 'inline-block';
        mensajeContacto.innerHTML = '<span class="text-success">Ahora puedes editar tus datos de contacto.</span>';
    });

    btnGuardarContacto.addEventListener('click', () => {
        // Validaciones básicas
        const correo = document.getElementById('contactCorreo').value;
        const telefono = document.getElementById('contactTelefono').value;
        
        if(!correo.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
            mensajeContacto.innerHTML = '<span class="text-danger">Ingresa un correo electrónico válido.</span>';
            return;
        }
        
        if(!telefono.match(/^\+51\s\d{3}\s\d{3}\s\d{3}$/)) {
            mensajeContacto.innerHTML = '<span class="text-danger">Formato de teléfono incorrecto. Use: +51 999 999 999</span>';
            return;
        }
        
        camposContacto.forEach(id => document.getElementById(id).disabled = true);
        btnGuardarContacto.style.display = 'none';
        btnCancelarContacto.style.display = 'none';
        btnEditarContacto.style.display = 'inline-block';
        mensajeContacto.innerHTML = '<span class="text-success">¡Datos de contacto actualizados correctamente!</span>';
        camposContacto.forEach((id,i) => valoresInicialesContacto[i] = document.getElementById(id).value);
    });

    btnCancelarContacto.addEventListener('click', () => {
        camposContacto.forEach((id,i) => {
            document.getElementById(id).value = valoresInicialesContacto[i];
            document.getElementById(id).disabled = true;
        });
        btnGuardarContacto.style.display = 'none';
        btnCancelarContacto.style.display = 'none';
        btnEditarContacto.style.display = 'inline-block';
        mensajeContacto.innerHTML = '';
    });

    // ===================== Editar Información Académica =====================
    const btnEditarAcademica = document.getElementById('btnEditarAcademica');
    const btnGuardarAcademica = document.getElementById('btnGuardarAcademica');
    const btnCancelarAcademica = document.getElementById('btnCancelarAcademica');
    const mensajeAcademica = document.getElementById('mensajeAcademica');

    const camposAcademica = [
        'infoTitulo', 'infoUniversidad', 'infoGrados', 
        'infoEspecializacion', 'infoExperiencia'
    ];
    
    const valoresInicialesAcademica = camposAcademica.map(id => document.getElementById(id).value);

    btnEditarAcademica.addEventListener('click', () => {
        camposAcademica.forEach(id => document.getElementById(id).disabled = false);
        btnEditarAcademica.style.display = 'none';
        btnGuardarAcademica.style.display = 'inline-block';
        btnCancelarAcademica.style.display = 'inline-block';
        mensajeAcademica.innerHTML = '<span class="text-success">Ahora puedes editar tu información académica.</span>';
    });

    btnGuardarAcademica.addEventListener('click', () => {
        camposAcademica.forEach(id => document.getElementById(id).disabled = true);
        btnGuardarAcademica.style.display = 'none';
        btnCancelarAcademica.style.display = 'none';
        btnEditarAcademica.style.display = 'inline-block';
        mensajeAcademica.innerHTML = '<span class="text-success">¡Información académica actualizada correctamente!</span>';
        camposAcademica.forEach((id,i) => valoresInicialesAcademica[i] = document.getElementById(id).value);
    });

    btnCancelarAcademica.addEventListener('click', () => {
        camposAcademica.forEach((id,i) => {
            document.getElementById(id).value = valoresInicialesAcademica[i];
            document.getElementById(id).disabled = true;
        });
        btnGuardarAcademica.style.display = 'none';
        btnCancelarAcademica.style.display = 'none';
        btnEditarAcademica.style.display = 'inline-block';
        mensajeAcademica.innerHTML = '';
    });

    // Manejo del resize de la ventana
    window.addEventListener('resize', () => {
        if (window.innerWidth >= 992) {
            sidebar.classList.remove('active');
            sidebarOverlay.classList.remove('active');
            content.classList.remove('sidebar-active');
        }
    });
});