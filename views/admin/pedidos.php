<!-- CONTENIDO -->
<div class="main-container">
    <div class="container__page-title">
        <h2 class="page__title">Todos los pedidos</h2>
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
                    $filterByProduct = array_search($_GET['filterBy'], array_column($products, 'id'));
                    echo ($_GET['filterBy'] != 'all') ? $products[$filterByProduct]['nombre'] : 'Todos';
                    ?>
                </button>
                <ul class="dropdown-menu">
                    <li><button type="button" class="dropdown-item filter__by-item" id="filter_by-all">Todos</button></li>

                    <?php
                    foreach ($products as $key => $product) { ?>
                        <li><button type="button" class="dropdown-item filter__by-item" id="filter_by-<?php echo $product['id'] ?>"><?php echo $product['nombre'] ?></button></li>
                    <?php
                    } ?>
                </ul>
            </div>

            <span>Mostrar todos </span>
            <div class="form-check form-switch">
                <input type="checkbox" class="form-check-input" id="check-allow-all" value="" <?php echo ($_GET['allowAll'] == 'yes') ? 'checked' : null; ?>>
            </div>
        </div>
    </div>


    <table class="container__table">
        <thead class="container__table-head">
            <tr class="container__table-row row-head">
                <th class="container__table-celd ">Código</th>
                <th class="container__table-celd ">Estado</th>
                <th class="container__table-celd ">Envio</th>
                <th class="container__table-celd ">Productos</th>
                <th class="container__table-celd ">Nombre completo</th>
                <th class="container__table-celd ">DNI</th>
                <th class="container__table-celd ">Telefono</th>
                <th class="container__table-celd ">Creación</th>
                <th class="container__table-celd ">Opciones</th>
            </tr>
        </thead>

        <tbody class="container__table-body" id="table_body">
            <?php
            foreach ($rowOrders as $key => $order) { ?>

                <tr class="container__table-row row-normal" id="row__order-<?php echo $order['id'] ?>">
                    <td class="container__table-celd celd-id"><?php echo $order['id'] ?></td>
                    <td class="container__table-celd celd-state"><?php echo $order['estado'] ?></td>
                    <?php
                    if ($order['id_envio'] == null) { ?>
                        <td class="container__table-celd celd-delivery">-</td>
                    <?php
                    } else { ?>
                        <td class="container__table-celd celd-delivery"><a href="envios.php?search=<?php echo $order['id_envio'] ?>&filterBy=all&allowAll=yes">Sí</a></td>
                    <?php
                    } ?>
                    <td class="container__table-celd celd-products"><?php echo $order['productos'] ?></td>
                    <td class="container__table-celd celd-name_surname"><?php echo $order['nombre_completo'] ?></td>
                    <td class="container__table-celd celd-dni"><?php echo $order['dni'] ?></td>
                    <td class="container__table-celd celd-tel"><?php echo $order['telefono'] ?></td>
                    <td class="container__table-celd celd-creation"><?php echo date('d/m/Y', strtotime($order['fecha_creacion'])) ?></td>
                    <td class="container__table-celd celd-options">
                        <button type="button" class="btn-update btn__update" id="update-<?php echo $order['id'] ?>" title="Editar">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-edit" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"></path>
                                <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"></path>
                                <path d="M16 5l3 3"></path>
                            </svg>
                        </button>
                        <?php
                        if ($order['fecha_entrega'] == null) { ?>
                            <button type="button" class="btn-deliver btn__deliver" id="deliver__order-<?php echo $order['id'] ?>" title="Entregado">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-check" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M5 12l5 5l10 -10" />
                                </svg>
                            </button>
                        <?php
                        } else { ?>
                            <button type="button" class="btn-deliver delivered btn__deliver" id="deliver__order-<?php echo $order['id'] ?>" title="Recuperar">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-x" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M18 6l-12 12" />
                                    <path d="M6 6l12 12" />
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


<!-- MODAL AGREGAR PEDIDOS -->
<div class="modal" id="modal__create">
    <div class="modal-content">
        <div class="modal-info">
            <h3>Agregar pedido</h3>
            <button type="button" class="btn-close-x modal-close" id="close--modal__create--1">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-x" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M18 6l-12 12" />
                    <path d="M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div class="modal-input-group">
            <label>Productos</label>
            <div class="products-container" id="create-product">
                <div id="container__product-1">
                    <div class="container__number-of-product">1.</div>
                    <div class="container__product-orders create">
                        <input type="number" class="modal-input input__cant-product create" id="create-product--number-1">
                        <select class="modal-input select-product create" id="create-product--select-1">
                            <option value="0" disabled hidden selected>Seleccione un producto</option>
                            <?php
                            foreach ($products as $key => $product) { ?>
                                <option value="<?php echo $product['id'] ?>" aria-stock="<?php echo $product['stock'] ?>"><?php echo $product['nombre'] ?></option>
                            <?php
                            } ?>
                        </select>
                        <p class="errormessage__form for-stock"></p>
                        <p class="errormessage__form for-product-name"></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-inputs-double">
            <div class="modal-input-group">
                <label>Nombre</label>
                <input type="text" id="create-name" class="modal-input">
                <p class="errormessage__form"></p>
            </div>
            <div class="modal-input-group">
                <label>Apellido</label>
                <input type="text" id="create-surname" class="modal-input">
                <p class="errormessage__form"></p>
            </div>
        </div>
        <div class="modal-input-group">
            <label>DNI</label>
            <input type="number" id="create-dni" class="modal-input input-no-flechas">
            <p class="errormessage__form"></p>
        </div>
        <div class="modal-input-group">
            <label>Telefono</label>
            <input type="number" id="create-tel" class="modal-input input-no-flechas">
            <p class="errormessage__form"></p>
        </div>
        <div class="modal-input-group">
            <label>Estado</label>
            <select id="create-state" class="modal-input select-state">
                <?php
                foreach ($states as $key => $state) { ?>
                    <option value="<?php echo $state['id'] ?>"><?php echo $state['descripcion'] ?></option>
                <?php
                } ?>
            </select>
            <p class="errormessage__form"></p>
        </div>
        <div class="modal-input-group container-check-delivery">
            <!-- Hacer checkbox para envio, si este esta activado, debe agregar 3 inputs: direccion, provincia, ciudad -->
            <div class="form-group delivery">
                <label class="label__delivery">
                    Envio a domicilio
                    <input type="checkbox" class="check-delivery" id="create-delivery">
                    <span class="checkmark"></span>
                </label>
            </div>
            <p class="errormessage__form"></p>
        </div>
        <div class="container__info-delivery" id="create-info-delivery">
            <div class="modal-inputs-double">
                <div class="modal-input-group">
                    <label>Provincia</label>
                    <select class="modal-input select-province create" id="create-province">
                        <option value="0" disabled hidden selected>Seleccione la provincia</option>
                        <?php
                        foreach ($provinces as $key => $province) { ?>
                            <option value="<?php echo $province['id'] ?>"><?php echo $province['nombre'] ?></option>
                        <?php
                        } ?>
                    </select>
                    <p class="errormessage__form"></p>
                </div>
                <div class="modal-input-group">
                    <label>Ciudad</label>
                    <input type="text" id="create-city" class="modal-input">
                    <p class="errormessage__form"></p>
                </div>
            </div>
            <div class="modal-input-group">
                <label>Dirección</label>
                <input type="text" id="create-address" class="modal-input">
                <p class="errormessage__form"></p>
            </div>
        </div>
        <div class="modal-btns">
            <button type="button" class="modal-btn cancel modal-close" id="close--modal__create--2">Cancelar</button>
            <button type="button" class="modal-btn confirm" id="create-submit">Confirmar</button>
        </div>
    </div>
</div>


<!-- MODAL EDITAR PEDIDO -->
<div class="modal" id="modal__update">
    <div class="modal-content">
        <div class="modal-info">
            <h3>Editar pedido</h3>
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
            <label>Productos</label>
            <div class="products-container" id="update-product">
                <div id="container__product-1">
                    <div class="container__number-of-product">1.</div>
                    <div class="container__product-orders update">
                        <input type="number" class="modal-input input__cant-product update" id="update-product--number-1">
                        <select class="modal-input select-product update" id="update-product--select-1">
                            <option value="0" disabled hidden selected>Seleccione un producto</option>
                            <?php
                            foreach ($products as $key => $product) { ?>
                                <option value="<?php echo $product['id'] ?>" aria-stock="<?php echo $product['stock'] ?>"><?php echo $product['nombre'] ?></option>
                            <?php
                            } ?>
                        </select>
                        <p class="errormessage__form for-stock"></p>
                        <p class="errormessage__form for-product-name"></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-inputs-double">
            <div class="modal-input-group">
                <label>Nombre</label>
                <input type="text" id="update-name" class="modal-input">
                <p class="errormessage__form"></p>
            </div>
            <div class="modal-input-group">
                <label>Apellido</label>
                <input type="text" id="update-surname" class="modal-input">
                <p class="errormessage__form"></p>
            </div>
        </div>
        <div class="modal-input-group">
            <label>DNI</label>
            <input type="number" id="update-dni" class="modal-input input-no-flechas">
            <p class="errormessage__form"></p>
        </div>
        <div class="modal-input-group">
            <label>Telefono</label>
            <input type="number" id="update-tel" class="modal-input input-no-flechas">
            <p class="errormessage__form"></p>
        </div>
        <div class="modal-input-group">
            <label>Estado</label>
            <select id="update-state" class="modal-input select-state">
                <?php
                foreach ($states as $key => $state) { ?>
                    <option value="<?php echo $state['id'] ?>"><?php echo $state['descripcion'] ?></option>
                <?php
                } ?>
            </select>
            <p class="errormessage__form"></p>
        </div>
        <div class="modal-input-group container-check-delivery">
            <!-- Hacer checkbox para envio, si este esta activado, debe agregar 3 inputs: direccion, provincia, ciudad -->
            <div class="form-group delivery">
                <label class="label__delivery">
                    Envio a domicilio
                    <input type="checkbox" class="check-delivery" id="update-delivery">
                    <span class="checkmark"></span>
                </label>
            </div>
            <p class="errormessage__form"></p>
        </div>
        <div class="container__info-delivery" id="update-info-delivery">
            <div class="modal-inputs-double">
                <div class="modal-input-group">
                    <label>Provincia</label>
                    <select class="modal-input select-province update" id="update-province">
                        <option value="0" disabled hidden selected>Seleccione la provincia</option>
                        <?php
                        foreach ($provinces as $key => $province) { ?>
                            <option value="<?php echo $province['id'] ?>"><?php echo $province['nombre'] ?></option>
                        <?php
                        } ?>
                    </select>
                    <p class="errormessage__form"></p>
                </div>
                <div class="modal-input-group">
                    <label>Ciudad</label>
                    <input type="text" id="update-city" class="modal-input">
                    <p class="errormessage__form"></p>
                </div>
            </div>
            <div class="modal-input-group">
                <label>Dirección</label>
                <input type="text" id="update-address" class="modal-input">
                <p class="errormessage__form"></p>
            </div>
        </div>
        <div class="modal-btns">
            <button type="button" class="modal-btn cancel modal-close" id="close--modal__update--2">Cancelar</button>
            <button type="button" class="modal-btn confirm" id="update-submit">Confirmar</button>
        </div>
    </div>
</div>

<script type="text/javascript" src="../../js/orders.js"></script>