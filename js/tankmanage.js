
$(function(){
    
    $("#btnAddTank").click(function(){
        
        addTank("T1","AGO");
        
    });
    
});







function refreshListTk(){
    
}
function addTank(typeTank,nomTank){
   
    try{
       $.post("manage/tankmng.php",
            {action:"addTank",nomTank:typeTank,typeTank:nomTank},
            function(data){
                $("#result").html(data);
            }
        );
   }
   catch(e){
       $("#error").html(e);
   }
        
    
    
}
function renameTank(){
    
}

