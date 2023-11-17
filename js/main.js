const failValidation = (input, msj) => {
    const form_group = input.parentElement;
    const msj_error = form_group.querySelector("p");
    msj_error.style.display = "block";
    msj_error.innerText = msj;
    form_group.classList.remove("success")
    form_group.classList.add("fail")
};
const successValidation = (input) => {
    const form_group = input.parentElement;
    const msj_error = form_group.querySelector("p");
    msj_error.style.display = "none";
    msj_error.innerText = null;
    form_group.classList.remove("fail")
    form_group.classList.add("success")
};


// Cerrar el modal si se hace clic fuera del contenido del modal
window.onclick = function (event) {
    var modals = document.getElementsByClassName('modal');
    for (var i = 0; i < modals.length; i++) {
        if (event.target == modals[i]) {
            modals[i].style.display = "none"
        }
    }
}