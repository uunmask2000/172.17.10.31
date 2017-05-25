<!DOCTYPE html>
<html>
<head>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script>

/*
$(document).ready(function(){
    $("button").click(function(){
        $.ajax({url: "./LOG_view/LOG_virw.php", success: function(result){
            $("#div1").html(result);
        }});
    });
});
*/
</script>

<script>
function myFunction() {
    //alert("Page is loaded");
    setInterval(function() {

        //alert("Hello");
        $.ajax({
            url: "./LOG_view/LOG_virw1.php",
            success: function(result) {
                $("#div1").html(result);
            }
        });
        $.ajax({
            url: "./LOG_view/LOG_virw2.php",
            success: function(result) {
                $("#div2").html(result);
            }
        });
        $.ajax({
            url: "./LOG_view/LOG_virw3.php",
            success: function(result) {
                $("#div3").html(result);
            }
        });

    }, 3000);

}
</script>

</head>
<body onload="myFunction()">
<H2> taskServer </H2>
<div id="div1" overflow：auto ></div>
<H2> broadcast </H2>
<div id="div2" overflow：auto ></div>
<H2> data_process </H2>
<div id="div3" overflow：auto ></div>
</body>
</html>
