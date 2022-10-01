<?php
include 'session/session_dash.php';
include('header.php');
include 'db.php';


?>
<div class="grid">
    <div class="row cells5">
        <div class="cell">
            <?php include 'gauchemenu.php'; ?>
            <img src="img/exp.jpg">
        </div>
        <!--        ---------fin gauche -------------------
        ---------partie central----------------->
        <div class="cell colspan4">
            <div class="row cells2">
                <!--        ---------pump ------------------->
                <div class="cell well">


                    <!--debut modal-->
                    <div class="modal fade" id="updateExpense" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-sm">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                    <h4 class="modal-title" id="myModalLabel">Update EXPENSE DATA</h4>
                                </div>

                                <div class="modal-body"> 

                                    <form method="post" onsubmit="return false;">
                                        <input type="hidden" id="idexpense"/>
                                        <div class="cell">
                                            <div class="input-control text full-size">
                                                <label class="text-light">Reason</label>
                                                <input type="text" name="reasonUpdate" id="reasonUpdate" placeholder="Reason" size="20"/>
                                            </div>
                                        </div>
                                        <br/>
                                        <div class="cell">
                                            <div class="input-control text">
                                                <label class="text-light">Amount</label>
                                                <input type="text" name="amountUpdate" id="amountUpdate" placeholder="amount" size="20"/>
                                            </div>
                                        </div>
                                        <br/>
                                        <div class="cell">
                                            <div class="input-control text" data-role="datepicker">
                                                <label class="text-light">Date</label>
                                                <input type="text" id="expensedateUpdate" name="expensedateUpdate">
                                                <button class="button"><span class="mif-calendar"></span></button>
                                            </div><br/>
                                        </div>
                                        <button  id="updateData" class="btn  btn-info">Update</button>
                                        <button type="button" class="btn btn-default" data-dismiss="modal" >Close</button>
                                        <br/><br/>
                                        <br/>
                                        <div id="resuUpdate"></div>

                                    </form>

                                </div>

                            </div>
                        </div>
                    </div>
                    <!--fin modal-->


                    <h4 class="text-light">EXPENSES REASONS</h4>
                    <br/>
                    <form method="post" onsubmit="return false;">
                        
                        <div class="cell">
                            <div class="input-control text full-size">
                                <label class="text-light">Reason</label>
                                <input type="text" name="reason" id="reason" placeholder="Reason" size="20"/>
                            </div>
                        </div>
                        <br/>
                        <div class="cell">
                            <div class="input-control text">
                                <label class="text-light">Amount</label>
                                <input type="text" name="amount" id="amount" placeholder="amount" size="20"/>
                            </div>
                        </div>
                        <br/>
                        <div class="cell">
                            <div class="input-control text" data-role="datepicker">
                                <label class="text-light">Date</label>
                                <input type="text" id="expensedate" name="expensedate">
                                <button class="button"><span class="mif-calendar"></span></button>
                            </div><br/>
                        </div>
                        <button  id="exvalide"class="button info">Validate</button>
                        <br/><br/>
                        <br/>
                        <div id="resu"></div>

                    </form>
                </div>
                <!--        ---------tank ------------------->

                <!-- bloc tableau-->
                <div class="cell">
                    <h4 class="text-light ">EXPENSES SUMMARY</h4>
                    <table class="table table striped hovered  border bordered" data-role="datatable" data-searching="true">
                        <thead>
                            <tr>
                                <th class="">Expenses reason</th>
                                <th class="">amount</th>
                                <th class="">Date</th>
                                <th class="">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $exp = $pdo->query("SELECT id,intitule,montant,DATE_FORMAT(datedepense, '%Y-%m-%d') AS datedepense,DATE_FORMAT(datedepense, '%Y.%m.%d') AS datedepenseUpdate "
                                    . " FROM depenses ORDER BY id DESC");

                            while ($depenses = $exp->fetch(PDO::FETCH_OBJ)) {
                                ?>
                                <tr>
                                    <td><?= $depenses->intitule; ?></td>
                                    <td><?= $depenses->montant; ?></td>
                                    <td><?= $depenses->datedepense; ?></td>
                                    <td>
                                        <button class = 'btn btn-warning btn-sm' data-toggle="modal"  data-target="#updateExpense"
                                                onclick="remplirModal(<?= $depenses->id; ?>,'<?= $depenses->intitule; ?>',<?= $depenses->montant; ?>,'<?= $depenses->datedepenseUpdate; ?>') ;">Update</button>
                                    </td>
                                </tr>

                                <?php
                            }
                            ?>

                        </tbody>
                    </table>
                </div>


            </div>


        </div>
    </div>

</div>
<!--        ----------fin partie central -------------->
<script>
    $(function(){
        activerSm("exp");
        activerMenu("home");
        
        $("#exvalide").click(function () {
            var reason = $("#reason").val();
            var amount = $("#amount").val();
            var expensedate = $("#expensedate").val();
            
            
            if (reason !== '' && amount !== '' && expensedate !== '') {
                $.ajax({
                    url: "traitement/expensesphp.php",
                    method: "post",
                    data: {reason: reason, amount: amount, expensedate: expensedate},
                    success: function (data) {
                        $("#resu").html(data);
                        $("#reason").val(' ');
                        $("#amount").val(' ');
                        $("#expensedate").val(' ');

                        window.location.reload();
                    }
                });
            }else {
                $("#resu").html("<p class='alert alert-danger'>Please, Complete empty Fields !!!</p>");
            }
        });
        
        $("#updateData").click(function(){
            
            var idexpense = $("#idexpense").val();
            var reason = $("#reasonUpdate").val();
            var amount = $("#amountUpdate").val();
            var expensedate = $("#expensedateUpdate").val();
            
            
            if (idexpense !== '' && reason !== '' && amount !== '' && expensedate !== '') {
                $.ajax({
                    url: "traitement/expensesphp.php",
                    method: "post",
                    data: {action:"update",idexpense:idexpense,reason: reason, amount: amount, expensedate: expensedate},
                    success: function (data) {
                        $("#resuUpdate").html(data);

                        window.location.reload();
                    }
                });
            }else {
                $("#resuUpdate").html("<p class='alert alert-danger'>Please, Complete empty Fields !!!</p>");
            }
            
        });
    });
    
    function remplirModal(idExpense,reason,amount,date){
            
            $("#idexpense").val(idExpense);
            $("#reasonUpdate").val(reason);
            $("#amountUpdate").val(amount);
            $("#expensedateUpdate").val(date);
          
    };
</script>

<?php include('footer.php'); ?>