<?php

/**
 * Descripción: Página de inicio del sistema informótico EXA
 * Fecha de creación:	2016-12-28
 * Desarrollador: Erik Niebla
 */

require_once('../../Librerias/procedimientos/almacenados_standar.php');
require_once('../../Librerias/config.php/register_globals.php');
require_once('../LOGICA/logica.php');
//require_once('../LOGICA/adm_log_login.php');

if (isset($heartBeatChat)) {
    include("adm_con_online_2.0.php");
    echo json_encode($response);
    exit();
}

if (isset($getReportsExa)) {
    $obBDr = new MysqlDatosContab(true);
    $obBDr->setReports(isset($title) ? $title : ' ', isset($subTitle) ? $subTitle : ' ');
    exit();
}

if (isset($historyChat)) {
    $obBD_conexion = new Class_Log_Conexion_Adm($_SESSION['Ses_Dat_Dis']);
    $obBD_con1 =  new Class_Log_Datos_Adm;
    $response['history'] = $obBD_con1->getArrayConsulta(216, filter_input_array(INPUT_POST), $obBD_conexion);
    $response['success'] = true;
    echo json_encode($response);
    exit();
}
if (isset($signalChat)) {
    $obBD_conexion = new Class_Log_Conexion_Adm($_SESSION['Ses_Dat_Dis']);
    $obBD_con1 =  new Class_Log_Datos_Adm;
    $obBD_con1->grabarv_registros(sentencias_adm(219, filter_input_array(INPUT_POST)), $obBD_conexion->conexion);
    $response['success'] = true;
    echo json_encode($response);
    exit();
}
if (isset($ClientGuid)) {
    $obBD_conexion = new Class_Log_Conexion_Adm($_SESSION['Ses_Dat_Dis']);
    $obBD_con1 =  new Class_Log_Datos_Adm;
    $obBD_con1->grabarv_registros(sentencias_adm(215, filter_input_array(INPUT_POST)), $obBD_conexion->conexion);
    $response['success'] = true;
    echo json_encode($response);
    exit();
}
if (isset($loginAjax)) {
    require('../LOGICA/adm_log_control.php');
    $obBD_conexionMaster = new Class_Log_Conexion_Cnt; // Creacion del Objeto de Conexion
    $obBD_con =  new Class_Log_Datos_Cnt; // Creacion del Objeto de Datos
    $row_data = $obBD_con->getRowConsulta(2, $Emp_Cod . '*' . trim($user_name), $obBD_conexionMaster); //Consulta que realiza la autenticacion del usuario
    $obBD_conexion = new Class_Log_Conexion_Cnt($row_data['Dat_Dis']); // Conexion a la base de datos distribuida, dinamica
    $row_rs_control = $obBD_con->getArrayConsulta(16, trim($user_name) . '*' . trim($encryptor) . '*' . $Emp_Cod . '*' . "$Suc_Cod", $obBD_conexion); //Consulta que realiza la autenticacion del usuario
    //var_dump($row_rs_control);
    foreach ($row_rs_control as $rowControl)
        if ($rowControl['Suc_Cod'] == $Suc_Cod || strtoupper($rowControl['Suc_Des']) == 'MATRIZ')
            $row_rs_control = $rowControl;
    if (isset($row_rs_control['Suc_Cod'])) {
        $rs_perfiles = $obBD_con->getArrayConsulta(21, $row_rs_control["Usu_Cod"], $obBD_conexion); /* Consulta los perfiles asignados al usuario */
        $lperf = array();
        $Per_Des = array();
        foreach ($rs_perfiles as $v0) {
            $lperf[] = $v0["Per_Cod"];
            $Per_Des[] = $v0["Per_Des"];
        }
        /* Variables de Sesion del usuario  */
        $_SESSION['Ses_Usu_Cod'] = $row_rs_control['Usu_Cod'];
        $_SESSION['Ses_Usu_Ced'] = $row_rs_control['Usu_Ced'];
        $_SESSION['Ses_Usu_Tip'] = isset($row_rs_control['Usu_Tip']) ? $row_rs_control['Usu_Tip'] : '';
        $_SESSION['Ses_Usu_Est'] = $row_rs_control['Usu_Est'];
        $_SESSION['Ses_Usu_Cad'] = $row_rs_control['Usu_Cad'];
        $_SESSION['Ses_Usu_Men'] = $row_rs_control['Usu_Men'];
        $_SESSION['Ses_Per_Cod'] = isset($row_rs_control['Per_Cod']) ? $row_rs_control['Per_Cod'] : '';
        /* Variable para definir la sucursal y empresa */
        $_SESSION['Ses_Suc_Cod'] = $row_rs_control['Suc_Cod'];
        $_SESSION['Ses_Suc_Nom'] = $row_rs_control['Suc_Des'];
        $_SESSION['Ses_Emp_Cod'] = $row_rs_control['Emp_Cod'];
        $_SESSION['Ses_Emp_Nom'] = $row_rs_control['Emp_Nom'];
        $_SESSION['Ses_Emp_Cor'] = $row_rs_control['Emp_Cor'];
        $_SESSION['Ses_Suc_Web'] = $row_rs_control['Suc_Web'];
        $_SESSION['Ses_Emp_Log'] = $row_rs_control['Emp_Log'];
        /* Variables del Perfil del usuario */
        $_SESSION['Ses_Lis_Per'] = $lperf;
        $_SESSION['Ses_Per_Des'] = $Per_Des; //Descripción del perfil
        /* Variable para la base de datos del sistema local */
        $_SESSION['Ses_Dat_Dis'] = $row_data['Dat_Dis']; //Base de datos distribuida local
        $_SESSION['Ses_Dat_Aut'] = $row_data['Dat_Aut']; //Base de datos auditoria
        $_SESSION['Ses_Dat_Stg'] = $row_data['Dat_Stg']; //Base de datos storage
        $responce['success'] = true;
    } else {
        $responce['success'] = false;
    }
    echo json_encode($responce);
    exit();
}
if (isset($setSucu)) {
    require('../LOGICA/adm_log_control.php');
    $obBD_con =  new Class_Log_Datos_Cnt; // Creacion del Objeto de Datos
    $obBD_conexion = new Class_Log_Conexion_Cnt($Ses_Dat_Dis);

    $row_rs_control = $obBD_con->getRowConsulta(22, trim($user_name) . '*' . $Ses_Emp_Cod . '*' . $Suc_Cod, $obBD_conexion); //Consulta que realiza la autenticacion del usuario
    if (!isset($row_rs_control['Usu_Cod']) || empty($row_rs_control['Usu_Cod'])) {
        echo json_encode(array('success' => false, 'ver' => null));
        exit();
    }
    $_SESSION['Ses_Suc_Cod'] = $Suc_Cod;
    $_SESSION['Ses_Suc_Nom'] = $Suc_Nom;
    $_SESSION['Ses_Usu_Cod'] = $row_rs_control['Usu_Cod'];
    $_SESSION['Ses_Usu_Ced'] = $row_rs_control['Usu_Ced'];
    $_SESSION['Ses_Usu_Tip'] = isset($row_rs_control['Usu_Tip']) ? $row_rs_control['Usu_Tip'] : '';
    $_SESSION['Ses_Usu_Est'] = $row_rs_control['Usu_Est'];
    $_SESSION['Ses_Usu_Cad'] = $row_rs_control['Usu_Cad'];
    $_SESSION['Ses_Usu_Men'] = $row_rs_control['Usu_Men'];
    $_SESSION['Ses_Per_Cod'] = isset($row_rs_control['Per_Cod']) ? $row_rs_control['Per_Cod'] : '';
    //var_dump($row_rs_control);
    echo json_encode(array('success' => true, 'ver' => $row_rs_control));
    exit();
}
if (!isset($_SESSION) || (!isset($_SESSION['Ses_Lis_Per']) || !isset($_SESSION['Ses_Emp_Cod']) || !isset($_SESSION['Ses_Usu_Ced']))) header('Location: ' . '../index.php');
$apellido = explode(' ', $_SESSION['Ses_Prs_Ape']);
$nombre = explode(' ', $_SESSION['Ses_Prs_Nom']);
$obBD_conexion1 = new Class_Log_Conexion_Adm; //Creacion del Objeto de conexion
$obBD_con1 =  new Class_Log_Datos_Adm; //Creaci�n del objeto mysql para las consultas
$rs_empresas = $obBD_con1->getArrayConsulta(222, trim($Ses_Usu_Ced), $obBD_conexion1); //consulta empresas
$rs_sucursales = $obBD_con1->getArrayConsulta(214, $Ses_Emp_Cod . '*' . $Ses_Usu_Ced, $obBD_conexion1);
$bd = $obBD_con1->getRowConsulta(223, $Ses_Emp_Cod, $obBD_conexion1);
$bd_nombre = $bd['Dat_Dis'];
$obBD_conexion = new Class_Log_Conexion_Adm($_SESSION['Ses_Dat_Dis']);
//Traer 10 notificaciones (WB)
$data_tickets = $obBD_con1->getArrayConsulta(224, $Ses_Emp_Cod, $obBD_conexion1);
//var_dump($data_tickets);
//Contar las notificaciones que no se han atendidos
$Cantidad_tickets = $obBD_con1->getRowConsulta(225, $Ses_Emp_Cod, $obBD_conexion1);
//echo ($Cantidad_tickets["TOTAL"]);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <title><?Php echo $Ses_Sys_Nom; ?></title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta charset="UTF-8">
    <meta name="description" content="overview &amp; stats" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
    <link rel="shortcut icon" type="image/x-icon" href="../../skins/img/favicon.png" />
    <!-- bootstrap & fontawesome -->

    <link rel="stylesheet" href="../../framework/plugins/fonts/font-awesome/font-awesome-4.4.0/css/font-awesome.min.css" />
    <link rel="stylesheet" href="../../skins/fonts/fontelo/fontello.css?x=0" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../../skins/css/jquery-ui.css" />
    <link rel="stylesheet" href="../../skins/css/jquery-ui.theme.css" />
    <link rel="stylesheet" href="../../skins/css/menu-nav.css" />
    <!-- text fonts -->
    <!--link rel="stylesheet" href="../../skins/css/ace.css_" class="ace-main-stylesheet" id="main-ace-style" /-->
    <!-- exa styles -->
    <!--script src="../../skins/js/ace-extra.js"></script-->
    <link rel="stylesheet" href="../../skins/css/bootstrap.min.css" />

    <link rel="stylesheet" href="https://code.jquery.com/ui/1.14.1/themes/base/jquery-ui.css">
    <!-- <script src="https://code.jquery.com/jquery-3.7.1.js"></script> -->
    <script src='../../skins/js/jquery/jquery3.js'></script>
    <script src="https://code.jquery.com/ui/1.14.1/jquery-ui.js"></script>
    <link id="pagestyle" href="../../skins/css/exa3.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.min.css">
    
