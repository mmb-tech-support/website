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
             // ����� ������������ 
             $UserId = 0;
	     // ���� �� ����� ����������� �������������� ������������ ��� ��������������� ������������

	     // ���� ��������� ����� ������ ���������� �� ����� ����������������
	     if ($viewsubmode == "ReturnAfterError") 
	     {

              ReverseClearArrays();

	      $UserEmail = $_POST['UserEmail'];
	      $UserName = str_replace( '"', '&quot;', $_POST['UserName']);
	      $UserBirthYear = (int)$_POST['UserBirthYear'];
	      $UserProhibitAdd = ($_POST['UserProhibitAdd'] == 'on' ? 1 : 0);

             } else {

	      $UserEmail = 'E-mail';
	      $UserName = '������� ���';
	      $UserBirthYear = '��� ��������';
	      $UserProhibitAdd = 0;

             }
            
            
             // ������ ��������� ���� ������ ������������ 
	     $AllowEdit = 1;
	     // ���������� ��������� ��������
	     $NextActionName = 'AddUser';
             // �������� �� ��������� ���� �� �����
	     $SaveButtonText = '����������������';


         } else {

           // �������� �������������
                //echo $viewsubmode;

		$UserId = $_POST['UserId']; 

		if ($UserId <= 0)
		{
		// ������ ���� ���������� �����������, �������� �������
		     return;
		}
           
		$sql = "select user_email, user_name, user_birthyear, user_prohibitadd from  Users where user_id = ".$UserId;
		$rs = MySqlQuery($sql);  
                $row = mysql_fetch_assoc($rs);
                mysql_free_result($rs);

	        // ���� ��������� ����� ������ ���������� �� ����� ����������������
	        if ($viewsubmode == "ReturnAfterError") 
		{

                  ReverseClearArrays();

		  $UserEmail = $_POST['UserEmail'];
		  $UserName = str_replace( '"', '&quot;', $_POST['UserName']);
		  $UserBirthYear = (int)$_POST['UserBirthYear'];
		  $UserProhibitAdd = ($_POST['UserProhibitAdd'] == 'on' ? 1 : 0);

                } else {

		  $UserEmail = $row['user_email'];  
		  $UserName = str_replace( '"', '&quot;', $row['user_name']); 
		  $UserBirthYear = (int)$row['user_birthyear'];  
		  $UserProhibitAdd = $row['user_prohibitadd'];  

                }

	        $NextActionName = 'UserChangeData';
		$AllowEdit = 0;
		$SaveButtonText = '��������� ���������';
		

                if ($UserId == $NowUserId or CheckAdmin($SessionId) == 1) 
		{
		  $AllowEdit = 1;
		}

	 }
         // ����� �������� �������� � �������������

	 
         if ($AllowEdit == 0) 
	 {
	    $OnSubmitFunction = 'return false;';
	    $DisabledText = 'disabled';
	 } else {
	    $OnSubmitFunction = 'return ValidateUserDataForm();';
	    $DisabledText = '';
	 }



// ������� javascrpit
?>

<!-- ������� javascrpit -->
<script language = "JavaScript">

        // ������� �������� ������������ ���������� �����
	function ValidateUserDataForm()
	{ 
		if (document.UserDataForm.UserName.value == '') 
		{
			alert('�� ������� ���.');           
			return false;
		} 

		if (document.UserDataForm.UserEmail.value == '') 
		{
			alert('�� ������ e-mail.');           
			return false;
		} 


		if (document.UserDataForm.UserBirthYear.value == '') 
		{
			alert('�� ������ ���.');           
			return false;
		} 
		
		if (!CheckEmail(document.UserDataForm.UserEmail.value)) 
		{
			alert('E-mail �� �������� �������� �������.');           
			return false;
		} 
		
		document.UserDataForm.action.value = "<? echo $NextActionName; ?>";
		return true;
	}
        // ����� �������� ������������ ���������� �����

	
	// ������� �������� ������
	function NewPassword()
	{ 
		document.UserDataForm.action.value = "SendEmailWithNewPassword";
		document.UserDataForm.submit();
	}
	// 

        // ������� ������ ���������
	function Cancel()
	{ 
		document.UserDataForm.action.value = "CancelChangeUserData";
		document.UserDataForm.submit();
	}
	// 

	// ������� ��������� ������ � �������
	function ViewTeamInfo(teamid, raidid)
	{ 
		document.UserTeamsForm.TeamId.value = teamid;
		document.UserTeamsForm.RaidId.value = raidid;
		document.UserTeamsForm.action.value = "TeamInfo";
		document.UserTeamsForm.submit();
	}
