<?php

         // �� ����, ��� ���� �������� ������ ������������ SessionId ����� post
	// ���������� �������� UserLogin � ������� �� ������ �� ������ - ��� �������� ������ ���� �� ���� ��������
         // � �������� ����� ����� �� ����������
          // �.�. ����� ���� ��������� ��� ����� action �.�.������ ������
         if (empty($SessionId))
	 {
		$SessionId =  $_POST['sessionid'];
	 } 

	 // ������� ������������
	 $NowUserId = GetSession($SessionId);


         if ($viewmode == 'Add')
	 {

		$RaidId = $_POST['RaidId'];
		if (empty($RaidId) or empty($NowUserId))
		{
	            $statustext = '��� ����������� ����� ������� ���������� ������������� ������������ � ���';
	  	    $alert = 1;
		    return;
		}

		$Sql = "select user_email from  Users where user_id = ".$NowUserId;
		$Result = MySqlQuery($Sql);  
		$Row = mysql_fetch_assoc($Result);
		mysql_free_result($Result);
		$UserEmail = $Row['user_email'];

                // ����� ������� 
                $TeamId = 0;

		// ���� ��������� ����� ������ ���������� �� ����� ����������������
		if ($viewsubmode == "ReturnAfterError") 
		{

		  $TeamNum = (int) $_POST['TeamNum'];
		  $TeamName = $_POST['TeamName'];
		  $DistanceId = $_POST['DistanceId'];
		  $TeamUseGPS = ($_POST['TeamUseGPS'] == 'on' ? 1 : 0);
		  $TeamMapsCount = $_POST['TeamMapsCount'];
		  $TeamRegisterDt = 0;
		  $TeamConfirmResult = ($_POST['TeamConfirmResult'] == 'on' ? 1 : 0);
		  $ModeratorConfirmResult = ($_POST['ModeratorConfirmResult'] == 'on' ? 1 : 0);
		  $TeamGreenPeace = ($_POST['TeamGreenPeace'] == 'on' ? 1 : 0);
                  $TeamNotOnLevelId = $_POST['TeamNotOnLevelId'];

                } else {

		  $TeamNum = '�����';
		  $TeamName = '�������� �������';
		  $DistanceId = 0;
		  $TeamUseGPS = 0;
		  $TeamMapsCount = 0;
		  $TeamRegisterDt = 0;
		  $TeamConfirmResult = 0;
		  $ModeratorConfirmResult = 0;
		  $TeamGreenPeace = 0;
                  $TeamNotOnLevelId = 0;

		}

                $TeamUser = 0;

		// ������ ��������� ���� ����� �������?
		//$AllowEdit = 1;
		// ���������� ��������� ��������
		$NextActionName = 'AddTeam';
		// �������� �� ��������� ���� �� �����
		$OnClickText =  'onClick = "javascript:this.value = \'\';"';
		// ������� �� ������
		$SaveButtonText = '����������������';


         } else {

           // �������� �������������
               // �������� ����� ������ ��� ������ ����������� ����� �������
                 // ������ ����� Id ���� � ���������� php, �� ��� � ���������� �����
		if (empty($TeamId))
		{
			$TeamId = $_POST['TeamId']; 
                }

		if ($TeamId <= 0)
		{
		// ������ ���� ���������� �������, ������� �������
		     return;
		}

		$sql = "select t.team_num, t.distance_id, t.team_usegps, t.team_name, 
		               t.team_mapscount, d.raid_id, t.team_registerdt, 
			       t.team_confirmresult, t.team_moderatorconfirmresult,
                               t.team_greenpeace, t.level_id,
                               TIME_FORMAT(t.team_result, '%H:%i') as team_result 
		        from  Teams t
			      inner join  Distances d on t.distance_id = d.distance_id
			      inner join  Raids r on d.raid_id = r.raid_id
			where t.team_id = ".$TeamId; 
//		echo 'sql '.$sql;
                $Result = MySqlQuery($sql);
		$Row = mysql_fetch_assoc($Result);
                mysql_free_result($Result);

                // ��� ������ ������ ���� �� ����
		  $RaidId = $Row['raid_id'];
		  $TeamRegisterDt = $Row['team_registerdt'];
                  $TeamResult = $Row['team_result'];


		// ���� ��������� ����� ������ ���������� �� ����� ����������������
		if ($viewsubmode == "ReturnAfterError") 
		{

		  $TeamNum = (int) $_POST['TeamNum'];
		  $TeamName = $_POST['TeamName'];
		  $DistanceId = $_POST['DistanceId'];
		  $TeamUseGPS = ($_POST['TeamUseGPS'] == 'on' ? 1 : 0);
		  $TeamMapsCount = $_POST['TeamMapsCount'];
		  $TeamConfirmResult = ($_POST['TeamConfirmResult'] == 'on' ? 1 : 0);
		  $ModeratorConfirmResult = ($_POST['ModeratorConfirmResult'] == 'on' ? 1 : 0);
		  $TeamGreenPeace = ($_POST['TeamGreenPeace'] == 'on' ? 1 : 0);
                  $TeamNotOnLevelId = $_POST['TeamNotOnLevelId'];

                } else {

		  $TeamNum = $Row['team_num'];
		  $TeamName = $Row['team_name'];
		  $DistanceId = $Row['distance_id'];
		  $TeamUseGPS = $Row['team_usegps'];
		  $TeamMapsCount = $Row['team_mapscount'];
		  $TeamConfirmResult = $Row['team_confirmresult'];
		  $ModeratorConfirmResult = $Row['team_moderatorconfirmresult'];
		  $TeamGreenPeace = $Row['team_greenpeace'];
                  $TeamNotOnLevelId = $Row['level_id'];
		}



		if (CheckTeamUser($SessionId, $TeamId))
		{
		  $TeamUser = 1;
		} else {
		  $TeamUser = 0;
		}


	        $NextActionName = 'TeamChangeData';
		$AllowEdit = 0;
		$OnClickText = '';
		$SaveButtonText = '��������� ���������';
		

	 }
         // ����� �������� �������� � ��������

         // ���������� ������ ������������
	 // � ����� �������, ��� ��� ����� �������, ��� ��� ������������ ��� �������� ���


         // ��������� ��� ������� �������� 
	 $sql = "select r.raid_resultpublicationdate, r.raid_registrationenddate, 
                        CASE WHEN r.raid_registrationenddate is not null and YEAR(r.raid_registrationenddate) <= 2011 
                             THEN 1 
                             ELSE 0 
                        END as oldmmb
		 from   Raids r 
		 where r.raid_id = ".$RaidId; 
		//echo 'sql '.$sql;
                $Result = MySqlQuery($sql);
		$Row = mysql_fetch_assoc($Result);
                mysql_free_result($Result);
		$RaidPublicationResultDate = $Row['raid_resultpublicationdate'];
                $RaidRegistrationEndDate = $Row['raid_registrationenddate'];
                $OldMmb = $Row['oldmmb'];

                
	 if (empty($RaidRegistrationEndDate))
         {
	  // ������ ���� ���������� ���� ��������� �����������
	     return;
         }
	 
	 if (CheckModerator($SessionId, $RaidId))
	 {
	   $Moderator = 1;
	 } else {
	   $Moderator = 0;
         }


        // ����� ������� ��� ����������� ��������������
	if ($viewmode == "Add" or $Moderator or ($TeamUser and !$TeamModeratorConfirmResult))
        {
          $AllowEdit = 1;
	  $DisabledText = '';
          $OnSubmitFunction = 'return ValidateTeamDataForm();';
        } else { 
	  $AllowEdit = 0;
	  $DisabledText = 'disabled';
          $OnSubmitFunction = 'return false;';
        }
 

