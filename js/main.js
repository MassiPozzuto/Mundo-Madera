
function crearModalConfirmacion(title, texto) {
    // Crear el modal y el contenido
    var modal = `<div class="modal" style="display: block;">
                    <div class="modal-content">
                        <div class="modal-info">
                            <h3>${title}</h3>
                            <p>${texto}</p>
                        </div>
                        <div class="modal-btns">
                            <button class="modal-btn cancel" id="modal-cancel">Cancelar</button>
                            <button class="modal-btn confirm" id="modal-confirm">Aceptar</button>
                        </div>
                    </div>
                </div>`
    document.body.insertAdjacentHTML('beforeend', modal);


    document.getElementById('modal-cancel').onclick = function () {
        console.log('Cancelar clicado');
        // Cerrar el modal
        document.querySelector('.modal').style.display = "none"
    };

    // AGREGAR LOS ADDEVENTLISTENER DEL BOTON DE CONFIRMAR
}



// Cerrar el modal si se hace clic fuera del contenido del modal
window.onclick = function (event) {
    var modals = document.getElementsByClassName('modal');
    for (var i = 0; i < modals.length; i++) {
        if (event.target == modals[i]) {
            modals[i].style.display = "none"
        }
    }
}