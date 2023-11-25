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
    },
    placeholder: "Selecciona una categoría"
});

//ABRIR INPUT FILE PARA AGREGAR IMG A LOS MODALS
btnsAddImg = document.querySelectorAll('.btn__add-img');
btnsAddImg.forEach(btnAddImg => {
    btnAddImg.addEventListener('click', () => {
        let typeModal = btnAddImg.id.split("-")[0];
        document.getElementById(`${typeModal}-img`).click();       
    })
})

const ALLOWED_TYPES = ['image/jpeg', 'image/png', 'image/webp'];
const MAX_SIZE = 5 * 1024 * 1024; // 5 MB en bytes
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
            if (!ALLOWED_TYPES.includes(file.type)) {
                alertMsj('Por favor, selecciona una imagen JPEG, PNG o WEBP.', 'error');
                input.value = ''; // Limpiar el input file
                return;
            }
            // Validar el tamaño del archivo (5 MB)
            if (file.size > MAX_SIZE) {
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
document.getElementById('create-submit').addEventListener('click', (e) => {
    const category = document.getElementById('create-cat');
    const name = document.getElementById('create-name');
    const stock = document.getElementById('create-stock');
    const price = document.getElementById('create-price');
    const img = document.getElementById('create-img');    

    if (validations(name, stock, price, img) == 4){
        var formData = new FormData();
        formData.append('name', name.value);
        formData.append('stock', stock.value);
        formData.append('price', price.value);
        formData.append('img', img.files[0]);

        const selectedCategories = Array.from(category.selectedOptions).map(option => option.value);
        formData.append('categories', JSON.stringify(selectedCategories));

        axios.post('../../api/products/create_product.php', formData)
            .then(response => {
                // Manejar la respuesta exitosa aquí
                console.log(response.data)
                if (response.data.success) {
                    location.reload();

                } else {
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
document.getElementById('update-submit').addEventListener('click', (e) => { 
    const id = document.getElementById('update-id');
    const newCategory = document.getElementById('update-cat');
    const newName = document.getElementById('update-name');
    const newStock = document.getElementById('update-stock');
    const newPrice = document.getElementById('update-price');
    const newImg = document.getElementById('update-img');

    
    
    if (validations(newName, newStock, newPrice, newImg.files[0]) == 4) {
        var formData = new FormData();
        formData.append('id', id.value);
        formData.append('name', newName.value);
        formData.append('stock', newStock.value);
        formData.append('price', newPrice.value);
        formData.append('img', newImg.files[0]);

        const selectedCategories = Array.from(newCategory.selectedOptions).map(option => option.value);
        formData.append('categories', JSON.stringify(selectedCategories));

        axios.post('../../api/products/update_product.php', formData)
            .then(response => {
                // Manejar la respuesta exitosa aquí
                console.log(response.data)
                if (response.data.success) {
                    //location.reload();
                    alertMsj('El producto fue editado correctamente', 'success')

                    let rowProduct = document.getElementById(`row__product-${id.value}`)
                    rowProduct.querySelector('.celd-type').innerText = Array.from(newCategory.selectedOptions).map(option => option.innerText).join(', ');
                    rowProduct.querySelector('.celd-name').innerText = newName.value
                    rowProduct.querySelector('.celd-price').innerText = "$" + newPrice.value
                    rowProduct.querySelector('.celd-stock').innerText = newStock.value

                    document.getElementById('modal__update').style.display = 'none'
                } else {
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




function validations(name, stock, price, img = null) {
    var isValid = 0;

    /*//CATEGORIA
    if (!category.value.trim() || category.value == 0) {
        failValidation(category, 'Debe seleccionar una categoría.')

    } else {
        successValidation(category)
        isValid++
    }*/

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
        } else if (!ALLOWED_TYPES.includes(img.files[0].type)) {
            failValidation(img, 'Debe ingresar una imagen de formato JPEG, PNG o WEBP.')
        } else if (img.files[0].size > MAX_SIZE) {
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

//LIMPIAR MODALS
function clearModals(typeModal) {
    let name = document.getElementById(`${typeModal}-name`)
    let stock = document.getElementById(`${typeModal}-stock`)
    let price = document.getElementById(`${typeModal}-price`)
    let category = document.getElementById(`${typeModal}-cat`)  
    let previewImg = document.getElementById(`${typeModal}-preview-img`)
    let img = document.getElementById(`${typeModal}-img`)

    name.value = ""
    successValidation(name)
    stock.value = ""
    successValidation(stock)
    price.value = ""
    successValidation(price)
    // Reemplazo el valor del select2
    $('#update-cat').val([]).trigger('change');
    //Reemplazo la imagen si existe
    previewImg.parentNode.style.display = 'none';
    previewImg.src = '';
    img.value = '';
    successValidation(img)
}

//LIMPIAR MODALS UPDATE
function clearModalUpdate(formData) {
    axios.post('../../api/products/get_product.php', formData)
        .then(response => {
            // Manejar la respuesta exitosa aquí
            console.log(response.data)

            if (response.data.success) {
                //Reemplazo todos los datos normales
                document.getElementById('update-id').value = response.data.product.id
                document.getElementById('update-name').value = response.data.product.nombre
                document.getElementById('update-stock').value = response.data.product.stock
                document.getElementById('update-price').value = response.data.product.precio

                // Reemplazo el valor del select2
                $('#update-cat').val(response.data.product.categorias).trigger('change');

                //Reemplazo la imagen si existe
                if (response.data.product.rutaImg != null) {
                    document.getElementById('update-preview-img').parentNode.style.display = 'block';
                    document.getElementById('update-preview-img').src = `${response.data.product.rutaImg}`;
                } else {
                    document.getElementById('update-preview-img').parentNode.style.display = 'none';
                    document.getElementById('update-preview-img').src = '';
                }
                document.getElementById('update-img').value = '';

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

//ELIMINAR PRODUCTO
function deleteItem(formData, button) {
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