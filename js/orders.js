// Igual que la del main pero modifico que si el modal es update tambien se limpie
window.onclick = function (event) {
    var modals = document.getElementsByClassName('modal');
    for (var i = 0; i < modals.length; i++) {
        if (event.target == modals[i]) {
            modals[i].style.display = "none"
            if (modals[i].id.includes('update')) {
                clearModals('update')
            }
        }
    }
}

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


var selectsCreate = document.querySelectorAll('.select-product.create');

selectsCreate.forEach(function (select) {
    $(select).on('change.select2', function () {
        agregarProducto(select, selectsCreate, 'create');
    });
});
// Cantidad de productos (le resto uno porque es el de 'Seleccionar producto')
var cantProducts = selectsCreate[0].options.length - 1

//OBTENER UN ARRAY CON TODOS LOS PRODUCTOS
var listOfProducts;
axios.post('../../api/orders/get_products.php')
    .then(response => {
        if (response.data.success) {
            listOfProducts = response.data.products
        } else {
            alertMsj('Ocurrió un error', 'error')
        }
    })
    .catch(error => {
        alertMsj('Ocurrió un error inesperado. Intentelo más tarde', 'error')
        console.log("Error atrapado:", error);
    });


// Contador para generar nombres únicos
var counterProducts = 1;
// Función para agregar dinámicamente campos de entrada
function agregarProducto(selectAnterior, selects, typeModal) {
    //Elimino el select en caso de que la opcion elegida sea 'Seleccionar producto'
    deleteSelect(selectAnterior, selects, typeModal)

    // Si todos los select están llenos, agregar una nueva fila
    if (validarParaAgregarSelect(selects)) {
        //Incremento en uno la cantidad de productos
        counterProducts++;
        createSelectProduct(listOfProducts, typeModal)
    }

    if (typeModal == 'create') {
        selectsCreate = document.querySelectorAll('.select-product.create');
    } else {
        selectsUpdate = document.querySelectorAll('.select-product.update');
    }
}

function deleteSelect(select, selects, typeModal) {
    if ($(select).val() == '0' || $(select).val() == null) {
        //Elimino el select siempre y cuando no sea el ultimo de todos ni el primero
        if (select != selects[selects.length - 1] && select != selects[0]) {
            select.parentNode.parentNode.remove()
            selects = document.querySelectorAll(`.select-product.${typeModal}`);

            //Para que los numeritos de arriba de los form-group de productos este en orden
            let numberOfProducts = document.getElementById(`${typeModal}-product`).querySelectorAll('.container__number-of-product')
            let i = 1;
            numberOfProducts.forEach(numberOfProducts => {
                numberOfProducts.innerText = `${i}.`
                i++
            })
        }
    }
}

