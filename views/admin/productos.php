<!-- CONTENIDO -->
<div class="main-container">
    <div class="container__page-title">
        <h2 class="page__title">Todos los productos</h2>
        <button type="button" class="btn-add" id="btn__open-create" title="Agregar">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                <path d="M12 5l0 14"></path>
                <path d="M5 12l14 0"></path>
            </svg>
        </button>
    </div>


    <div class="container__submenu">
        <div class="submenu__search-bar">
            <form method="GET" class="container__search-bar" action="">
                <input type="search" name="search" placeholder="Buscar..." value="<?php echo (isset($_GET['search'])) ? $_GET['search'] : null; ?>">
                <?php
                if (isset($_GET['for']) && $_GET['for'] == 'id') { ?>
                    <input type="text" name="for" value="<?php echo $_GET['for']; ?>" hidden>
                <?php
                } ?>
                <input type="text" name="filterBy" value="<?php echo $_GET['filterBy'] ?>" hidden>
                <input type="text" name="allowAll" value="<?php echo $_GET['allowAll'] ?>" hidden>
                <button type="submit">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-search" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
                        <path d="M21 21l-6 -6" />
                    </svg>
                </button>
            </form>
        </div>

        <div class="submenu__filter-by">
            <span>Filtrar por </span>
            <div class="dropdown dropdown__filter-by">
                <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <?php
                    $filterByCategory = array_search($_GET['filterBy'], array_column($categories, 'id'));
                    echo ($_GET['filterBy'] != 'all') ? $categories[$filterByCategory]['nombre'] : 'Todos';
                    ?>
                </button>
                <ul class="dropdown-menu">
                    <li><button type="button" class="dropdown-item filter__by-item" id="filter_by-all">Todos</button></li>

                    <?php
                    foreach ($categories as $key => $category) { ?>
                        <li><button type="button" class="dropdown-item filter__by-item" id="filter_by-<?php echo $category['id'] ?>"><?php echo $category['nombre'] ?></button></li>
                    <?php
                    } ?>
                </ul>
            </div>

            <span>Mostrar los eliminados </span>
            <div class="form-check form-switch">
                <input type="checkbox" class="form-check-input" id="check-allow-all" value="" <?php echo ($_GET['allowAll'] == 'yes') ? 'checked' : null; ?>>
            </div>
        </div>
    </div>


    <table class="container__table">
        <thead class="container__table-head">
            <tr class="container__table-row row-head">
                <th class="container__table-celd ">Código</th>
                <th class="container__table-celd ">Nombre</th>
                <th class="container__table-celd ">Categorías</th>
                <th class="container__table-celd ">Precio</th>
                <th class="container__table-celd ">Stock</th>
                <th class="container__table-celd ">Opciones</th>
            </tr>
        </thead>

        <tbody class="container__table-body" id="table_body">
            <?php
            foreach ($rowProducts as $key => $product) { ?>

                <tr class="container__table-row row-normal" id="row__product-<?php echo $product['id'] ?>">
                    <td class="container__table-celd celd-id"><?php echo $product['id'] ?></td>
                    <td class="container__table-celd celd-name"><?php echo $product['nombre'] ?></td>
                    <td class="container__table-celd celd-type">
                        <?php
                        if (!empty($product['categorias'])) { ?>
                            <ul class="table-celd-list">
                                <?php
                                foreach ($product['categorias'] as $productCategory) { ?>
                                    <li><?php echo $productCategory ?></li>
                                <?php
                                } ?>
                            </ul>
                        <?php
                        } else { ?>
                            <ul class="table-celd-list">
                                <li>No tiene categorías</li>
                            </ul>
                        <?php
                        } ?>
                    </td>
                    <td class="container__table-celd celd-price">$<?php echo $product['precio'] ?></td>
                    <td class="container__table-celd celd-stock"><?php echo $product['stock'] ?></td>
                    <td class="container__table-celd celd-options">
                        <button type="button" class="btn-update btn__update" id="update-<?php echo $product['id'] ?>" title="Editar">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-edit" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"></path>
                                <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"></path>
                                <path d="M16 5l3 3"></path>
                            </svg>
                        </button>
                        <?php
                        if ($product['fecha_eliminacion'] == null) { ?>
                            <button type="button" class="btn-delete btn__delete" id="delete__product-<?php echo $product['id'] ?>" title="Eliminar">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-trash" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M4 7l16 0"></path>
                                    <path d="M10 11l0 6"></path>
                                    <path d="M14 11l0 6"></path>
                                    <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"></path>
                                    <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"></path>
                                </svg>
                            </button>
                        <?php
                        } else { ?>
                            <button type="button" class="btn-delete deleted btn__delete" id="delete__product-<?php echo $product['id'] ?>" title="Recuperar">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-back-up" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M9 14l-4 -4l4 -4" />
                                    <path d="M5 10h11a4 4 0 1 1 0 8h-1" />
                                </svg>
                            </button>
                        <?php
                        } ?>
                    </td>
                </tr>
            <?php
            } ?>
        </tbody>
    </table>

    <!-- PAGINADOR -->
    <?php
    if ($total_paginas > 1) { ?>
        <div id="paginator" class="paginator">
            <button type="button" id="paginator_page-<?php echo ($page - 1 > 1) ? $page - 1 : 1; ?>" class="btn__paginator btn_navigatigation-page" id="prev-page" class="btn">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-circle-arrow-left-filled" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M12 2a10 10 0 0 1 .324 19.995l-.324 .005l-.324 -.005a10 10 0 0 1 .324 -19.995zm.707 5.293a1 1 0 0 0 -1.414 0l-4 4a1.048 1.048 0 0 0 -.083 .094l-.064 .092l-.052 .098l-.044 .11l-.03 .112l-.017 .126l-.003 .075l.004 .09l.007 .058l.025 .118l.035 .105l.054 .113l.043 .07l.071 .095l.054 .058l4 4l.094 .083a1 1 0 0 0 1.32 -1.497l-2.292 -2.293h5.585l.117 -.007a1 1 0 0 0 -.117 -1.993h-5.586l2.293 -2.293l.083 -.094a1 1 0 0 0 -.083 -1.32z" stroke-width="0" fill="currentColor" />
                </svg>
            </button>
            <div id="page-numbers" class="page-numbers">
                <button type="button" id="paginator_page-1" class='btn__paginator <?php echo ($page == 1) ? 'active' : null; ?>'>1</button>

                <?php
                // Calcular los botones del medio
                $middle_start = max(2, min($page - 1, $total_paginas - 3));
                $middle_end = min($middle_start + 2, $total_paginas);
                for ($i = $middle_start; $i <= $middle_end; $i++) { ?>
                    <button type="button" id="paginator_page-<?php echo $i ?>" class='<?php echo ($total_paginas <  5 && $i == $middle_end) ? 'last-page' : null; ?> btn__paginator <?php echo ($page == $i) ? 'active' : null; ?>'><?php echo $i ?></button>
                <?php
                } ?>

                <?php
                if ($total_paginas > 4) { ?>
                    <button type="button" id="paginator_page-<?php echo $total_paginas ?>" class='last-page btn__paginator <?php echo ($page == $total_paginas) ? 'active' : null; ?>'><?php echo $total_paginas ?></button>
                <?php
                } ?>
            </div>
            <button type="button" id="paginator_page-<?php echo ($page + 1 < $total_paginas) ? $page + 1 : $total_paginas; ?>" class="btn__paginator btn_navigatigation-page" id="next-page" class="btn">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-circle-arrow-right-filled" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M12 2l.324 .005a10 10 0 1 1 -.648 0l.324 -.005zm.613 5.21a1 1 0 0 0 -1.32 1.497l2.291 2.293h-5.584l-.117 .007a1 1 0 0 0 .117 1.993h5.584l-2.291 2.293l-.083 .094a1 1 0 0 0 1.497 1.32l4 -4l.073 -.082l.064 -.089l.062 -.113l.044 -.11l.03 -.112l.017 -.126l.003 -.075l-.007 -.118l-.029 -.148l-.035 -.105l-.054 -.113l-.071 -.111a1.008 1.008 0 0 0 -.097 -.112l-4 -4z" stroke-width="0" fill="currentColor" />
                </svg>
            </button>
        <?php
    } ?>
        </div>
