<?php

ob_start();

if (session_status() === PHP_SESSION_NONE)
    session_start();

if (!isset($_SESSION["access"][0])) {
    session_unset();
    session_destroy();

    header("Location: /");
} else {
    if ($_SESSION["access"][2] === "Administrador") {
        $datestart = date("Y-m-01");
        $dateend   = date("Y-m-t");
?>
<!DOCTYPE html>
<html lang="en">
    <?php include "src/includes/head.php" ?>
    <!-- Toastr -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" integrity="sha512-vKMx8UnXk60zUwyUnUPM3HbQo8QfmNx7+ltw8Pm5zLusl1XIfwcxo8DbWCqMGKaWeNxWA8yrx5v3SaVpMvR3CA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        #details thead tr th:nth-child(3),
        #details tbody tr td:nth-child(3) {
            width: 30%;
        }

        #details thead tr th:nth-child(4),
        #details thead tr th:nth-child(5),
        #details thead tr th:nth-child(6),
        #details thead tr th:nth-child(7) {
            width: 10%;
        }

        #details tbody tr td:nth-child(7) {
            color: green;
            font-size: 1.8rem;
            font-weight: 500;
            text-align: right;
        }

        #details tfoot tr {
            /*background-color: green;
            color: white;*/
            font-size: 1.8rem;
            font-weight: 500;
            text-align: right;
        }
    </style>
    <body class="hold-transition skin-blue sidebar-mini">
        <div class="wrapper">
            <?php include "src/includes/header.php";?>
            <?php include "src/includes/wrapper.php";?>
            <!--Contenido-->
            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">        
                <!-- Main content -->
                <section class="content">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="box">
                                <div class="box-header with-border">
                                    <h1 class="box-title text-capitalize text-maroon" style="font-size: 3rem; text-shadow: 1px 1px 2px black;">nota de pedidos&nbsp;&nbsp;&nbsp;
                                        <button class="btn btn-success" id="btnadd"><i class="fa fa-plus-circle"></i> Crear Nota de Pedido</button>
                                    </h1>
                                    <div class="box-tools pull-right"></div>
                                </div>
                                <!-- /.box-header -->
                                <!-- centro -->
                                <div class="panel-body" id="panelfilters">
                                    <div class="row">
                                        <div class="form-group form-group-sm col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                            <select class="form-control selectpicker text-bold" title="Seleccione uno o más establecimientos" data-live-search="true" name="complex-filter" id="complex-filter" multiple data-max-options="10" data-size="10"></select>
                                        </div>
                                        <div class="form-group form-group-sm col-lg-2 col-md-2 col-sm-2 col-xs-12">
                                            <input type="date" class="form-control text-right text-bold" name="datestart" id="datestart" value="<?php echo $datestart; ?>" />
                                        </div>
                                        <div class="form-group form-group-sm col-lg-2 col-md-2 col-sm-2 col-xs-12">
                                            <input type="date" class="form-control text-right text-bold" name="dateend" id="dateend" value="<?php echo $dateend; ?>" />
                                        </div>
                                        <div class="form-group col-lg-2 col-md-2 col-sm-2 col-xs-12">
                                            <button type="button" class="btn btn-info btn-sm" id="btnrefresh"><i class="fa fa-search-plus"></i>&nbsp;&nbsp;Refrescar Lista</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel-body table-responsive" id="listordernotes">
                                    <table id="tbllistprice" class="table table-striped table-bordered table-condensed table-hover" style="width:100%">
                                        <thead class="bg-gray-light">
                                            <tr>
                                                <th>ACCIONES</th>
                                                <th>TIPO DOCUMENTO</th>
                                                <th>NRO DOCUMENTO</th>
                                                <th>FECHA ENTREGA</th>
                                                <th>ESTABLECIMIENTO</th>
                                                <th>MONTO TOTAL</th>
                                                <th>STATUS</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="panel-body" id="panelordernote">
                                    <form method="POST" name="form">
                                        <div class="row">
                                            <div class="form-group col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                                <label for="type">Tipo Documento:</label>
                                                <input type="hidden" name="headerdocumentid" id="headerdocumentid" value="" />
                                                <input type="hidden" name="net" id="net" value="" />
                                                <input type="hidden" name="tax" id="tax" value="" />
                                                <input type="hidden" name="total" id="total" value="" />
                                                <input type="hidden" name="applytotal" id="applytotal" value="" />
                                                <select name="type" id="type" class="form-control selectpicker" data-live-search="false">
                                                    <option value="notapedido">Nota de Pedido</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                                <label for="folio">Numero Documento (*):</label>
                                                <input type="number" name="folio" id="folio" class="form-control text-bold text-right" />
                                            </div>
                                            <div class="form-group col-lg-2 col-md-2 col-sm-2 col-xs-12">
                                                <label for="deliverdate">Fecha Entrega (*):</label>
                                                <input type="date" name="deliverdate" id="deliverdate" class="form-control text-bold text-right" value="<?php echo date("Y-m-d"); ?>" />
                                            </div>
                                            <div class="form-group col-lg-2 col-md-2 col-sm-2 col-xs-12">
                                                <label for="dateorder">Fecha Orden (*):</label>
                                                <input type="date" name="dateorder" id="dateorder" class="form-control text-bold text-right" value="<?php echo date("Y-m-d") ?>" />
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                                <label for="establishment">Complejo (*):</label>
                                                <select class="form-control selectpicker" name="establishment" id="establishment" data-live-search="true" title="Selección del Establecimiento" data-size="10"></select>
                                            </div>
                                            <div class="form-group col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                                <label for="customername">Nombre del Cliente (*):</label>
                                                <input type="text" class="form-control" name="customername" id="customer" placeholder="Nombre del cliente" disabled />
                                            </div>
                                            <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                <label for="street">Dirección:</label>
                                                <input type="text" class="form-control" name="street" id="street" disabled />
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                <label for="email">E-Mail:</label>
                                                <input type="text" class="form-control" name="email" id="email" disabled />
                                            </div>
                                            <div class="form-group col-lg-2 col-md-2 col-sm-2 col-xs-12">
                                                <label for="phone1">Teléfono:</label>
                                                <input type="text" class="form-control" name="phone1" id="phone1" disabled />
                                            </div>
                                            <div class="form-group col-lg-2 col-md-2 col-sm-2 col-xs-12">
                                                <label for="phone">Teléfono 2:</label>
                                                <input type="text" class="form-control text-bold" name="phone2" id="phone2" disabled />
                                            </div>
                                            <div class="form-group col-lg-2 col-md-2 col-sm-2 col-xs-12">
                                                <label for="typefolio">Tipo Cliente:</label>
                                                <input type="text" class="form-control text-bold" name="typefolio" id="typefolio" disabled />
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                <label for="gloss">Observaciones:</label>
                                                <input type="text" class="form-control text-bold" name="gloss" id="gloss" maxlength="75" />
                                            </div>
                                        </div>
                                        <!-- Tabla detalles productos -->
                                         <div class="row">
                                            <div class="form-group col-lg-12 col-md-12 col-sm-12 col-sx-12">
                                                <hr style="border: 1px solid green">
                                            </div>
                                            <div class="form-group col-lg-6 col-md-6 col-sm-6 col-sx-12">
                                                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#formModal" id="btnaddproduct"><i class="fa fa-plus"></i> Agregar Producto</button>
                                                <button type="button" class="btn btn-warning" id="btnremoveall"><i class="fa fa-trash-o"></i> Eliminar Todos</button>
                                            </div>
                                            <div class="form-group col-lg-6 col-md-6 col-sm-6 col-sx-12">
                                                <input type="text" class="form-control" name="textboxfilter" id="textboxfilter" data-toggle="tooltip" title="Haga clic aquí para filtrar en la tabla" data-placement="top" maxlength="25" placeholder="Ingrese contenido a buscar" />
                                            </div>
                                         </div>
                                        <div class="row">
                                            <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                <div style="border-bottom: 2px solid green; padding: 0.175rem; overflow-y: scroll; height: 30rem;">
                                                    <table class="table table-bordered" id="details">
                                                        <thead class="bg-green-gradient">
                                                            <tr>
                                                                <th>ACCIONES</th>
                                                                <th>CODIGO PRODUCTO</th>
                                                                <th>DESCRIPCION</th>
                                                                <th>CANTIDAD</th>
                                                                <th>PRECIO</th>
                                                                <th>DCTO</th>
                                                                <th>SUB-TOTAL</th>
                                                                <th class="hidden">D-ID</th>
                                                                <th class="hidden">P-ID</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody></tbody>
                                                        <tfoot class="bg-green-gradient">
                                                            <tr>
                                                                <td></td>
                                                                <td></td>
                                                                <td></td>
                                                                <td class="text-right">Totales:</td>
                                                                <td>0</td>
                                                                <td>0</td>
                                                                <td>0</td>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Fin Tabla detalles productos -->
                                         <div class="row">
                                             <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                 <button class="btn btn-primary" type="submit" id="btnsave"><i class="fa fa-save"></i> Guardar</button>
                                                 <button class="btn btn-danger" type="button" id="btncancel"><i class="fa fa-arrow-circle-left"></i> Cancelar</button>
                                             </div>
                                         </div>
                                    </form>
                                </div>
                                <!-- Modal -->
                                <div class="modal fade" id="formModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content modal-lg">
                                            <div class="modal-header bg-blue-gradient">
                                                <div class="form-group">
                                                    <h3 class="modal-title" id="exampleModalLabel">Módulo de Productos</h3>
                                                    <!--<button type="button" class="close" data-dismiss="modal" arial-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>-->
                                                </div>
                                            </div>
                                            <div class="modal-body table-responsive">
                                                <table id="listproducts" class="table table-bordered table-hover table-striped" style="width: 100%;">
                                                    <thead class="bg-blue-gradient">
                                                        <tr>
                                                            <th>ACCIONES</th>
                                                            <th>CODIGO PRODUCTO</th>
                                                            <th>DESCRIPCION</th>
                                                            <th>PRECIO</th>
                                                            <th>DCTO</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody></tbody>
                                                </table>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div> <!-- Find Modal -->
                                <!--Fin centro -->
                            </div><!-- /.box -->
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </section><!-- /.content -->
            </div><!-- /.content-wrapper -->
            <!--Fin-Contenido-->
        </div>
        <?php include "src/includes/footer.php" ?>
        <!-- jQuery -->
        <script src="src/public/js/jquery-3.1.1.min.js"></script>
        <!-- Bootstrap 3.3.5 -->
        <script src="src/public/js/bootstrap.min.js"></script>
        <!-- AdminLTE App -->
        <script src="src/public/js/app.min.js"></script>
        <!-- DATATABLES -->
        <script src="src/public/datatables/jquery.dataTables.min.js"></script>    
        <script src="src/public/datatables/dataTables.buttons.min.js"></script>
        <script src="src/public/datatables/buttons.html5.min.js"></script>
        <script src="src/public/datatables/buttons.colVis.min.js"></script>
        <script src="src/public/datatables/jszip.min.js"></script>
        <script src="src/public/datatables/pdfmake.min.js"></script>
        <script src="src/public/datatables/vfs_fonts.js"></script> 

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="src/public/js/bootstrap-select.min.js"></script>

        <!-- Toastr -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js" integrity="sha512-VEd+nq25CkR676O+pLBnDW09R7VQX9Mdiij052gVCp5yVH3jGtH70Ho/UUv4mJDsEdTvqRCFZg0NKGiojGnUCw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

        <script src="src/ajax/ordernote.js"></script>
    </body>
</html>
<?php
    } // Cierre if
} // Cierre if
ob_end_flush();
?>