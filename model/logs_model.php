<?php
require '../esl_dbc_pdo.php'; 
require './error_msgs.php';
require './write_system_log.php';

// returns array (of logs) _or_ FLASE + ERROR MSG
function get_all_logs($db, $private)
{
    global $NO_LOG, $SQL_ERROR; // STRING: imported from error_msgs.php
    $all_sailing_logs = array();
    $sql = create_sql($private);
    try 
    {
        $stmt = $db->query($sql) ;
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);         
        if ($results){
            foreach ($results as $row) {
                $all_sailing_logs[]=$row;
            }
        }
        else {
            $all_sailing_logs[0]=false;
            $all_sailing_logs[1]=$NO_LOG;
        }
        $db = null;
    }
    catch(PDOException $e){
        $db = null;
        $all_sailing_logs[0]=false;
        $all_sailing_logs[1]=$SQL_ERROR;
        write_to_SYSTEM_log("\nPDOException: ".$e->getMessage());
    }
    return $all_sailing_logs;
}

// returns an SQL string based on user login status and the link clicked 
// (my-logs vs. other prople's public log)
function create_sql($private){
    $sql="SELECT * FROM es_log WHERE ";
    if (isset($_SESSION['user']) && !$private) // get other prople's public logs
    {
        $sql .= "user!='".$_SESSION['user']."' AND private =0";
    }
    elseif (isset($_SESSION['user']) && $private) // get my logs
    {
        $sql .= "user='".$_SESSION['user']."'";  
    }
    elseif (!isset($_SESSION['user']) && !$private) // not logged in, get only public logs
    {
        $sql .= "private = 0";
    }
    else{
        // if (!isset($_SESSION['user']) && !$private) 
        // not logged in, but trying to see "my logs"
        // This case is handeled in the controller, but as an extra measure set sql to null
        $sql = null; // this will throw an SQL error which will be caught in get_all_log() function
    }
    $sql .=" ORDER BY id DESC";
    return $sql;
}

// returns array (one log) _or_ FLASE + ERROR MSG array
function get_log($db, $sailing_log_id) {
    global $NO_LOG_FOUND, $SQL_ERROR; // STRING: imported from error_msgs.php
    $sailing_log = array();
    try
    {
        $sql="SELECT * FROM es_log WHERE id =".$sailing_log_id." AND ";
        // Normally, this part is not necessary if called from UI, but
        // this would prevent viewing a private log by entring its ID in URL
        // Note that get_log() function can be called on both "my logs" and "public logs"
        if (isset($_SESSION['user'])){
            $user=$_SESSION['user'];
            $sql .= "(private=0 OR user='".$user."')";
        }
        else {
            $sql .="private=0";
        }
        $sth = $db->prepare($sql);
        $sth->execute();
        $result = $sth->fetch(PDO::FETCH_ASSOC);
        if (!$result){ 
            $sailing_log[0]=false;
            $sailing_log[1]=$NO_LOG_FOUND;
        }
        else {
            $sailing_log[0]=$result;
        }
        $db = null;
    }
    catch(PDOException $ex)
    {
        $db = null;
        $sailing_log[0]=false;
        $sailing_log[1]=$SQL_ERROR;
        write_to_SYSTEM_log('Log ID: '. $sailing_log_id.': '.$ex->getMessage());
    }
    return $sailing_log;
}

// returns array of entries _or_ FLASE + ERROR MSG
function get_entries($db, $sailing_log_id) {
    global $NO_ENTRY, $SQL_ERROR; // STRING: imported from error_msgs.php
    $entries = array();
    try 
    {
        $sql= "SELECT * FROM log_entry WHERE es_log_id =".$sailing_log_id." ORDER BY date ASC";
        $sth = $db->prepare($sql);
        $sth->execute();
        $result = $sth->fetchAll(PDO::FETCH_ASSOC);
        if (!$result){
            $entries[0]= false;
            $entries[1]= $NO_ENTRY;
        }
        else {
            foreach ($result as $row) {
                $entries[] = $row;
            }           
        }
    }
    catch(PDOException $e){
        $db = null;
        $entries[0]= false;
        $entries[1]= $SQL_ERROR;
        write_to_SYSTEM_log("PDOException in get_entries() function:\n".$e->getMessage());
    }
    return $entries;
}

