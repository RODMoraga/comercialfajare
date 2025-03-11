<?php

ob_start();

session_start();

if (!isset($_SESSION["access"][0])) {
    session_unset();
    session_destroy();

    header("Location: /");
} else {
    if ($_SESSION["access"][2] === "Administrador") {
?>
<!DOCTYPE html>
<html lang="en">
    <?php include "src/includes/head.php" ?>
    <!-- Toastr -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" integrity="sha512-vKMx8UnXk60zUwyUnUPM3HbQo8QfmNx7+ltw8Pm5zLusl1XIfwcxo8DbWCqMGKaWeNxWA8yrx5v3SaVpMvR3CA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
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
                                    <h1 class="box-title text-capitalize text-maroon" style="font-size: 3rem; text-shadow: 1px 1px 2px black;">maestra precios&nbsp;&nbsp;&nbsp;
                                        <button class="btn btn-success flat" id="btnadd"><i class="fa fa-plus-circle"></i> Agregar Precios</button>
                                        <!--<a href="../reportes/rptarticulos.php" target="_blank">
                                            <button class="btn btn-info" id="btnReport"><i class="fa fa-clipboard"></i> Reporte</button>
                                        </a>-->
                                    </h1>
                                    <div class="box-tools pull-right"></div>
                                </div>
                                <!-- /.box-header -->
                                <!-- centro -->
                                <div class="panel-body" id="panelfilters">
                                    <div class="row">
                                        <div class="form-group form-group-sm col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                            <select class="form-control selectpicker" title="Seleccione uno o más establecimientos" data-live-search="true" name="complex-filter" id="complex-filter" multiple data-max-options="5" data-size="7"></select>
                                        </div>
                                        <div class="form-group form-group-sm col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                            <select class="form-control selectpicker" title="Seleccione uno o más productos" data-live-search="true" name="product-filter" id="product-filter" multiple data-max-options="5" data-size="7"></select>
                                        </div>
                                        <div class="form-group col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                            <button type="button" class="btn btn-info btn-sm"><i class="fa fa-search-plus"></i>&nbsp;&nbsp;Refrescar Lista</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel-body table-responsive" id="listprices">
                                    <table id="tblprice" class="table table-striped table-bordered table-condensed table-hover" style="width:100%">
                                        <thead class="bg-gray-light">
                                            <tr>
                                                <th>Acciones</th>
                                                <th>Nombre Cliente</th>
                                                <th>Nombre Establecimiento</th>
                                                <th>Código Producto</th>
                                                <th>Nombre Producto</th>
                                                <th>Precio Base</th>
                                                <th>Dcto 1</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="panel-body" id="panelprice">
                                    <form method="POST" name="form">
                                        <div class="row">
                                            <div class="form-group col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                <input type="hidden" name="headerpriceid" id="headerpriceid" value="" />
                                                <label for="customerid">Nombre Establecimiento (*):</label>
                                                <select class="form-control selectpicker" data-live-search="true" data-size="10" title="Seleccione un Establecimiento" name="complex" id="complex"></select>
                                            </div>
                                            <div class="form-group col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                <label for="customername">Nombre Responsable (*):</label>
                                                <select class="form-control selectpicker" data-live-search="true" data-size="10" title="Seleccione nombre del cliente" name="customername" id="customername"></select>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                <label for="street">Dirección Despacho:</label>
                                                <input type="text" class="form-control text-bold" name="street" id="street" disabled />
                                            </div>
                                            <div class="form-group col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                <label for="phone1">Teléfono:</label>
                                                <input type="text" class="form-control text-bold" name="phone1" id="phone1" disabled />
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-lg-12 col-md-12 col-sm-12 col-sx-12">
                                                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#formModal" id="btnaddproduct"><i class="fa fa-plus"></i> Agregar Producto</button>
                                                <button type="button" class="btn btn-warning" id="btnremoveitemtable"><i class="fa fa-trash-o"></i> Eliminar Todos</button>
                                            </div>
                                        </div>
                                        <!-- Tabla detalles productos -->
                                        <div class="row">
                                            <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                <div style="overflow-y: scroll;height: 40rem;">
                                                    <table class="table table-bordered" id="details">
                                                        <thead class="bg-green-gradient">
                                                            <tr>
                                                                <th>Id. Detalle</th>
                                                                <th>Id. Producto</th>
                                                                <th>Código Producto</th>
                                                                <th>Descripción Producto</th>
                                                                <th>Precio</th>
                                                                <th>Dcto(1)</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody></tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Fin Tabla detalles productos -->
                                        <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                            <button class="btn btn-primary" type="submit" id="btnsave"><i class="fa fa-save"></i> Guardar</button>
                                            <button class="btn btn-danger" type="button" id="btncancel"><i class="fa fa-arrow-circle-left"></i> Cancelar</button>
                                        </div>
                                    </form>
                                </div>
                                <!-- Modal -->
                                <div class="modal fade" id="formModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
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
                                                            <th>Opciones</th>
                                                            <th>Código Producto</th>
                                                            <th>Nombre Producto</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody></tbody>
                                                </table>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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

        <script src="src/ajax/prices.js"></script>
    </body>
</html>
<?php
    } // Cierre if
} // Cierre if
ob_end_flush();
?>