// ������� javascrpit
?>

<script language = "JavaScript">

        // ������� �������� ������������ ���������� �����
	function ValidateTeamDataForm()
	{ 
	        document.TeamDataForm.action.value = "<? echo $NextActionName; ?>";
		return true;
	}
        // ����� �������� ������������ ���������� �����


        // ������� �������
	function HideTeam()
	{ 
	  document.TeamDataForm.action.value = 'HideTeam';
	  document.TeamDataForm.submit();
	}

        // ������� ������������
	function HideTeamUser(teamuserid)
	{ 
          document.TeamDataForm.HideTeamUserId.value = teamuserid;
	  document.TeamDataForm.action.value = 'HideTeamUser';
	  document.TeamDataForm.submit();
          
	}

	// ������� ������ ���������
	function Cancel()
	{ 
		document.TeamDataForm.action.value = "CancelChangeTeamData";
		document.TeamDataForm.submit();
	}
	

        // ���������� ������� ������������
	function ViewUserInfo(userid)
	{ 
	  document.TeamDataForm.UserId.value = userid;
	  document.TeamDataForm.action.value = 'UserInfo';
	  document.TeamDataForm.submit();
	}


        // ������� ���� ����� ������������
	function TeamUserOut(teamuserid, levelid)
	{ 
          document.TeamDataForm.HideTeamUserId.value = teamuserid;
          document.TeamDataForm.UserOutLevelId.value = levelid;
	  document.TeamDataForm.action.value = 'TeamUserOut';
	  document.TeamDataForm.submit();
          
	}

