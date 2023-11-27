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


var selects = document.querySelectorAll('.select-product.create');
selects.forEach(function (select) {
    $(select).on('change.select2', function () {
        agregarProducto(select);
    });
});
// Cantidad de productos (le resto uno porque es el de 'Seleccionar producto')
var cantProducts = selects[0].options.length - 1


// Contador para generar nombres únicos
var counterProducts = 1;
// Función para agregar dinámicamente campos de entrada
function agregarProducto(selectAnterior) {

    //Elimino el select en caso de que la opcion elegida sea 'Seleccionar producto'
    if ($(selectAnterior).val() == '0' || $(selectAnterior).val() == null) {
        //Elimino el select siempre y cuando no sea el ultimo de todos ni el primero
        if (selectAnterior != selects[selects.length - 1] && selectAnterior != selects[0]) {
            selectAnterior.parentNode.parentNode.remove()
            selects = document.querySelectorAll('.select-product.create');

            let numberOfProducts = document.querySelectorAll('.container__number-of-product')
            let i = 1;
            numberOfProducts.forEach(numberOfProducts => {
                numberOfProducts.innerText = `${i}.`
                i++
            })
        }
    }

    
    // Si todos los select están llenos, agregar una nueva fila
    if (validarParaAgregarSelect()) {
        axios.post('../../api/orders/get_products.php')
            .then(response => {
                // Incrementar el contador para el próximo conjunto de campos
                counterProducts++;

                if (response.data.success) {

                    // Obtener el contenedor donde se agregarán los campos
                    var contenedor = document.getElementById("create-product");

                    // Obtener el contenedor donde se agregarán los campos
                    var inputGroupHtml =
                        `<div id="container__product-${counterProducts}">
                            <div class="container__number-of-product">${selects.length + 1}.</div>
                            <div class="container__product-orders create">
                                <input type="number" class="modal-input input__cant-product create" id="create-product--number-${counterProducts}">
                                <select class="modal-input select-product create" id="create-product--select-${counterProducts}">
                                    <option value="0" selected>Seleccione un producto</option>`

                    response.data.products.forEach(product => {
                        inputGroupHtml +=
                                    `<option value="${product.id}">${product.nombre}</option>`
                    })

                    inputGroupHtml += `
                                </select>
                            </div>
                        </div>`

                    // Agregar elemento al contenedor
                    contenedor.insertAdjacentHTML('beforeend', inputGroupHtml);

                    // Agregar el evento change al nuevo select
                    var nuevoSelect = document.getElementById(`create-product--select-${counterProducts}`);
                    $(nuevoSelect).select2({
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

                    $(nuevoSelect).on('change.select2', function () {
                        agregarProducto(nuevoSelect);
                    });

                    
                    selects = document.querySelectorAll('.select-product.create');
                } else {
                    alertMsj('Ocurrió un error', 'error')
                }
            })
            .catch(error => {
                alertMsj('Ocurrió un error inesperado. Intentelo más tarde', 'error')
                console.log("Error atrapado:", error);
            });
        
    }
}

function validarParaAgregarSelect(){
    let ok = true;
    try {
        selects.forEach(function (selectVerify) {
            // Verifica si el valor seleccionado es 0 o ninguno
            if ($(selectVerify).val() == '0' || $(selectVerify).val() == null) {
                console.log("a")
                // Realiza la acción que desees si cumple la condición
                ok = false
                throw BreakException
            }
        });
    } catch (e) {
        return ok;
    }
    
    //Verifica que la cantidad de selects no supere o iguale a la cantidad de productos
    if (cantProducts <= selects.length) {
        ok = false
    }
    return ok;
}



// CHECKBOX ENVIO
var isWithDelivery = document.getElementById('delivery')
isWithDelivery.addEventListener('click', () => {
    if (isWithDelivery.checked) {
        //Es CON envio a domicilio
        var inputsDeliveryHtml =
            `<div class="modal-inputs-double">
                <div class="modal-input-group">
                    <label>Provincia</label>
                    <select class="modal-input select-province create" id="create-province">
                        <option value="0" disabled hidden selected>Seleccione la provincia</option>
                        <option value="1" >Ciudad Autónoma de Buenos Aires</option>
                        <option value="2" >Buenos Aires</option>
                    </select>
                    <p class="errormessage__form"></p>
                </div>
                <div class="modal-input-group">
                    <label>Ciudad</label>
                    <input type="text" id="create-city" class="modal-input">
                    <p class="errormessage__form"></p>
                </div>
            </div>
            <div class="modal-input-group">
                <label>Dirección</label>
                <input type="text" id="create-address" class="modal-input">
                <p class="errormessage__form"></p>
            </div>`
        
        isWithDelivery.parentNode.parentNode.parentNode.insertAdjacentHTML('afterend', inputsDeliveryHtml)
        $('#create-province').select2({
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
    } else {
        //Es SIN envio a domicilio
        document.getElementById('create-province').parentNode.parentNode.remove()
        document.getElementById('create-address').parentNode.remove()
    }
})


//CREACION DEL PRODUCTO
document.getElementById('create-submit').addEventListener('click', (e) => {
    const name = document.getElementById(`create-name`)
    const surname = document.getElementById(`create-surname`)
    const dni = document.getElementById(`create-dni`)
    const tel = document.getElementById(`create-tel`)
    const state = document.getElementById(`create-state`)
    const productsContainer = document.querySelectorAll('.container__product-orders.create');
    const delivery = document.getElementById(`delivery`)
    
    var province = null;
    var city = null;
    var address = null; 
    if (delivery.checked) {
        province = document.getElementById('create-province');
        city = document.getElementById('create-city');
        address = document.getElementById('create-address'); 
    } 
    
    if (validations(name, surname, dni, tel, state, productsContainer, delivery.checked, province, city, address)) {
        var formData = new FormData();
        formData.append('name', name.value);
        formData.append('surname', surname.value);
        formData.append('dni', dni.value);
        formData.append('tel', tel.value);
        formData.append('state', $(state).val());
        formData.append('delivery', delivery.checked);
        formData.append('province', (province != null) ? $(province).val() : null);
        formData.append('city', (city != null) ? city.value : null);
        formData.append('address', (address != null) ? address.value : null);

        const selectedProducts = validateProducts(productsContainer);
        formData.append('products', JSON.stringify(selectedProducts));

        console.log(formData.get('products'))
        axios.post('../../api/orders/create_order.php', formData)
            .then(response => {
                // Manejar la respuesta exitosa aquí
                console.log(response.data)
                if (response.data.success) {
                    location.reload();

                } else {
                    successValidation(name)
                    successValidation(surname)
                    successValidation(dni)
                    successValidation(tel)
                    successValidation(state)
                    if (delivery) {
                        successValidation(province)
                        successValidation(city)
                        successValidation(address)
                    }
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

//ACTUALIZAR DATOS DEL PRODUCTO
document.getElementById('update-submit').addEventListener('click', (e) => {
    //---
})

function validations(name, surname, dni, tel, state, productsContainer, isDelivery = false, province = null, city = null, address = null) {
    var isValid = true;

    //NOMBRE
    if (!name.value.trim()) {
        failValidation(name, 'Debe ingresar un nombre.')
        isValid = false;
    } else if (name.value.length > 35) {
        failValidation(name, 'Debe ingresar un nombre menor a 35 caracteres.')
        isValid = false;
    } else {
        successValidation(name)
    }

    //APELLIDO
    if (!surname.value.trim()) {
        failValidation(surname, 'Debe ingresar un apellido.')
        isValid = false;
    } else if (surname.value.length > 35) {
        failValidation(surname, 'Debe ingresar un apellido menor a 35 caracteres.')
        isValid = false;
    } else {
        successValidation(surname)
    }

    //DNI
    if (!/^\d{7,8}$/.test(dni.value.trim())) {
        failValidation(dni, 'Debe ingresar un DNI válido')
        isValid = false;
    } else {
        successValidation(dni)
    }

    //TELEFONO
    if (!/^\d{8,}$/g.test(tel.value.trim())) {
        failValidation(tel, 'Debe ingresar un telefono válido.')
        isValid = false;
    } else {
        successValidation(tel)
    }

    //ESTADO
    if (!$(state).val() == null || $(state).val() == 0) {
        failValidation(state, 'Debe seleccionar un estado para el pedido.')
        isValid = false;
    } else {
        successValidation(state)
    }

    //PRODUCTOS
    if (!validateProducts(productsContainer)){
        isValid = false;
    }

    if (isDelivery) {
        //PROVINCIA
        if (!$(province).val() == null || $(province).val() == 0) {
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
    }

    return isValid;
}

function validateProducts(productsContainer, typeModal) {
    var valores = [];
    var cantidadInputs;
    var selectProductos;

    for (var i = 0; i < productsContainer.length; i++) {
        cantidadInputs = productsContainer[i].querySelector(`input`);
        selectProductos = productsContainer[i].querySelector(`select`);

        // Verificar que al menos un select tenga un valor distinto de 0
        if ($(selectProductos).val() != 0 && $(selectProductos).val() != null) {
            // Verificar que la cantidad sea un número positivo
            if (!/^\d+$/.test(cantidadInputs.value) || cantidadInputs.value <= 0) {
                alertMsj('Debe ingresar una cantidad válida', 'error');
                return false;
            }

            valores.push({ id: $(selectProductos).val(), cantidad: cantidadInputs.value }); // Pasa la validación
            continue;// Pasa la validación
        }

        // No pasa la validación
        if (i == productsContainer.length - 1 && valores.length < 1) {
            alertMsj('Debe ingresar al menos un producto', 'error');
            return false;
        }
    }

    // Verificar que no haya valores duplicados
    var productosUnicos = [...new Set(valores.map(item => item.id))];
    if (productosUnicos.length !== valores.length) {
        alertMsj('No pueden repetirse los mismos productos', 'error');
        return false; // No pasa la validación
    }

    // Pasa ambas validaciones
    return valores;
}



//LIMPIAR MODALS
function clearModals(typeModal) {
    let name = document.getElementById(`${typeModal}-name`)
    let surname = document.getElementById(`${typeModal}-surname`)
    let dni = document.getElementById(`${typeModal}-dni`)
    let tel = document.getElementById(`${typeModal}-tel`)
    let state = document.getElementById(`${typeModal}-state`)
    let products = document.querySelectorAll(`.select-product.${typeModal}`)

    name.value = ""
    successValidation(name)
    surname.value = ""
    successValidation(surname)
    dni.value = ""
    successValidation(dni)
    tel.value = ""
    successValidation(tel)
    // Reemplazo el valor del select2
    $(state).val(1).trigger('change');
    successValidation(state)
    //Hago click en el delivery para que vaya al addeventlistener
    if (isWithDelivery.checked) {
        document.getElementById('create-province').parentNode.parentNode.remove()
        document.getElementById('create-address').parentNode.remove()
        isWithDelivery.checked = false
    }
    
    products.forEach(product => {
        if (product.id != `${typeModal}-product--select-1`) { 
            product.parentNode.parentNode.remove()
        } else {
            $(product).val('0').trigger('change');
            successValidation(product)
        }
    })

    counterProducts = 1;
    selects = document.querySelectorAll('.select-product.create');
}


//LIMPIAR MODALS UPDATE
function clearModalUpdate(formData) {
    axios.post('../../api/orders/get_order.php', formData)
        .then(response => {
            // Manejar la respuesta exitosa aquí
            console.log(response.data)

            if (response.data.success) {
                //Reemplazo todos los datos normales
                

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