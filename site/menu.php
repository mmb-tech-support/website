

<script language = "JavaScript">

        // ������� �������� ������������ ���������� �����
	function ValidateUserLoginForm()
	{ 
		if (document.UserLoginForm.Login.value == '') 
		{
			alert('�� ������ e-mail.');           
			return false;
		} 

		if (!CheckEmail(document.UserLoginForm.Login.value)) 
		{
			alert('E-mail �� �������� �������� �������.');           
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
		print('<tr><td class = "input"><input type="text" name="Login"
                       size="21" value="E-mail" tabindex = "101"
                       onclick = "javascript: if (trimBoth(this.value) == \'E-mail\') {this.value=\'\';}"
		       onblur = "javascript: if (trimBoth(this.value) == \'\') {this.value=\'E-mail\';}"
                       ></td></tr>'."\r\n"); 
		print('<tr><td class = "input"><input type="password" name="Password"  style = "width:106px;" size="10" value="" tabindex = "102">
                            <input type="submit" name="RegisterButton" value="����" tabindex = "103"  style = "margin-left:5px; width:70px;"></td></tr>'."\r\n"); 
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


	function RecalcRaidResults()
	{ 
		document.AdminServiceForm.action.value = "RecalcRaidResults";
                document.AdminServiceForm.RaidId.value = document.FindTeamForm.RaidId.value; 
		document.AdminServiceForm.submit();
	}

        function ClearTables()
	{ 
		document.AdminServiceForm.action.value = "ClearTables";
		document.AdminServiceForm.submit();
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
	print('<select name="RaidId"  style = "width:141px;" class = "leftmargin" tabindex = "201"  title = "������ ����-�������">'."\r\n"); 

                $Result = MySqlQuery('select raid_id, raid_name from  Raids where raid_registrationenddate is not null order by 1 desc');

		while ($Row = mysql_fetch_assoc($Result))
		{
		  print('<option value = "'.$Row['raid_id'].'"  '.(($Row['raid_id'] == $_POST['RaidId']) ? 'selected' : '').' >'.$Row['raid_name']."\r\n");
		}

                mysql_free_result($Result);
	print('</select>'."\r\n");  
	print('</td></tr>'."\r\n");
	if ($UserId > 0)
	{
		print('<tr><td><a href = "javascript:NewTeam();" title = "������� � ����� ����������� ����� ������� �� ��������� ���� ���">����� �������</a></td></tr>'."\r\n"); 
	}
	print('<tr><td><a href = "javascript:RaidTeams();" title = "������ ������ �� �������� ������� ��� ���������� ���� ���">�������</a></td></tr>'."\r\n"); 
        //print('<tr><td class = "input">�����:</td></tr>'."\r\n"); 
	print('<tr><td style = "padding-top:15px;"><input  type="text" name="TeamNum" size="12" value="����� �������" tabindex = "206"  title = "�������� ������� � ��������� ������� ��� ���������� ���� ���"
                       onclick = "javascript: if (trimBoth(this.value) == \'����� �������\') {this.value=\'\';}"
		       onblur = "javascript: if (trimBoth(this.value) == \'\') {this.value=\'����� �������\';}"
                 > 
	       <input type="submit"  name="FindButton" value="�����"   class = "leftmargin" tabindex = "207"></td></tr>'."\r\n"); 
	print('</table>'."\r\n");
	print('</form>'."\r\n");
	// ����� ���������
	print('<form  name = "FindUserForm"  action = "'.$MyPHPScript.'" method = "post" onSubmit = "return ValidateFindUserForm();">'."\r\n");
	if ($UserId > 0)
	{
                print('<input type = "hidden" name = "sessionid" value = "'.$SessionId.'">'."\r\n"); 
	}                
	print('<input type = "hidden" name = "action" value = "FindUser">'."\r\n"); 
	print('<input type = "hidden" name = "view" value = "'.$view.'">'."\r\n");
	print('<table  class = "menu" border = "0" cellpadding = "0" cellspacing = "0">'."\r\n");
	//print('<tr><td class = "input">����� ������������</td></tr>'."\r\n"); 
	print('<tr><td class = "input"><input  type="text" name="FindString" size="12" value="����� ���" tabindex = "301" 
	       title = "������ ������������� �� ��������, ��� ��� �������� �������� �����. ��� ������ ���� ��������: ���-��� (����� � ���-���-���)."
                       onclick = "javascript: if (trimBoth(this.value) == \'����� ���\') {this.value=\'\';}"
		       onblur = "javascript: if (trimBoth(this.value) == \'\') {this.value=\'����� ���\';}"
		>
	       <input type="submit"  name="FindButton" value="�����"   class = "leftmargin" tabindex = "302"></td></tr>'."\r\n"); 
	print('</table>'."\r\n");
	print('</form>'."\r\n");
	print('</br>'."\r\n");

       // ����� �������� ��������������
        if (CheckAdmin($SessionId) == 1) 
	{
	  print('<form  name = "AdminServiceForm"  action = "'.$MyPHPScript.'" method = "post">'."\r\n");
	  print('<input type = "hidden" name = "sessionid" value = "'.$SessionId.'">'."\r\n"); 
	  print('<input type = "hidden" name = "action" value = "">'."\r\n"); 
	  print('<input type = "hidden" name = "view" value = "'.$view.'">'."\r\n");
	  print('<input type = "hidden" name = "RaidId" value = "0">'."\r\n");


          //  ���������� ������ "�������� �����������" 
          print('<input type="button" style = "width:185px;" name="RecalcRaidResults" value="����������� ����������" 
                          onclick = "javascript: RecalcRaidResults();"
                          tabindex = "205">'."\r\n"); 
	  print('</br>'."\r\n");

          //  ���������� ������ "�������� �������" 
	  print('<input type="button" style = "width:185px; margin-top:10px;" name="ClearTables" value="�������� �������" 
                          onclick = "javascript: ClearTables();"
                          tabindex = "205">'."\r\n"); 

	  print('</form>'."\r\n");
	  print('</br>'."\r\n");

	} 
		
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
	print('<table  class = "menu" border = "0" cellpadding = "0" cellspacing = "0">'."\r\n");
	print('<tr><td><a style = "font-family: Times New Roman, Serif; font-size: 100%;" href="javascript: ShowEmail();" title = "����� ���������� �������, ��� � �������� �����">���@����������.��</a> <small>(en)</small></td></tr>'."\r\n");
        print('<tr><td><a href="https://github.com/realtim/mmb/wiki/%D0%A1%D0%B2%D0%B5%D0%B4%D0%B5%D0%BD%D0%B8%D1%8F-%D0%BE-%D1%81%D0%B5%D1%80%D0%B2%D0%B8%D1%81%D0%B5-%D0%9C%D0%9C%D0%91"  target = "_blank">� �������</a>, 
                       <a href="https://github.com/realtim/mmb/wiki/%D0%90%D0%B2%D1%82%D0%BE%D1%80%D1%8B" target = "_blank">������</a>
               </td></tr>'."\r\n"); 
	print('</table>'."\r\n");

	
?>
