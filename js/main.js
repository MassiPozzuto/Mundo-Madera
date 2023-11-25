// Cerrar el modal si se hace clic fuera del contenido del modal
window.onclick = function (event) {
    var modals = document.getElementsByClassName('modal');
    for (var i = 0; i < modals.length; i++) {
        if (event.target == modals[i]) {
            modals[i].style.display = "none"
        }
    }
}


const failValidation = (input, msj) => {
    const form_group = input.parentElement;
    const msj_error = form_group.querySelector("p");
    msj_error.style.display = "block";
    msj_error.innerText = msj;

    //Estilos para los select2
    if (input.nodeName.toLowerCase() === 'select') {
        var elemento = document.querySelector(`[aria-labelledby="select2-${input.id}-container"]`);
        elemento.style.cssText += 'border-color: red !important;';
    } 
    form_group.classList.remove("success")
    form_group.classList.add("fail")
};
const successValidation = (input) => {
    const form_group = input.parentElement;
    const msj_error = form_group.querySelector("p");
    msj_error.style.display = "none";
    msj_error.innerText = null;

    //Estilos para los select2
    if (input.nodeName.toLowerCase() === 'select') {
        var elemento = document.querySelector(`[aria-labelledby="select2-${input.id}-container"]`);
        elemento.style.cssText = elemento.style.cssText.replace('border-color: red !important;', '');
    } 
    form_group.classList.remove("fail")
    form_group.classList.add("success")
};

const alertMsj = (msj, type, time = 2000) => {
    const alertMsjHTML = `<div class="alert_msj msj_${type}">
                            <span>${msj}</span>
                            <span id="border_msj"></span>
                        </div>`
    document.querySelector('body').insertAdjacentHTML('afterend', alertMsjHTML);

    setTimeout(() => {
        // Eliminar la alerta después de 1 segundo
        document.querySelector('.alert_msj').remove()
    }, time);
    
    // Ajustar la animación del borde según el tiempo
    document.getElementById('border_msj').style.animationDuration = `${time + 3}ms`;
};






/**********  TEMPORAL  **************/

window.onload = function () {
    //ABRIR MODAL DE CREAR
    document.getElementById('btn__open-create').addEventListener('click', () => {
        document.getElementById('modal__create').style.display = 'block'
    })
    
    // Encuentra el contenedor de tabla o el elemento más cercano que esté presente desde el principio
    var tableBody = document.getElementById('table_body');
    // Agrega un escuchador de eventos al contenedor
    tableBody.addEventListener('click', function (event) {
        // Buscar el botón padre, que debería ser el botón de actualización o eliminación
        var button = event.target.closest('.btn__update, .btn__delete');
    
        if (button) {
            const idItem = button.id.split("-")[1]
            var formData = new FormData();
            formData.append('id', idItem);
    
            // Verifica si el clic se hizo en un botón de actualizacion o de eliminacion
            if (button.classList.contains('btn__update')) {
                //ABRIR MODAL DE ACTUALIZAR, REEMPLAZANDO SU CONTENIDO SEGUN EL ITEM SELECCIONADO
                clearModalUpdate(formData)
            } else if (button.classList.contains('btn__delete')) {
                //ELIMINAR ITEM
                deleteItem(formData, button)
            }
        }
    });
    
    //CERRAR MODALS
    var modalsClose = document.querySelectorAll('.modal-close')
    modalsClose.forEach(modalClose => {
        modalClose.addEventListener('click', () => {
            const idModal = modalClose.id.split("--")[1]
            document.getElementById(idModal).style.display = 'none'
    
            //LIMPIO LOS INPUTS DE LOS MODALS
            if (idModal.includes('create')) {
                clearModals('create')
            } else {
                clearModals('update')
            }
        })
    })


    /************** FILTROS ******************/
    const URL_PARAMS = new URLSearchParams(window.location.search);
    //CAMBIAR PARAMETRO DE VER O NO ITEM ELIMINADOS
    document.getElementById('check-allow-deleted').addEventListener('click', (e) => {
        var newUrl;
        if (e.target.checked) {
            //Desea ver los eliminados
            URL_PARAMS.set('allowDeleted', 'yes')
            newUrl = `${window.location.pathname}?${URL_PARAMS.toString()}`;
        } else {
            //No desea ver los eliminados
            URL_PARAMS.set('allowDeleted', 'no')
            newUrl = `${window.location.pathname}?${URL_PARAMS.toString()}`;
        }

        window.location.href = newUrl;
    })
    //CAMBIAR PARAMETRO DE LA PAGINA ACTUAL
    var btnsPaginator = document.querySelectorAll('.btn__paginator')
    btnsPaginator.forEach(btnPaginator => {
        btnPaginator.addEventListener('click', () => {
            newPage = btnPaginator.id.split("-")[1]

            console.log(newPage)
            URL_PARAMS.set('page', newPage)
            var newUrl = `${window.location.pathname}?${URL_PARAMS.toString()}`;

            window.location.href = newUrl;
        })
    })
    //CAMBIAR PARAMETRO DE FILTROS
    var btnsFilterBy = document.querySelectorAll('.filter__by-item') || null
    if (btnsFilterBy) {
        btnsFilterBy.forEach(btnFilterBy => {
            btnFilterBy.addEventListener('click', () => {
                filterBy = btnFilterBy.id.split("-")[1]

                URL_PARAMS.set('filterBy', filterBy)
                var newUrl = `${window.location.pathname}?${URL_PARAMS.toString()}`;

                window.location.href = newUrl;
            })
        })
    }
    /************** FILTROS ******************/






}

