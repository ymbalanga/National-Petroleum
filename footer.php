<div class="grid">
    <div class="row cells">
        <div class="cell">
            <div style="background:#c3d9c8; font-family:verdana; height: 21px;text-align: center">
                <P style=" text-align: center">Copyright DevLab-Conception November 2017</P>
            </div>
        </div>

    </div>
</div>



<script type="text/javascript">

function activerSm(sm){
    $("#sm-"+sm).addClass("active");
}

function activerMenu(menuid){
    $("#m-"+menuid).addClass("active");
}
function showTarification(){
    ///activerMenu("tarif");
    
    $.ajax({
        url: "tarification.php",
        method: "get",
        beforeSend: function () {
            $("#load").removeClass("hidden");
        },
        success: function (data) {

            $("#data").html(data);
            $("#load").addClass("hidden");
        },
        error: function (e) {
            alert(e);
        }
    });

}

</script>
</body>
</html>

