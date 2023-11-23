const urlParams = new URLSearchParams(window.location.search);


const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
const maxSize = 5 * 1024 * 1024; // 5 MB en bytes

//SELECT2 PARA LAS CATEGORIAS
$('.select-category').select2({
    language: "es",
    matcher: function (params, data) {
        // Si la opción está deshabilitada u oculta, no es un candidato de coincidencia
        if (data.disabled || data.element.hidden) {
            return null;
        }

        // Aplica la lógica de búsqueda predeterminada para otras opciones
        return $.fn.select2.defaults.defaults.matcher(params, data);
    }
});

//CERRAR MODALS
var modalsClose = document.querySelectorAll('.modal-close')
modalsClose.forEach(modalClose => {
    modalClose.addEventListener('click', () => {
        const idModal = modalClose.id.split("--")[1]
        document.getElementById(idModal).style.display = 'none'

        //LIMPIO LOS INPUTS DE LOS MODALS
        if (idModal.includes('new')) {
            clearModals('new__product')
        } else {
            clearModals('update__product')
        }
    })
})
function clearModals(typeModal) {
    document.getElementById(`${typeModal}-name`).value = ""
    document.getElementById(`${typeModal}-stock`).value = ""
    document.getElementById(`${typeModal}-price`).value = ""
    // Reemplazo el valor del select2
    $(`#${typeModal}-cat`).val(0).trigger('change');
    //Reemplazo la imagen si existe
    document.getElementById(`${typeModal}-preview-img`).parentNode.style.display = 'none';
    document.getElementById(`${typeModal}-preview-img`).src = '';
    document.getElementById(`${typeModal}-img`).value = '';
}

// Encuentra el contenedor de tabla o el elemento más cercano que esté presente desde el principio
var tableBody = document.getElementById('table_body');

// Agrega un escuchador de eventos al contenedor
tableBody.addEventListener('click', function (event) {
    // Buscar el botón padre, que debería ser el botón de actualización o eliminación
    var button = event.target.closest('.btn__update-product, .btn__delete-product');
    
    if (button) {
        const idProduct = button.id.split("-")[1]
        var formData = new FormData();
        formData.append('id', idProduct);

        // Verifica si el clic se hizo en un botón de actualizacion o de eliminacion
        if (button.classList.contains('btn__update-product')) {
            //ABRIR MODAL DE ACTUALIZAR PRODUCTO, REEMPLAZANDO SU CONTENIDO SEGUN EL PRODUCTO SELECCIONADO
            axios.post('../../api/products/get_product.php', formData)
                .then(response => {
                    // Manejar la respuesta exitosa aquí
                    console.log(response.data)

                    if (response.data.success) {
                        //Reemplazo todos los datos normales
                        document.getElementById('update__product-id').value = response.data.product.id
                        document.getElementById('update__product-name').value = response.data.product.nombre
                        document.getElementById('update__product-stock').value = response.data.product.stock
                        document.getElementById('update__product-price').value = response.data.product.precio
                        // Reemplazo el valor del select2
                        $('#update__product-cat').val(response.data.product.id_categoria).trigger('change');

                        //Reemplazo la imagen si existe
                        if (response.data.product.rutaImg != null) {
                            document.getElementById('update__product-preview-img').parentNode.style.display = 'block';
                            document.getElementById('update__product-preview-img').src = `${response.data.product.rutaImg}`;
                        } else {
                            document.getElementById('update__product-preview-img').parentNode.style.display = 'none';
                            document.getElementById('update__product-preview-img').src = '';
                        }
                        document.getElementById('update__product-img').value = '';

                        document.getElementById('modal__update-product').style.display = "block";
                    } else {
                        alertMsj(response.data.error, 'error')
                    }
                })
                .catch(error => {
                    alertMsj('Ocurrió un error inesperado. Intentelo más tarde', 'error')
                    console.log("Error atrapado:", error);
                });
            
        } else if (button.classList.contains('btn__delete-product')) {
            //ELIMINAR PRODUCTO
            axios.post('../../api/products/delete_product.php', formData)
                .then(response => {
                    // Manejar la respuesta exitosa aquí
                    console.log(response.data)

                    if (response.data.success) {
                        //Producto eliminado correctamente
                        alertMsj(response.data.msj, 'success')

                        if (response.data.success) {
                            if (response.data.action == 'delete') {
                                //Si la accion fue eliminar el producto
                                button.classList.add('deleted')
                                button.innerHTML =
                                    `<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-back-up" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M9 14l-4 -4l4 -4" />
                                        <path d="M5 10h11a4 4 0 1 1 0 8h-1" />
                                    </svg>`
                            } else {
                                //Si la accion fue recuperar el producto
                                button.classList.remove('deleted')
                                button.innerHTML =
                                    `<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-trash" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M4 7l16 0"></path>
                                        <path d="M10 11l0 6"></path>
                                        <path d="M14 11l0 6"></path>
                                        <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"></path>
                                        <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"></path>
                                    </svg>`
                            }
                        }
                    } else {
                        //No se pudo eliminar el producto
                        alertMsj(response.data.msj, 'error')
                    }
                })
                .catch(error => {
                    alertMsj('Ocurrió un error inesperado. Intentelo más tarde', 'error')
                    console.log("Error atrapado:", error);
                });
        }
    }
});



