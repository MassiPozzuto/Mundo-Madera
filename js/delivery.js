
//ENTREGAR PEDIDO
function deliverItem(formData, button) {
    axios.post('../../api/orders/deliver_order.php', formData)
        .then(response => {
            // Manejar la respuesta exitosa aquí
            console.log(response.data)

            if (response.data.success) {
                //Pedido eliminado correctamente
                alertMsj(response.data.msj, 'success')

                if (response.data.success) {
                    if (response.data.action == 'delivered') {
                        //Si la accion fue eliminar el pedido
                        button.classList.add('delivered')
                        button.innerHTML =
                            `<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-x" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M18 6l-12 12" />
                                <path d="M6 6l12 12" />
                            </svg>`
                        
                        button.parentNode.parentNode.querySelector('.celd-state_order').innerText = 'Entregado'
                        button.parentNode.parentNode.querySelector('.celd-state_delivery').innerText = 'Entregado'
                    } else {
                        //Si la accion fue recuperar el pedido
                        button.classList.remove('delivered')
                        button.innerHTML =
                            `<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-check" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M5 12l5 5l10 -10" />
                            </svg>`
                        button.parentNode.parentNode.querySelector('.celd-state_order').innerText = 'En proceso'
                        button.parentNode.parentNode.querySelector('.celd-state_delivery').innerText = 'Pendiente'
                    }
                }
            } else {
                //No se pudo eliminar el pedido
                alertMsj(response.data.msj, 'error')
            }
        })
        .catch(error => {
            alertMsj('Ocurrió un error inesperado. Intentelo más tarde', 'error')
            console.log("Error atrapado:", error);
        });
}