

<script language = "JavaScript">

        // ������� �������� ������������ ���������� �����
	function ValidateUserLoginForm()
	{ 
		if (document.UserLoginForm.Login.value == '') 
		{
			alert('�� ������ e-mail.');           
			return false;
		} 

		if (document.UserLoginForm.Password.value == '') 
		{
			alert('�� ������ ������.');           
			return false;
		} 

		return true;
	}
        // ����� �������� ������������ ���������� �����

	
	function UserLogout()
	{ 
		document.UserLoginForm.action.value = "UserLogout";
		document.UserLoginForm.submit();
	}

	function ViewUserInfo(userid)
	{ 
		document.UserLoginForm.action.value = "UserInfo";
		document.UserLoginForm.UserId.value = userid;
		document.UserLoginForm.submit();
	}

        // ���������� �������� ���  ����������� ����������� � ����������������.
	// �� ������, ��� ��������� ���
	function NewUser()
	{ 
		document.UserLoginForm.action.value = "ViewNewUserForm";
		document.UserLoginForm.submit();
	}


	// ������� �������� ������
	function RestorePassword()
	{ 
		document.UserLoginForm.action.value = "RestorePasswordRequest";
		document.UserLoginForm.submit();
	}


	
	

</script>

<?php


	 $UserId = 0;

         if (empty($SessionId))
	 {
		$SessionId =  $_POST['sessionid'];
	 } 
	 
	 $UserId = GetSession($SessionId);
	 
//        echo "������������: ".$UserId.", ������: ".$SessionId;
        // ����� �������� ����
	if ($UserId <= 0)
	{
		print('<form  name = "UserLoginForm"  action = "'.$MyPHPScript.'" method = "post" onSubmit = "return ValidateUserLoginForm();">'."\r\n");
                print('<input type = "hidden" name = "action" value = "UserLogin">'."\r\n"); 
		print('<input type = "hidden" name = "view" value = "'.$view.'">'."\r\n");
		print('<table  class = "menu" border = "0" cellpadding = "0" cellspacing = "0">'."\r\n");
		print('<tr><td class = "input"><input type="text" name="Login" size="20" value="E-mail" tabindex = "101" onClick = "javascript:this.value = \'\';"></td></tr>'."\r\n"); 
		print('<tr><td class = "input"><input type="password" name="Password" size="10" value="" tabindex = "102"><input type="submit" name="RegisterButton" value="����" tabindex = "103" class = "leftmargin" ></td></tr>'."\r\n"); 
		print('<tr><td><a href = "javascript:RestorePassword();"  title = "����� ������ ������ � ������ ������ �� ��������� ���� e-mail">������ ������?</a></td></tr>'."\r\n"); 
		print('<tr><td><a href = "javascript:NewUser();"  title = "������� � ����� ����������� ������ ������������">����� ������������</a></td></tr>'."\r\n"); 
		print('</table>'."\r\n");
		print('</form>'."\r\n");
	} else {

                $Result = MySqlQuery('select user_name from  Users where user_id = '.$UserId);
		$Row = mysql_fetch_assoc($Result);
		$UserName = $Row['user_name'];
                print('<form  name = "UserLoginForm"  action = "'.$MyPHPScript.'" method = "post">'."\r\n");
                print('<input type = "hidden" name = "sessionid" value = "'.$SessionId.'">'."\r\n"); 
                print('<input type = "hidden" name = "action" value = "">'."\r\n"); 
                print('<input type = "hidden" name = "UserId" value = "">'."\r\n"); 
		print('<input type = "hidden" name = "view" value = "'.$view.'">'."\r\n");
		print('<table  class = "menu" border = "0" cellpadding = "0" cellspacing = "0">'."\r\n");
		print('<tr><td><a href = "javascript:ViewUserInfo('.$UserId.');"  title = "������� � ����� �������� ������������">'.$UserName.'</a></tr>'."\r\n"); 
		print('<tr><td><a href = "javascript:UserLogout();" style = "font-size: 80%;">�����</a></td></tr>'."\r\n"); 
		print('</table>'."\r\n");
		print('</form>'."\n");
	}
		print('</br>'."\n");


?>


<script language = "JavaScript">


	        // ������� �������� ������������ ���������� �����
	function ValidateFindTeamForm()
	{ 
		if (document.FindTeamForm.TeamNum.value == '') 
		{
			alert('�� ������ ����� �������.');           
			return false;
		} 

		if (document.FindTeamForm.RaidId.value <= 0) 
		{
			alert('�� ������ ���.');           
			return false;
		} 

		return true;
	}

	function NewTeam()
	{ 
		document.FindTeamForm.action.value = "RegisterNewTeam";
		document.FindTeamForm.submit();
	}


	function RaidTeams()
	{ 
		document.FindTeamForm.action.value = "ViewRaidTeams";
		document.FindTeamForm.submit();
	}


        function ShowEmail()
	{
         var begstr = "<? echo substr(trim($_SERVER['SERVER_NAME']), 0, 4); ?>";

	  begstr = begstr.replace("\.","\@");
	  begstr = begstr + "progressor.ru";
	  alert(begstr);
	}