//ABRIR MODAL DE NUEVO PRODUCTO
document.getElementById('btn__open-new__product').addEventListener('click', () => {
    document.getElementById('modal__new-product').style.display = 'block'
})
//ABRIR INPUT FILE PARA AGREGAR IMG A LOS MODALS
btnsAddImg = document.querySelectorAll('.btn__add-img');
btnsAddImg.forEach(btnAddImg => {
    btnAddImg.addEventListener('click', () => {
        let typeModal = btnAddImg.id.split("-")[0];
        document.getElementById(`${typeModal}-img`).click();       
    })
})
//PREVIEW DE IMG EN LOS MODALS
inputsFileImg = document.querySelectorAll('.input__file-img');
inputsFileImg.forEach(inputFileImg => {
    inputFileImg.addEventListener('change', () => {
        let typeModal = inputFileImg.id.split("-")[0];

        const input = document.getElementById(`${typeModal}-img`);
        const previewImage = document.getElementById(`${typeModal}-preview-img`);

        const file = input.files[0];
        if (file) {
            // Validar el tipo de archivo
            if (!allowedTypes.includes(file.type)) {
                alertMsj('Por favor, selecciona una imagen JPEG, PNG o WEBP.', 'error');
                input.value = ''; // Limpiar el input file
                return;
            }
            // Validar el tamaño del archivo (5 MB)
            if (file.size > maxSize) {
                alertMsj('La imagen no debe superar los 5 MB.', 'error');
                input.value = ''; // Limpiar el input file
                return;
            }

            const reader = new FileReader();
            reader.onload = function (e) {
                previewImage.src = e.target.result;
            };
            reader.readAsDataURL(file);

            previewImage.parentNode.style.display = 'block';
        }
    })
})
//ELIMINAR PREVIEW DE IMAGEN EN LOS MODALS
var btnsDelete = document.querySelectorAll('.btn__remove-img');
btnsDelete.forEach(btnDelete => {
    btnDelete.addEventListener('click', () => {
        let typeModal = btnDelete.id.split("-")[0];

        const input = document.getElementById(`${typeModal}-img`);
        const previewImage = document.getElementById(`${typeModal}-preview-img`);

        input.value = ''; // Limpiar el input file
        previewImage.parentNode.style.display = 'none';
        previewImage.src = '';
    })
})


