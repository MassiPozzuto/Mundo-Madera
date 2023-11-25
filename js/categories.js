
//CREACION DEL PRODUCTO
document.getElementById('create-submit').addEventListener('click', (e) => {
    const name = document.getElementById('create-name');
    const description = document.getElementById('create-description');

    if (validations(name, description) == 2) {
        var formData = new FormData();
        formData.append('name', name.value);
        formData.append('description', description.value);

        axios.post('../../api/categories/create_category.php', formData)
            .then(response => {
                // Manejar la respuesta exitosa aquí
                console.log(response.data)
                if (response.data.success) {
                    location.reload();

                } else {
                    successValidation(name)
                    successValidation(description)
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
    const newName = document.getElementById('update-name');
    const newDescription = document.getElementById('update-description');

    if (validations(newName, newDescription) == 2) {
        var formData = new FormData();
        formData.append('id', id.value);
        formData.append('name', newName.value);
        formData.append('description', newDescription.value);

        axios.post('../../api/categories/update_category.php', formData)
            .then(response => {
                // Manejar la respuesta exitosa aquí
                console.log(response.data)
                if (response.data.success) {
                    //location.reload();
                    alertMsj('La categoría fue editada correctamente', 'success')

                    let rowCategory = document.getElementById(`row__category-${id.value}`)
                    rowCategory.querySelector('.celd-name').innerText = newName.value
                    rowCategory.querySelector('.celd-description').innerText = newDescription.value

                    document.getElementById('modal__update').style.display = 'none'
                } else {
                    successValidation(newName)
                    successValidation(newDescription)
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



function validations(name, description) {
    var isValid = 0;

    //NOMBRE
    if (!name.value.trim()) {
        failValidation(name, 'Debe ingresar un nombre.')

    } else if (name.value.length > 50) {
        failValidation(name, 'Debe ingresar un nombre menor a 50 caracteres.')

    } else {
        successValidation(name)
        isValid++
    }

    //DESCRIPCION
    if (description.value.trim() > 200) {
        failValidation(description, 'La descripción debe ser de máximo 200 caracteres.')

    } else {
        successValidation(description)
        isValid++
    }

    return isValid;
}

//LIMPIAR MODALS
function clearModals(typeModal) {
    let name = document.getElementById(`${typeModal}-name`)
    let description = document.getElementById(`${typeModal}-description`)

    name.value = ""
    successValidation(name)
    description.value = ""
    successValidation(description)
}

//LIMPIAR MODALS UPDATE
function clearModalUpdate(formData) {
    axios.post('../../api/categories/get_category.php', formData)
        .then(response => {
            // Manejar la respuesta exitosa aquí
            console.log(response.data)

            if (response.data.success) {
                //Reemplazo todos los datos normales
                document.getElementById('update-id').value = response.data.category.id
                document.getElementById('update-name').value = response.data.category.nombre
                document.getElementById('update-description').value = response.data.category.descripcion

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

//ELIMINAR CATEGORIA
function deleteItem(formData, button) {
    axios.post('../../api/categories/delete_category.php', formData)
        .then(response => {
            // Manejar la respuesta exitosa aquí
            console.log(response.data)

            if (response.data.success) {
                //Categoria eliminada correctamente
                alertMsj(response.data.msj, 'success')

                if (response.data.success) {
                    if (response.data.action == 'delete') {
                        //Si la accion fue eliminar la categoria
                        button.classList.add('deleted')
                        button.innerHTML =
                            `<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-back-up" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M9 14l-4 -4l4 -4" />
                                        <path d="M5 10h11a4 4 0 1 1 0 8h-1" />
                                    </svg>`
                        
                        let rowCategory = document.getElementById(`row__category-${formData.get('id')}`)
                        rowCategory.querySelector('.celd-related_roducts').innerText = 0
                        rowCategory.querySelector('.celd-stock_related_products').innerText = 0
                    } else {
                        //Si la accion fue recuperar la categoria
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
                //No se pudo eliminar la categoria
                alertMsj(response.data.msj, 'error')
            }
        })
        .catch(error => {
            alertMsj('Ocurrió un error inesperado. Intentelo más tarde', 'error')
            console.log("Error atrapado:", error);
        });
}