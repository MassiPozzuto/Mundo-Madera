
$('select').select2({
    language: "es",
    matcher: function (params, data) {
        // Si la opción está deshabilitada u oculta, no es un candidato de coincidencia
        if (data.disabled || data.element.hidden) {
            return null;
        }

        // Aplica la lógica de búsqueda predeterminada para otras opciones
        return $.fn.select2.defaults.defaults.matcher(params, data);
    },
});

//ACTUALIZAR DATOS DEL PRODUCTO
document.getElementById('update-submit').addEventListener('click', (e) => {
    const id = document.getElementById('update-id');
    const newState = document.getElementById('update-state');
    const newProvince = document.getElementById('update-province');
    const newCity = document.getElementById('update-city');
    const newAddress = document.getElementById('update-address');


    if (validations(newState, newProvince, newCity, newAddress)) {
        var formData = new FormData();
        formData.append('id', id.value);
        formData.append('state', $(newState).val());
        formData.append('province', $(newProvince).val());
        formData.append('city', newCity.value);
        formData.append('address', newAddress.value);


        axios.post('../../api/deliveries/update_delivery.php', formData)
            .then(response => {
                // Manejar la respuesta exitosa aquí
                console.log(response.data)
                if (response.data.success) {
                    //location.reload();
                    alertMsj('El envio fue editado correctamente', 'success')

                    if ($(newState).val() == 7) {
                        console.log(id.value)
                        document.getElementById(`row__delivery-${id.value}`).querySelector('.btn__deliver').classList.add('delivered')
                        document.getElementById(`row__delivery-${id.value}`).querySelector('.btn__deliver').innerHTML =
                            `<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-x" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M18 6l-12 12" />
                                <path d="M6 6l12 12" />
                            </svg>`

                    } else {
                        document.getElementById(`row__delivery-${id.value}`).querySelector('.btn__deliver').classList.remove('delivered')
                        document.getElementById(`row__delivery-${id.value}`).querySelector('.btn__deliver').innerHTML =
                            `<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-check" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M5 12l5 5l10 -10" />
                            </svg>`

                    }

                    let rowDelivery = document.getElementById(`row__delivery-${id.value}`)
                    rowDelivery.querySelector('.celd-state_delivery').innerText = $(`#${newState.id} option:selected`).text()
                    rowDelivery.querySelector('.celd-province').innerText = $(`#${newProvince.id} option:selected`).text()
                    rowDelivery.querySelector('.celd-city').innerText = newCity.value
                    rowDelivery.querySelector('.celd-address').innerText = newAddress.value

                    document.getElementById('modal__update').style.display = 'none'
                } else {
                    Object.keys(response.data.errors).forEach(fieldId => {
                        const errorMessage = response.data.errors[fieldId];
                        const input = document.getElementById(fieldId);
                        if (input) {
                            failValidation(input, errorMessage);
                        }
                    });
                }
            })
            .catch(error => {
                alertMsj('Ocurrió un error inesperado. Intentelo más tarde', 'error')
                console.log("Error atrapado:", error);
            });
    }
})

function validations(state, province, city, address) {
    var isValid = true;

    //ESTADO
    if (!$(state).val() == null || $(state).val() == 0) {
        failValidation(state, 'Debe seleccionar un estado para el pedido.')
        isValid = false;
    } else {
        successValidation(state)
    }
    
    //PROVINCIA
    if ($(province).val() == null || $(province).val() == 0) {
        failValidation(province, 'Debe seleccionar una provincia para el envio.')
        isValid = false;
    } else {
        successValidation(province)
    }

    //CIUDAD
    if (!city.value.trim()) {
        failValidation(city, 'Debe ingresar un ciudad para el envio.')
        isValid = false;
    } else if (city.value.length > 50) {
        failValidation(city, 'Debe ingresar una ciudad menor a 50 caracteres.')
        isValid = false;
    } else {
        successValidation(city)
    }

    //DIRECCION
    if (!address.value.trim()) {
        failValidation(address, 'Debe ingresar una dirección de envio.')
        isValid = false;
    } else if (address.value.length > 50) {
        failValidation(address, 'Debe ingresar una direccion menor a 50 caracteres.')
        isValid = false;
    } else {
        successValidation(address)
    }

    return isValid;
}

//LIMPIAR MODALS
function clearModals(typeModal) {
    let city = document.getElementById(`${typeModal}-city`)
    let address = document.getElementById(`${typeModal}-address`)

    city.value = ""
    successValidation(city)
    address.value = ""
    successValidation(address)
    // Reemplazo el valor del select2
    $(`#${typeModal}-state`).val(0).trigger('change');
    $(`#${typeModal}-province`).val(0).trigger('change');
}

//LIMPIAR MODALS UPDATE
function clearModalUpdate(formData) {
    axios.post('../../api/deliveries/get_delivery.php', formData)
        .then(response => {
            // Manejar la respuesta exitosa aquí
            console.log(response.data)

            if (response.data.success) {
                //Reemplazo todos los datos normales
                document.getElementById('update-id').value = response.data.delivery.id
                document.getElementById('update-city').value = response.data.delivery.ciudad
                document.getElementById('update-address').value = response.data.delivery.direccion

                // Reemplazo el valor del select2
                $('#update-state').val(response.data.delivery.id_estado).trigger('change');
                $('#update-province').val(response.data.delivery.id_provincia).trigger('change');


                document.getElementById('modal__update').style.display = "block";
            } else {
                alertMsj(response.data.error, 'error')
            }
        })
        .catch(error => {
            alertMsj('Ocurrió un error inesperado. Intentelo más tarde', 'error')
            console.log("Error atrapado:", error);
        });
}


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