</script>

<?
	
	// ������� ���� ��� ������ ������� 
	print('<form  name = "FindTeamForm"  action = "'.$MyPHPScript.'" method = "post" onSubmit = "return ValidateFindTeamForm();">'."\r\n");

	if ($UserId > 0)
	{
                print('<input type = "hidden" name = "sessionid" value = "'.$SessionId.'">'."\r\n"); 
	}                
	print('<input type = "hidden" name = "action" value = "FindTeam">'."\r\n"); 
	print('<input type = "hidden" name = "view" value = "'.$view.'">'."\r\n");
	print('<table  class = "menu" border = "0" cellpadding = "0" cellspacing = "0">'."\r\n");
	print('<tr><td class = "input">���'."\r\n"); 
	print('<select name="RaidId"  style = "width:130px;" class = "leftmargin" tabindex = "201"  title = "������ ����-�������">'."\r\n"); 

                $Result = MySqlQuery('select raid_id, raid_name from  Raids where raid_registrationenddate is not null order by 1 desc');

		while ($Row = mysql_fetch_assoc($Result))
		{
		  print('<option value = "'.$Row['raid_id'].'"  '.(($Row['raid_id'] == $_POST['RaidId']) ? 'selected' : '').' >'.$Row['raid_name']."\r\n");
		}

                mysql_free_result($Result);
	print('</select>'."\r\n");  
	print('</td></tr>'."\r\n");
	print('<tr><td class = "input">N <input  type="text" name="TeamNum" size="10" value="" tabindex = "202" class = "leftmargin" title = "�������� ������� � ��������� ������� ��� ���������� ���� ���"> 
	       <input type="submit"  name="FindButton" value="�����"   class = "leftmargin" tabindex = "203"></td></tr>'."\r\n"); 
	if ($UserId > 0)
	{
		print('<tr><td><a href = "javascript:NewTeam();" title = "������� � ����� ����������� ����� ������� �� ��������� ���� ���">����� �������</a></td></tr>'."\r\n"); 
	}
	print('<tr><td><a href = "javascript:RaidTeams();" title = "������ ������ �� �������� ������� ��� ���������� ���� ���">�������</a></td></tr>'."\r\n"); 
	print('</table>'."\r\n");
	print('</form>'."\r\n");

	print('</br>'."\r\n");


	// ����� ���������
	print('<form  name = "FindUserForm"  action = "'.$MyPHPScript.'" method = "post" onSubmit = "return ValidateFindUserForm();">'."\r\n");
	if ($UserId > 0)
	{
                print('<input type = "hidden" name = "sessionid" value = "'.$SessionId.'">'."\r\n"); 
	}                
	print('<input type = "hidden" name = "action" value = "FindUser">'."\r\n"); 
	print('<input type = "hidden" name = "view" value = "'.$view.'">'."\r\n");
	print('<table  class = "menu" border = "0" cellpadding = "0" cellspacing = "0">'."\r\n");
	print('<tr><td class = "input">����� ������������</td></tr>'."\r\n"); 
	print('<tr><td class = "input"><input  type="text" name="FindString" size="12" value="" tabindex = "301" 
	       title = "������ ������������� �� ��������, ��� ��� �������� �������� �����. ���� �� ��������� ���� - ����� ������ ��� ������������.">
	       <input type="submit"  name="FindButton" value="�����"   class = "leftmargin" tabindex = "302"></td></tr>'."\r\n"); 
	print('</table>'."\r\n");
	print('</form>'."\r\n");

	print('</br>'."\r\n");
	
	// ������� �����
	print('<table  class = "menu" border = "0" cellpadding = "0" cellspacing = "0">'."\r\n");
	print('<tr><td><a href="http://www.livejournal.com/community/_mmb_" target = "_blank">��</a></td></tr>'."\r\n"); 
	print('<tr><td><a href="http://slazav.mccme.ru/maps/" target = "_blank">�����</a></td></tr>'."\r\n"); 
	print('<tr><td><a href="http://mmb.progressor.ru/vp.html" target = "_blank">�����������</a></td></tr>'."\r\n"); 
	print('<tr><td><a href="http://mmb.progressor.ru/icons.html" target = "_blank">������</a></td></tr>'."\r\n"); 
	print('<tr><td> <a href="http://slazav.mccme.ru/mmb/ludir.htm" target = "_blank">�������</a></td></tr>'."\r\n"); 
	print('<tr><td><a href="http://slazav.mccme.ru/mmb/ludi.htm" target = "_blank">���������</a></td></tr>'."\r\n"); 
	print('</table>'."\r\n");

	print('</br>'."\r\n");

        // �����
	print('<a style = "font-family: Times New Roman, Serif; font-size: 100%;" href="javascript: ShowEmail();" title = "����� ���������� �������, ��� � �������� �����">���@����������.��</a> <small>(en)</small>'."\r\n");

	
?>
