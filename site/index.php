<?php

        // ����� ���������
	include("settings.php");
	// ���������� �������
	include("functions.php");

        // ��������� ���������� POST GET REAUEST COOKIE  � ������ ��������� sql �������� � �����
        ClearArrays();

        // ���� ������ (1 - ���� ������ statustext ���������� ������� � ��������� ���� � ����������) 
	// � �����, ������� ��������� � ��������� ������ (��� �������)
	$alert = 0; 
	$statustext = ""; //"�������: ".date("d.m.Y")."  &nbsp; �����: ".date("H:i:s");

	if ($action == "") 
	{
	// �������� �� �������
		$view = "MainPage";

	} elseif ($action == "StartPage") {

                $view = $_POST['view'];

	} else {	
	        // ���������� �������, ��������� � �������������
		include ("useraction.php");

	        // ���������� �������, ��������� � ��������
		include ("teamaction.php");

	        // ���������� �������, ��������� � ������������ �������
		include ("teamresultaction.php");

	}

    // 15,01,2012 ���������� �������� � ����� �����, � �� ����� 
    //$action = "";

?>

<html>
 <head>
  <title>���</title>
  <link rel="Stylesheet" type="text/css"  href="styles/mmb.css" />

 </head>

 <body>
 

	<table  width = "100%"  border = "0" cellpadding = "0" cellspacing = "0" valign = "top" align = "left"  >
	<tr>
<!--
		<td  align="left" width = "10">
			</br>
		</td>
-->
		<td  align="left" width = "220" valign = "top" >
		<!--����� ������� -->
                   <div style = "padding-left: 10px; padding-right: 15px; padding-bottom: 25px;  
		                 border-right-color: #000000;  border-right-style: solid; border-right-width: 1px;
				 border-bottom-color: #000000;  border-bottom-style: solid; border-bottom-width: 1px;">

                        <form name = "StartPageForm" action = "<? echo $MyPHPScript; ?>" method = "post">
				<input type = "hidden" name = "action" value = "StartPage">
				<input type = "hidden" name = "view" value = "MainPage">
				<input type = "hidden" name = "sessionid" value = "<? echo (!empty($SessionId) ? $SessionId : $_POST['sessionid']); ?>">
				<input type = "hidden" name = "RaidId" value = "<? echo (!empty($RaidId) ? $RaidId : $_POST['RaidId']); ?>">
				<a href="javascript:document.StartPageForm.submit();"><img  style = "margin-bottom: 15px;" width = "157" height = "139" border = "0" alt = "���"  src = "http://mmb.progressor.ru/mmbicons/mmb2012v-logo-s_4.png"></a>
                       </form> 

			<!-- ������� ���� �� php -->
			<?php  include("menu.php"); ?>
			<!-- ����� ������� ���� �� php -->

                   </div>
		<!--����� ����� ������� -->
		</td>
		<td align="left" valign = "top">
		<!--������ ������� -->

                    <div style = "padding-left: 20px; padding-right: 10px;">

			<!-- ���������  -->
			<?php 

                         if (!empty($statustext))
                         {
			    print('<div class = "ErrorText">'.$statustext.'</div>'."\n");
                            //print('<table width = "100%"><tr><td>'.$statustext.'</td><td style = "border-top-style: dotted; border-top-width: 2px; border-top-color: #CC0000;">&nbsp;</td></tr></table>'."\n");
                          }

                         // ��������� �������� �����			
			 include("mainpart.php"); 

                         // ���������� ��������
			 $action = "";
                         // �.�. ����� � view ���������� 
			 $viewsubmode  = "";
			?>
		   </div>
		<!--����� ������ ������� -->
		</td>
	</tr>
	</table>

 </body>
</html>
