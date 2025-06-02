(function(){ //Para proteger todo lo que este dentro del archivo y nada salga de este archivo 

    obtenerTareas();

    let tareas = [];
    let filtradas = [];

   // Botón para mostrar el modal de agregar tarea
    const nuevaTareaBtn = document.querySelector('#agregar-tarea')
    nuevaTareaBtn.addEventListener('click', function () {
        mostrarFormulario();
    });

    //Filtros de búsqueda
    const filtros = document.querySelectorAll('#filtros input[type="radio"]');
    filtros.forEach(radio => {
        radio.addEventListener('input', filtrarTareas);
    });

    function filtrarTareas(e) {
        const filtro = e.target.value;
        if (filtro !== '') {
            filtradas = tareas.filter(tarea => tarea.estado === filtro);
        } else {
            filtradas = [];
        }
        mostrarTareas()
    }

    async function obtenerTareas() {
        try {
            const proyectoUrl = obtenerUrlProyecto();
            const url = `http://localhost:3000/api/tareas?url=${proyectoUrl}`;
            
            const respuesta = await fetch(url);
            
            const resultado = await respuesta.json();

            tareas = resultado.tareas;
            //console.log(tareas);
            mostrarTareas();
            
        } catch (error) {
            console.log(error);         
        }
    }

    function mostrarTareas() {
        limpiarTareas();
        totalPendientes();
        totalCompletadas();

        const arrayTareas = filtradas.length ? filtradas : tareas; //Si filtradas tiene algo, asignamos filtradas, sino asignamos tareas

        if (arrayTareas.length === 0) {
            const listadoTareas = document.querySelector('#listado-tareas');

            const textoNoTareas = document.createElement('LI'); //Un li porque estamos dentro de un ul
            textoNoTareas.textContent = 'Aún no hay tareas';
            textoNoTareas.classList.add('no-tareas');

            listadoTareas.appendChild(textoNoTareas); //Agregar textoNoTareas a listadoTareas
            return;
        }

        const estados = {
            0: 'Pendiente',
            1: 'Completa'
        };
        arrayTareas.forEach(tarea => {
            const contenedorTarea = document.createElement('LI');
            contenedorTarea.dataset.tareaId = tarea.id;
            contenedorTarea.classList.add('tarea');
            const nombreTarea = document.createElement('P');
            nombreTarea.textContent = tarea.nombre;
            nombreTarea.ondblclick = function () {
                mostrarFormulario(true, {...tarea});
            }

            const opcionesDiv = document.createElement('DIV');
            opcionesDiv.classList.add('opciones');

            //Botones
            const btnEstadoTarea = document.createElement('BUTTON');
            btnEstadoTarea.classList.add('estado-tarea')
            btnEstadoTarea.classList.add(`${estados[tarea.estado].toLowerCase()}`)
            btnEstadoTarea.textContent = estados[tarea.estado];
            btnEstadoTarea.dataset.estadoTarea = tarea.estado;
            btnEstadoTarea.ondblclick = function () { //ondblclick, al dar doblre click
                cambiarEstadoTarea({...tarea}); //Le pasamos es una copia del objeto para no modificar el original
            }

            const btnEliminarTarea = document.createElement('BUTTON');
            btnEliminarTarea.classList.add('eliminar-tarea');
            btnEliminarTarea.textContent = 'Eliminar';
            btnEliminarTarea.dataset.idTarea = tarea.id;
            btnEliminarTarea.ondblclick = function(){
                confirmEliminarTarea({...tarea});
            }

            //console.log(btnEliminarTarea);
            opcionesDiv.appendChild(btnEstadoTarea);
            opcionesDiv.appendChild(btnEliminarTarea);

            contenedorTarea.appendChild(nombreTarea);
            contenedorTarea.appendChild(opcionesDiv);

            const listadoTareas = document.querySelector('#listado-tareas');
            listadoTareas.appendChild(contenedorTarea);
           // console.log(listadoTareas);   
        });  
    }

    function totalPendientes(){
        const totalPendientes = tareas.filter(tarea => tarea.estado === '0');
        const pendientesRadio = document.querySelector('#pendientes');

        if (totalPendientes.length === 0) {
            pendientesRadio.disabled = true;
        } else {
            pendientesRadio.disabled = false;
        }  
    }

    function totalCompletadas() {
        const totalCompletadas = tareas.filter(tarea => tarea.estado === '1');
        const completadasRadio = document.querySelector('#completadas');

        if (totalCompletadas.length === 0) {
            completadasRadio.disabled = true;
        } else {
            completadasRadio.disabled = false;
        } 
    }

    function mostrarFormulario(editar = false, tarea) {        
        const modal = document.createElement('DIV');
        modal.classList.add('modal');
        modal.innerHTML = `
            <form class="formulario nueva-tarea"> 
                <legend>${editar ? 'Editar tarea' : 'Añade una nueva tarea'}</legend> 
                <div class="campo">
                    <label>Tarea</label>
                    <input type="text" name="tarea" id="tarea" placeholder="Título de la tarea" value="${editar ? tarea.nombre : ''}"/>
                </div>
            <div class="opciones">
                <input type="submit" class="submit-nueva-tarea" value="${editar ? 'Editar tarea' : 'Añadir tarea'}">
                <button type="button" class="cerrar-modal">Cancelar </button>
            </div>
            </from>
        `;

        setTimeout(() => {
            const formulario = document.querySelector('.formulario');
            formulario.classList.add('animar');
        }, 0);
         
        modal.addEventListener('click', function (e) {
            e.preventDefault();
            if (e.target.classList.contains('cerrar-modal') || e.target.classList.contains('modal')) {
                const formulario = document.querySelector('.formulario');
                formulario.classList.add('cerrar');
                setTimeout(() => {
                    modal.remove();
                }, 200);
            } 
            if (e.target.classList.contains('submit-nueva-tarea')) {
                const nombreTarea = document.querySelector('#tarea').value.trim(); //.trim() para eliminar los espacios al inicio y al final
                if(!nombreTarea){
                    //Mostrar alerta de error
                    mostrarAlerta('error', 'El nombre de la tarea es obligatorio', document.querySelector('.formulario legend'));
            //        console.log('No hay nada en el tarea');
                    return;        
                }  
                if (editar) {
                    tarea.nombre = nombreTarea;
                    actualizarTarea(tarea);
                } else {
                    agregarTarea(nombreTarea);
                }
            }         
        });
        document.querySelector('.dashboard').appendChild(modal);
    }
    
    //Mostrar alerta en la interfaz       referencia, es en donde queremos que se muestre la alerta, es un elemento
    function mostrarAlerta(tipo, mensaje, referencia) {
        //Prevenir que se muestren multiples alertas
        const alertaPrevia = document.querySelector('.alerta');
        if (alertaPrevia) {
            alertaPrevia.remove(); 
        }

        const alerta = document.createElement('DIV');
        alerta.classList.add('alerta', tipo);
        alerta.textContent = mensaje;

        //Insertar la alerta antes del legend
        referencia.parentElement.insertBefore(alerta, referencia);
        //referencia.appendChild(alerta);
        
        //Eliminar la alerta despues de 4s tiempo
        setTimeout(() => {
            alerta.remove();
        }, 4000);
    }

    //Consultar el servidor para agregar una nueva tarea
    async function agregarTarea(tarea) {
        //Construir la peticion
        const datos = new FormData();
        datos.append('nombre', tarea);
        datos.append('proyectoId', obtenerUrlProyecto()); //Traer valor de la URL

        try {
            const url = 'http://localhost:3000/api/tarea';
            const respuesta = await fetch(url, {
                method: 'POST',
                body: datos
            });

            const resultado = await respuesta.json();
         //   console.log(resultado);

            mostrarAlerta(resultado.tipo, resultado.mensaje, document.querySelector('.formulario legend'));

            if (resultado.tipo === 'exito') {
                const modal = document.querySelector('.modal');
                setTimeout(() => {
                    modal.remove();
                }, 3000);

                // Agregar el objeto de tarea al global de tareas
                const tareaObj = {
                    id: String(resultado.id),
                    nombre: tarea,
                    estado: "0",
                    proyectoId: resultado.proyectoId
                } 

                tareas = [...tareas, tareaObj]//Tomar una copia de las tareas y tambien le pasamos tareaObj
                mostrarTareas();         
            }
        } catch (error) {
            console.log(error);
        }
    }

    function cambiarEstadoTarea(tarea) {
        // Ternario
        const nuevoEstado = tarea.estado === "1" ? "0" :"1"; //Si es 1, pasa a 0 y viceversa
        tarea.estado = nuevoEstado;
        actualizarTarea(tarea);
    }

    async function actualizarTarea(tarea) {

        const {estado, id, nombre, proyectoId} = tarea;

        //Construir peticion
        const datos = new FormData();
        datos.append('id', id);
        datos.append('nombre', nombre);
        datos.append('estado', estado);
        datos.append('proyectoId', obtenerUrlProyecto());    
        
        //Imprimir valores de datos
        // for (let valor of datos.values()) {
        //    console.log(valor);            
        // }

        try {
            const url = 'http://localhost:3000//api/tarea/actualizar';
            const respuesta = await fetch(url, {
                method: 'POST',
                body: datos
            });
            
            const resultado = await respuesta.json();
            if(resultado.respuesta.tipo === 'exito'){
                //mostrar alerta con la funcion creada
               // mostrarAlerta(resultado.respuesta.tipo, resultado.respuesta.mensaje, document.querySelector('.listado-tareas'))   
                
                //con sweetAlert
                Swal.fire('Actualizada!', resultado.respuesta.mensaje, 'success');

                //Cerrar modal despues de actualizar
                const modal = document.querySelector('.modal');
                if (modal) {
                    modal.remove();
         
                }

                tareas = tareas.map(tareaMemoria => { //.map(), itera el arreglo y crea un nuevo arreglo actualizado
                    // console.log(tareaMemoria.id);

                    // console.log('Modificando', id);

                    if (tareaMemoria.id === id) {
                        tareaMemoria.estado = estado;      
                        tareaMemoria.nombre = nombre;      
                    }
                    return tareaMemoria;       
                }); 

                mostrarTareas(); //Para que se vea el cambio en pantalla automatico, en tiempo real
            }
        } catch (error) {
            console.log(error);        
        }
    }

    function confirmEliminarTarea(tarea) {
        Swal.fire({
            title: "¿Seguro que quieres eliminar la tarea?",
            showCancelButton: true,
            confirmButtonText: "Si, Seguro",
            cancelButtonText: `No, Cancelar`
          }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            if (result.isConfirmed) {
                eliminarTarea(tarea); 
            }
          });
    }

    async function eliminarTarea(tarea) {
        const {estado, id, nombre, proyectoId} = tarea;

        //Construir peticion
        const datos = new FormData();
        datos.append('id', id);
        datos.append('nombre', nombre);
        datos.append('estado', estado);
        datos.append('proyectoId', obtenerUrlProyecto());    

        try {
            const url = 'http://localhost:3000//api/tarea/eliminar';
            const respuesta = await fetch(url, {
                method: 'POST',
                body: datos
            });
     
            const resultado = await respuesta.json();

            if(resultado.respuesta){
                //Mostrar alerta con la funcion creada
             //   mostrarAlerta(resultado.respuesta.tipo, resultado.respuesta.mensaje, document.querySelector('.listado-tareas'))   
                
                //Mostras alerta con sweetAlert
                Swal.fire('Eliminada!', resultado.respuesta.mensaje, 'success');
            }

            //.filter(), itera el arreglo y crea un nuevo arreglo actualizado, sirve para sacar todos excepto 1 o sacar 1 excepto todos
            tareas = tareas.filter(tareaMemoria => tareaMemoria.id !== tarea.id); //Traer todas las que sean diferentes a la que queremos eliminar 

            mostrarTareas(); //Para que se vea el cambio en pantalla automatico, en tiempo real

        } catch (error) {
            console.log(error);    
        }
    }

    function obtenerUrlProyecto() {
         //Leer y traer valores de la URL
         const proyectoParams = new URLSearchParams(window.location.search); //Datos de la url
         const proyecto = Object.fromEntries(proyectoParams.entries()); //Traer los valores del objeto
         return proyecto.url;
    }

    //Eliminar las tareas anterirores para que no se dupliquen
    function limpiarTareas() {
        const listadoTareas = document.querySelector('.listado-tareas');
        
        while (listadoTareas.firstChild) { //Mientras hayan elementos
            listadoTareas.removeChild(listadoTareas.firstChild);
        }
    }
})();