<?php
ob_start();
session_start();
require 'write_system_log.php';
require 'functions.php';
require_once 'model/logs_model.php';


$updated_log= array();
$owner=array();
$owner[0]=false;
$sailing_log=null;
$this_log_id=null;

if (isset($_SESSION['current_sailing_log'])){
    $sailing_log=$_SESSION['current_sailing_log'];
    $this_log_id= $_SESSION['current_sailing_log']['id'];
    $owner=get_owner($db, $this_log_id);
}    

if (check_login() && $owner[0]==$_SESSION['user']){
    if ( isset( $_POST['updateLog'] ) ) {
        $updated_log=update_log($db, $this_log_id);
        if ($updated_log[0]){
            header("Location:log_controller.php?id=".$this_log_id);
            exit();
        } 
        else {
            // something went wrong. Error msg will be displayed in view
        }
    }
    elseif ( isset( $_POST['cancel'] ) ) {
        header("Location:log_controller.php?id=".$this_log_id);
        exit();    
    }
    else {
        // show form
        $updated_log[0]=true;
    }
}    
else {
    $updated_log[0]=false;
    $updated_log[1]=$NO_LOGIN ."<br><br>OR<br><br> ".$NO_PERMISSION ;
}

/*
$error_messge=null;
login_logout();

if (!isset($_SESSION['user'])){
    $error_messge=$NO_LOGIN;
}
elseif ( isset( $_POST['updateLog'] ) ) {
    $updated_log=update_log($db, $sailing_log['id']);
    if ($updated_log[0]){
        header("Location:log_controller.php?id=".$sailing_log['id']);
        exit();
    } 
    else {
        $error_messge= $UNABLE_NEW_LOG;
    }
}
elseif ( isset( $_POST['cancel'] ) ) {
    header("Location:log_controller.php?id=". $sailing_log['id']);
    exit();    
}
else {
    // everything is ok, in view -> show_edit_form($sailing_log);
} */

require './view/edit_log_view.php';