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