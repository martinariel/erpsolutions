<?php

	/*
	Funciones de impresion HTML
	
	@Author Martín Fernández. ERP Solutions 2007
	@E-Mail  martin.fernandez@erp-solutions.com
	@Last Update: 2007/08/14
	
	*/
	
	
	function addDiv ($id,$style='',$innerHtml='')
	{
		echo "<div id=\"$id\" $style>$innerHtml</div>";
	}
	
	function addJs ($js) 
	{
		echo "<script language=\"javascript\" src=$js>";
		?>
		</script>
		<?php
	}
	
	function iniciarHtmlSimple(&$pagina){
	
	?>
<html>
<head>
<title><?php echo $pagina->get_page_name() ?> - WEB Interface</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="css/layout.css" rel="stylesheet" type="text/css">
<link rel="STYLESHEET" type="text/css" href="css/dhtmlXCombo.css">

<script type="text/javascript" language="javascript" src="js/prototype.js"></script>
</head>

<body>
<center>
<div id="Container">
  <div id="Content" class="mainText"> 
  <center>
  <?php
	} // fin iniciarHTMLSimple
	
	function iniciarHtml(&$pagina){
	
	?>
<html>
<head>
<title><?php echo $pagina->get_page_name() ?> - WEB Interface</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="css/layout.css" rel="stylesheet" type="text/css">
<link rel="STYLESHEET" type="text/css" href="css/dhtmlXCombo.css">

<script type="text/javascript" language="javascript" src="js/prototype.js"></script>
</head>

<body>
<center>
<div id="Container">
	
	<div id="Header"> 
		<img src="img/logo.png" class="floatLeft">
		<img src="img/erp.png" class="floatRight" width="140" style="margin-top:30px;margin-right:5px">

		<div id="login_info">
			<?php
					if (logged()){ 
						echo "Logged as ".$_SESSION ['username'];
						echo '&nbsp;<a href=logout.php>[salida]</a>';
					}
					?>
		</div>
	</div>


	<ul id="menu">
	<?php $pagina->menu();?>
	</ul>

<br>
	  	
  <div id="Content" class="mainText"> 
  <center>
  <?php
	} // fin iniciarHTML
	
	function cerrarHtml() {
	
	?>
	</center>
  </div>
	<div id="Footer"><p>ERP Solutions</p></div>
</div>
</center>
</body>
</html>

	<?php
	} //fin cerrarHtml
	
	
	function cerrarHtmlSimple() {
	
	?>
	</center>
  </div>
</div>
</center>
</body>
</html>

	<?php
	} //fin cerrarHtmlSimple
	?>
