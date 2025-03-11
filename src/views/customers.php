<?php

ob_start();

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
                                    <h1 class="box-title text-capitalize text-maroon" style="font-size: 3rem; text-shadow: 1px 1px 2px black;">maestra clientes&nbsp;&nbsp;&nbsp;
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
                                <div class="panel-body table-responsive" id="listcustomer">
                                    <table id="tblcustomer" class="table table-striped table-bordered table-condensed table-hover" style="width:100%">
                                        <thead class="bg-gray-light">
                                            <tr>
                                                <th>Acciones</th>
                                                <th>Rut</th>
                                                <th>Nombre Cliente</th>
                                                <th>Nombre Complejo</th>
                                                <th>Ruta</th>
                                                <th>Ciudad</th>
                                                <th>Comuna</th>
                                                <th>Direcion</th>
                                                <th>Teléfono</th>
                                                <th>Tipo Documento</th>
                                                <th>Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="panel-body" id="panelcustomer">
                                    <form name="form" method="POST">
                                        <div class="row">
                                            <div class="form-group col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                <label for="customerid">Rut:</label>
                                                <input type="hidden" name="customerid" id="customerid" value="" />
                                                <input type="text" class="form-control" name="customercode" id="customercode" maxlength="10" placeholder="9999999-9" />
                                            </div>
                                            <div class="form-group col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                <label for="customername">Nombre Cliente (*):</label>
                                                <input type="text" class="form-control" name="customername" id="customername" maxlength="45" placeholder="Escriba nombre del cliente" />
                                            </div>
                                            <div class="form-group col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                <label for="complex">Nombre del Complejo (*):</label>
                                                <input type="text" class="form-control" name="complex" id="complex" placeholder="Escriba nombre del complejo" />
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-lg-8 col-md-8 col-sm-8 col-xs-12">
                                                <label for="commercialbusiness">Grigo Comercial:</label>
                                                <input type="text" class="form-control" name="commercialbusiness" id="commercialbusiness" maxlength="45" placeholder="Escriba giro comercial" />
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                <label for="paymentid">Forma de pago (*):</label>
                                                <select class="form-control selectpicker" data-live-search="false" name="paymentid" id="paymentid">
                                                    <option value="">*** Seleccione Forma de Pago ***</option>
                                                    <option value="1">Efectivo</option>
                                                    <option value="2">Tarjetas de Debito o Crédito</option>
                                                    <option value="3">Cheques</option>
                                                    <option value="4">Transferencia Bancaria</option>
                                                    <option value="5">Tarjetas Virtuales</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                <label for="expiration">Día de Pago:</label>
                                                <input type="number" class="form-control text-right" name="expiration" id="expiration" min="0" max="60" value="0" />
                                            </div>
                                            <div class="form-group col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                <label for="credit">Credito:</label>
                                                <input type="number" class="form-control text-right" name="credit" id="credit" min="0" max="10000000" value="0" />
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                <label for="regionid">Region (*):</label>
                                                <select class="form-control selectpicker" data-live-search="false" title="Lista de regiones" name="regionid" id="regionid" data-size="10"></select>
                                            </div>
                                            <div class="form-group col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                <label for="cityid">Ciudad (*):</label>
                                                <select class="form-control selectpicker" data-live-search="false" title="Lista de ciudades" name="cityid" id="cityid" data-size="10"></select>
                                            </div>
                                            <div class="form-group col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                <label for="communeid">Comuna (*):</label>
                                                <select class="form-control selectpicker" data-live-search="false" title="Lista de comunas" name="communeid" id="communeid" data-size="10"></select>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-lg-8 col-md-8 col-sm-8 col-xs-12">
                                                <label for="street">Direccion (*):</label>
                                                <input type="text" class="form-control" name="street" id="street" maxlength="45" placeholder="Escriba una dirección">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-lg-2 col-md-2 col-sm-2 col-xs-12">
                                                <label for="phone1">Telefono 1 (*):</label>
                                                <input type="tel" class="form-control" name="phone1" id="phone1" maxlength="10" placeholder="9 9999 9999" />
                                            </div>
                                            <div class="form-group col-lg-2 col-md-2 col-sm-2 col-xs-12">
                                                <label for="phone1">Telefono 2:</label>
                                                <input type="tel" class="form-control" name="phone2" id="phone2" maxlength="10" placeholder="9 9999 9999" />
                                            </div>
                                            <div class="form-group col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                <label for="email">E-Mail:</label>
                                                <input type="text" class="form-control" name="email" id="email" maxlength="45" placeholder="correo@correo.cl" />
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-lg-2 col-md-2 col-sm-2 col-xs-12">
                                                <label for="typefolio">Tipo Documento (*):</label>
                                                <select class="form-control selectpicker" data-live-search="false" name="typefolio" id="typefolio">
                                                    <option value="0">Cliente Sin Factura</option>
                                                    <option value="1">Cliente Con Factura</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-lg-2 col-md-2 col-sm-2 col-xs-12">
                                                <label for="typeorder">Orden Para Rutas (*):</label>
                                                <input type="text" class="form-control text-right" name="typeorder" id="typeorder" min="0" max="200" value="0" />
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                <label for="employeeid">Ruta (*):</label>
                                                <select class="form-control selectpicker" data-live-search="false" name="employeeid" id="employeeid">
                                                    <option value="0">*** Seleccione Ruta ***</option>
                                                    <option value="1">LUCAS</option>
                                                    <option value="2">ISRAEL</option>
                                                    <option value="3" selected>Todos</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                <label for="weekday">Día Semana (*):</label>
                                                <input type="text" class="form-control" name="weekday" id="weekday" maxlength="45" />
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

        <script src="src/ajax/customer.js"></script>
    </body>
</html>
<?php
    } // Cierre if
} // Cierre if
ob_end_flush();
?>