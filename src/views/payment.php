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
            <div class="content-wrapper">        
                <!-- Main content -->
                <section class="content">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="box">
                                <div class="box-header with-border">
                                    <h1 class="box-title text-capitalize text-maroon" style="font-size: 3rem; text-shadow: 1px 1px 2px black;">credito&nbsp;&nbsp;&nbsp;
                                        <!-- <button class="btn btn-success" id="btnadd"><i class="fa fa-plus-circle"></i> Crear Nota de Pedido</button> -->
                                    </h1>
                                    <div class="box-tools pull-right"></div>
                                </div>
                                <!-- /.box-header -->
                                <!-- centro -->
                                <div class="panel-body" id="panelfilters">
                                    <div class="row">
                                        <div class="form-group form-group-sm col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                            <select class="form-control selectpicker text-bold" title="Seleccione uno o más establecimientos" data-live-search="true" name="establishment" id="establishment" multiple data-max-options="10" data-size="10"></select>
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
                                    <table id="tbllistpayment" class="table table-striped table-bordered table-condensed table-hover" style="width:100%">
                                        <thead class="bg-gray-light">
                                            <tr>
                                                <th>ACCIONES</th>
                                                <th>TIPO DOCUMENTO</th>
                                                <th>NRO DOCUMENTO</th>
                                                <th>FECHA ENTREGA</th>
                                                <th>ESTABLECIMIENTO</th>
                                                <th>MONTO TOTAL</th>
                                                <th>MONTO PAGAGO ACTUAL</th>
                                                <th>SALDO PENDIENTE</th>
                                                <th>STATUS</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                                <!-- Formulario Modal -->
                                <div class="modal fade" id="modalPayment" tabindex="-1" role="dialog" aria-labelledby="modalPaymentLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header bg-blue-gradient">
                                                <div class="form-group">
                                                    <h3 class="modal-title" id="modalPaymentLabel">Crédito</h3>
                                                </div>
                                            </div>
                                            <form name="form" id="form" method="POST">
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="form-group col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                                            <label for="type">Tipo Documento:</label>
                                                            <input type="hidden" name="headerdocumentid" id="headerdocumentid" />
                                                            <input type="text" class="form-control" name="type" id="type" disabled />
                                                        </div>
                                                        <div class="form-group col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                                            <label for="type">Número Documento:</label>
                                                            <input type="text" class="form-control text-right" name="folio" id="folio" disabled />
                                                        </div>
                                                        <div class="form-group col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                                            <label for="deliverdate">Fecha Documento:</label>
                                                            <input type="date" class="form-control text-right" name="deliverdate" id="deliverdate" disabled />
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="form-group col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                                            <label for="methodpayment">Método de Pago:</label>
                                                            <select class="form-control selectpicker" data-live-search="false" name="methodpayment" id="methodpayment" >
                                                                <option value="0">Efectivo</option>
                                                                <option value="1">Transferencia Electronica</option>
                                                                <option value="2">Tarjetas de Créido o Débito</option>
                                                            </select>
                                                        </div>
                                                        <div class="form-group col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                                            <label for="document">Número Documento:</label>
                                                            <input type="text" class="form-control text-right" name="document" id="document" />
                                                        </div>
                                                        <div class="form-group col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                                            <label for="paymentdate">Fecha Pago (*):</label>
                                                            <input type="date" class="form-control text-right" name="paymentdate" id="paymentdate" value="<?php echo date('Y-m-d') ?>" />
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="form-group col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                                            <label for="bankid">Banco (*):</label>
                                                            <select class="form-control selectpicker" title="Seleccione un banco" name="bankid" id="bankid"></select>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="form-group col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                                            <label for="total">Monto Documento:</label>
                                                            <input type="number" class="form-control text-right" name="total" id="total" disabled />
                                                        </div>
                                                        <div class="form-group col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                                            <label for="payment">Pago Actual:</label>
                                                            <input type="number" class="form-control text-right" name="payment" id="payment" />
                                                        </div>
                                                        <div class="form-group col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                                            <label for="balance">Saldo Pendiente:</label>
                                                            <input type="number" class="form-control text-right" name="balance" id="balance" disabled />
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                            <label for="comment">Comentarios:</label>
                                                            <textarea class="form-control" name="comment" id="comment"></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button class="btn btn-primary" type="submit" id="btnsave"><i class="fa fa-save"></i>&nbsp;&nbsp;Pagar Documento</button>
                                                    <button class="btn btn-danger" type="button" id="btncancel" data-dismiss="modal" arial-label="Close"><i class="fa fa-arrow-circle-left"></i> Cancelar</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>                                
                                <!--Fin centro -->
                            </div><!-- /.box -->
                            <!-- Formulario Modal Detalle -->
                            <div class="modal fade" id="modalPaymentList" tabindex="-1" role="dialog" aria-labelledby="modalPaymentListLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header bg-blue-gradient">
                                                <div class="form-group">
                                                    <h3 class="modal-title" id="modalPaymentListLabel">Transacciones</h3>
                                                </div>
                                            </div>
                                            <div class="modal-body">
                                                <table class="table table-bordered table-hover table-striped" id="tbltransaction">
                                                    <thead class="row bg-primary">
                                                        <tr>
                                                            <th>ACCION</th>
                                                            <th>NRO TRANSACC</th>
                                                            <th>FECHA TRANSACC</th>
                                                            <th>MONTO TRANSACC</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody></tbody>
                                                    <tfoot class="bg-success"></tfoot>
                                                </table>
                                            </div>
                                            <div class="modal-footer">
                                                <!-- <button class="btn btn-primary" type="submit" id="btnsave"><i class="fa fa-save"></i>&nbsp;&nbsp;Pagar Documento</button>-->
                                                <button class="btn btn-default" type="button" id="btncancellist" data-dismiss="modal" arial-label="Close"><i class="fa fa-arrow-circle-left"></i> Cerrar</button>
                                            </div>
                                        </div>
                                    </div>
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

        <script src="src/ajax/payment.js"></script>
    </body>
</html>
<?php
    } // Cierre if
} // Cierre if
ob_end_flush();
?>