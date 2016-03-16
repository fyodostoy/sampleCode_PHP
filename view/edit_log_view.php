<?php
$title ='Edit Log';
if ($updated_log[0]==false)
{   print_error($updated_log[1]);
}
else
{
    show_edit_form($sailing_log);  //$sailing_log is defined in the controller
}    
$content = ob_get_clean();
require ('layout.php'); 

/* ============================================================================= 
                                    FUNCTIONS
============================================================================= */ 

function show_edit_form($sailing_log)
{   
?>
    <article class="container-white">
        <h2>Edit log</h2>
        <form id="log_form" action="" method="post" data-ajax=false>
            <fieldset>
                <legend></legend>              
                    <label>
                        <input name="private" type="checkbox" value='1'> Make this log private
                    </label>
                    <br>
                        <label for="datepicker">Date Started: *</label>
                        <input data-theme="a" type="text" id="datepicker_start" value="<?php echo $sailing_log['start_date']; ?>" name="datepicker_start" title="mm/dd/yyyy" required>
                    <br>
                        <label for="datepicker2">Date Finished: *</label>
                        <input data-theme="a" type="text" id="datepicker_end" value="<?php echo $sailing_log['end_date']; ?>" name="datepicker_end" title="mm/dd/yyyy" required>
                    <br>
                        <label for="title">Title: *</label>
                        <input data-theme="a" type="text" id="title" name="title" value="<?php echo $sailing_log['title']; ?>" title="e.g. : Cruising Gulf Islands, May 2014" required>
                    <br>
                        <label for="boat">Boat's Name: *</label>
                        <input data-theme="a" type="text" id="boat" name="boat" value="<?php echo $sailing_log['boat_name']; ?>"  title="Boat's Name required">
                    <br>
                        <label for="boat">Port of Departure: *</label>
                        <input data-theme="a" type="text" id="port-departure" name="port-departure" value="<?php echo $sailing_log['departure']; ?>"  >
                    <br>
                        <label for="boat">Final Destination:</label>
                        <input data-theme="a" type="text" id="final-destination" name="final-destination" value="<?php echo $sailing_log['destination']; ?>" >
                    <br>
                        <label for="skipper">Skipper: *</label>
                        <input data-theme="a" type="text" id="skipper" name="skipper" value="<?php echo $sailing_log['skipper']; ?>" aria-describedby="name-format" 
                                       required aria-required="true" title="Firstname Lastname"
                                       placeholder="Firstname Lastname" > <!-- pattern="[A-Za-z-0-9]+\s[A-Za-z-'0-9]+"   -->
                    <br>
                        <label for="crew">Crew:</label>
                        <textarea id="crew" name="crew" title="Crew's Name" rows="4" cols="50" ><?php echo $sailing_log['crew']; ?>
                        </textarea>
                    <br>
                        <label for="summary">A short description of the trip:</label>
                        <textarea id="summary" name="summary" title="A short description of the trip" rows="4" cols="50"><?php echo $sailing_log['summary']; ?>
                        </textarea>
                    <br>
            </fieldset>
            <!-- SAVE & CANCEL BUTTONS -->
            <section class="button_frame">
                <div class="button-box-left">
                    <button id="saveLog" type="submit"  name="updateLog"
                       class="ui-btn ui-shadow ui-corner-all ui-btn-icon-right ui-icon-check right">Update Log</button>
                </div>
                <div class="button-box-right">
                    <button id="resetBtn" type="submit" name="cancel" value="cancel" 
                           class="ui-btn ui-shadow ui-corner-all ui-btn-icon-right ui-icon-delete right">Cancel</button>
                </div> 
            </section>
        </form>
        <div class="removable-space"></div>
    </article> 
    <div class="removable-space"></div>
        <?php
}