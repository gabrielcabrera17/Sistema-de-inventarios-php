//Selecciona todos los elementos del DOM  y los almacena en la variable formularios_ajax.
const formularios_ajax=document.querySelectorAll(".FormularioAjax");

function enviar_formulario_ajax(e){
    // Previene el comportamiento por defecto del envío de formularios.
    e.preventDefault();

    // Pregunta al usuario si realmente quiere enviar el formulario.
    let enviar=confirm("Quieres enviar el formulario");

    // Si el usuario confirma que quiere enviar el formulario.
    if(enviar==true){

        //Crea un objeto FormData a partir del formulario actual y lo almacena en la variable data.
        let data=new FormData(this);
        //la variable methos Obtiene el método del formulario (POST o GET)..
        let method=this.getAttribute("method");
        //la variable action guardara la url donde enviamos el formulario. 
        let action=this.getAttribute("action");
        
        //Encabezado necesario para la configuración que utilizaremos con la api de javascript fetch()
        let encabezados= new Headers();

        //Configuración completa para la solicitud fetch.
        let config={
            method: method,
            headers: encabezados,
            mode: 'cors',
            cache: 'no-cache',
            body: data
        }

        // Realiza la solicitud fetch al servidor.
        fetch(action,config)
        //envio  los datos, pero estoy esperando una respuesta cuando envie los datos
        .then(respuesta => respuesta.text())
        .then(respuesta =>{
            // Manipula la respuesta del servidor.
            let contenedor=document.querySelector(".form-rest");
            contenedor.innerHTML = respuesta;
        });
    }
    
}

//Itera sobre cada formulario seleccionado y ejecuta el código dentro del bucle para cada uno de ellos
formularios_ajax.forEach(formulario =>{
    //Para cada formulario, se añade un evento que escucha el evento de envío (submit). Cuando se envía un formulario, la función enviar_formulario_ajax se llamará.
    formulario.addEventListener("submit",enviar_formulario_ajax);
});

