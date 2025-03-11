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
                                    <h1 class="box-title text-capitalize text-maroon" style="font-size: 3rem; text-shadow: 1px 1px 2px black;">maestra productos&nbsp;&nbsp;&nbsp;
                                        <button class="btn btn-success flat" id="btnadd"><i class="fa fa-plus-circle"></i> Agregar</button>
                                        <!--<a href="../reportes/rptarticulos.php" target="_blank">
                                            <button class="btn btn-info" id="btnReport"><i class="fa fa-clipboard"></i> Reporte</button>
                                        </a>-->
                                    </h1>
                                    <div class="box-tools pull-right">
                                    </div>
                                </div>
                                <!-- /.box-header -->
                                <!-- centro -->
                                <div class="panel-body table-responsive" id="listproduct">
                                    <table id="tblproduct" class="table table-striped table-bordered table-condensed table-hover" style="width:100%">
                                        <thead class="bg-gray-light">
                                            <tr>
                                                <th>Acciones</th>
                                                <th>Código Producto</th>
                                                <th>Nombre Producto</th>
                                                <th>Codigo Barra</th>
                                                <th>Marca</th>
                                                <th>Fecha Ingreso</th>
                                                <th>Fecha Actualizado</th>
                                                <th>Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="panel-body" id="panelproduct">
                                    <form name="form" method="POST">
                                        <div class="row">
                                            <div class="form-group col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                <input type="hidden" name="productid" id="productid" value="" />
                                                <label for="productcode">Código Producto (*):</label>
                                                <input type="text" class="form-control" name="productcode" id="productcode" maxlength="15" placeholder="AA10101" />
                                            </div>
                                            <div class="form-group col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                <label for="productname">Nombre Producto (*):</label>
                                                <input type="text" class="form-control" name="productname" id="productname" maxlength="45" placeholder="Escriba nombre del producto" />
                                            </div>
                                            <div class="form-group col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                <label for="barcode">Codigo de Barra:</label>
                                                <input type="text" class="form-control" name="barcode" id="barcode" placeholder="Escriba un codigo de barra" />
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                <label for="uomid">Unidad de Medida (*):</label>
                                                <select class="form-control selectpicker" data-live-search="false" name="uomid" id="uomid"></select>
                                            </div>
                                            <div class="form-group col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                <label for="categoryid">Categoria (*):</label>
                                                <select class="form-control selectpicker" data-live-search="false" name="categoryid" id="categoryid"></select>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                <label for="weight">Peso:</label>
                                                <input type="number" class="form-control text-right" name="weight" id="weight" min="0" value="0" />
                                            </div>
                                            <div class="form-group col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                <label for="volume">Volumen:</label>
                                                <input type="number" class="form-control text-right" name="volume" id="volume" min="0" value="0" />
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                <label for="long">Largo:</label>
                                                <input type="number" class="form-control text-right" name="long" id="long" min="0" value="0" />
                                            </div>
                                            <div class="form-group col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                <label for="width">Ancho:</label>
                                                <input type="number" class="form-control text-right" name="width" id="width" min="0" value="0" />
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                <label for="height">Alto:</label>
                                                <input type="number" class="form-control text-right" name="height" id="height" min="0" value="0" />
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-lg-8 col-md-8 col-sm-8 col-xs-12">
                                                <label for="brand">Marca del Producto:</label>
                                                <input type="text" class="form-control" name="brand" id="brand" maxlength="45" placeholder="Marca del producto" />
                                            </div>
                                        </div>
                                        <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                            <button class="btn btn-primary" type="submit" id="btnsave"><i class="fa fa-save"></i> Guardar</button>
                                            <button class="btn btn-danger" type="button" id="btncancel"><i class="fa fa-arrow-circle-left"></i> Cancelar</button>
                                        </div>
                                    </form>
                                </div>
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

        <script src="src/ajax/product.js"></script>
    </body>
</html>
<?php
    } // Cierre if
} // Cierre if
ob_end_flush();
?>