<script>
    $(function() {
        $.widget("ui.menu", $.ui.menu, {
            delay: 0 // Ajusta el delay a 0 milisegundos
        });

        // Inicializa el menú
        $("#menu-pro-exa").menu();

        // Ocultar los submenús al salir del área de los submenús
        $("#menu-pro-exa ul").mouseleave(function() {
            $(this).hide();
        });

        // Mostrar los submenús al entrar en el área del menú principal
        $("#menu-pro-exa li").mouseenter(function() {
            $(this).children("ul").show(); // Muestra el submenú correspondiente
        }).mouseleave(function() {
            $(this).children("ul").hide(); // Oculta el submenú cuando el mouse sale
        });

        // Asegúrate de que este código se ejecute después de que el menú se haya inicializado
        $("#menu-pro-exa > li:has(ul)").each(function() {
            $(this).find('.ui-menu-icon.ui-icon.ui-icon-caret-1-e').remove(); // O usa .hide() si prefieres ocultarlo
        });
        $(".submenu li").mouseenter(function() {
            $(this).children("ul").show(); // Muestra el submenú correspondiente
            var alturaPantalla = $(window).height();
var alturaMenú = $(this).children("ul").height();
if (alturaMenú > alturaPantalla) {
  $(this).children("ul").addClass("submenu-scroll-enabled");
} else {
  $(this).children("ul").removeClass("submenu-scroll-enabled");
}
        }).mouseleave(function() {
            $(this).children("ul").hide(); // Oculta el submenú cuando el mouse sale
        });
    });

