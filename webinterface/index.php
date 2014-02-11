<html>
<head>
<script type="text/javascript" src="jquery.js"></script>
<link rel="stylesheet" type="text/css" href="fancybox/jquery.fancybox-1.3.4.css" media="screen" />
<link rel="stylesheet" type="text/css" href="jquery-ui-1.8.18.custom.css" />
<link rel="stylesheet" type="text/css" href="styles.css" />
<script type="text/javascript" src="fancybox/jquery.mousewheel-3.0.4.pack.js"></script>
<script type="text/javascript" src="fancybox/jquery.fancybox-1.3.4.pack.js"></script>
<script type="text/javascript">
		$(document).ready(function() {
				$("a.vergroot").fancybox();   
				$(".video").click(function() {
					$.fancybox({
						'padding'		: 0,
						'autoScale'		: false,
						'transitionIn'	: 'none',
						'transitionOut'	: 'none',
						'title'			: this.title,
						'width'			: 640,
						'height'		: 385,
						'href'			: this.href.replace(new RegExp("watch\\?v=", "i"), 'v/'),
						'type'			: 'swf',
						'swf'			: {
							'wmode'				: 'transparent',
							'allowfullscreen'	: 'true'
						}
					});
				return false;
				});     
		});
</script>
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-38955983-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
</head>
<body>
<?php





if(!isset($_GET['page']))
	$_GET['page'] = 1;

$aantal = 16;	
$j = 0;
$begin = ($_GET['page']-1)*$aantal;
$end = $begin+$aantal; 

if(!isset($_GET['dag'])){
	$dag = date("d",time());
	$maand = substr(date("F",time()),0,3);
	$jaar = date("Y",time());
	$current = $dag.$maand.$jaar;
}
else
	$current = $_GET['dag'];

echo "<div class='month'>You're watching: $current</div><ul id='month_list' style='overflow:hidden'>";


if ($handle = opendir('pics') ) {
	$months = array();
	while (false !== ($entry = readdir($handle))) {  	
        if($entry != "." && $entry != ".."){
        	array_push($months, $entry);
        }
    }
    sort($months);
    
    
    for($f = 0; $f < count($months); $f++){
    	$dag = substr($months[$f],0,2);
			$maand = substr($months[$f],2,3);
			$jaar =  substr($months[$f],5,4);
        
        	echo "<li  class='button' style='float:left'><a style='text-decoration:none;color: #fff;' href='index.php?page=1&dag=".$dag.$maand.$jaar."'>".$dag.$maand.$jaar."</a></li>";
        	
        	}
}
echo "</ul>";

    	
    $filecount = count(glob("pics/$current/" .  "*.jpg"));
	
	echo "<h2>There are $filecount images available</h2><br/>";
    	

$current_hour = date("G",time());
if($current_hour==0)
	$current_hour = 24;

echo "<a href='index.php?dag=$current'  class='button' >Look sequentially</a>&nbsp;&nbsp;&nbsp;<a  class='button'  href='index.php?dag=$current&uur=".$current_hour."'>Look hourly</a><br/><br/>";


if(is_dir("pics/$current")){
if(!isset($_GET['uur'])){



if ($handle = opendir("pics/$current") ) {

    /* This is the correct way to loop over the directory. */
    $stack = array();

    while (false !== ($entry = readdir($handle))) {  	
        if($entry != "." && $entry != ".."){
        	array_push($stack, $entry);
        }
    }
    
    sort($stack);
   
    $cols = 4; $i = 0;
    if(count($stack) <  $end)
    	$end = count($stack);

    	
    for($f = 0; $f <= count($stack)/$aantal; $f++){
    	if($f%20==0)
			echo "<br/><br/><br/>";
    	if($_GET['page']==$f+1)
	    	echo "<a  class='button'  href='index.php?page=".($f+1)."&dag=".$current."'><span style='text-decoration:underline;color:#B23232;'>". ($f+1) ."</span></a>";
    	else
    		echo "<a class='button'  href='index.php?page=".($f+1)."&dag=".$current."'><span>". ($f+1) ."</span></a>";
    }
    echo "</ul>";
        echo "<br/><br/>&nbsp;<br/>";
        
     echo "<table><tr>";
    for($f = $begin; $f < $end; $f++){
    	if($i%$cols == 0)
    		echo "</tr><tr>";
    	$i++;
    	$uur = substr($stack[$f],10,2)+1;
    	$min = substr($stack[$f],12,2);
    	$sec = substr($stack[$f],14,2);
    	
    	echo "<td style='text-align:center'><a class='vergroot' title='".$dag." ".$maand." ". $jaar ." - ".$uur. ":" . $min. ":". $sec."' rel='reeks' href='pics/".substr($stack[$f],0,9)."/$stack[$f]'><img src='pics/".substr($stack[$f],0,9)."/$stack[$f]' width='300' style='float:left;'><br/>".$dag." ".$maand." ". $jaar . " - ".$uur. ":" . $min. ":". $sec. "</td></a>";
    }
    echo "</tr></table>";
    
    closedir($handle);
}
}
else{

echo "<br/><br/><br/>";
	for($i = 1; $i <= 24 ; $i++){
		if($i%14==0)
			echo "<br/><br/><br/><br/>";
		if($_GET['uur']==$i)
    		echo "<a class='button' href='index.php?dag=".$current."&uur=$i'><span style='text-decoration:underline;color:#B23232;'>".$i." u</span></a>&nbsp;&nbsp;&nbsp;";
    	else
		echo "<a class='button' href='index.php?dag=".$current."&uur=$i'>".$i." u</a>&nbsp;&nbsp;&nbsp;";
	}
	echo "<br/><br/><br/>";
	
	if ($handle = opendir("pics/$current") ) {

    /* This is the correct way to loop over the directory. */
    $stack = array();

    while (false !== ($entry = readdir($handle))) {  
    	
	   $uur =  substr($entry,10,2)+1;
	   if($uur == 0)
		   $uur = 24;
	   
        if($entry != "." && $entry != ".." && $uur == $_GET['uur']){
        	array_push($stack, $entry);
        }
    }
    
    sort($stack);
    echo "<table><tr>";
    $cols = 4; $i = 0;
    	
    for($f = 0; $f < count($stack); $f++){
    	if($i%$cols == 0)
    		echo "</tr><tr>";
    	$i++;
    	
    	$dag = substr($stack[$f],0,2);
		$maand = substr($stack[$f],2,3);
		$jaar =  substr($stack[$f],5,4);
    	
    	$uur = substr($stack[$f],10,2)+1;
    	$min = substr($stack[$f],12,2);
    	$sec = substr($stack[$f],14,2);
    	
    	echo "<td style='text-align:center'><a class='vergroot' title='".$dag." ".$maand." ". $jaar ." - ".$uur. ":" . $min. ":". $sec."' rel='24feb' href='pics/".substr($stack[$f],0,9)."/$stack[$f]'><img src='pics/".substr($stack[$f],0,9)."/$stack[$f]' width='300' style='float:left;'><br/>".$dag." ".$maand." ". $jaar . " - ".$uur. ":" . $min. ":". $sec. "</td></a>";
    }
    echo "</tr></table>";
    
    closedir($handle);
}

}



}
else
	print "day whut?..";
?>
</body>
</html>