//VALIDO SELECTS PARA AGREGAR OTRO SELECT
function validarParaAgregarSelect(selects) {
    let ok = true;
    try {
        selects.forEach(function (selectVerify) {
            // Verifica si el valor seleccionado es 0 o ninguno
            if ($(selectVerify).val() == '0' || $(selectVerify).val() == null) {
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

function createSelectProduct(products, typeModal) {
    
    // Obtener el contenedor donde se agregarán los campos
    var contenedor = document.getElementById(`${typeModal}-product`);

    // Obtener el contenedor donde se agregarán los campos
    var inputGroupHtml =
        `<div id="container__product-${counterProducts}">
            <div class="container__number-of-product">${(typeModal == 'create') ? selectsCreate.length + 1 : selectsUpdate.length + 1}.</div>
            <div class="container__product-orders ${typeModal}">
                <input type="number" class="modal-input input__cant-product ${typeModal}" id="${typeModal}-product--number-${counterProducts}" >
                <select class="modal-input select-product ${typeModal}" id="${typeModal}-product--select-${counterProducts}">
                    <option value="0" selected>Seleccione un producto</option>`

    if (typeModal == 'create') {
        products.forEach(product => {
            inputGroupHtml +=
                        `<option value="${product.id}" aria-stock="${product.stock}">${product.nombre}</option>`
        })
    } else {
        products.forEach(product => {
            inputGroupHtml +=
                `<option value="${product.id}" aria-stock="${parseInt(product.stock) + ((amtProducts[product.id] != undefined) ? parseInt(amtProducts[product.id]) : 0) }">${product.nombre}</option>`
        })
    }

    inputGroupHtml += `
                </select>
                <p class="errormessage__form for-stock"></p>
                <p class="errormessage__form for-product-name"></p>
            </div>
        </div>`

    // Agregar elemento al contenedor
    contenedor.insertAdjacentHTML('beforeend', inputGroupHtml);

    // Agregar el evento change al nuevo select
    var nuevoSelect = document.getElementById(`${typeModal}-product--select-${counterProducts}`);
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
        if (typeModal == 'create') {
            agregarProducto(nuevoSelect, selectsCreate, 'create');
        } else {
            agregarProducto(nuevoSelect, selectsUpdate, 'update');
        }
    });
}


//CREACION DEL PRODUCTO
document.getElementById('create-submit').addEventListener('click', (e) => {
    const name = document.getElementById(`create-name`)
    const surname = document.getElementById(`create-surname`)
    const dni = document.getElementById(`create-dni`)
    const tel = document.getElementById(`create-tel`)
    const state = document.getElementById(`create-state`)
    const productsContainer = document.querySelectorAll('.container__product-orders.create');
    const delivery = document.getElementById(`create-delivery`)
    
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
                    if (response.data.errors.general != null) {
                        alertMsj(response.data.errors.general, 'error')
                    }
                    successValidation(name)
                    successValidation(surname)
                    successValidation(dni)
                    successValidation(tel)
                    successValidation(state)
                    if (delivery.checked) {
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
    const newName = document.getElementById(`update-name`)
    const newSurname = document.getElementById(`update-surname`)
    const newDni = document.getElementById(`update-dni`)
    const newTel = document.getElementById(`update-tel`)
    const newState = document.getElementById(`update-state`)
    const newProductsContainer = document.querySelectorAll('.container__product-orders.update');
    const newDelivery = document.getElementById(`update-delivery`)

    var newProvince = null;
    var newCity = null;
    var newAddress = null;
    if (newDelivery.checked) {
        newProvince = document.getElementById('update-province');
        newCity = document.getElementById('update-city');
        newAddress = document.getElementById('update-address');
    }

    if (validations(newName, newSurname, newDni, newTel, newState, newProductsContainer, newDelivery.checked, newProvince, newCity, newAddress)) {
        var formData = new FormData();
        formData.append('id', document.getElementById(`update-id`).value);
        formData.append('name', newName.value);
        formData.append('surname', newSurname.value);
        formData.append('dni', newDni.value);
        formData.append('tel', newTel.value);
        formData.append('state', $(newState).val());
        formData.append('delivery', newDelivery.checked);
        formData.append('province', (newProvince != null) ? $(newProvince).val() : null);
        formData.append('city', (newCity != null) ? newCity.value : null);
        formData.append('address', (newAddress != null) ? newAddress.value : null);

        const selectedProducts = validateProducts(newProductsContainer);
        formData.append('products', JSON.stringify(selectedProducts));

        axios.post('../../api/orders/update_order.php', formData)
            .then(response => {
                // Manejar la respuesta exitosa aquí
                console.log(response.data);
                if (response.data.success) {
                    location.reload();
                } else {
                    if (response.data.errors.general != null) {
                        alertMsj(response.data.errors.general, 'error');
                    }
                    successValidation(newName);
                    successValidation(newSurname);
                    successValidation(newDni);
                    successValidation(newTel);
                    successValidation(newState);
                    if (newDelivery.checked) {
                        successValidation(newProvince);
                        successValidation(newCity);
                        successValidation(newAddress);
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
                alertMsj('Ocurrió un error inesperado. Intentelo más tarde', 'error');
                console.log("Error atrapado:", error);
            });
    }
})



//LIMPIAR MODALS
function clearModals(typeModal) {
    let name = document.getElementById(`${typeModal}-name`)
    let surname = document.getElementById(`${typeModal}-surname`)
    let dni = document.getElementById(`${typeModal}-dni`)
    let tel = document.getElementById(`${typeModal}-tel`)
    let state = document.getElementById(`${typeModal}-state`)
    let products = document.querySelectorAll(`.select-product.${typeModal}`)
    let delivery = document.getElementById(`${typeModal}-delivery`)

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
    if (delivery.checked) {
        document.getElementById(`${typeModal}-delivery`).checked = false
        document.getElementById(`${typeModal}-info-delivery`).style.display = 'none'
        $(`#${typeModal}-province`).val(0).trigger('change')
        document.getElementById(`${typeModal}-city`).value = ""
        document.getElementById(`${typeModal}-address`).value = ""
    }
    
    products.forEach(product => {
        if (product.id != `${typeModal}-product--select-1`) { 
            product.parentNode.parentNode.remove()
        } else {
            $(product).val('0').trigger('change');
            successValidation(product, 'p.for-product-name')
            let cantOfProduct = document.getElementById(`${typeModal}-product--number-1`)
            cantOfProduct.value = "";
            successValidation(cantOfProduct, 'p.for-stock')
        }
    })

    counterProducts = 1;
    if (typeModal == 'create') {
        selectsCreate = document.querySelectorAll('.select-product.create');
    } else {
        selectsUpdate = document.querySelectorAll('.select-product.update');
    }
}



var selectsUpdate = document.querySelectorAll('.select-product.update');

var amtProducts;
//LIMPIAR MODALS UPDATE
function clearModalUpdate(formData) {
    axios.post('../../api/orders/get_order.php', formData)
        .then(response => {
            // Manejar la respuesta exitosa aquí
            console.log(response.data)

            if (response.data.success) {
                //Reemplazo todos los datos normales
                document.getElementById('update-id').value = response.data.order.id_pedido
                document.getElementById('update-name').value = response.data.order.nombre
                document.getElementById('update-surname').value = response.data.order.apellido
                document.getElementById('update-dni').value = response.data.order.dni
                document.getElementById('update-tel').value = response.data.order.telefono
                
                $('#update-state').val(response.data.order.id_estado).trigger('change')

                //Productos
                var products = response.data.order.productos.split(",");
                amtProducts = products.reduce((accumulator, product) => {
                    var propProduct = product.split(":");
                    accumulator[propProduct[0]] = propProduct[1];
                    return accumulator;
                }, {});;
                

                for (let i = 0; i < products.length; i++){
                    var propProduct = products[i].split(":")

                    if (i > 0) {
                        agregarProducto(document.getElementById(`update-product--select-${i}`), selectsUpdate, 'update');
                    }
                    document.getElementById(`update-product--number-${i + 1}`).value = propProduct[1];
                    $(`#update-product--select-${i + 1}`).val(propProduct[0]).trigger('change.select2');
                }

                selectsUpdate.forEach(function (select) {
                    $(select).on('change.select2', function () {
                        agregarProducto(select, selectsUpdate, 'update');
                    });
                });

                
                //Cambio el stock de las options del select principal, para sumarle la cantidad que ya tiene de dicho stock
                var optionsPrincipalSelectCreate = document.getElementById(`create-product--select-1`).querySelectorAll('option')
                var stockProduct = [];
                optionsPrincipalSelectCreate.forEach(optionPrincipalSelectCreate => {
                    if (optionPrincipalSelectCreate.value > 0) {
                        stockProduct[optionPrincipalSelectCreate.value] = parseInt(optionPrincipalSelectCreate.getAttribute('aria-stock'))
                    }
                })
                var optionsPrincipalSelect = document.getElementById(`update-product--select-1`).querySelectorAll('option')
                optionsPrincipalSelect.forEach(optionPrincipalSelect => {
                    if (optionPrincipalSelect.value > 0) {
                        let stockAndItsQuantity = stockProduct[optionPrincipalSelect.value] + ((amtProducts[optionPrincipalSelect.value] != undefined) ? parseInt(amtProducts[optionPrincipalSelect.value]) : 0)
                        optionPrincipalSelect.setAttribute('aria-stock', stockAndItsQuantity)
                    }
                })

                //Envio
                if (response.data.order.id_envio != null) {
                    document.getElementById('update-delivery').checked = true
                    document.getElementById('update-info-delivery').style.display = 'flex'
                    $('#update-province').val(response.data.order.id_provincia).trigger('change')
                    document.getElementById('update-city').value = response.data.order.ciudad
                    document.getElementById('update-address').value = response.data.order.direccion
                } else {
                    document.getElementById('update-delivery').checked = false
                    document.getElementById('update-info-delivery').style.display = 'none'
                    $('#update-province').val(0).trigger('change')
                    document.getElementById('update-city').value = ""
                    document.getElementById('update-address').value = ""
                }

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


// ENVIOS
var checkboxDeliveries = document.querySelectorAll('.check-delivery')
checkboxDeliveries.forEach(checkboxDelivery => {
    checkboxDelivery.addEventListener('click', () => {
        let typeModal = checkboxDelivery.id.split("-")[0]
        if (checkboxDelivery.checked) {
            //Es CON envio a domicilio
            document.getElementById(`${typeModal}-info-delivery`).style.display = 'flex'
        } else {
            //Es SIN envio a domicilio
            document.getElementById(`${typeModal}-info-delivery`).style.display = 'none'
            $(`#${typeModal}-province`).val(0).trigger('change')
            document.getElementById(`${typeModal}-city`).value = ""
            document.getElementById(`${typeModal}-address`).value = ""
        }
    })
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
    if (!validateProducts(productsContainer)) {
        isValid = false;
    }

    if (isDelivery) {
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
    }

    return isValid;
}

function validateProducts(productsContainer) {
    let cantValid = true;
    var valores = [];
    var cantidadInput;
    var selectProductos;
    var optionSelected;
    for (var i = 0; i < productsContainer.length; i++) {
        cantidadInput = productsContainer[i].querySelector(`input`);
        selectProductos = productsContainer[i].querySelector(`select`);
        optionSelected = $(selectProductos).select2('data')[0]

        // Verificar que el select no sea 0
        if ($(selectProductos).val() != 0 && $(selectProductos).val() != null) {
            // Verificar que la cantidad sea un número positivo
            if (cantidadInput.value < 1) {
                failValidation(cantidadInput, 'Ingresa una cantidad', 'p.for-stock')
                cantValid = false;
            }
            if (cantidadInput.value > parseInt(optionSelected.element.getAttribute('aria-stock'))) {
                failValidation(cantidadInput, `Stock: ${optionSelected.element.getAttribute('aria-stock')}`, 'p.for-stock')
                cantValid = false;
            }

            if (cantValid) {
                successValidation(cantidadInput, 'p.for-stock')
            }
            successValidation(selectProductos, 'p.for-product-name')

            valores.push({ id: $(selectProductos).val(), cantidad: cantidadInput.value }); // Pasa la validación
            continue;// Pasa la validación
        }

        // Si no hay selectsCreate distintos a 0
        if (i == productsContainer.length - 1 && valores.length < 1) {
            failValidation(cantidadInput, 'Ingresa una cantidad', 'p.for-stock')
            failValidation(selectProductos, 'Debe seleccionar mínimo un producto', 'p.for-product-name')
            return false;
        }
    }

    // Verificar que no haya valores duplicados
    var productosUnicos = [...new Set(valores.map(item => item.id))];
    if (productosUnicos.length !== valores.length) {
        alertMsj('No pueden repetirse los mismos productos', 'error');
        return false; // No pasa la validación
    }

    if (!cantValid) {
        return cantValid
    }
    // Pasa ambas validaciones
    return valores;
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
                        button.parentNode.parentNode.querySelector('.celd-state').innerText = 'Entregado'
                    } else {
                        //Si la accion fue recuperar el pedido
                        button.classList.remove('delivered')
                        button.innerHTML =
                            `<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-check" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M5 12l5 5l10 -10" />
                            </svg>`
                        button.parentNode.parentNode.querySelector('.celd-state').innerText = 'En proceso'
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