/*	

        ����� ��� ������� � ����
	// ������� �������� e-mail
	function CheckEmail(email) 
	{
		var template = /^[A-Za-z0-9](([_\.\-]?[a-zA-Z0-9]+)*)@([A-Za-z0-9]+)(([\.\-]?[a-zA-Z0-9]+)*)\.([A-Za-z])+$/;
//		email = drop_spaces(email); //������� drop_spaces() ��. ����
		if (template.test(email)) 
		{
		        return true;
		}
		return false; 
	}


	function trimLeft(str) {
	  return str.replace(/^\s+/, '');
	}

	function trimRight(str) {
	  return str.replace(/\s+$/, '');
	}

	function trimBoth(str) {
	  return trimRight(trimLeft(str));
	}

	function trimSpaces(str) {
	  return str.replace(/\s{2,}/g, ' ');
	}
*/	
</script>
<!-- ����� ������ javascrpit -->


<?

         // ������� ����� � ������� ������������
	 
	 print('<form  name = "UserDataForm"  action = "'.$MyPHPScript.'" method = "post" onSubmit = "'.$OnSubmitFunction.'">'."\r\n");
         print('<input type = "hidden" name = "sessionid" value = "'.$SessionId.'">'."\r\n");
         print('<input type = "hidden" name = "UserId" value = "'.$UserId.'">'."\r\n");
         print('<input type = "hidden" name = "action" value = "">'."\r\n");

	 if ($AllowEdit == 1) 
	 {
          print('<div style = "margin-top: 10px; margin-bottom: 10px; font-size: 80%; text-align: left">����������� ��������� ���������� ��� ��������� ������� ����:</div>'."\r\n");
	 } 

         print('<table  class = "menu" border = "0" cellpadding = "0" cellspacing = "0" width = "300">'."\r\n");

         // ���� �� ��������� ������ - �� ���������� ����� �����
         if ($AllowEdit == 1) 
	 {
	  print('<tr><td class = "input"><input type="text" name="UserEmail" size="50" value="'.$UserEmail.'" tabindex = "1"  '.$DisabledText.'
                 '.($viewmode <> 'Add' ? '' : 'onclick = "javascript: if (trimBoth(this.value) == \''.$UserEmail.'\') {this.value=\'\';}"').'
                 '.($viewmode <> 'Add' ? '' : 'onblur = "javascript: if (trimBoth(this.value) == \'\') {this.value=\''.$UserEmail.'\';}"').'
	         title = "E-mail - ������������ ��� ������������� ������������"></td></tr>'."\r\n");
         }

         print('<tr><td class = "input"><input type="text" name="UserName" size="50" value="'.$UserName.'" tabindex = "2"   '.$DisabledText.'
                 '.($viewmode <> 'Add' ? '' : 'onclick = "javascript: if (trimBoth(this.value) == \''.$UserName.'\') {this.value=\'\';}"').'
                 '.($viewmode <> 'Add' ? '' : 'onblur = "javascript: if (trimBoth(this.value) == \'\') {this.value=\''.$UserName.'\';}"').'
                title = "��� - ��� ����� ��������� ���������� � ������������ � ���������� � �� �����"></td></tr>'."\r\n");

         print('<tr><td class = "input"><input type="text" name="UserBirthYear" maxlength = "4" size="11" value="'.$UserBirthYear.'" tabindex = "3" '.$DisabledText.'
                 '.($viewmode <> 'Add' ? '' : 'onclick = "javascript: if (trimBoth(this.value) == \''.$UserBirthYear.'\') {this.value=\'\';}"').'
                 '.($viewmode <> 'Add' ? '' : 'onblur = "javascript: if (trimBoth(this.value) == \'\') {this.value=\''.$UserBirthYear.'\';}"').'
	        title = "��� ��������"></td></tr>'."\r\n");

         print('<tr><td class = "input"><input type="checkbox" name="UserProhibitAdd" '.(($UserProhibitAdd == 1) ? 'checked="checked"' : '').' tabindex = "4" '.$DisabledText.'
	        title = "���� ���� ����� e-mail ������ ������������ �� ������ ������� ��� ���������� ����� ������� - ������ �� ���� ��� ��������� ���" /> ������ �������� � ������� ������ �������������</td></tr>'."\r\n");


         // ���� �� ��������� ����� - �� ���������� ������
	 if ($AllowEdit == 1) 
	 {
	  print('<tr><td class = "input"  style =  "padding-top: 10px;">'."\r\n");
	  print('<input type="button" onClick = "javascript: if (ValidateUserDataForm()) submit();"  name="RegisterButton" value="'.$SaveButtonText.'" tabindex = "5">'."\r\n");

           // ���� ����������� ������ ������������ - �� ����� ������ "������" � "������� ������"
          if ($viewmode <> 'Add')
	  {
            print('<input type="button" onClick = "javascript: Cancel();"  name="CancelButton" value="������" tabindex = "6" title = "������ ��������� ������ �� ����">'."\r\n");		

	    // 15,01,2012 ����� ��������
	    // �.�. �������� ������ � ����� ��������� � �������������� �������� ������ � ����� ������
	    //if ($UserId > 0 and $UserId == $NowUserId)
	    //{
             print('<input type="button" onClick = "javascript: if (confirm(\'�� �������, ��� ������ ������� ������ �� ����� '.trim($UserEmail).' ��� ����� ������: '.trim($UserName).'? \')) { NewPassword(); }"  name="ChangePasswordButton" value="������� ������" tabindex = "7">'."\r\n");		
	    //}
          }

          print('</td></tr>'."\r\n"); 
         }
         // ����� ������ ������

	 print('</table></form>'."\r\n"); 
	 // ����� ������ ����� � ������� ������������

	 

          // ������� ������ ������, � ������� ���������� ������ ������������ 
          print('<div style = "margin-top: 20px; margin-bottom: 10px; text-align: left">���������� � ��������:</div>'."\r\n");
          print('<form  name = "UserTeamsForm"  action = "'.$MyPHPScript.'" method = "post">'."\r\n");
          print('<input type = "hidden" name = "action" value = "">'."\r\n");
	  print('<input type = "hidden" name = "RaidId" value = "0">'."\n");
	  print('<input type = "hidden" name = "TeamId" value = "0">'."\n");
 	  print('<input type = "hidden" name = "sessionid" value = "'.$SessionId.'">'."\n");
	  
		
                 
		$sql = "select tu.teamuser_id, t.team_name, t.team_id, d.distance_name, r.raid_name, t.team_num, r.raid_id 
		        from  TeamUsers tu
			     inner join  Teams t
			     on tu.team_id = t.team_id
			     inner join  Distances d
			     on t.distance_id = d.distance_id
			     inner join  Raids r
			     on d.raid_id = r.raid_id
			where tu.teamuser_hide = 0 and user_id = ".$UserId."
			order by r.raid_id desc "; 
                //echo 'sql '.$sql;
		$Result = MySqlQuery($sql);

		while ($Row = mysql_fetch_assoc($Result))
		{
		  print('<div align = "left" style = "padding-top: 5px;"><a href = "javascript:ViewTeamInfo('.$Row['team_id'].','.$Row['raid_id'].');"  title = "������� � �������� �������">'.$Row['team_name'].'</a> 
		         (N '.$Row['team_num'].', ���������: '.$Row['distance_name'].', ���: '.$Row['raid_name'].')</div>'."\r\n");
		}

                mysql_free_result($Result);
	        print('</form>'."\r\n");


?>



