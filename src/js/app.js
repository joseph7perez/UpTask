const mobileMenuBtn = document.querySelector('#mobile-menu');
const cerrarMenuBtn = document.querySelector('#cerrar-menu');
const sidebar = document.querySelector('.sidebar');

if (mobileMenuBtn) {
    mobileMenuBtn.addEventListener('click', function () {
        sidebar.classList.add('mostrar');
        sidebar.classList.remove('ocultar');

    })
}

if (cerrarMenuBtn) {
    cerrarMenuBtn.addEventListener('click', function () {
        sidebar.classList.add('ocultar');

        setTimeout(() => {
            sidebar.classList.remove('mostrar');
        }, 500);
    })
}

// Elimina la clase de mostrar en el tamaño de tablet y desktop

window.addEventListener('resize', function () {
    console.log('resize');
    
    const anchoPantalla = document.body.clientWidth; //identificar el ancho de la pantalla actual

    if (anchoPantalla >= 768) {
        sidebar.classList.remove('mostrar');
    }
})

