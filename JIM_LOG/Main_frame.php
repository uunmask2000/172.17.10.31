



<script type="text/javascript">
function SetCwinHeight()
{
var iframeid=document.getElementById("mainframe"); //iframe id
  if (document.getElementById)
  {
   if (iframeid && !window.opera)
   {
    if (iframeid.contentDocument && iframeid.contentDocument.body.offsetHeight)
     {
       iframeid.height = iframeid.contentDocument.body.offsetHeight;
     }else if(iframeid.Document && iframeid.Document.body.scrollHeight)
     {
       iframeid.height = iframeid.Document.body.scrollHeight;
      }
    }
   }
}
</script>
<?php

for ($i = 3; $i <= 5; $i++) {

	//echo '<iframe src="TEST_frame.php?room=' . $i . '" ></iframe>';
	echo '<iframe src="TEST_frame.php?room=' . $i . '" name="mainframe" width="100%" marginwidth="0" marginheight="0" onload="Javascript:SetCwinHeight()"  scrolling="yes" frameborder="0" id="mainframe"  ></iframe>';
}

?>
