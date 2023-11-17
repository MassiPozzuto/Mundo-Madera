<div class="modal" id="modal-producto">
    <div class="modal-content">
        <div class="modal-info">
            <h3>Agregar producto</h3>
        </div>
        <div class="modal-input-group">
            <label>Categoría</label>
            <select id="cat" class="modal-input">
                <option value="0" hidden disabled selected>Selecciona una categoría</option>
                <option value="1">Roble</option>
                <option value="2">Pino</option>
                <option value="4">Cedro</option>
            </select>
        </div>
        <div class="modal-input-group">
            <label>Nombre</label>
            <input type="text" id="nombre" class="modal-input">
        </div>
        <div class="modal-input-group">
            <label>Stock</label>
            <input type="number" id="stock" class="modal-input">
        </div>
        <div class="modal-input-group">
            <label>Precio</label>
            <input type="number" id="precio" class="modal-input">
        </div>
        <div class="modal-input-group input-group-img">
            <button class="" id="btn-open-file">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-camera-plus" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M12 20h-7a2 2 0 0 1 -2 -2v-9a2 2 0 0 1 2 -2h1a2 2 0 0 0 2 -2a1 1 0 0 1 1 -1h6a1 1 0 0 1 1 1a2 2 0 0 0 2 2h1a2 2 0 0 1 2 2v3.5" />
                    <path d="M16 19h6" />
                    <path d="M19 16v6" />
                    <path d="M9 13a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                </svg>
                <span>Agregar imagen ilustrativa</span>
            </button>
            <input type="file" id="imagen" class="modal-input" hidden>
        </div>
        <div class="modal-btns">
            <button class="modal-btn cancel" id="modal-cancel">Cancelar</button>
            <button class="modal-btn confirm" id="modal-confirm">Confirmar</button>
        </div>
    </div>
</div>


<div class="modal" style="display: none;">
    <div class="body-modal">
        <button class="btn-close" onclick="hide_modal('modal-producto')"><i class="fa fa-times" aria-hidden="true"></i></button>
        <h3>Añadir producto</h3>
        <input type="text" id="codigo" style="display: none;">
        <div class="div-flex">
            <label>Categoria</label>
            <select id="cat">
                <option value="4">Cedro</option>
                <option value="3">Roble</option>
                <option value="1">Pino</option>
            </select>
        </div>
        <div class="div-flex">
            <label>Nombre</label>
            <input type="text" id="nombre">
        </div>
        <div class="div-flex">
            <label>Stock</label>
            <input type="number" id="stock">
        </div>
        <div class="div-flex">
            <label>Precio</label>
            <input type="number" id="precio">
        </div>
        <div class="div-flex">
            <input type="file" id="imagen" style="margin-left: 100px;">
        </div>
        <div>
            <button onclick="save_producto()">Guardar</button>
        </div>
    </div>
</div>

<div class="modal" id="modal-producto-edit" style="display: none;">
    <div class="body-modal">
        <button class="btn-close" onclick="hide_modal('modal-producto-edit')"><i class="fa fa-times" aria-hidden="true"></i></button>
        <h3>Editar producto</h3>
        <div class="div-flex">
            <label>Código</label>
            <input type="text" id="codigo-e" disabled>
        </div>
        <div class="div-flex">
            <label>Categoria</label>
            <select id="cat-e">
                <option value="4">Cedro</option>
                <option value="3">Roble</option>
                <option value="1">Pino</option>
            </select>
        </div>
        <div class="div-flex">
            <label>Nombre</label>
            <input type="text" id="nombre-e">
        </div>
        <div class="div-flex">
            <label>Precio</label>
            <input type="text" id="precio-e">
        </div>
        <div class="div-flex">
            <label>Stock</label>
            <input type="number" id="stock-e">
        </div>
        <div class="div-flex">
            <input type="file" id="imagen-e" style="margin-left: 100px;">
        </div>
        <button onclick="update_producto()">Actualizar</button>
    </div>
</div>

