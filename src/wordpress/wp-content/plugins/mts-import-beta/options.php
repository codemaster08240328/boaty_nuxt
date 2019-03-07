<?php 
//$operators    = showSelectOperators();
//$destinations = showSelectDestinations();

 ?>
<script>

var $ = jQuery;
    $(document).ready(function(){
        $("#select").click(function(){
            $('.unimportant').prop('checked', true);
        });

        $("#unselect").click(function(){
            $('.unimportant').prop('checked', false);
        });


    });

</script>

<div class="wrap" id="mts-import">
<h2>Welcome to MTS Import data page</h2>
<hr />
<?echo $message;?>


<hr />


<table border="1" class="main_table">
    <tr>
        <td>

        <h2>Boat characteristics</h2>
        <hr />

            <form method="get" action="options-general.php">
                <input type="hidden" name="page" value="mts-import-data-beta" />
                <input type="hidden" name="action" value="" />
                <input type="submit" name="import" value="start" />
                <hr />
                
            </form> 
            
            <h2>Import images</h2>
            <hr />

            <form method="get" action="options-general.php">

                <input type="hidden" name="page" value="mts-import-data-beta" />
                <input type="hidden" name="action" value="" />
                <input type="submit" name="import" value="start" />


                <hr />
            </form>           
            
            <h2>Import prices</h2>
            <hr />

            <form method="get" action="options-general.php">

                <input type="hidden" name="page" value="mts-import-data-beta" />
                <input type="hidden" name="action" value="" />
                <input type="submit" name="import" value="start" />
                <hr />
            </form>           

        </td>


        <td>
        <h2>Import Equipment</h2>
        <hr />

            <form method="get" action="options-general.php">
                <input type="hidden" name="page" value="mts-import-data-beta" />
                <input type="hidden" name="action" value="equipment" />
                <input type="submit" name="import" value="start" />
                <hr />

            </form>  
            
        <h2>Import extra prices</h2>
        <hr />

            <form method="get" action="options-general.php">

                <input type="hidden" name="page" value="mts-import-data-beta" />
                <input type="hidden" name="action" value="" />
                 <input type="submit" name="import" value="start" />
                <hr />
            </form>     
            
        <h2>Import boat brand and type</h2>
        <hr />

            <form method="get" action="options-general.php">

                <input type="hidden" name="page" value="mts-import-data-beta" />
                <input type="hidden" name="action" value="" />
                <input type="submit" name="import" value="start" />

                <hr />
            </form>                           

        </td>

        <td>

        <h2>Availability</h2>
        <hr />

            <form method="get" action="options-general.php">

                <input type="hidden" name="page" value="mts-import-data-beta" />
                <input type="hidden" name="action" value="" />
                <input type="submit" name="import" value="start" />
                <hr />

            </form>
            
            
            <h2>Template for all boats</h2>
            <hr />

            <form method="get" action="options-general.php">
            <input type="hidden" name="action" value="" />
            <input type="submit" name="import" value="start" />
            </form>     

        </td>

        <td>


        
        
         <h2>Duplicated boats into Russian language</h2>
        <hr />

        <form method="get" action="options-general.php">
            <input type="hidden" name="page" value="mts-import-data-beta" />
            <input type="hidden" name="action" value="new_base" />
            <button type="submit" name="when" value="now" >Start import</button>
        </form>
        
        


        </td>



    </tr>
</table>

</div>
<div>
    <div class="export">
        <form method="get" action="options-general.php">
             <input type="hidden" name="page" value="mts-import-data-beta" />
             <input type="hidden" name="action" value="export" />
             <input type="hidden" name="export" value="true" />
            <h2>Export all boats in XML file</h2>
            <input type="submit" name="start" value="export" class="start_import"/>
        </form>
    </div>

</div>



<style>
    
    
    .main_table td{
        width: 25%;
        vertical-align: top;
        padding: 0 10px;
    }    
    .unimportant_td{
        text-align: right;
    }
    
</style>
<?php 




 ?>