</script>

    <!-- <link id="pagestyle" href="../../skins/css/menu.css" rel="stylesheet" /> -->
    <!-- <script type="text/javascript">
        window.jQuery || document.write("<script src='../../skins/js/jquery.js'>" + "<" + "/script>");
    </script> -->
    <!--[if IE]><script type="text/javascript">window.jQuery || document.write("<script src='../../skins/js/jquery1x.js'>"+"<"+"/script>");</script><![endif]-->
</head>
<style>

    .modal_notificacion {
        display: none;
    }
</style>
<nav class="navbar navbar-main navbar-expand-lg px-0 mx-1 my-3 shadow border-radius-xl position-sticky left-auto top-1 z-index-sticky bg-white" id="navbarBlur" navbar-scroll="true">
    
    <div class="container-fluid py-1 px-3 text-center">
    <a class="navbar-brand mx-3" href="#" target="_blank">
        <img src="../../skins/img/logo-sin-tagline.png" class="img-fluid" alt="main_logo" width="100">
    </a>
       <div class="dropdown my-3">
    <a class="text-dark dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-building text-primary"></i>
        <span class="d-none d-md-inline"><?= ucwords(strtolower($Ses_Emp_Nom)); ?></span>
        <?= !empty($Ses_Suc_Nom) ? ' <b>[' . strtoupper($Ses_Suc_Nom) . ']</b>' : ''; ?>
    </a>
    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown" data-bs-popper="static">
        <?php foreach ($rs_sucursales as $rowSuc): ?>
            <li class="nav-item">
            <i class="bi bi-building text-primary"></i>
                <a tabindex="-1" href="#" onclick="setSucu('<?= $rowSuc['Suc_Cod']; ?>', '<?= $rowSuc['Suc_Des']; ?>');">
                    <?= $rowSuc['Suc_Des']; ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
  

        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4 show" id="navbar">
            <div class="ms-md-auto pe-md-3 d-flex align-items-center"></div>
            <ul class="navbar-nav justify-content-end">
                <li class="nav-item d-xl-none ps-3 d-flex align-items-center">
                    <a href="#" class="nav-link p-0 me-3" id="iconNavbarSidenav" aria-label="Toggle Sidenav">
                        <div class="sidenav-toggler-inner">
                            <i class="sidenav-toggler-line"></i>
                            <i class="sidenav-toggler-line"></i>
                            <i class="sidenav-toggler-line"></i>
                        </div>
                    </a>
                </li>
                <li class="nav-item pe-2">
                <?php if (count($rs_empresas) > 1): ?>
                                <a class="btn btn-primary p-2 mb-0" onclick="$('#empresa').modal('show');">
                                    <i class="bi bi-briefcase-fill text-white pe-2"></i> Cambiar Empresa
                                </a>
                            <?php endif; ?>
                </li>
                <li class="nav-item dropdown pe-2 d-flex align-items-center">
                    <a href="#" class="dropdown-toggle" id="notificiaciones" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa fa-bell cursor-pointer text-primary" aria-hidden="true"></i>
                        <span class="d-none d-md-inline"> Notificaciones <?= $cantidad_tickets . " " . $message_tickets; ?></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificiaciones" data-bs-popper="static">
                        <li class="mb-2">
                            <?php foreach ($data_tickets as $row): ?>
                                <a class="nav-link text-dark" href="/administrador/FRONT/adm_gst_soporte.php">
                                    <div class="d-flex justify-content-center">
                                        <span><i class="fa fa-bell" aria-hidden="true"></i></span>
                                        <span class="mx-2">
                                            <p class="text-primary"><?= utf8_encode($row['Tic_Tem']); ?></p>
                                            <span><?= $row['Tic_Fec_Cre']; ?></span>
                                        </span>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </li>
                    </ul>
                </li>
                <li class="nav-item dropdown pe-2 d-flex align-items-center">
                    <a href="#" class="nav-link dropdown-toggle p-0 fs-5 me-4 text-dark" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-circle text-primary"></i>
                        <span class="d-none d-md-inline"><?= $Ses_Prs_Ape . " " . $Ses_Prs_Nom; ?></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton" data-bs-popper="static">
                        
                        <li class="mb-2">
                            <a class="dropdown-item" href="./adm_pas_usuarios_2.0.php" target="contenido">
                                <i class="bi bi-key text-primary"></i> Cambiar Clave
                            </a>
                        </li>
                        <li class="mb-2">
                            <a class="dropdown-item" onclick="$('#modalAlertPrinter').modal('show');">
                                <i class="bi bi-printer-fill text-primary"></i> Impresoras
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item pe-2">
                                <a class="btn btn-primary p-2 mb-0" href="../LOGICA/logout.php">
                                    <i class="bi bi-box-arrow-left text-white pe-2"></i> Salir
                                </a>                  
                </li>
            </ul>
        </div>
    </div>