// returns array (one entry) _or_ FLASE + ERROR MSG
function get_entry($db, $entry_id) {
    global $SQL_ERROR, $NO_ENTRY; // STRING: imported from error_msgs.php
    $entry= array();
    try 
    {
        $sql= "SELECT * FROM log_entry WHERE id=".$entry_id;
        $sth = $db->prepare($sql);
        $sth->execute();
        $result = $sth->fetch(PDO::FETCH_ASSOC);
        
        $sql2= "SELECT user FROM es_log WHERE id=".$result['es_log_id'];
        $sth2 = $db->prepare($sql2);
        $sth2->execute();
        $result2 = $sth2->fetch(PDO::FETCH_ASSOC);
        
        if ($result && $result2){
            $entry[0]=$result;
            $entry[1]=$result2['user'];            
        }
        else { // no entry or there is an entry but the author can't be fetched
            $entry[0]=false;
            $entry[1]= $NO_ENTRY; 
        }
    }
    catch(PDOException $e){
        $db = null;
        $entry[0]=false;
        $entry[1]=$SQL_ERROR;
        write_to_SYSTEM_log("PDOException in get_entries() function:\n".$e->getMessage());
    }
    return $entry;
}

// returns array (bool + last insert ID)
function add_new_log($db)
{
    global $UNABLE_NEW_LOG; // STRING: imported from error_msgs.php
    $title      = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
    $date_strt  = filter_input(INPUT_POST, 'datepicker_start', FILTER_SANITIZE_STRING);
    $date_strt_f= date("Y-m-d",strtotime($date_strt));
    $date_end   = filter_input(INPUT_POST, 'datepicker_end', FILTER_SANITIZE_STRING);
    $date_end_f = date("Y-m-d",strtotime($date_end));
    $boat       = filter_input(INPUT_POST, 'boat', FILTER_SANITIZE_STRING);
    $port_dep   = filter_input(INPUT_POST, 'port-departure', FILTER_SANITIZE_STRING);
    $final_des  = filter_input(INPUT_POST, 'final-destination', FILTER_SANITIZE_STRING);
    $skipper    = filter_input(INPUT_POST, 'skipper', FILTER_SANITIZE_STRING);
    $crew       = filter_input(INPUT_POST, 'crew', FILTER_SANITIZE_STRING);
    $summary    = filter_input(INPUT_POST, 'summary', FILTER_SANITIZE_STRING);
    $user       = $_SESSION['user'];
    $private=0;
    
    if (isset($_POST['private']))
    {
        $private=1;
    }
    $added_log=array();
    
    try { 
        $stmt = $db->prepare("INSERT INTO es_log (start_date, end_date, user, title, boat_name, departure, destination, skipper, crew, summary, private) 
                                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)" );
        $stmt->execute(array( $date_strt_f, $date_end_f, $user , $title, $boat, $port_dep, $final_des, $skipper, $crew, $summary, $private));
        $added_log[0]=true;
        $added_log[1] = $db->lastInsertId();
        $db = null;
    }
    catch(PDOException $e)
    {
        $added_log[0]=false;
        $added_log[1]=$UNABLE_NEW_LOG;
        $db = null;
        write_to_SYSTEM_log('PDOException in add_new_log() function: '. $e->getMessage());
    }
    return $added_log;
}

