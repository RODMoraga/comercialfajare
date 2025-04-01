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
                                    <h1 <h1 class="box-title text-capitalize text-maroon" style="font-size: 3rem; text-shadow: 1px 1px 2px black;">informe de ventas diarias</h1>
                                    <div class="box-tools pull-right"></div>
                                </div>
                                <!-- /.box-header -->
                                <!-- centro -->
                                <div class="panel-body" style="margin: 0 2rem;">
                                    <div class="row">
                                        <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                            <label for="datestart">Fecha Inicio</label>
                                            <input type="date" class="form-control text-right" name="datestart" id="datestart" value="<?php echo $datestart; ?>" />
                                        </div>
                                        <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                            <label for="dateend">Fecha Termino</label>
                                            <input type="date" class="form-control text-right" name="dateend" id="dateend" value="<?php echo $dateend; ?>" />
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                            <label for="establishment">Establecimiento</label>
                                            <select class="form-control selectpicker" name="establishment" id="establishment" title="Seleccione uno o más establecimientos" data-live-search="true" multiple data-max-options="10" data-size="10"></select>
                                        </div>
                                        <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                            <button type="button" class="btn btn-default" style="margin-top: 2.5rem;" id="btnchartrefresh"><i class="fa fa-refresh" aria-hidden="true"></i>&nbsp;Consultar</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel-body">
                                    <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
                                        <div class="box box-primary">
                                            <div class="box-header with-border text-center text-bold">
                                                Gráfica Ventas Emitidas Por Fechas&nbsp;&nbsp;&nbsp;<i class="fa fa-line-chart"></i>
                                            </div>
                                            <div class="box-body" id="graphic">
                                                <canvas id="dialySales" width="400" height="200"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                        <div class="box box-primary">
                                            <div class="box-header with-border text-center text-bold">
                                                Tabla Ventas Emitidas Por Fecha&nbsp;&nbsp;&nbsp;<i class="fa fa-bar-chart"></i>
                                            </div>
                                            <div class="box-body">
                                                <table class="table table-bordered table-hover" id="tbldailysales">
                                                    <thead class="bg-light-blue-gradient">
                                                        <tr>
                                                            <th>Fecha</th>
                                                            <th>Venta Neta</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody></tbody>
                                                    <tfoot class="bg-teal-gradient"></tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
        <!-- Sweetalert2 -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <!-- bootstrap-select.min -->
        <script src="src/public/js/bootstrap-select.min.js"></script>
        <!-- ChartJS -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
        <!-- Toastr -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js" integrity="sha512-VEd+nq25CkR676O+pLBnDW09R7VQX9Mdiij052gVCp5yVH3jGtH70Ho/UUv4mJDsEdTvqRCFZg0NKGiojGnUCw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

        <script src="src/ajax/dailysales.js"></script>
    </body>
</html>
<?php
    } // Cierre if
} // Cierre if
ob_end_flush();
?>