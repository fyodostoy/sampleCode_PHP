<?php
$title ='Edit Entry';
if ($entry[0]==false) {   
    print_error($entry[1]);
}
else
{
    show_edit_entry_form($entry[0]);  //$sailing_log is defined in the controller
}    
$content = ob_get_clean();
require ('layout.php'); 

/* ============================================================================= 
                         show_edit_entry_form()    
============================================================================= */ 

function show_edit_entry_form($entry){?>
<article class="container-white">
    <h2>Update Entry</h2>
    <form id="entry_form" action="" method="post" data-ajax=false>
        <fieldset>
            <legend></legend>
            <section>
                <article>        
                    <label for="datepicker_date">Date:</label>
                    <input type="text" id="datepicker_date" value="<?php echo $entry['date']; ?>" name="datepicker_date">
                </article><br>  
               <article>        
                    <label for="departure">Departure:</label>
                    <input type="text" id="departure" value="<?php echo $entry['departure']; ?> " name="departure">
                </article><br>  
                <article>
                    <label for="destination">Destination:</label>
                    <input type="text" id="destination" value="<?php echo $entry['destination']; ?> " name="destination">
                </article><br>
                <article>
                    <label for="weather">Weather forecast:</label>
                    <textarea  id="weather" name="weather" rows="4" cols="50"><?php echo $entry['weather']; ?></textarea>
                </article><br>
                <article>
                    <label for="tide">Tide forecast:</label>
                    <textarea id="tide" name="tide" rows="4" cols="50"><?php echo $entry['tide']; ?></textarea>
                </article><br>
                <article>
                    <label for="logentry">Log Entry:</label>                  
                    <textarea class="height-200" name="logentry" id="logentry" rows="10" cols="50"><?php echo $entry['entry']; ?></textarea>                     
                </article><br>
            </section>
        </fieldset>
        <!-- SAVE & CANCEL BUTTONS -->
        <section class="button_frame">
            <div class="button-box-left">
            <button id="saveEntry" type="submit"  name="updateEntry"
               class="ui-btn ui-shadow ui-corner-all ui-btn-icon-right ui-icon-check right">Update Entry</button>
            </div>
            <div class="button-box-right">
            <button id="cancelBtn" type="submit" name="cancelBtn"
                   class="ui-btn ui-shadow ui-corner-all ui-btn-icon-right ui-icon-delete right">Cancel</button>
            </div>
        </section>
        <br>
    </form>
</article>  
<div class="removable-space"></div>
 <?php
}