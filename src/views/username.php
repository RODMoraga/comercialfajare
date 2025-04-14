<?php

ob_start();

if (session_status() === PHP_SESSION_NONE)
    session_start();

if (!isset($_SESSION["access"][0])) {
    $_SESSION = [];

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
                                    <h1 class="box-title text-capitalize text-maroon" style="font-size: 3rem; text-shadow: 1px 1px 2px black;">maestra usuarios&nbsp;&nbsp;&nbsp;
                                        <button class="btn btn-success flat" id="btnadd" data-toggle="modal" data-target="#usernameModal"><i class="fa fa-plus-circle"></i> Agregar</button>
                                    </h1>
                                    <div class="box-tools pull-right">
                                    </div>
                                </div>
                                <!-- /.box-header -->
                                <!-- centro -->
                                <div class="panel-body table-responsive">
                                    <table id="tblusername" class="table table-striped table-bordered table-condensed table-hover" style="width:100%">
                                        <thead class="bg-green-gradient">
                                            <tr>
                                                <th>ACCIONES</th>
                                                <th>USUARIO</th>
                                                <th>DESCRIPCIÓN</th>
                                                <th>FECHA CREACIÓN</th>
                                                <th>PERFIL</th>
                                                <th>STATUS</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                                <!-- Modal -->
                                <form method="post" name="form">
                                    <div class="modal fade" id="usernameModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header bg-maroon-gradient">
                                                    <h5 class="modal-title">Módulo Usuarios</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-xs-6">
                                                            <input type="hidden" name="userid" id="userid" />
                                                            <label for="username">Nombre Usuario:</label>
                                                            <input type="text" class="form-control" name="username" id="username" maxlength="15" />
                                                        </div>
                                                        <div class="col-xs-6">
                                                            <label for="description">Descripción</label>
                                                            <input type="text" class="form-control" name="description" id="description" maxlength="35" />
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-xs-6">
                                                            <label for="password">Contraseña:</label>
                                                            <input type="password" class="form-control" name="password" id="password" maxlength="15" />
                                                        </div>
                                                        <div class="col-xs-6">
                                                            <label for="confirmpassword">Confirmar Contraseña:</label>
                                                            <input type="password" class="form-control" name="confirmpassword" id="confirmpassword" />
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-xs-12">
                                                            <label for="profile">Perfiles:</label>
                                                            <select class="form-control selectpicker" title="Seleccione el perfil de usuario" name="profile" id="profile"></select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal" id="btnclosed"><i class="fa fa-times-circle" aria-hidden="true"></i>&nbsp;Cerrar</button>
                                                    <button type="submit" class="btn btn-primary"><i class="fa fa-floppy-o" aria-hidden="true"></i>&nbsp;Guardar Cambios</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--Fin centro -->
                                </form>
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

        <script src="src/ajax/username.js"></script>
    </body>
</html>
<?php
    } // Cierre if
} // Cierre if
ob_end_flush();
?>