</div>


<!-- MODAL AGREGAR PRODUCTOS -->
<div class="modal" id="modal__create">
    <div class="modal-content">
        <div class="modal-info">
            <h3>Agregar producto</h3>
            <button type="button" class="btn-close-x modal-close" id="close--modal__create--1">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-x" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M18 6l-12 12" />
                    <path d="M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div class="modal-input-group">
            <label>Categoría</label>
            <select id="create-cat" class="modal-input select-category" multiple="multiple">
                <?php
                foreach ($categories as $key => $category) { ?>
                    <option value="<?php echo $category['id'] ?>"><?php echo $category['nombre'] ?></option>
                <?php
                } ?>
            </select>
            <p class="errormessage__form"></p>
        </div>
        <div class="modal-input-group">
            <label>Nombre</label>
            <input type="text" id="create-name" class="modal-input">
            <p class="errormessage__form"></p>
        </div>
        <div class="modal-input-group">
            <label>Stock</label>
            <input type="number" id="create-stock" class="modal-input">
            <p class="errormessage__form"></p>
        </div>
        <div class="modal-input-group">
            <label>Precio</label>
            <input type="number" id="create-price" class="modal-input">
            <p class="errormessage__form"></p>
        </div>
        <div class="modal-input-group input-group-img fail">
            <button type="button" class="btn__add-img" id="create-add-img">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-camera-plus" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M12 20h-7a2 2 0 0 1 -2 -2v-9a2 2 0 0 1 2 -2h1a2 2 0 0 0 2 -2a1 1 0 0 1 1 -1h6a1 1 0 0 1 1 1a2 2 0 0 0 2 2h1a2 2 0 0 1 2 2v3.5" />
                    <path d="M16 19h6" />
                    <path d="M19 16v6" />
                    <path d="M9 13a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                </svg>
                <span>Agregar imagen ilustrativa</span>
            </button>
            <input type="file" id="create-img" class="modal-input input__file-img" accept="image/jpeg,image/png,image/webp" hidden>
            <div class="modal__preview-img">
                <img class="preview__img" alt="Preview" id="create-preview-img">
                <button type="button" class="btn__remove-img" id="create-delete-img">
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
            <button type="button" class="modal-btn cancel modal-close" id="close--modal__create--2">Cancelar</button>
            <button type="button" class="modal-btn confirm" id="create-submit">Confirmar</button>
        </div>
    </div>
