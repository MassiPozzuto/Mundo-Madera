<!-- CONTENIDO -->
<div class="main-container">
    <div class="container__page-title">
        <h2 class="page__title">Todos los productos</h2>
        <button type="button" class="btn-add" id="btn__open-new__product" title="Agregar">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                <path d="M12 5l0 14"></path>
                <path d="M5 12l14 0"></path>
            </svg>
        </button>
    </div>

    <table class="container__table">
        <thead class="container__table-head">
            <tr class="container__table-row row-head">
                <th class="container__table-celd ">Código</th>
                <th class="container__table-celd ">Categoría</th>
                <th class="container__table-celd ">Nombre</th>
                <th class="container__table-celd ">Precio</th>
                <th class="container__table-celd ">Stock</th>
                <th class="container__table-celd ">Opciones</th>
            </tr>
        </thead>

        <tbody class="container__table-body">
            <?php
            foreach ($rowProducts as $key => $product) { ?>

                <tr class="container__table-row row-normal">
                    <td class="container__table-celd "><?php echo $product['id'] ?></td>
                    <td class="container__table-celd "><?php echo $product['tipo'] ?></td>
                    <td class="container__table-celd "><?php echo $product['nombre'] ?></td>
                    <td class="container__table-celd "><span>$<?php echo $product['precio'] ?></td>
                    <td class="container__table-celd "><span><?php echo $product['stock'] ?></td>
                    <td class="container__table-celd  celd-options">
                        <button type="button" class="btn-update btn__update-product" id="update__product-<?php echo $product['id'] ?>" title="Editar">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-edit" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"></path>
                                <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"></path>
                                <path d="M16 5l3 3"></path>
                            </svg>
                        </button>
                        <button type="button" class="btn-delete btn__delete-product" id="delete__product-<?php echo $product['id'] ?>" title="Eliminar">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-trash" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M4 7l16 0"></path>
                                <path d="M10 11l0 6"></path>
                                <path d="M14 11l0 6"></path>
                                <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"></path>
                                <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"></path>
                            </svg>
                        </button>
                    </td>
                </tr>
            <?php
            } ?>
        </tbody>
    </table>
</div>


<!-- MODAL AGREGAR PRODUCTOS -->
<div class="modal" id="modal__new-product">
    <div class="modal-content">
        <div class="modal-info">
            <h3>Agregar producto</h3>
            <button type="button" class="btn-close-x modal-close" id="close--modal__new-product--1">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-x" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M18 6l-12 12" />
                    <path d="M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div class="modal-input-group">
            <label>Categoría</label>
            <select id="new__product-cat" class="modal-input select-category">
                <!--Se agregan dinamicamente mediante JS-->
            </select>
            <p class="errormessage__form"></p>
        </div>
        <div class="modal-input-group">
            <label>Nombre</label>
            <input type="text" id="new__product-name" class="modal-input">
            <p class="errormessage__form"></p>
        </div>
        <div class="modal-input-group">
            <label>Stock</label>
            <input type="number" id="new__product-stock" class="modal-input">
            <p class="errormessage__form"></p>
        </div>
        <div class="modal-input-group">
            <label>Precio</label>
            <input type="number" id="new__product-price" class="modal-input">
            <p class="errormessage__form"></p>
        </div>
        <div class="modal-input-group input-group-img fail">
            <button type="button" class="btn__add-img" id="new__product-add-img">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-camera-plus" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M12 20h-7a2 2 0 0 1 -2 -2v-9a2 2 0 0 1 2 -2h1a2 2 0 0 0 2 -2a1 1 0 0 1 1 -1h6a1 1 0 0 1 1 1a2 2 0 0 0 2 2h1a2 2 0 0 1 2 2v3.5" />
                    <path d="M16 19h6" />
                    <path d="M19 16v6" />
                    <path d="M9 13a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                </svg>
                <span>Agregar imagen ilustrativa</span>
            </button>
            <input type="file" id="new__product-img" class="modal-input input__file-img" accept="image/jpeg,image/png,image/webp" hidden>
            <div class="modal__preview-img">
                <img class="preview__img" alt="Preview" id="new__product-preview-img">
                <button type="button" class="btn__remove-img" id="new__product-delete-img">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-x" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M18 6l-12 12" />
                        <path d="M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <p class="errormessage__form"></p>
        </div>
        <div class="modal-btns">
            <button type="button" class="modal-btn cancel modal-close" id="close--modal__new-product--2">Cancelar</button>
            <button type="button" class="modal-btn confirm" id="new__product-submit">Confirmar</button>
        </div>
    </div>
</div>


<!-- MODAL EDITAR PRODUCTOS -->
<div class="modal" id="modal__update-product">
    <div class="modal-content">
        <div class="modal-info">
            <h3>Editar producto</h3>
            <button type="button" class="btn-close-x modal-close" id="close--modal__update-product--1">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-x" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M18 6l-12 12" />
                    <path d="M6 6l12 12" />
                </svg>
            </button>
        </div>

        <input type="number" id="update__product-id" class="modal-input" hidden>

        <div class="modal-input-group">
            <label>Categoría</label>
            <select id="update__product-cat" class="modal-input select-category">
                <!--Se agregan dinamicamente mediante JS-->
            </select>
            <p class="errormessage__form"></p>
        </div>
        <div class="modal-input-group">
            <label>Nombre</label>
            <input type="text" id="update__product-name" class="modal-input">
            <p class="errormessage__form"></p>
        </div>
        <div class="modal-input-group">
            <label>Stock</label>
            <input type="number" id="update__product-stock" class="modal-input">
            <p class="errormessage__form"></p>
        </div>
        <div class="modal-input-group">
            <label>Precio</label>
            <input type="number" id="update__product-price" class="modal-input">
            <p class="errormessage__form"></p>
        </div>
        <div class="modal-input-group input-group-img fail">
            <button type="button" class="btn__add-img" id="update__product-add-img">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-camera-plus" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M12 20h-7a2 2 0 0 1 -2 -2v-9a2 2 0 0 1 2 -2h1a2 2 0 0 0 2 -2a1 1 0 0 1 1 -1h6a1 1 0 0 1 1 1a2 2 0 0 0 2 2h1a2 2 0 0 1 2 2v3.5" />
                    <path d="M16 19h6" />
                    <path d="M19 16v6" />
                    <path d="M9 13a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                </svg>
                <span>Agregar imagen ilustrativa</span>
            </button>
            <input type="file" id="update__product-img" class="modal-input input__file-img" accept="image/jpeg,image/png,image/webp" hidden>
            <div class="modal__preview-img">
                <img class="preview__img" alt="Preview" id="update__product-preview-img">
                <button type="button" class="btn__remove-img" id="update__product-delete-img">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-x" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M18 6l-12 12" />
                        <path d="M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <p class="errormessage__form"></p>
        </div>
        <div class="modal-btns">
            <button type="button" class="modal-btn cancel modal-close" id="close--modal__update-product--2">Cancelar</button>
            <button type="button" class="modal-btn confirm" id="update__product-submit">Confirmar</button>
        </div>
    </div>
</div>

<script type="text/javascript" src="../../js/products.js"></script>