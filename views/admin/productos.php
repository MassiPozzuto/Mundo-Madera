<div class="modal" id="modal-producto" style="display: none;">
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
            <label>stock</label>
            <input type="number" id="stock">
        </div>
        <div class="div-flex">
            <label>Precio</label>
            <input type="number" id="precio">
        </div>
        <button onclick="save_producto()">Guardar</button>
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
        <button onclick="update_producto()">Actualizar</button>
    </div>
</div>
<div class="main-container">
    <div class="body-page">
        <h2>Mis productos</h2>
        <table class="mt10">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Categoria</th>
                    <th>Nombre</th>
                    <th>Precio</th>
                    <th>Stock</th>
                    <th class="td-option">Opciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT * from productos";
                $stmt = sqlsrv_query($conn, $sql);
                while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_BOTH)) {
                    echo
                    '<tr>
                    <td>' . $row['id'] . '</td>
                    <td>' . $row['id_categoria'] . '</td>
                    <td>' . $row['nombre'] . '</td>
                    <td>' . $row['precio'] . '</td>
                    <td>' . $row['stock'] . '</td>
                    <td class="td-option">
                        <div class="div-flex div-td-button">
                            <button onclick="edit_product(' . $row['id'] . ')"><i class="fa fa-pencil" aria-hidden="true"></i></button>
                            <button onclick="delete_product(' . $row['id'] . ')"><i class="fa fa-trash" aria-hidden="true"></i></button>
                        </div>
                    </td>
                </tr>';
                }
                ?>
            </tbody>
        </table>
        <button class="mt10" onclick="show_modal('modal-producto')">Agregar nuevo</button>
    </div>
</div>
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
        let request = new XMLHttpRequest();
        request.open('POST', 'api/producto_save.php', true);
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
        request.open('POST', 'api/get_product.php', true);
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