</div>


<!-- MODAL EDITAR PRODUCTOS -->
<div class="modal" id="modal__update">
    <div class="modal-content">
        <div class="modal-info">
            <h3>Editar producto</h3>
            <button type="button" class="btn-close-x modal-close" id="close--modal__update--1">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-x" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M18 6l-12 12" />
                    <path d="M6 6l12 12" />
                </svg>
            </button>
        </div>

        <input type="number" id="update-id" class="modal-input" hidden>

        <div class="modal-input-group">
            <label>Categoría</label>
            <select id="update-cat" class="modal-input select-category" multiple="multiple">
                <?php
                foreach ($categories as $key => $category) { ?>
                    <option value="<?php echo $category['id'] ?>"><?php echo $category['nombre'] ?></option>
                <?php
                } ?>
            </select>
            <p class="errormessage__form"></p>
        </div>
        <div class="modal-input-group">
            <label>Nombre</label>
            <input type="text" id="update-name" class="modal-input">
            <p class="errormessage__form"></p>
        </div>
        <div class="modal-input-group">
            <label>Stock</label>
            <input type="number" id="update-stock" class="modal-input">
            <p class="errormessage__form"></p>
        </div>
        <div class="modal-input-group">
            <label>Precio</label>
            <input type="number" id="update-price" class="modal-input">
            <p class="errormessage__form"></p>
        </div>
        <div class="modal-input-group input-group-img fail">
            <button type="button" class="btn__add-img" id="update-add-img">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-camera-plus" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M12 20h-7a2 2 0 0 1 -2 -2v-9a2 2 0 0 1 2 -2h1a2 2 0 0 0 2 -2a1 1 0 0 1 1 -1h6a1 1 0 0 1 1 1a2 2 0 0 0 2 2h1a2 2 0 0 1 2 2v3.5" />
                    <path d="M16 19h6" />
                    <path d="M19 16v6" />
                    <path d="M9 13a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                </svg>
                <span>Agregar imagen ilustrativa</span>
            </button>
            <input type="file" id="update-img" class="modal-input input__file-img" accept="image/jpeg,image/png,image/webp" hidden>
            <div class="modal__preview-img">
                <img class="preview__img" alt="Preview" id="update-preview-img">
                <button type="button" class="btn__remove-img" id="update-delete-img">
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
            <button type="button" class="modal-btn cancel modal-close" id="close--modal__update--2">Cancelar</button>
            <button type="button" class="modal-btn confirm" id="update-submit">Confirmar</button>
        </div>
    </div>
</div>

<script type="text/javascript" src="../../js/products.js"></script>