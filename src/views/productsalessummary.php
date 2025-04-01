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
    <body class="hold-transition skin-blue sidebar-mini">
        <div class="wrapper">
            <?php include "src/includes/header.php";?>
            <?php include "src/includes/wrapper.php";?>
            <!--Contenido-->
            <!-- Content Wrapper. Contains page content -->
            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">        
                <!-- Main content -->
                <section class="content">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="box">
                                <div class="box-header with-border">
                                    <h1 <h1 class="box-title text-capitalize text-maroon" style="font-size: 3rem; text-shadow: 1px 1px 2px black;">resumen de ventas por productos</h1>
                                    <div class="box-tools pull-right"></div>
                                </div>
                                <!-- /.box-header -->
                                <!-- centro -->
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                            <label for="datestart">Fecha Inicio</label>
                                            <input type="date" class="form-control text-right" name="datestart" id="datestart" value="<?php echo $datestart; ?>" />
                                        </div>
                                        <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                            <label for="dateend">Fecha Termino</label>
                                            <input type="date" class="form-control text-right" name="dateend" id="dateend" value="<?php echo $dateend; ?>" />
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                                            <label for="establishment">Establecimiento</label>
                                            <select class="form-control selectpicker" name="establishment" id="establishment" title="Seleccione uno o más establecimientos" data-live-search="true" multiple data-max-options="10" data-size="10"></select>
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                                            <label for="productname">Productos</label>
                                            <select class="form-control selectpicker" name="productname" id="productname" title="Seleccione uno o más productos" data-live-search="true" multiple data-max-options="10" data-size="10"></select>
                                        </div>
                                        <div class="col-lg-1 col-md-1 col-sm-12 col-xs-12">
                                            <button type="button" class="btn btn-default" style="margin-top: 2.5rem;" id="btntablerefresh"><i class="fa fa-area-chart"></i>&nbsp; Consultar</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel-body">
                                    <div class="box box-primary">
                                        <div class="row" style="margin-bottom: 1rem;">
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"></div>
                                        </div>
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                            <table class="table table-bordered table-hover" id="tblproductsummary" style="width: 100%;">
                                                <thead class="bg-light-blue-gradient">
                                                    <tr>
                                                        <th>ACCIONES</th>
                                                        <th>CODIGO PRODUCTO</th>
                                                        <th>NOMBRE PRODUCTO</th>
                                                        <th>CANTIDADES</th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- /.box -->
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                    <div class="modal fade" id="modalProductTransaction" tabindex="-1" role="dialog" aria-labelledby="modalPaymentListLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header bg-blue-gradient">
                                    <div class="form-group">
                                        <h3 class="modal-title" id="modalProductTransactionLabel">Transacciones</h3>
                                    </div>
                                </div>
                                <div class="modal-body">
                                    <table class="table table-bordered table-hover table-striped" id="tbltransaction" style="width: 100%;">
                                        <thead class="row bg-primary">
                                            <tr>
                                                <th>NRO DOCUMENTO</th>
                                                <th>FEC DOCUMENTO</th>
                                                <th>CLIENTE</th>
                                                <th>CANTIDAD</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                        <tfoot class="bg-success"></tfoot>
                                    </table>
                                </div>
                                <div class="modal-footer">
                                    <button class="btn btn-default" type="button" id="btncancellist" data-dismiss="modal" arial-label="Close"><i class="fa fa-arrow-circle-left"></i> Cerrar</button>
                                </div>
                            </div>
                        </div>
                    </div>                                
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
        <!-- Sweetalert2 -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <!-- bootstrap-select.min -->
        <script src="src/public/js/bootstrap-select.min.js"></script>
        <!-- Toastr -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js" integrity="sha512-VEd+nq25CkR676O+pLBnDW09R7VQX9Mdiij052gVCp5yVH3jGtH70Ho/UUv4mJDsEdTvqRCFZg0NKGiojGnUCw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

        <script src="src/ajax/productsalessummary.js"></script>
    </body>
</html>
<?php
    } // Cierre if
} // Cierre if
ob_end_flush();
?>