</nav>

<body class="g-sidenav-show bg-dark" style="background-image: url(../../skins/img/bg.png);background-repeat: no-repeat;">
    <div class="d-flex bd-highlight   min-vh-100">
        <div class="flex-shrink-1 bd-highlight  d-none d-md-none d-lg-block">

            <?php //} 
            ?>
            <?php
            if ($_SESSION['Ses_Usu_Men'] == 'B') { ?>
            <?php } else {
                require_once("../LOGICA/adm_log_menu_tree.php");
                $obBD_con1 =  new Class_Sys_Menu();
                echo ($obBD_con1->menuToHtml(1, $obBD_con1->getMenuContainer($_SESSION['Ses_Lis_Per'], $obBD_conexion), '', (!isset($_COOKIE['ace_hover']) || $_COOKIE['ace_hover'] == 'true' || $_COOKIE['ace_compact'] == 'true' ? '' : ''))); ?>
            <?php } ?>
        </div>


        <div class="px-2 py-0 w-100 vh-100 bd-highlight">

            <iframe name="contenido" id="contenido" frameborder="0" class="h-100 w-100" src="../../skins/html/index.html" allowfullscreen style="border: 0; overflow: hidden;"></iframe>
            <div id='ajaxConexion'>
                
            

            </div>
            
        </div>
    
    </div>

    <!-- <div class="container-fluid">
        <div class="row flex">
            <div class="col bg-white rounded-3 ">
                <div class="d-flex flex-column align-items-center px-2 pt-2 text-dark min-vh-100 h-100 ">
                <?php //} 
                ?>
                <?php
                if ($_SESSION['Ses_Usu_Men'] == 'B') { ?>
                <?php } else {
                    require_once("../LOGICA/adm_log_menu_tree.php");
                    $obBD_con1 =  new Class_Sys_Menu();
                    echo ($obBD_con1->menuToHtml(1, $obBD_con1->getMenuContainer($_SESSION['Ses_Lis_Per'], $obBD_conexion), 'navbar-nav flex-column mb-sm-auto mb-0 ', (!isset($_COOKIE['ace_hover']) || $_COOKIE['ace_hover'] == 'true' || $_COOKIE['ace_compact'] == 'true' ? 'nav-item dropend px-2 py-2' : ''))); ?>
                <?php } ?>
          
                </div>
            </div>
            <div class="col-10 py-3">
            <iframe name="contenido" id="contenido" frameborder="0" class="h-100 w-100" src="../../skins/html/index.html" allowfullscreen style="border: 0; overflow: hidden;"></iframe>
                    <div id='ajaxConexion'></div>
            </div>
        </div>
    </div> -->



    <footer class="footer bg-dark text-white bottom-0 w-100 p-1 position-fixed  ">
        <!--footer class="footer p-3 bg-dark "-->
        <div class="container-fluid">
            <div class="row align-items-center justify-content-lg-between">
                <div class="col-lg-6 mb-lg-0 mb-4">
                    <div class="copyright text-center text-sm text-white text-lg-start ">
                        Todos los Derechos Reservados © <script>
                            document.write(new Date().getFullYear())
                        </script>,
                        Desarrollado con <i class="fa fa-heart"></i> por
                        <a href="https://www.exacontable.com" class="font-weight-bold text-white" target="_blank">Ofsercont</a>
                    </div>
                </div>
                <div class="col-lg-6 text-white">
                    <ul class="nav nav-footer justify-content-center justify-content-lg-end text-white">
                        <li class="nav-item text-white fs-3">
                            <a href="https://www.facebook.com/ExaContable" class="nav-link text-white" target="_blank"><i class="bi bi-facebook"></i></a>
                        </li>
                        <li class="nav-item  text-white fs-3">
                            <a href="https://www.instagram.com/ExaContable" class="nav-link text-white" target="_blank"><i class="bi bi-instagram"></i></a>
                        </li>
                        <li class="nav-item   text-white fs-3">
                            <a href="https://www.youtube.com/ExaContable" class="nav-link text-white" target="_blank"><i class="bi bi-youtube"></i></a>
                        </li>
                        <li class="nav-item text-white">
                            <a href="https://www.exacontable.com" class="nav-link text-white" target="_blank">Homepage</a>
                        </li>
                        <li class="nav-item text-white">
                            <a href="https://www.exacontable.com" class="nav-link text-white " target="_blank">Nosotros</a>
                        </li>
                        <li class="nav-item text-white">
                            <a href="https://www.exacontable.com" class="nav-link text-white" target="_blank">Documentación</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>
    <div id="modalAlert" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title red"><i class="ace-icon fa fa-exclamation-triangle"></i>&nbsp;&nbsp;<b id="alertTitle">Alerta</b></h4>
                </div>
                <div class="modal-body">
                    <h4 id="alertBody" class="bolder blue">probar</h4>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-xs btn-default" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <div id="modalAlertPrinter" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title text-primary"><i class="ace-icon glyphicon glyphicon-print"></i>&nbsp;&nbsp;<b>IP/Puerto Servidor de Impresoras</b></h4>
                    <button type="button" class="btn btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal">
                        <!-- Prepended text-->
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="Usu_Ced">IP:</label>
                            <div class="col-sm-5">
                                <input id="Ip_Printer" value="127.0.0.1" class="form-control" placeholder="" type="text">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="Usu_Ced">IP:</label>
                            <div class="col-sm-5">
                                <input id="Port_Printer" value="80" class="form-control" placeholder="" type="text">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="setPrintersIp($('#Ip_Printer').val(),$('#Port_Printer').val())" data-bs-dismiss="modal" class="btn btn-xs btn-primary">Guardar</button>
                    <button type="button" class="btn btn-xs btn-default" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>


    <div id="empresa" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">

                    <h4 class="modal-title">Cambiar Empresa</h4>
                    <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal" id="loginChange">
                        <!-- Prepended text-->
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="Usu_Ced">Cédula:</label>
                            <div class="col-sm-5">
                                <div class="input-group text-primary">
                                    <span class="input-group-text"><i class="fa fa-user"></i></span>
                                    <input id="Usu_Ced" name="user_name" value="<?php echo $Ses_Usu_Ced; ?>" class="form-control" placeholder="" type="text" readonly="readonly">
                                </div>
                            </div>
                        </div>

                        <!-- Prepended text-->
                     <div class="form-group">
    <input type="hidden" id="Suc_Cod" name="Suc_Cod" />
    <label class="col-sm-2 control-label" for="Emp_Des">Empresa:</label>
    <div class="w-100">
        <div class="input-group">
            <span class="input-group-text w-auto"><i class="bi bi-building"></i></span>
            <span class="py-2 w-90 p-0 form-select"><select id="Emp_Cod" name="Emp_Cod" class="form-select form-control chosen-select" data-placeholder="Seleccione Empresa...">
                <option value=""></option>
                <?php foreach ($rs_empresas as $row_rs_empresas) {
                    if ($row_rs_empresas['Emp_Cod'] !== $Ses_Emp_Cod) {
                        echo '<option value="' . $row_rs_empresas['Emp_Cod'] . '" data-Emp_Nom="' . $row_rs_empresas['Emp_Nom'] . '"  data--suc_-cod="' . $row_rs_empresas['Suc_Cod'] . '">' . $row_rs_empresas['Emp_Cor'] . ' (' . utf8_encode($row_rs_empresas['Suc_Des']) . ")" . '</option>';
                    }
                } ?>
            </select></span>
       
        </div>
    </div>
