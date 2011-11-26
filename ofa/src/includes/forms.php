<?php
	/*
	Framework para formularios
	
	@author Martin Fernandez
	*/

	function iniciarForm ($name,$action,$method='post',$js = '', $table=false){
		echo "<form id=\"$name\" name=\"$name\" action=\"$action\" method=\"$method\" $js onSubmit=\"return false\">";
		if ($table) echo '<table align=center class=tableForm>';
	}
	
	function cerrarForm ($table=false){
		if ($table) echo '</table>';
		echo '</form>'.$retorno;
	}
	
	function ajaxComboBox($xml,$id,$name,$caption,$modo=0){
		addJs ('js/dhtmlXCommon.js');
		addJs ('js/dhtmlXCombo.js');
			
		echo "<tr><td align=right>$caption</td><td>";
		addDiv ($id,"style=\"display:inline;width:200px; height:30px;\" ","");
		?>
			<script>window.dhx_globalImgPath="img/";</script>
			<script>
				
				<?php
				if (modo == 0){
				?>
				var z=new dhtmlXCombo("<?php echo $id?>","<?php echo $name?>",200);
				z.enableFilteringMode(true,"<?php echo $xml?>",true);
				<?php
				}
				else{
				?>
				var z=new dhtmlXComboFromSelect("<?php echo $id?>");
				z.enableFilteringMode(true)
				}
				<?php
				}
				?>
			</script>
		<?php
		
		echo "</td></tr>";
	}
	
	function comboBox (&$db,$strsql,$caption,$name,$selected=0,$onChange='' , $table=true, $selectedIdx=0,$mostrarSeleccione = true){
		$rs = $db->ejecutar_sql($strsql);
		
		if ($rs && !$rs->EOF){
			if ($table) echo "<tr><td align=right>$caption</td><td>";
			
			echo "<select class=combo id=\"$name\" name=\"$name\" onChange=\"$onChange\">".$retorno;
			if ($mostrarSeleccione) echo '<option  value=0>-seleccione-</option>';
			$i=0;
			while ( !$rs->EOF){
			
				$option = $rs->fields[0];
				$text   = $rs->fields[1];
				
				if ($selected == $rs->fields[0] || ($selectedIdx != 0 && $selectedIdx-1 == $i) ){
					echo "<option selected value=\"$option\">$text</option>$retorno";
				}
				else
				{
					echo "<option value=\"$option\">$text</option>$retorno";
				}
				$rs->MoveNext();
				$i++;
			}
			echo '</select>'.$retorno;
			if ($table)  echo "</td></tr>";
		}
	}
	
	function textBox ($caption,$name,$value,$table=true,$style='',$js='', $max = 100 , $size=20){
		if ($table) echo "<tr><td align=right>$caption</td><td>";
		echo "<input $js size=$size style=\"$style\" maxlength=\"$max\" type=text id=\"$name\" name=\"$name\" value=\"$value\">$retorno";
		if ($table) echo '</td></tr>';
	}
	
	function textArea ($caption,$name,$value, $table=true, $style= '') {
		if ($table) echo "<tr><td align=right>$caption</td><td>";
		echo "<textarea style=\"$style\" name=\"$name\" id=\"$name\">$value</textarea>";
		if ($table) echo '</td></tr>';
	}
	
	function textBoxPassword ($caption,$name,$keypress=''){
		echo "<tr><td align=right>$caption</td><td>";
		echo "<input onkeypress=\"$keypress\" type=password id=\"$name\" name=\"$name\">$retorno";
		echo "</td></tr>";
	}
	
	function hidden ($name, $value = ''){
		echo "<input type=hidden id=\"$name\" name=\"$name\" value=\"$value\">".$retorno;
	}
	
	function button ($value,$onClick){
		echo "<input class=button type=button value=\"$value\" onClick=\"$onClick\" >&nbsp;$retorno";
	}
	
	function buttonTable ($value,$onClick){
		echo '<tr><td align=center colspan=2>';
		button ($value,$onClick);
		echo '</td></tr><tr><td align=center colspan=2>';
	}
	
	function radio ($name,$value,$onclick,$checked){
		echo "<input style=border:0 onclick=\"$onclick\" id=\"$name\" $checked type=radio name=\"$name\" value=\"$value\" >$retorno";
	}
	
	function check ($name,$value,$onClick,$checked) {
		echo "<input style=border:0 onClick=\"$onClick\" id=\"$name\" $checked type=checkbox name=\"$name\" value=\"$value\" >$retorno";
	}

?>
