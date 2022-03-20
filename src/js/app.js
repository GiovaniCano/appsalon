const APIbaseURL = "http://localhost:3000/api/"
// const APIbaseURL = "http://127.0.0.1:3000/api/"

let paso = 1
const cita = {
    usuarioId: "",
    nombre: "",
    fecha: "",
    hora: "",
    servicios: []
}

$(function(){
    iniciarAPP()
})
function iniciarAPP() {
    tabs() //cambia la seccion cuando se presionen los tabs
    botonesPaginador() //agrega o quita los botones del paginador
    paginaAnterior()
    paginaSiguiente()

    // consultarAPI_Fetch()
    consultarAPI_jQuery()

    idCliente() //añade el id del cliente al objeto de cita
    nombreCliente() //añade el nombre del cliente al objeto de cita
    seleccionarFecha() //añade la fecha de la cita en el objeto
    seleccionarHora() //añade la hora de la cita en el objeto
}

/* Cita */
function reservarCita_jQuery() {
    const datos = {...cita}
    datos.servicios = datos.servicios.map(servicio=>servicio.id)
    $.ajax({
        url: "/api/citas",
        method: "post",
        dataType: "json",
        data: datos,
        error: alertaErrorPOST,
        success: function(respuesta) {
            if(respuesta.resultado.resultado) {
                //@ts-ignore
                Swal.fire({
                    icon: 'success',
                    title: 'Cita Creada',
                    text: 'Tu cita fué creada correctamente',
                    customClass: {popup:"swal2-custom-size"},
                    // confirmButtonText: "Ok"
                }).then(()=>window.location.reload())
            } else {
                alertaErrorPOST()
            }
        }
    })
}
async function reservarCita_Fetch() {
    const {usuarioId, fecha, hora, servicios} = cita

    const idServicios = servicios.map(servicio=>servicio.id)

    const datos = new FormData()
    datos.append("usuarioId", usuarioId)
    datos.append("fecha", fecha)
    datos.append("hora", hora)
    datos.append("servicios", idServicios)
    // cl([...datos]); return

    try {
        /* peticion hacia la api */
        const respuesta = await fetch("/api/citas", {
            method: 'post',
            body: datos
        })
        const resultado = await respuesta.json()
        if(resultado.resultado.resultado) {
            //@ts-ignore
            Swal.fire({
                icon: 'success',
                title: 'Cita Creada',
                text: 'Tu cita fué creada correctamente',
                customClass: {popup:"swal2-custom-size"},
                // confirmButtonText: "Ok"
            }).then(()=>window.location.reload())
        } else {
            alertaErrorPOST()
        }
    } catch (error) {
        alertaErrorPOST()
    }
}
function alertaErrorPOST() {
    //@ts-ignore
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'Hubo un error al guardar la cita',
        customClass: {popup:"swal2-custom-size"},
        footer: `<a href="/cita">Recargar la página</a>`
    })
}

function mostrarResumen() {
    const resumen = $("#resumen")
    if(Object.values(cita).includes("") || cita.servicios.length === 0) {
        resumen.children().remove()
        mostrarAlerta("Faltan datos o elegir al menos un servicio", "error", "#resumen", false)
    } else {
        resumen.children().remove()

        const {nombre, servicios} = cita

        let hora = cita.hora.split(":")
        if(hora[0] > 12) {
            hora = hora[0]-12+":"+hora[1]+" p.m."
        } else if(hora[0] == 12) {
            hora = hora.join(":")+" p.m."
        } else {
            hora = hora.join(":")+" a.m."
        }
        /* fecha vanilla JS */
            let fecha = cita.fecha.split("-")
            fecha = new Date(fecha[0], fecha[1]-1, fecha[2])
            fecha = fecha.toLocaleDateString("es", {year: "numeric", weekday: "long", day: "numeric", month: "long"})
            fecha = fecha[0].toUpperCase() + fecha.substring(1)        
        /* fecha moment.js */
            // let fecha = moment(cita.fecha).format("dddd D [de] MMMM [de] YYYY")
            // fecha = fecha[0].toUpperCase() + fecha.substring(1)

        const nombreCliente = $("<p></p>").html(`<span>Nombre:</span> ${nombre}`)
        const fechaCita = $("<p></p>").html(`<span>Fecha:</span> ${fecha}`)
        const horaCita = $("<p></p>").html(`<span>Hora:</span> ${hora}`)
        resumen.append(nombreCliente, fechaCita, horaCita)

        servicios.forEach(servicio => {
            const {id, precio, nombre} = servicio
            const contenedorServicio = $("<div></div>").addClass("contenedor-servicio")
            const textoServicio = $("<p></p>").text(nombre)
            const precioServicio = $("<div></div>").html("<span>Precio: </span>"+"$ "+precio)
            contenedorServicio.append(textoServicio, precioServicio)

            resumen.append(contenedorServicio)
        });

        const botonReservar = $("<button></button>").addClass("boton").text("Reservar Cita")
        // botonReservar.on("click", reservarCita_Fetch)
        botonReservar.on("click", reservarCita_jQuery)
        resumen.append(botonReservar)
    }
}
function seleccionarHora() {
    $("#hora").on("input", function(e) {
        const horaSeleccionada = $(this).val()
        //@ts-ignore
        const hora = horaSeleccionada.split(":")[0]
        if(hora < 10 || hora > 18) {
            $(this).val("")
            mostrarAlerta("Abierto de 10:00 a.m. a 6:00 p.m.", "error", "#paso-2")
            cita.hora = ""
        } else {
            cita.hora = horaSeleccionada
        }
    })
}
function seleccionarFecha() {
    $("#fecha").on("input", function(e) {
        const fechaSeleccionada = $(this).val()
        //@ts-ignore
        const dia = new Date(fechaSeleccionada).getUTCDay()
        
        if([0,6].includes(dia)) {
            $(this).val("")
            mostrarAlerta("Fines de semana no abrimos", "error", "#paso-2")
            cita.fecha = ""
        } else {
            cita.fecha = fechaSeleccionada
        }
    })
}
function nombreCliente() {
    cita.nombre = $("#nombre").val()    
}
function idCliente() {
    cita.usuarioId = $("#id").val()
}
function mostrarAlerta(mensaje, tipo, elemento, desaparece = true) {
    if($(".alerta").length) $(".alerta").remove()
    const alerta = $("<div></div>").text(mensaje).addClass("alerta").addClass(tipo)
    $(elemento).prepend(alerta)
    if(desaparece) setTimeout(() => alerta.remove(), 4000)
}
function quitarAlertas() {
    const alerta = $(".alerta")
    if(alerta) setTimeout(() => alerta.remove(), 4000)
}

