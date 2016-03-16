<?php
ob_start();
session_start();
require 'write_system_log.php';
require 'functions.php';
require_once 'model/logs_model.php';

$entry = array();

if (check_login()){
    $current_log_id=$_SESSION['current_sailing_log']['id'];
    $user= $_SESSION['user'];
    $this_entry_id = filter_input(INPUT_GET, 'entry_id', FILTER_SANITIZE_NUMBER_INT);

    // edit_entry_view.php will display different views depending on the value of the $entry
    $entry = get_entry($db, $this_entry_id);
       
    if ($entry[0]==false) {
        //do nothing, error will be displayed in view
    } 
    elseif ((isset($entry[1]) && $entry[1]==$user) ) {
        if ( isset( $_POST['updateEntry'] ) ) {
            // returns BOOL
            $success=update_entry($db, $this_entry_id);
            if ($success){
                header("Location:log_controller.php?id=".$current_log_id);
                exit();
            } 
            else {
                // overwrites the original values of $entry
                $entry[0]=false;
                $entry[1]=$UNABLE_UPDATE_ENTRY;
            }
        }
        elseif ( isset( $_POST['cancelBtn'] ) ) {
            header("Location:log_controller.php?id=".$current_log_id);
            exit();    
        }
        else {
            // in edit_entry_view.php -> show_edit_entry_form($entry[0]);
        }
    }
    else {
        $entry[0]=false;
        $entry[1]=$NO_PERMISSION;    
    }
}
else {
    $entry[0]=false;
    $entry[1]=$NO_LOGIN;    
}
require './view/edit_entry_view.php';