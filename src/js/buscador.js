$(function() {
    iniciarAPP()
})
function iniciarAPP() {
    buscarPorFecha()
}

function buscarPorFecha() {
    const fechaInput = $("#fecha")
    fechaInput.on("input", function() {
        const fechaSeleccionada = $(this).val()
        window.location.href = `?fecha=${fechaSeleccionada}`
    })
}

/* otras */
function cl(algo) {
    console.log(algo)
}