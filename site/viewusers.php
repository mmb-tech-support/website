

<script language = "JavaScript">

        
	// ������� ��������� ������ � �������
	function ViewUserInfo(userid)
	{ 
		document.UsersForm.UserId.value = userid;
		document.UsersForm.action.value = "UserInfo";
		document.UsersForm.submit();
	}
	
	

	
</script>

<?php


		if (trim($FindString) == '' or trim($FindString) == '����� ���')
                {
 		  return;
                }


	
	//$FindString = trim($_POST['FindString']); 

         if (empty($SessionId))
	 {
		$SessionId =  $_POST['sessionid'];
	 } 

                // ������� ������ �������������, ������� �������
                print('<div style = "margin-top: 10px; margin-bottom: 10px; text-align: left">������������, ��� ��� �������� "'.trim($FindString).'":</div>'."\r\n");
           	print('<form  name = "UsersForm"  action = "'.$MyPHPScript.'" method = "post">'."\r\n");
                print('<input type = "hidden" name = "action" value = "">'."\r\n");
	        print('<input type = "hidden" name = "UserId" value = "0">'."\n");
		print('<input type = "hidden" name = "sessionid" value = "'.$SessionId.'">'."\n");
		
		
                 
		$sql = "select u.user_id, u.user_name 
		        from  Users u
			where ltrim(COALESCE(u.user_password, '')) <> '' 
                              and u.user_hide = 0
                              and user_name like '%".trim($sqlFindString)."%'
			order by user_name "; 
                
		//echo 'sql '.$sql;
		
		$Result = MySqlQuery($sql);

                $RowsCount = mysql_num_rows($Result);
	
		
		if ($RowsCount > 0)
		{
		
			while ($Row = mysql_fetch_assoc($Result))
			{
			  print('<div align = "left" style = "padding-top: 5px;"><a href = "javascript:ViewUserInfo('.$Row['user_id'].');">'.$Row['user_name'].'</a></div>'."\r\n");
			}

		} else {

			  print('<div class= "input" align = "left">�� �������</div>'."\r\n");
		}
	        print('</form>'."\r\n");
                mysql_free_result($Result);



?>
<!--		
		</td></tr>
		</table>
-->
		

		</br>