</script>


<?

  print('<form  name = "TeamDataForm"  action = "'.$MyPHPScript.'" method = "post" onSubmit = "'.$OnSubmitFunction.'">'."\r\n");
  print('<input type = "hidden" name = "sessionid" value = "'.$SessionId.'">'."\r\n");
  print('<input type = "hidden" name = "action" value = "">'."\r\n");
  print('<input type = "hidden" name = "view" value = "'.(($viewmode == "Add") ? 'ViewRaidTeams' : 'ViewTeamData').'">'."\r\n");
  print('<input type = "hidden" name = "TeamId" value = "'.$TeamId.'">'."\r\n");
  print('<input type = "hidden" name = "RaidId" value = "'.$RaidId.'">'."\r\n");
  print('<input type = "hidden" name = "HideTeamUserId" value = "0">'."\r\n");
  print('<input type = "hidden" name = "UserOutLevelId" value = "0">'."\r\n");
  print('<input type = "hidden" name = "UserId" value = "0">'."\r\n");
  print('<table  class = "menu" border = "0" cellpadding = "0" cellspacing = "0">'."\r\n");


        $TabIndex = 0;

         // ����� �������
	 if ($viewmode=="Add")
	 {
             // ��������� ����� �������
             // ���� ������ ��� - ��������� �������������� ������, ����� ����� �� �������
             if ($OldMmb == 1)
             {
                print('<tr><td class = "input">������� N <input type="text" name="TeamNum" size="10" 
                         value="0" tabindex = "'.(++$TabIndex).'" 
                         title = "��� ������� ��� ������� ����� �������"></td></tr>'."\r\n");
              } else {
		print('<tr><td class = "input"><b>����� �������!</b>
                            <input type="hidden" name="TeamNum" value="0"></td></tr>'."\r\n");
              } 

         } else {
              // ��� ������������ �������
 	     print('<tr><td class = "input">������� N <b>'.$TeamNum.'</b>
                            <input type="hidden" name="TeamNum" value="'.$TeamNum.'">'."\r\n");

             // ��������� ����� �� ������ ����� �������� ������ �������� ���� �������
             if ($AllowEdit == 1) 
             {
  	        print(' &nbsp; <input type = "button" style = "margin-left: 180px;" onClick = "javascript: if (confirm(\'�� �������, ��� ������ ������� �������: '.trim($TeamName).'? \')) {HideTeam();}" name="HideTeamButton" 
                                value = "������� �������"  tabindex = "'.(++$TabIndex).'">'."\r\n");
             }
	     print('</td></tr>'."\r\n");

	     print('<tr><td class = "input">����������������: '.$TeamRegisterDt.'</td></tr>'."\r\n");

	 }

  	 print('<tr><td class = "input">����� ��������� �����������: '.$RaidRegistrationEndDate.'</td></tr>'."\r\n");


        
         // ��������� 
	print('<tr><td class = "input">���������'."\r\n"); 
	print('<select name="DistanceId"  class = "leftmargin" tabindex = "'.(++$TabIndex).'" '.$DisabledText.'>'."\r\n"); 

	//echo 'RaidId '.$RaidId;

        $sql = "select distance_id, distance_name from  Distances where raid_id = ".$RaidId; 
        //echo 'sql '.$sql;
	$Result = MySqlQuery($sql);

	while ($Row = mysql_fetch_assoc($Result))
	{
	  $distanceselected = ($Row['distance_id'] == $DistanceId ? 'selected' : '');
	  print('<option value = "'.$Row['distance_id'].'" '.$distanceselected.' >'.$Row['distance_name']."\r\n");
	}
	mysql_free_result($Result);
	print('</select>'."\r\n");  
	print('</td></tr>'."\r\n");

        // �������� �������
        print('<tr><td class = "input"><input type="text" name="TeamName" size="50" value="'.$TeamName.'" 
                                        tabindex = "'.(++$TabIndex).'" '.$OnClickText.' '.$DisabledText.' 
                                        title = "�������� �������"></td></tr>'."\r\n");

        print('<tr><td class = "input">'."\r\n");

        // ������������� GPS
        print('GPS <input type="checkbox" name="TeamUseGPS" '.(($TeamUseGPS == 1) ? 'checked="checked"' : '').'
                  tabindex = "'.(++$TabIndex).'" '.$DisabledText.'
  	           title = "��������, ���� ������� ���������� ��� �������������� GPS"/> &nbsp; '."\r\n");


        // ����� ����
        print('  &nbsp;  ����� ���� <input type="text" name="TeamMapsCount" size="5" value="'.$TeamMapsCount.'" 
                                        tabindex = "'.(++$TabIndex).'" '.$OnClickText.' '.$DisabledText.' 
                                        title = "">  &nbsp;  '."\r\n");


        // ��� ��������� ��������!
        print('  &nbsp; ��� ��������� ��������! <input type="checkbox" name="TeamGreenPeace" '.(($TeamGreenPeace >= 1) ? 'checked="checked"' : '').'
                  tabindex = "'.(++$TabIndex).'" '.$DisabledText.'
  	           title = "��������, ���� ������� ���� ���������� ������������� �������������"/>'."\r\n");

        print('</td></tr>'."\r\n");

       
        // ��������� 
	print('<tr><td class = "input">'."\r\n");
        //print('<div style = "margin-top: 20px; margin-bottom: 5px;">���������:</div>'."\r\n");
                 
                 
		$sql = "select tu.teamuser_id, u.user_name, u.user_birthyear, tu.level_id, u.user_id 
		        from  TeamUsers tu
			     inner join  Users u
			     on tu.user_id = u.user_id
			where tu.teamuser_hide = 0 and team_id = ".$TeamId; 
                //echo 'sql '.$sql;
		$Result = MySqlQuery($sql);

		while ($Row = mysql_fetch_assoc($Result))
		{
		  //print('<div class= "input"><a href = "javascript:ViewUserInfo('.$Row['user_id'].');">'.$Row['user_name'].'</a> '.$Row['user_birthyear']."\r\n");
                 //    echo 'eee'. $Row['teamuser_id'].','.$Row['level_id'];

		  print('<div style = "margin-top: 5px;">'."\r\n");
                  // ������ ������� ������ ������ � ��� ������, ���� �������� ��������� ��� �������� �������
                  if ($Moderator or $TeamUser) 
		  {
			  //print('<a style = "margin-left: 20px;" href = "javascript:if (confirm(\'�� �������, ��� ������ ������� ���������: '.$Row['user_name'].' \')) {HideTeamUser('.c.');}">�������</a>'."\r\n");
			  print('<input type = "button" style = "margin-right: 15px;" 
                                  onClick = "javascript:if (confirm(\'�� �������, ��� ������ ������� ���������: '.$Row['user_name'].'? \')) { HideTeamUser('.$Row['teamuser_id'].'); }" 
                                  name = "HideTeamUserButton" tabindex = "'.(++$TabIndex).'" value = "�������">'."\r\n");


		  }

			  // ���� ������� ���� ������ ������� ��������� ����������� - ���������� ���� �����
			  if ($viewmode<>"Add" and $RaidRegistrationEndDate < date('Y-m-d'))
			  {

			    // ������ ������, ����� �������, �� ����� ����� ��������
			    print('����: <select name="UserOut'.$Row['teamuser_id'].'" style = "margin-right: 15px;"
                                    title = "����, �� ������� ����� ��������"
                                    onChange = "javascript:if (confirm(\'�� �������, ��� ������ �������� ���� ���������: '.$Row['user_name'].'? \')) { TeamUserOut('.$Row['teamuser_id'].', this.value); }" 
                                    tabindex = "'.(++$TabIndex).'" '.$DisabledText.'>'."\r\n"); 
                     

			    $sqllevels = "select level_id, level_name from  Levels where distance_id = ".$DistanceId." order by level_order"; 
			    //echo 'sql '.$sql;
			    $ResultLevels = MySqlQuery($sqllevels);

			    $userlevelselected =  ($Row['level_id'] == 0 ? 'selected' : '');
			    print('<option value = "0" '.$userlevelselected.' >-'."\r\n");

			    while ($RowLevels = mysql_fetch_assoc($ResultLevels))
			    {
			      $userlevelselected = ($RowLevels['level_id'] == $Row['level_id'] ? 'selected' : '');
			      print('<option value = "'.$RowLevels['level_id'].'" '.$userlevelselected.' >'.$RowLevels['level_name']."\r\n");
			    }
			    mysql_free_result($ResultLevels);
			    print('</select>'."\r\n");  
                         
                          }
                          // ����� �������� �� ������� �����

                 // ����� �������� �� ������ (�������������� ������ �������, �����
		  print('<a href = "javascript:ViewUserInfo('.$Row['user_id'].');">'.$Row['user_name'].'</a> '.$Row['user_birthyear']."\r\n");
		  print('</div>'."\r\n");
		}
                mysql_free_result($Result);

	print('</td></tr>'."\r\n");

       // ���� ������� ���� ������ ������� ��������� ����������� - ���������� ���� �������������
        if ($AllowEdit == 1) 
        {
  	  print('<tr><td class = "input"  style =  "padding-top: 10px;">'."\r\n");

	  if ($viewmode == "Add" and  !$Moderator)
	  {
            // ����� ������� � ������� �� ���������
	     print($UserEmail.'<input type="hidden" name="NewTeamUserEmail" size="50" value="'.$UserEmail.'" >'."\r\n");
          } else {
	     print('<input type="text" name="NewTeamUserEmail" size="50" value="Email ������ ���������"
                      tabindex = "'.(++$TabIndex).'" onClick = "javascript:this.value = \'\';"
                      title = "������� e-mail ������������, �������� �� ������ �������� � �������. ������������ ����� ��������� ��������� ���� � ������� � ���������� ����� ������� ������.">'."\r\n");
          }

	  print('</td></tr>'."\r\n"); 

       if ($viewmode<>"Add" and $RaidRegistrationEndDate < date('Y-m-d'))
       {


          // ������ ������, ����� �������, �� ����� ������� �� ����� (�� ��������� ���������, ��� ����� �� ��)
      	  print('<tr><td style = "padding-top: 10px; font-size: 80%;"><b>����������:</b></td></tr>'."\r\n");
         print('<tr><td class = "input">�� ����� �� ����: &nbsp; '."\r\n");
	    print('<select name="TeamNotOnLevelId"  style = "margin-left: 10px;margin-right: 10px;" tabindex = "'.(++$TabIndex).'" '.$DisabledText.'
                     title = "������ ���������: ��������� ����� ���� ������ �� ����� ������������ ���� ������ ��� ����� ������.">'."\r\n"); 
	    $sql = "select level_id, level_name from  Levels where distance_id = ".$DistanceId." order by level_order"; 
	    //echo 'sql '.$sql;
	    $Result = MySqlQuery($sql);

            $teamlevelselected =  ($TeamNotOnLevelId == 0 ? 'selected' : '');
	     print('<option value = "0" '.$teamlevelselected.' >-'."\r\n");

	    while ($Row = mysql_fetch_assoc($Result))
	    {
	      $teamlevelselected = ($Row['level_id'] == $TeamNotOnLevelId ? 'selected' : '');
	      print('<option value = "'.$Row['level_id'].'" '.$teamlevelselected.' >'.$Row['level_name']."\r\n");
	    }
	    mysql_free_result($Result);
	    print('</select>'."\r\n");  
          print(' &nbsp; ����� �����: '.$TeamResult.'</td></tr>'."\r\n");



	  print('<tr><td class = "input"> �������������:  &nbsp; '."\r\n");

	  // ������������� ������������ ����������� ��������
	  print(' �������
             <input type="checkbox" name="TeamConfirmResult" '.(($TeamConfirmResult == 1) ? 'checked="checked"' : '').'
                  tabindex = "'.(++$TabIndex).'" '.$DisabledText.'
  	           title = "����������� ����� ����� �����������. ��������, ���� ������� ��������� ��� ������ � �������� � ����"/>  &nbsp; '."\r\n");

	  if ($Moderator)
	  {
	    $ModeratorConfirmResultDisabledText = '';
	  } else {
	    $ModeratorConfirmResultDisabledText = 'disabled';
          }
	  // ������������� ������������ ����������� �����������
	  print(' ����������
		<input type="checkbox" name="ModeratorConfirmResult" '.(($ModeratorConfirmResult == 1) ? 'checked="checked"' : '').'
		    tabindex = "'.(++$TabIndex).'" '.$ModeratorConfirmResultDisabledText.'
		    title = "����������� ����������� ����� �������� �����������."/>'."\r\n");
	 

          print('</td></tr>'."\r\n");



       }



          print('<tr><td class = "input"  style =  "padding-top: 20px;">'."\r\n");
	  print('<input type="button" onClick = "javascript: if (ValidateTeamDataForm()) submit();"
                   name="RegisterButton" value="'.$SaveButtonText.'" tabindex = "'.(++$TabIndex).'">'."\r\n");
          print('<select name="CaseView" onChange = "javascript:document.TeamDataForm.view.value = document.TeamDataForm.CaseView.value;"  
                    class = "leftmargin" tabindex = "'.(++$TabIndex).'">'."\r\n"); 
	  print('<option value = "ViewTeamData"  '.(($viewmode <> "Add") ? 'selected' : '').'>� �������� �� ���� ��������'."\r\n"); 
	  print('<option value = "ViewRaidTeams"  '.(($viewmode == "Add") ? 'selected' : '').'>� ������� � ������ ������'."\r\n"); 
	  print('</select>'."\r\n"); 
          print('<input type="button" onClick = "javascript: Cancel();"  name="CancelButton" value="������"
                     tabindex = "'.(++$TabIndex).'">'."\r\n"); 

	

           print('</td></tr>'."\r\n"); 

        }

        print('</table></form>'."\r\n"); 

?>