/* consulta API para mostrar servicios */
async function consultarAPI_Fetch() {
    try {
        const resultado = await fetch("/api/servicios")
        const servicios = await resultado.json()
        mostrarServicios(servicios)        
    } catch (error) {
        cl(error)
    }
}
function consultarAPI_jQuery() {
    $.ajax({
        url: "/api/servicios",
        type: "get",
        dataType: "json",
        success: response => mostrarServicios(response),
        error: () => cl("error al cargar los servicios"),
        timeout: 4000
    })
}
/* DOM scripting */
function mostrarServicios(servicios) {
    servicios.forEach(servicio => {
        const {id, nombre, precio} = servicio
        
        const nombreServicio = $("<p></p>").addClass("nombre-servicio").text(nombre)
        const precioServicios = $("<p></p>").addClass("precio-servicio").text("$ "+precio)
        const servicioDiv = $("<div></div>").addClass("servicio").attr("data-id-servicio", id)
        servicioDiv.on("click", function() {
            seleccionarServicio(servicio)
        })

        servicioDiv.append(nombreServicio, precioServicios)
        $("#servicios").append(servicioDiv)
    })
}
function seleccionarServicio(servicio) {
    const {id} = servicio
    const {servicios} = cita

    /* comprobar si un servicio ya fue agregado o quitado */
    if(servicios.some(servicio=>servicio.id === id)) {
    // if(servicios.includes(servicio)) {
        /* eliminarlo */
        cita.servicios = servicios.filter(servicio=>servicio.id !== id)
        $(`[data-id-servicio="${id}"]`).removeClass("seleccionado")
    } else {
        /* agregarlo */
        cita.servicios = [...servicios, servicio]
        $(`[data-id-servicio="${id}"]`).addClass("seleccionado")
    }
}

/* js */
function tabs() {
    $(".tabs button").on("click", function(e) {
        paso = $(this).data("paso") // parseInt(e.target.dataset.paso)

        mostrarSeccion()
    })
}

function mostrarSeccion() {
    /* ocular la seccion que tenga la clase de mostrar */
    const seccionAnterior = $(".mostrar")
    seccionAnterior.removeClass("mostrar")

    /* seleccionar la seccion con el paso */
    const seccion = $(`#paso-${paso}`);
    seccion.addClass("mostrar")

    /* quita la clase de actual al tab anterior */
    const tabAnterior = $(".actual")
    tabAnterior.removeClass("actual")

    /* resalta el tab actual */
    const tab = $(`[data-paso="${paso}"]`)
    tab.addClass("actual")

    botonesPaginador()
    if(paso === 3) mostrarResumen()
}

function botonesPaginador() {
    const paginaAnterior = $("#anterior")
    const paginaSiguiente = $("#siguiente")

    if(paso === 1) {
        paginaAnterior.addClass("ocultar")
        paginaSiguiente.removeClass("ocultar")
    } else if(paso === 3) {
        paginaSiguiente.addClass("ocultar")
        paginaAnterior.removeClass("ocultar")
    } else {
        paginaSiguiente.removeClass("ocultar")
        paginaAnterior.removeClass("ocultar")
    }
}

function paginaAnterior() {
    $("#anterior").on("click", function() { 
        paso-- 
        mostrarSeccion()
    })    
}
function paginaSiguiente() {
    $("#siguiente").on("click", function() { 
        paso++ 
        mostrarSeccion()
    })    
}


/* otras */
function cl(foo) {
    console.log(foo)
}