</div>


  
                        <!-- Prepended text-->

                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="Usu_Pas">Contrase&ntilde;a:</label>
                            <div class="col-sm-5">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa fa-key"></i></span>
                                    <input id="Usu_Pas" onkeypress="if (event.keyCode===13){loginAjax();return false;}" name="encryptor" class="form-control" placeholder="" type="password" required="true" autofocus="true">
                                </div>
                            </div>
                        </div>
                        <div id="msgAlert" style="height: 38px;"></div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="loginAjax()" class="btn btn-xs btn-primary">Cambiar Empresa</button>
                    <button type="button" class="btn btn-xs btn-default" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->


    <!-- basic scripts -->
    <!-- <script type="text/javascript">
        if ('ontouchstart' in document.documentElement) document.write(
            "<script src='../../skins/js/jquery.mobile.custom.js'>" + "<" + "/script>");
    </script> -->
    <script src="../../skins/js/bootstrap.min.js"></script>
    <script src="../../skins/js/bootstrap.bundle.min.js"></script>
    <script src="../../skins/js/popper.min.js"></script>
    <script src="../../skins/js/perfect-scrollbar.min.js"></script>
    <script src="../../skins/js/smooth-scrollbar.min.js"></script>
   
<script src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.jquery.min.js"></script>


    <!-- ace scripts NEW-->
    <!--script src="../../framework/jquery/bootstrap/bootstrap-3.3.5/js/tooltip.js"></script>
    <script src="../../framework/jquery/bootstrap/bootstrap-3.3.5/js/popover.js"></script-->
    <!-- <script src="../../skins/js/ace/ace.js"></script>
    <script src="../../skins/js/ace/ace-elements.js"></script> -->
    <!-- inline scripts related to this page -->
    <!-- inline scripts related to this page -->

    <!-- <link rel="stylesheet" type="text/css" media="screen" href="../../framework/jquery/chosen/chosen-1.4.2/chosen.min.css" />
    <script type="text/javascript" src="../../framework/jquery/chosen/chosen-1.4.2/chosen.min.js"></script>
    <script type="text/javascript" src="../../framework/jquery/chosen/chosenDesc/chosenDesc.js"></script>
    <script>
        $(document).ready(function() {
            var win = navigator.platform.indexOf('Win') > -1;
            if (win && document.querySelector('#sidenav-scrollbar')) {
                var options = {
                    damping: '0.5'
                }
                Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
            }
        });
    </script>
    <style>
        .chosen-container-single .chosen-search:after {
            content: ''
        }

        .chosen-single.form-control {
            border-radius: 0 !important;
        }

        li.nav-item {
            /*background: white !important;*/
        }
    </style> -->
    

    <script type="text/javascript">
    $(document).ready(function() {
        // Inicializa Chosen
        $(".chosen-select").chosen({
            placeholder_text_single: "Seleccione Empresa...",
            no_results_text: "No se encontraron resultados",
            width: "100%" // Para que ocupe el 100% del contenedor
        });
    });