function update_log($db, $id)
{
    global $UNABLE_UPDATE_LOG;
    $update_log=array();
    
    $title      = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
    $date_strt  = filter_input(INPUT_POST, 'datepicker_start', FILTER_SANITIZE_STRING);
    $date_strt_f= date("Y-m-d",strtotime($date_strt));
    $date_end   = filter_input(INPUT_POST, 'datepicker_end', FILTER_SANITIZE_STRING);
    $date_end_f = date("Y-m-d",strtotime($date_end));
    $boat       = filter_input(INPUT_POST, 'boat', FILTER_SANITIZE_STRING);
    $port_dep   = filter_input(INPUT_POST, 'port-departure', FILTER_SANITIZE_STRING);
    $final_des  = filter_input(INPUT_POST, 'final-destination', FILTER_SANITIZE_STRING);
    $skipper    = filter_input(INPUT_POST, 'skipper', FILTER_SANITIZE_STRING);
    $crew       = filter_input(INPUT_POST, 'crew', FILTER_SANITIZE_STRING);
    $summary    = filter_input(INPUT_POST, 'summary', FILTER_SANITIZE_STRING);
    $private=0;
    
    if (isset($_POST['private']))
    {
        $private=1;
    }  
    try { 
        $stmt = $db->prepare("UPDATE es_log 
            SET start_date=?, end_date=?, title=?, boat_name=?, departure=?, destination=?, skipper=?, crew=?, summary=?, private=? WHERE id=?"); 
        $stmt->execute(array( $date_strt_f, $date_end_f, $title, $boat, $port_dep, $final_des, $skipper, $crew, $summary, $private, $id));
        $update_log[0]=true;
        $db = null;
    }
    catch(PDOException $e)
    {
        $update_log[0]=false;
        $update_log[1]=$UNABLE_UPDATE_LOG;
        $db = null;
        write_to_SYSTEM_log('PDOException in add_new_log() function: '. $e->getMessage());
    }
    return $update_log;
}

function delete_log($db, $id){
    $deleted_log=array();
    try 
    {
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->beginTransaction();
        
        $stmt_log = $db->prepare("DELETE from es_log WHERE id=?");
        $stmt_log->execute(array($id));
        
        $stmt_entry = $db->prepare("DELETE from log_entry WHERE es_log_id=?");
        $stmt_entry->execute(array($id));
        
        $deleted_log[0]=true;
        $db->commit();        
    }
    catch(PDOException $e)
    {
        $deleted_log[0]=false;
        $db->rollBack();
        write_to_SYSTEM_log('PDOException in delete_log() function: '. $e->getMessage());        
    }
    $db=null;
    return $deleted_log;
}

function delete_entry($db, $id){
    $deleted_entry=array();
    try 
    {
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->beginTransaction();
        
        $stmt_log = $db->prepare("DELETE from log_entry WHERE id=?");
        $stmt_log->execute(array($id));
        
        $deleted_entry[0]=true;
        $db->commit();        
    }
    catch(PDOException $e)
    {
        $deleted_entry[0]=false;
        $db->rollBack();
        write_to_SYSTEM_log('PDOException in delete_log() function: '. $e->getMessage());        
    }
    $db=null;
    return $deleted_entry;
}

// Returns BOOL
function update_entry($db, $this_entry_id) {
    $date               = filter_input(INPUT_POST, 'datepicker_date', FILTER_SANITIZE_STRING);
    $date_f             = date("Y-m-d",strtotime($date));
    $weather            = filter_input(INPUT_POST, 'weather', FILTER_SANITIZE_STRING);
    $tide               = filter_input(INPUT_POST, 'tide', FILTER_SANITIZE_STRING);
    $sailing_logentry   = filter_input(INPUT_POST, 'logentry', FILTER_SANITIZE_STRING);
    $destination        = filter_input(INPUT_POST, 'destination', FILTER_SANITIZE_STRING);
    $departure          = filter_input(INPUT_POST, 'departure', FILTER_SANITIZE_STRING);
    $updated_entry;
    
    try { 
        $stmt = $db->prepare("UPDATE log_entry SET date=?, weather=?, tide=?, entry=?, destination=?, departure=? WHERE id=?");
        $stmt->execute(array($date_f, $weather, $tide, $sailing_logentry, $destination, $departure, $this_entry_id));
        $updated_entry=TRUE;
    }
    catch(PDOException $e) {
        $updated_entry=FALSE;
        write_to_SYSTEM_log("\nPDOException in add_new_entry(): failed to add entry tolog number: ".$e->getMessage()); //$current_log_id. "\n". 
    }
    $db = null;
    return $updated_entry;     
}

// Returns array of BOOL, other values might be added to the array in the controller
function add_new_entry($db, $current_log_id, $session_user)
{
    global $UNABLE_NEW_ENTRY, $NO_PERMISSION;
    $date               = filter_input(INPUT_POST, 'datepicker_date', FILTER_SANITIZE_STRING);
    $date_f             = date("Y-m-d",strtotime($date));
    $weather            = filter_input(INPUT_POST, 'weather', FILTER_SANITIZE_STRING);
    $tide               = filter_input(INPUT_POST, 'tide', FILTER_SANITIZE_STRING);
    $sailing_logentry   = filter_input(INPUT_POST, 'logentry', FILTER_SANITIZE_STRING);
    $destination        = filter_input(INPUT_POST, 'destination', FILTER_SANITIZE_STRING);
    $departure          = filter_input(INPUT_POST, 'departure', FILTER_SANITIZE_STRING);
    $added_entry        = array();
    
    // A user can click on add "entry button" on one of his/her own entries, then change the log_id in URL
    // To prevent this, double check that session_user and log_user (log author) are the same
    if (check_user_permission($db, $current_log_id, $session_user))
    {
        try { 
            $stmt = $db->prepare("INSERT INTO log_entry (es_log_id, date, weather, tide, entry, destination, departure) 
                                     VALUES (?, ?, ?, ?, ?, ?, ?)" );
            $stmt->execute(array($current_log_id, $date_f, $weather, $tide, $sailing_logentry, $destination, $departure ));
            $added_entry[0]=true;
            $db = null;            
        }
        catch(PDOException $e)
        {
            $added_entry[0]=false;
            $added_entry[1]=$UNABLE_NEW_ENTRY;
            $db = null;
            write_to_SYSTEM_log("\nPDOException in add_new_entry(): failed to add entry to log number: ".$current_log_id. "\n". $e->getMessage());
        }
    }
    else  
    {
        $added_entry[0]=false;
        $added_entry[1]=$NO_PERMISSION;
        write_to_SYSTEM_log("\nERROR: check_user_permission() returned false\n");
    }
    return $added_entry;
}

// Returns BOOL
function check_user_permission($db, $log_id, $session_user)
{
    $permitted=FALSE;
    try 
    {
        $check_user_stmt = $db->prepare("select user FROM es_log where id=?");
        $check_user_stmt->execute(array($log_id));
        $row = $check_user_stmt->fetch(PDO::FETCH_ASSOC);
        if ($row && $row['user']==$session_user)
        {
            $permitted=TRUE;
        }
        else {
            write_to_SYSTEM_log("\nERROR in check_user_permission(): Unauthorized user OR can't fetch user from es_log table \n".
                    "-log id : ". $log_id. 
                    "\n-log user: ".$row['user'] . 
                    "\n-session user: " .$session_user);  
        }
    } catch (PDOException $ex) {
        $db = null;
        write_to_SYSTEM_log("\nPDOException: check_user_permission() failed to fetch user: "
                .$session_user." -log id: ".$log_id. "\nSQL Error: ". $ex->getMessage());
    }
    return $permitted;
}

function get_owner($db, $log_id) {
    global $SQL_ERROR;
    $owner=array();
    try
    {
        $stmt = $db->prepare("SELECT user FROM es_log WHERE id =?");
        $stmt->execute(array($log_id));
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$result){ 
            $owner[0]=false;
            $owner[1]=$SQL_ERROR;
            write_to_SYSTEM_log('Cannot get_owner()');
        }
        else {
            $owner[0]=$result['user'];
        }
        $db = null;
    }
    catch(PDOException $ex)
    {
        $db = null;
        $owner[0]=false;
        $owner[1]=$SQL_ERROR;
        write_to_SYSTEM_log($ex->getMessage());
    }
    return $owner;
}