//CREACION DEL PRODUCTO
document.getElementById('new__product-submit').addEventListener('click', (e) => {
    const category = document.getElementById('new__product-cat');
    const name = document.getElementById('new__product-name');
    const stock = document.getElementById('new__product-stock');
    const price = document.getElementById('new__product-price');
    const img = document.getElementById('new__product-img');    

    if (validations(category, name, stock, price, img) == 5){
        var formData = new FormData();
        formData.append('category', category.value);
        formData.append('name', name.value);
        formData.append('stock', stock.value);
        formData.append('price', price.value);
        formData.append('img', img.files[0]);

        axios.post('../../api/products/create_product.php', formData)
            .then(response => {
                // Manejar la respuesta exitosa aquí
                console.log(response.data)
                if (response.data.success) {
                    location.reload();

                } else {
                    successValidation(category)
                    successValidation(name)
                    successValidation(stock)
                    successValidation(price)
                    successValidation(img)
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
document.getElementById('update__product-submit').addEventListener('click', (e) => { 
    const id = document.getElementById('update__product-id');
    const newCategory = document.getElementById('update__product-cat');
    const newName = document.getElementById('update__product-name');
    const newStock = document.getElementById('update__product-stock');
    const newPrice = document.getElementById('update__product-price');
    const newImg = document.getElementById('update__product-img');
    
    if (validations(newCategory, newName, newStock, newPrice) == 5) {
        var formData = new FormData();
        formData.append('id', id.value);
        formData.append('category', newCategory.value);
        formData.append('name', newName.value);
        formData.append('stock', newStock.value);
        formData.append('price', newPrice.value);
        formData.append('img', newImg.files[0]);

        axios.post('../../api/products/update_product.php', formData)
            .then(response => {
                // Manejar la respuesta exitosa aquí
                console.log(response.data)
                if (response.data.success) {
                    //location.reload();
                    alertMsj('El producto fue editado correctamente', 'success')

                    let rowProduct = document.getElementById(`row__product-${id.value}`)
                    rowProduct.querySelector('.celd-type').innerText = newCategory.options[newCategory.selectedIndex].innerText
                    rowProduct.querySelector('.celd-name').innerText = newName.value
                    rowProduct.querySelector('.celd-price').innerText = "$" + newPrice.value
                    rowProduct.querySelector('.celd-stock').innerText = newStock.value

                    document.getElementById('modal__update-product').style.display = 'none'
                } else {
                    successValidation(newCategory)
                    successValidation(newName)
                    successValidation(newStock)
                    successValidation(newPrice)
                    successValidation(newImg)
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


//CAMBIAR PARAMETRO DE VER O NO PRODUCTOS ELIMINADOS
document.getElementById('check-allow-deleted').addEventListener('click', (e) => {
    var newUrl;
    if (e.target.checked) {
        //Desea ver los eliminados
        urlParams.set('allowDeleted', 'yes')
        newUrl = `${window.location.pathname}?${urlParams.toString()}`;
    } else {
        //No desea ver los eliminados
        urlParams.set('allowDeleted', 'no')
        newUrl = `${window.location.pathname}?${urlParams.toString()}`;
    }

    window.location.href = newUrl;
})
//CAMBIAR PARAMETRO DE FILTROS POR CATEGORIA
var btnsFilterBy = document.querySelectorAll('.filter__by-item')
btnsFilterBy.forEach(btnFilterBy => {
    btnFilterBy.addEventListener('click', () => {
        filterBy = btnFilterBy.id.split("-")[1]
        
        urlParams.set('filterBy', filterBy)
        var newUrl = `${window.location.pathname}?${urlParams.toString()}`;
        
        window.location.href = newUrl;
    })
})
//CAMBIAR PARAMETRO DE LA PAGINA ACTUAL
var btnsPaginator = document.querySelectorAll('.btn__paginator')
btnsPaginator.forEach(btnPaginator => {
    btnPaginator.addEventListener('click', () => {
        newPage = btnPaginator.id.split("-")[1]

        console.log(newPage)
        urlParams.set('page', newPage)
        var newUrl = `${window.location.pathname}?${urlParams.toString()}`;

        window.location.href = newUrl;
    })
})



function validations(category, name, stock, price, img = null) {
    var isValid = 0;

    //CATEGORIA
    if (!category.value.trim() || category.value == 0) {
        failValidation(category, 'Debe seleccionar una categoría.')

    } else {
        successValidation(category)
        isValid++
    }

    //NOMBRE
    if (!name.value.trim()) {
        failValidation(name, 'Debe ingresar un nombre.')

    } else if (name.value.length > 50) {
        failValidation(name, 'Debe ingresar un nombre menor a 50 caracteres.')

    } else {
        successValidation(name)
        isValid++
    }

    //STOCK
    if (!stock.value.trim() || parseInt(stock.value, 10) > 2147483647 || parseInt(stock.value, 10) < 0) {
        failValidation(stock, 'Debe ingresar una cantidad de stock válida.')

    } else {
        successValidation(stock)
        isValid++
    }

    //PRECIO
    if (!price.value.trim() || parseInt(price.value, 10) > 2147483647 || parseInt(stock.value, 10) < 0) {
        failValidation(price, 'Debe ingresar un precio válido.')

    } else {
        successValidation(price)
        isValid++
    }

    //IMG
    if (img != null) {
        if (!img.files[0]) {
            failValidation(img, 'Debe ingresar una imagen.')
        } else if (!allowedTypes.includes(img.files[0].type)) {
            failValidation(img, 'Debe ingresar una imagen de formato JPEG, PNG o WEBP.')
        } else if (img.files[0].size > maxSize) {
            failValidation(img, 'Debe ingresar una imagen de máximo 5MB.')
    
        } else {
            successValidation(img)
            isValid++
        }
    } else {
        isValid++
    }

    return isValid;
}