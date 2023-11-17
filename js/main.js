const selectsCategory = document.querySelectorAll('.select-category')
axios.post('../../api/get_categories.php')
    .then(response => {
        // Manejar la respuesta exitosa aquí
        selectsCategory.forEach(selectCategory => {
            // Crear un nuevo elemento option
            var optionDefault = document.createElement('option');
            optionDefault.value = 0;
            optionDefault.hidden = true;
            optionDefault.disabled = true;
            optionDefault.selected = true;
            optionDefault.text = "Selecciona una categoría";
            selectCategory.appendChild(optionDefault);

            response.data.forEach(category => {
                var optionCategory = document.createElement('option');
                optionCategory.value = category.id;
                optionCategory.text = category.tipo;
                selectCategory.appendChild(optionCategory);
            })
            
        });
    })
    .catch(error => {
        alert('Ocurrió un error inesperado. Intentelo más tarde', 'error')
        console.log("Error atrapado:", error);
    });

document.getElementById('btn-open-file').addEventListener('click', () => {
    document.getElementById('new__product-img').click()
})

document.getElementById('new__product-img').addEventListener('change', () => {
    const input = document.getElementById('new__product-img');
    const previewImage = document.getElementById('new__product-preview-img');

    const file = input.files[0];

    if (file) {
        // Validar el tipo de archivo
        const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        if (!allowedTypes.includes(file.type)) {
            alert('Por favor, selecciona una imagen JPEG, PNG o WEBP.');
            input.value = ''; // Limpiar el input file
            return;
        }

        // Validar el tamaño del archivo (5 MB)
        const maxSize = 5 * 1024 * 1024; // 5 MB en bytes
        if (file.size > maxSize) {
            alert('La imagen no debe superar los 5 MB.');
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

document.getElementById('new__product-delete-img').addEventListener('click', () => {
    const input = document.getElementById('new__product-img');
    const previewImage = document.getElementById('new__product-preview-img');

    input.value = ''; // Limpiar el input file
    previewImage.parentNode.style.display = 'none';
    previewImage.src = '';
})

document.getElementById('new__product-submit').addEventListener('click', (e) => {
    const category = document.getElementById('new__product-cat');
    const name = document.getElementById('new__product-name');
    const stock = document.getElementById('new__product-stock');
    const price = document.getElementById('new__product-price');
    const img = document.getElementById('new__product-img');
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

    } else if(name.value.length > 50) {
        failValidation(name, 'Debe ingresar un nombre menor a 50 caracteres.')

    }else {
        successValidation(name)
        isValid++
    }

    
    //STOCK
    if (!stock.value.trim() || parseInt(stock.value, 10) > 2147483647) {
        failValidation(stock, 'Debe ingresar una cantidad de stock válida.')

    } else {
        successValidation(stock)
        isValid++
    }

    //PRECIO
    if (!price.value.trim() || parseInt(price.value, 10) > 2147483647) {
        failValidation(price, 'Debe ingresar un precio válido.')

    } else {
        successValidation(price)
        isValid++
    }

    //IMG
    const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
    const maxSize = 5 * 1024 * 1024; // 5 MB en bytes
    if (!allowedTypes.includes(img.files[0].type)) {
        failValidation(img, 'Debe ingresar una imagen de formato JPEG, PNG o WEBP.')
    } else if(img.files[0].size > maxSize) {
        failValidation(img, 'Debe ingresar una imagen de máximo 5MB.')

    } else {
        successValidation(img)
        isValid++
    }

    if(isValid == 5){
        var formData = new FormData();
        formData.append('category', category.value);
        formData.append('name', name.value);
        formData.append('stock', stock.value);
        formData.append('price', price.value);
        formData.append('img', img.files[0]);

        axios.post('../../api/producto_save.php', formData)
            .then(response => {
                // Manejar la respuesta exitosa aquí
                console.log(response.data)
            })
            .catch(error => {
                alertMsj('Ocurrió un error inesperado. Intentelo más tarde', 'error')
                console.log("Error atrapado:", error);
            });
    }

})

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