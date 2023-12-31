<div class="body-page">
    <h2>Inicio / Pendientes de despacho</h2>
    <table class="mt10">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Telefono</th>
                <th>Tipo</th>
                <th>Estado</th>
                <th>Dirección</th>
                <th>Teléfono</th>
                <th>Opciones</th>
            </tr>
        </thead>				
        <tbody>
            <?php
                    $sql="SELECT ped.*,usu.*,pro.*,
                    CASE WHEN ped.estado=2
                    THEN 'Por pagar'
                    ELSE 
                        CASE WHEN ped.estado=3
                            THEN 'Por entregar'
                            ELSE
                            CASE WHEN ped.estado=4
                                THEN 'En camino'
                                ELSE 'Otro'
                            END
                        END
                    END estadotexto, ped.estado estadoped
                    from pedido ped
                    inner  join usuario usu
                    on ped.codusu=usu.codusu
                    inner  join producto pro
                    on ped.codpro=pro.codpro
                    where ped.estado=2 or ped.estado=3 or ped.estado=4";
                $stmt=sqlsrv_query($conn,$sql);
                while ($row=sqlsrv_fetch_array($stmt, SQLSRV_FETCH_BOTH)) {
                    echo 
            '<tr>
                <td>'.$row['codped'].'</td>
                <td>'.$row['codusu'].' - '.$row['nomusu'].'</td>
                <td>'.$row['codpro'].' - '.$row['nompro'].'</td>
                <td>'.$row['fecped'].'</td>
                <td>'.$row['estadotexto'].'</td>
                <td>'.$row['dirusuped'].'</td>
                <td>'.$row['telusuped'].'</td>';
                if ($row['estadoped']==4) {
                    echo
                '<td class="td-option">
                    <button onclick="confirmar_entrega('.$row['codped'].')">Entregado</button>
                </td>';	
                }else{
                    echo
                '<td class="td-option">
                    <button onclick="despachado('.$row['codped'].')">Despachar</button>
                </td>';
                }
                echo
            '</tr>';
                }
            ?>
        </tbody>
    </table>
</div>

<script type="text/javascript">
    function show_modal(id){
        document.getElementById(id).style.display="block";
    }
    function hide_modal(id){
        document.getElementById(id).style.display="none";
    }
    function despachado(codped){
        let fd=new FormData();
        fd.append('codped',codped);
        let request=new XMLHttpRequest();
        request.open('POST','api/pedido_confirm.php',true);
        request.onload=function(){
            if (request.readyState==4 && request.status==200) {
                let response=JSON.parse(request.responseText);
                console.log(response);
                if (response.state) {
                    window.location.reload();
                }else{
                    alert(response.detail);
                }
            }
        }
        request.send(fd);
    }
    function confirmar_entrega(codped){
        let fd=new FormData();
        fd.append('codped',codped);
        let request=new XMLHttpRequest();
        request.open('POST','api/pedido_confirm_entrega.php',true);
        request.onload=function(){
            if (request.readyState==4 && request.status==200) {
                let response=JSON.parse(request.responseText);
                console.log(response);
                if (response.state) {
                    window.location.reload();
                }else{
                    alert(response.detail);
                }
            }
        }
        request.send(fd);
    }
</script>