</script>
    <script type="text/javascript">
        $(document).ready(function() {
            var socketVentanas;
            var Ses_Emp_Cod = <?php echo $Ses_Emp_Cod; ?>,
                Ses_Suc_Cod = <?php echo $Ses_Suc_Cod; ?>,
                Ses_Usu_Cod = <?php echo $Ses_Usu_Cod; ?>,
                Ses_Prs_Cod = <?php echo $Ses_Prs_Cod; ?>,
                Ses_Bd_Nom = <?php echo "'" . $bd_nombre . "'"; ?>;

            $.isUnd = function(v) {
                return v === undefined;
            };
            $.varValid = $.vv = function(v) {
                return (v !== null && !$.isUnd(v));
            };
            $.isObject = $.isObj = function(v) {
                return $.vv(v) && !$.isArray(v) && typeof v === 'object';
            };

            $.jsonParser = function(v) {
                if ($.isArray(v) || $.isObj(v)) {
                    return JSON.stringify(v);
                } else {
                    try {
                        return JSON.parse(v);
                    } catch (e) {
                        return v;
                    }
                }
            };

            $.setLocalStore = function(name, data) {
                localStorage.setItem(name, $.jsonParser(data));
                if ($.isUnd(data)) localStorage.removeItem(name);
            };
            $.getLocalStore = function(name) {
                var data = localStorage.getItem(name);
                if ($.varValid(data)) return $.jsonParser(data);
            };
            $.getCookie = function(cname) {
                var na = cname + "=",
                    dc = decodeURIComponent(document.cookie),
                    ca = dc.split(';');
                for (var i = 0; i < ca.length; i++) {
                    var c = ca[i];
                    while (c.charAt(0) === ' ') {
                        c = c.substring(1);
                    }
                    if (c.indexOf(na) === 0) {
                        return c.substring(na.length, c.length);
                    }
                }
                return "";
            };


            function setPrintersIp(ip, port) {
                var local = $.getLocalStore("printers") || {};
                local['ip_printers'] = ip || "127.0.0.1";
                local['port_printers'] = port || "80";
                $.setLocalStore("printers", local);
                loadPrinters();
            }

            function loadPrinters() {
                var local = $.getLocalStore("printers") || {};
                var ip_printers = local['ip_printers'] || "127.0.0.1",
                    port_printers = local['port_printers'] || '80';
                var link = "http://" + ip_printers + ":" + port_printers + "/exa/printers/getPrinters.php";
                $.setLocalStore('printers', undefined);

                $.get(link, function(data) {
                    if (data.success === true) {
                        if (Ses_Prs_Cod === 1) console.log(data);
                        $.setLocalStore('printers', {
                            has_printers: data.printers.length > 0,
                            ip_printers: ip_printers,
                            port_printers: port_printers,
                            printers: data.printers
                        });
                    }
                }, 'json');
            }
        })
    </script>
    <script type="text/javascript">
        // Cambiado x Erik xq lo anterior mo era funcional
        <?php if ($_SESSION['Ses_Usu_Cad'] == 'N') { ?>
            var Ses_Sys_Tim = '0<?php echo strtotime(date('Y-m-d H:i:s')) - strtotime($Ses_Sys_Tim); ?>' * 1;
            setInterval(function() {
                var s = Ses_Sys_Tim,
                    h = Math.floor(s / 3600),
                    m = Math.floor(s / 60) - (h * 60);
                s = Math.floor(s - (h * 3600) - (m * 60));
                $('#ajaxConexion').html('<b>Online:</b> ' + Math.abs(h) + 'hrs ' + Math.abs(m) + 'min ' + Math.abs(s) +
                    'seg');
                Ses_Sys_Tim += 1;
            }, 1000);
        <?Php
        } ?>

        function openAlert(title, body) {
            $('#alertTitle').html(title);
            $('#alertBody').html(body);
            $('#modalAlert').modal("show");
        }

        function resizeMain() {
            $('#contenido').css('min-height', (window.innerHeight - 50 + 5) + 'px');
        }
        $(window).on('resize', resizeMain);

        function setSucu(Suc_Cod, Suc_Nom) {
            $.post("<?Php echo filter_input(INPUT_SERVER, 'PHP_SELF', FILTER_SANITIZE_STRING); ?>", {
                setSucu: true,
                Suc_Cod: Suc_Cod,
                Suc_Nom: Suc_Nom,
                user_name: $('#Usu_Ced').val()
            }, function(response) {
                if (response['success'] === true) {
                    window.location.href =
                        "<?Php echo filter_input(INPUT_SERVER, 'PHP_SELF', FILTER_SANITIZE_STRING); ?>";
                } else {
                    openAlert('ERROR SISTEMA: Usuarios',
                        'No se logro cambiar de <b class="green">SUCURSAL</b>!<br/><br/><span class="grey">Revise el acceso de su usuario a la Sucursal ' +
                        Suc_Nom + ".</span>"
                    ); /*$msg='<div class="alert alert-warning fade in"><button type="button" class="close" data-dismiss="alert">x</button><strong>[ERROR]</strong> &nbsp;&nbsp;Contrase&ntilde;a Incorrecta.</div>';*/
                }
            }, 'json').fail(function(error) {
                openAlert('ERROR SISTEMA', 'No se logro conectar con el <b class="green">SERVIDOR</b>!');
            }).always(function() {});
        }

        function loginAjax() {
            var $msg;
            $.post("<?Php echo filter_input(INPUT_SERVER, 'PHP_SELF', FILTER_SANITIZE_STRING); ?>", {
                    loginAjax: true,
                    Emp_Cod: $('#Emp_Cod').val(),
                    Suc_Cod: $('#Emp_Cod option:selected').data('Suc_Cod'),
                    user_name: $('#Usu_Ced').val(),
                    encryptor: md5($('#Usu_Pas').val())
                }, function(response) {
                    if (response['success'] === true) {
                        $msg =
                            '<div class="alert alert-success fade in"><button type="button" class="close" data-dismiss="alert">x</button><strong>[SISTEMA]</strong> &nbsp;&nbsp;Login Correcto. Direccionando....</div>';
                        setTimeout(function() {
                            window.location.href =
                                "<?Php echo filter_input(INPUT_SERVER, 'PHP_SELF', FILTER_SANITIZE_STRING); ?>";
                        }, 2500);
                    } else {
                        $msg =
                            '<div class="alert alert-warning fade in"><button type="button" class="close" data-dismiss="alert">x</button><strong>[ERROR]</strong> &nbsp;&nbsp;Contrase&ntilde;a Incorrecta.</div>';
                    }
                }, 'json').fail(function(error) {
                    $msg =
                        '<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert">x</button><strong>[ERROR]</strong> &nbsp;&nbsp;El Servidor ha fallado en responder!.</div>';
                })
                .always(function() {
                    $('#msgAlert').html($msg);
                    $('#msgAlert .alert').hide();
                    $('#msgAlert .alert').show();
                    setTimeout(function() {
                        $('#msgAlert .alert').removeClass('in').addClass('out');
                    }, 4000);
                });
        }
        $(document).ready(function() {
            $('[data-tooltip="tooltip"]').tooltip({
                container: 'body'
            });
            $('#Emp_Cod').chosenDesc({
                width: '100%',
                template: function(t, d) {
                    return '<div class="over"><b>' + t + '</b></div><div class="over desc">' + d[
                        'emp_nom'] + '</div>';
                }
            });
            $("#Emp_Cod_chosen").addClass('bs-chosen').find('.chosen-single').addClass('form-control');
            if (ace.cookie.get('ace_tree') === 'true') {
                $('.ace-settings-con').hide();
                $('#sidebar').css({
                    'border-right-width': '1px',
                    'border-right-style': 'solid'
                });
                $('#nav-tree .treeMenuDefault nobr:first-child').on('mousedown', function() {
                    $('.sidebar[data-sidebar-scroll=true]').ace_sidebar_scroll('reset');
                });
            };
            $('.menu-link').on('click', function() {
                $('.menu-link').parent().removeClass('active');
                $('ul.highlight').removeClass('highlight');
                $(this).parent().addClass('active').parent().addClass('highlight');
            });
            loadPrinters();
            /* socketVentanas = new SocketVentanas();
            socketVentanas.setMain();
            socketVentanas.connectDefault();
            setTimeout(function() {
                socketVentanas.send('login');
            }, 1000); */
        });
        ace.vars['base'] = '..';

        //NUEVAS FUNCIONES PARA APARECER EL MODAL DE TICKETS (WB)

        document.getElementById('notificaciones_trigger').addEventListener('click', function() {
            var modal = document.getElementById('modal_notificacion');
            if (modal.style.display === 'none' || modal.style.display === '') {
                modal.style.display = 'block';
            } else {
                modal.style.display = 'none';
            }
        });
    </script>
    <script>
        var win = navigator.platform.indexOf('Win') > -1;
        if (win && document.querySelector('#sidenav-scrollbar')) {
            var options = {
                damping: '0.5'
            }
            Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
        }
    </script>
    <!--NUEVAS LIBRERIAS-->
    <!-- <script src="../../skins/js/ace/ace.settings.js"></script>
    <script src="../../skins/js/ace/ace.settings-skin.js"></script> -->
    <!--FIN FUNCIONES-->
    <!--script src="../../skins/js/exa.js"></script-->
    <script language="javascript" src="../../Librerias/validaciones/validacion.js"></script>
    <!-- <script src="../../framework/php/ventanasSocket/socketExaVentanas.js"></script> -->
    <?php //var_dump($rs_sucursales); 
    ?>
 <script src="../../skins/js/exa.js"></script>


</body>

</html>