<div class="main-container">

    <div class="container__page-title">
        <h2 class="page__title">Productos actuales</h2>
        <button type="button" class="btn-add" onclick="show_modal('modal-producto')">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                <path d="M12 5l0 14"></path>
                <path d="M5 12l14 0"></path>
            </svg>
        </button>
    </div>

    <div class="grid-container">
        <div class="grid-header">Código</div>
        <div class="grid-header">Categoría</div>
        <div class="grid-header">Nombre</div>
        <div class="grid-header">Precio</div>
        <div class="grid-header">Stock</div>
        <div class="grid-header">Opciones</div>


        <?php
        foreach ($rowProducts as $key => $product) { ?>
            <div class="grid-item"><?php echo $product['id'] ?></div>
            <div class="grid-item"><?php echo $product['tipo'] ?></div>
            <div class="grid-item"><?php echo $product['nombre'] ?></div>
            <div class="grid-item"><span><?php echo $product['precio'] ?></div>
            <div class="grid-item"><span><?php echo $product['stock'] ?></div>
            <div class="grid-item item-options">
                <button class="btn-update" onclick="edit_product(<?php echo $product['id'] ?>)">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-edit" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"></path>
                        <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"></path>
                        <path d="M16 5l3 3"></path>
                    </svg>
                </button>
                <button class="btn-delete" onclick="delete_product(<?php echo $product['id'] ?>)">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-trash" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M4 7l16 0"></path>
                        <path d="M10 11l0 6"></path>
                        <path d="M14 11l0 6"></path>
                        <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"></path>
                        <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"></path>
                    </svg>
                </button>
            </div>
        <?php
        } ?>
    </div>

    <?php /*
    foreach ($rowProducts as $key => $product) { ?>
        <!-- REVISAR MODELOS DE METRONIC -->
        <div class="container__product">
            <div class="container__product-img">
                <img src="../../img/products/<?php echo $product['rutimapro'] ?>" alt="">
            </div>

            <div class="container__product-data">
                <div>
                    <span>Categoria:</span>
                    <span><?php echo $product['tipo'] ?></span>
                    <span>Producto:</span>
                    <span><?php echo $product['nombre'] ?></span>
                </div>
                <div>
                    <span>Precio:</span>
                    <span><?php echo $product['precio'] ?></span>
                    <span>Stock:</span>
                    <span><?php echo $product['stock'] ?></span>
                </div>
            </div>

            <div class="container__product-manage">
                <div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-edit" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"></path>
                        <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"></path>
                        <path d="M16 5l3 3"></path>
                    </svg>
                </div>
                <div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-trash" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M4 7l16 0"></path>
                        <path d="M10 11l0 6"></path>
                        <path d="M14 11l0 6"></path>
                        <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"></path>
                        <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"></path>
                    </svg>
                </div>
            </div>
        </div>
    <?php
    } */ ?>

</div>

<script type="text/javascript" src="../../js/main.js"></script>

<script type="text/javascript">
    function show_modal(id) {
        document.getElementById(id).style.display = "block";
    }

    function hide_modal(id) {
        document.getElementById(id).style.display = "none";
    }

    function save_producto() {
        let fd = new FormData();
        fd.append('id', document.getElementById('codigo').value);
        fd.append('nombre', document.getElementById('nombre').value);
        fd.append('stock', document.getElementById('stock').value);
        fd.append('precio', document.getElementById('precio').value);
        fd.append('cat', document.getElementById('cat').value);
        fd.append('imagen', document.getElementById('imagen').files[0]);
        let request = new XMLHttpRequest();
        request.open('POST', '../../api/producto_save.php', true);
        request.onload = function() {
            if (request.readyState == 4 && request.status == 200) {
                let response = JSON.parse(request.responseText);
                console.log(response);
                if (response.state) {
                    alert("Producto guardado");
                    window.location.reload();
                } else {
                    alert(response.detail);
                }
            }
        }
        request.send(fd);
    }

    function delete_product(codpro) {
        var c = confirm("Estas seguro de eliminar el producto de codigo " + codpro + "?");
        if (c) {
            let fd = new FormData();
            fd.append('codpro', codpro);
            let request = new XMLHttpRequest();
            request.open('POST', 'api/delete_product.php', true);
            request.onload = function() {
                if (request.readyState == 4 && request.status == 200) {
                    let response = JSON.parse(request.responseText);
                    console.log(response);
                    if (response.state) {
                        alert("Producto eliminado");
                        window.location.reload();
                    } else {
                        alert(response.detail);
                    }
                }
            }
            request.send(fd);
        }
    }

    function edit_product(codpro) {
        let fd = new FormData();
        fd.append('codpro', codpro);
        let request = new XMLHttpRequest();
        request.open('POST', '../../api/get_product.php', true);
        request.onload = function() {
            if (request.readyState == 4 && request.status == 200) {
                let response = JSON.parse(request.responseText);
                console.log(response);
                document.getElementById("codigo-e").value = codpro;
                document.getElementById("nombre-e").value = response.product.nompro;
                document.getElementById("stock-e").value = response.product.despro;
                document.getElementById("precio-e").value = response.product.prepro;
                document.getElementById("cat-e").value = response.product.estado;
                show_modal('modal-producto-edit');
                //imagen-e
            }
        }
        request.send(fd);
    }

    function update_producto() {
        let fd = new FormData();
        fd.append('codigo', document.getElementById('codigo-e').value);
        fd.append('nombre', document.getElementById('nombre-e').value);
        fd.append('descripcion', document.getElementById('descripcion-e').value);
        fd.append('precio', document.getElementById('precio-e').value);
        fd.append('estado', document.getElementById('estado-e').value);
        fd.append('imagen', document.getElementById('imagen-e').files[0]);
        fd.append('rutimapro', document.getElementById("rutimapro-aux").value);
        let request = new XMLHttpRequest();
        request.open('POST', 'api/producto_update.php', true);
        request.onload = function() {
            if (request.readyState == 4 && request.status == 200) {
                let response = JSON.parse(request.responseText);
                console.log(response);
                if (response.state) {
                    alert("Producto actualizado");
                    window.location.reload();
                } else {
                    alert(response.detail);
                }
            }
        }
        request.send(fd);
    }
</script>