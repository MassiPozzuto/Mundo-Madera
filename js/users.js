
//CREACION DEL PRODUCTO
document.getElementById('create-submit').addEventListener('click', (e) => {
    const username = document.getElementById('create-username');
    const name = document.getElementById('create-name');
    const surname = document.getElementById('create-surname');
    const pass = document.getElementById('create-password');

    if (validations(username, name, surname, pass) == 4) {
        var formData = new FormData();
        formData.append('username', username.value);
        formData.append('name', name.value);
        formData.append('surname', surname.value);
        formData.append('pass', pass.value);

        axios.post('../../api/users/create_user.php', formData)
            .then(response => {
                // Manejar la respuesta exitosa aquí
                console.log(response.data)
                if (response.data.success) {
                    location.reload();

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

//ACTUALIZAR DATOS DEL PRODUCTO
document.getElementById('update-submit').addEventListener('click', (e) => {
    const id = document.getElementById('update-id');
    const newUsername = document.getElementById('update-username');
    const newName = document.getElementById('update-name');
    const newSurname = document.getElementById('update-surname');
    const newPassword = document.getElementById('update-new_password');
    const oldPassword = document.getElementById('update-old_password');

    if (validations(newUsername, newName, newSurname, newPassword, oldPassword) == 5) {
        var formData = new FormData();
        formData.append('id', id.value);
        formData.append('username', newUsername.value);
        formData.append('name', newName.value);
        formData.append('surname', newSurname.value);
        formData.append('newPass', newPassword.value);
        formData.append('oldPass', oldPassword.value);

        axios.post('../../api/users/update_user.php', formData)
            .then(response => {
                // Manejar la respuesta exitosa aquí
                console.log(response.data)
                if (response.data.success) {
                    //location.reload();
                    alertMsj('El usuario fue editado correctamente', 'success')
                    
                    let rowProduct = document.getElementById(`row__user-${id.value}`)
                    rowProduct.querySelector('.celd-username').innerText = newUsername.value
                    rowProduct.querySelector('.celd-name').innerText = newName.value
                    rowProduct.querySelector('.celd-surname').innerText = newSurname.value
                    
                    clearModals('update')
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




function validations(username, name, surname, pass, oldPass = null) {
    var isValid = 0;

    //NOMBRE DE USUARIO
    if (!username.value.trim()) {
        failValidation(username, 'Debe ingresar un nombre de usuario.')

    } else if (username.value.length > 25) {
        failValidation(username, 'Debe ingresar un nombre de usuario menor a 25 caracteres.')

    } else {
        successValidation(username)
        isValid++
    }

    //NOMBRE
    if (name.value.trim() != null && name.value.length > 30) {
        failValidation(name, 'El nombre debe tener menos de 30 caracteres.')

    } else {
        successValidation(name)
        isValid++
    }

    //APELLIDO
    if (surname.value.trim() != null && surname.value.length > 30) {
        failValidation(surname, 'El apellido debe tener menos de 30 caracteres.')

    } else {
        successValidation(surname)
        isValid++
    }

    //CONTRASEÑA NUEVA
    if (!pass.value.trim()) {
        failValidation(pass, 'Debe ingresar una contraseña.')
        
    }else if (pass.length < 5) {
        failValidation(pass, 'Debe ingresar una contraseña de más de 5 caracteres.')

    } else {
        successValidation(pass)
        isValid++
    }

    //CONTRASEÑA VIEJA
    if (oldPass != null) {
        if (!oldPass.value.trim()) {
            failValidation(oldPass, 'Debe ingresar su contraseña actual.')
            
        } else {
            successValidation(oldPass)
            isValid++
        }
    }

    return isValid;
}

//LIMPIAR MODALS
function clearModals(typeModal) {
    let username = document.getElementById(`${typeModal}-username`)
    let name = document.getElementById(`${typeModal}-name`)
    let surname = document.getElementById(`${typeModal}-surname`)
    let newPass = document.getElementById(`${typeModal}-new_password`) || document.getElementById(`${typeModal}-password`)
    if (typeModal == 'update') {
        let oldPass = document.getElementById(`${typeModal}-old_password`)
        oldPass.value = ""
        successValidation(oldPass)
    }

    username.value = ""
    successValidation(username)
    name.value = ""
    successValidation(name)
    surname.value = ""
    successValidation(surname)
    newPass.value = ""
    successValidation(newPass)
}

//LIMPIAR MODALS UPDATE
function clearModalUpdate(formData) {
    axios.post('../../api/users/get_user.php', formData)
        .then(response => {
            // Manejar la respuesta exitosa aquí
            console.log(response.data)

            if (response.data.success) {
                //Reemplazo todos los datos normales
                document.getElementById('update-id').value = response.data.user.id
                document.getElementById('update-username').value = response.data.user.username
                document.getElementById('update-name').value = response.data.user.nombre
                document.getElementById('update-surname').value = response.data.user.apellido

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
    axios.post('../../api/users/delete_user.php', formData)
        .then(response => {
            // Manejar la respuesta exitosa aquí
            console.log(response.data)

            if (response.data.success) {
                //Producto eliminado correctamente
                alertMsj(response.data.msj, 'success')
                button.parentNode.parentNode.remove()

                if (response.data.redirectUrl) {
                    window.location.href = response.data.redirectUrl;
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