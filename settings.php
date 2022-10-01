<?php 
session_start();
include('header.php'); 

?>
<div class="grid">
    <div class="row cells5">


        <div class="cell">
            <?php include('gauchemenuadmin.php'); ?>
        </div>

        

        <div class="cell colspan3">
            <img src = "img/conf.jpg">
        </div>


        <div class="cell">
            <?php include('droitmenu.php'); ?>
        </div>

    </div>
</div>
<script type="text/javascript">
    
$(function(){activerMenu("settings");});
</script>
<?php include('footer.php'); ?>