<?php

// ���������� ������� "������"

	//print('view = ^'.$view.'^ action = '.$action);

         
	if ($view == ""  or  $view == "MainPage") {
		// ��������� ��������
		include("mainpage.php");
	} elseif ($view == "ViewUserData") {
		//������ � ������������
		include("viewuserdata.php");
	} elseif ($view == "ViewTeamData") {
		// ������ � ������� 
		include("viewteamdata.php");
		include("viewteamresultdata.php");
	} elseif ($view == "ViewUsers") {
		// ���������� ������ ������������ 
		include("viewusers.php");
	} elseif ($view == "ViewRaidTeams") {
		// ���������� ��� (�� ���� ��������)
		include("viewraidteams.php");
	}

	// ������� ����������
	$view = "";

?>
