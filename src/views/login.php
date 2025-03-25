<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title><?php echo APP_NAME;?></title>
        <!-- Tell the browser to be responsive to screen width -->
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <link rel="shortcut icon" href="src/public/img/user2-160x160.jpg" type="image/x-icon">
        <!-- Bootstrap 3.3.5 -->
        <link rel="stylesheet" href="src/public/css/bootstrap.min.css">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="src/public/css/font-awesome.css">

        <!-- Theme style -->
        <link rel="stylesheet" href="src/public/css/AdminLTE.min.css">
        <!-- iCheck -->
        <link rel="stylesheet" href="src/public/css/blue.css">
    </head>
    <body class="hold-transition login-page">
        <div class="login-box">
            <div class="login-logo">
                <a href="" class="">Comercial Fajaré</a>
            </div><!-- /.login-logo -->
            <div class="login-box-body" style="box-shadow: 10px 10px 20px 5px rgba(28,82,148,0.78);">
                <p class="login-box-msg text-bold">Ingrese sus datos de Acceso</p>
                <form method="post" name="form">
                    <div class="form-group has-feedback">
                        <input type="text" id="username" name="username" class="form-control" placeholder="Nombre Usuario" autofocus />
                        <span class="fa fa-user form-control-feedback"></span>
                    </div>
                    <div class="form-group has-feedback">
                        <input type="password" id="password" name="password" class="form-control" placeholder="Contraseña" />
                        <span class="fa fa-key form-control-feedback"></span>
                    </div>
                    <div class="row">
                        <div class="col-xs-8">

                        </div><!-- /.col -->
                        <div class="col-xs-4">
                            <button type="submit" class="btn btn-primary btn-block btn-flat">Ingresar</button>
                        </div><!-- /.col -->
                    </div>
                </form>
            </div><!-- /.login-box-body -->
        </div><!-- /.login-box -->

        <!-- jQuery -->
        <script src="src/public/js/jquery-3.1.1.min.js"></script>
        <!-- Bootstrap 3.3.5 -->
        <script src="src/public/js/bootstrap.min.js"></script>
        <!-- Sweetalert -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script type="text/javascript" src="src/ajax/login.js"></script>
    </body>
</html>