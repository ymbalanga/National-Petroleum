<?php 

include 'session/session_dash.php';



include('header.php'); 

?>
<div class="grid">
    <div class="row cells5">
        <div class="cell">
               <?php include 'gauchemenu.php'; ?>
            <img src = "img/dash.png">
        </div>
        <!--        ---------fin gauche -------------------
        ---------partie central----------------->
        <div class="cell colspan4">
            <img src = "img/wel3.jpg" width="1050" height="300">
        </div>
    </div>

</div>
<!--        ----------fin partie central -------------->
</div>
</div>
<script type="text/javascript">
    
$(function(){activerMenu("home");});
</script>

<?php include('footer.php'); ?>