<?php
   // ���������� ��������, ��������� � �������������   

  //echo $action;
   
   if ($action == "") 
   {
     // �������� �� �������
       $view = "MainPage";
       // $statustext = "���������: ".$employeename.", ��������� �����: ".$tabnum ;

   } elseif ($action == "UserLogin")  {
    // ��������� �����������
         
        // ��������� �������� ������ 
	if ($_POST['Login'] == "") 
	{
           $statustext = "�� ������ e-mail.";
           $alert = 1; 
           return;

        } elseif ($_POST['Password']== "") {

           $statustext = "�� ������ ������.";
           $alert = 1; 
           return;
        } 
         // ����� ��������� �������� ������� ������

        $Sql = "select user_id, user_name from  Users where trim(user_email) = trim('".$_POST['Login']."') and user_password = '".md5(trim($_POST['Password']))."'";
		
	//echo $Sql;
		
	$Result = MySqlQuery($Sql);  
	$Row = mysql_fetch_assoc($Result);
	$UserId = $Row['user_id'];
		
	if ($UserId <= 0) 
	{
		$statustext = "�������� email ��� ������.";
		  //.$login." �� ������!";
		$password = "";
		mysql_close($Connection);
		$alert = 1; 
		return;  
	} 
		//����� �������� ������������ � ������

	$SessionId = StartSession($UserId);
	$view = "MainPage";
	//$statustext = "ua ������������: ".$UserId.", ������: ".$SessionId;
		

   } elseif ($action == "UserInfo")  {
    // �������� ���������� ������� ��� ������� ������������  � ����� ������� ���������
   
	$view = "ViewUserData";
		

   } elseif ($action == "ViewNewUserForm")  {
    // �������� ���������� ������� ����� ������������

           $view = "ViewUserData";
	   $viewmode = "Add";	

   } elseif ($action == "UserChangeData" or $action == "AddUser")  {
     // �������� ���������� ���� ��� ����������� ������ ������������ ���� ��� ������ ������ �������

   	   $view = "ViewUserData";
           

	   $SessionId =  $_POST['sessionid'];
           $pUserEmail = $_POST['UserEmail'];
           $pUserName = $_POST['UserName'];
           $pUserBirthYear = $_POST['UserBirthYear'];
           $pUserProhibitAdd = ($_POST['UserProhibitAdd'] == 'on' ? 1 : 0);
           $pUserId = $_POST['UserId']; 

   
	   if ($action == 'AddUser')
	   {
             // ����� ������������ 
             $pUserId = 0;
	     $viewmode = "Add";
           } else {
	     $viewmode = "";
	   }
 
           if (trim($pUserEmail) == '')
	   {
		$statustext = "�� ������ e-mail.";
	        $alert = 1;
                $viewsubmode = "ReturnAfterError"; 
		return; 
	   }

           if (trim($pUserName) == '')
	   {
		$statustext = "�� ������� ���.";
	        $alert = 1; 
                $viewsubmode = "ReturnAfterError"; 
		return; 
	   }

           if ($pUserBirthYear < 1930 or $pUserBirthYear > date("Y"))
	   {
		$statustext = "��� �� ������ ��� ������ ������������.";
	        $alert = 1; 
                $viewsubmode = "ReturnAfterError"; 
		return; 
	   }


           $sql = "select count(*) as resultcount from  Users where trim(user_email) = '".$pUserEmail."' and user_id <> ".$pUserId;
      //     echo $sql;
	   $rs = MySqlQuery($sql);  
	   $Row = mysql_fetch_assoc($rs);
	   if ($Row['resultcount'] > 0)
	   {
   		$statustext = "��� ���� ������������ � ����� email.";
	        $alert = 1; 
                $viewsubmode = "ReturnAfterError"; 
                return; 
	   }


           $sql = "select count(*) as resultcount from  Users where  trim(user_name) = '".$pUserName."' and user_birthyear = ".$pUserBirthYear." and user_id <> ".$pUserId;
           //echo $sql;
	   $rs = MySqlQuery($sql);  
	   $Row = mysql_fetch_assoc($rs);
	   if ($Row['resultcount'] > 0)
	   {
   		$statustext = "��� ���� ������������ � ����� ������ � ����� ��������.";
	        $alert = 1; 
                $viewsubmode = "ReturnAfterError"; 
                return; 
	   }



	   if ($action == 'AddUser')
	   {
             // ����� ������������ 

                 // ��� ����� ������ �������� ����� ������ �������������� ���������:
		 // �� ������������ ����� ������, � �������� �� ����� ������
		 // �����������, ������, ��� �������������� ���� (��������, ����� �������� �������), ����� ������� ���������� ������

                 // ������ �����
                  //$NewPassword = GeneratePassword(6);

                //  echo         $NewPassword;           

                  // ������ ������� �� �����, �������� �� ����� � ����� ��� ��� � ����


		 $ChangePasswordSessionId = uniqid();

                 // ���������� ������ ������������
                 // ������ ������, ������ ��� ����� ������ � ����� �������� �������

		 $sql = "insert into  Users (user_email, user_name, user_birthyear, user_password, user_registerdt,
		                             user_sessionfornewpassword, user_sendnewpasswordrequestdt, user_prohibitadd)
		                     values ('".$pUserEmail."', '".$pUserName."', ".$pUserBirthYear.", '', now(),
				             '".$ChangePasswordSessionId."', now(), '.$pUserProhibitAdd.')";
//                 echo $sql;  
                 // ��� insert ������ ��������� �������� id - ��� ����������� �  MySqlQuery
		 $UserId = MySqlQuery($sql);  
	
//	         echo $UserId; 
//                 $UserId = mysql_insert_id($Connection);
		 if ($UserId <= 0)
		 {
                        $statustext = '������ ������ ������ ������������.';
			$alert = 1;
			$viewsubmode = "ReturnAfterError"; 
			return;
		 } else {

                   // ���������� ������������� ������������
 		   $UserId = 0;

                   // ����� �� ������ ����� ��� - �.�. � � ������ �� ����
		   $Msg = "������������!\r\n\r\n";
		   $Msg =  $Msg."���-�� (��������, ��� ���� ��) ��������������� ������� ������ �� ����� ���, ��������� � ���� ������� e-mail.".".\r\n";
		   $Msg =  $Msg."��� ��������� ������������ � ��������� ������ ���������� ������� �� ������:".".\r\n";
		   $Msg =  $Msg.$MyHttpLink.$MyPHPScript.'?action=sendpasswordafterrequest&changepasswordsessionid='.$ChangePasswordSessionId."\r\n\r\n";
		   $Msg =  $Msg."������� ������ ��� ��������� ����� ���� �������.".".\r\n";
		   $Msg =  $Msg."P.S. ���� ��� ���������������� ��� ������ ������� - ������ �������������� ������ - �������� ��������� �� ������������ ����������."."\r\n";
			    
                   // ���������� ������
		   SendMail(trim($pUserEmail), $Msg, $pUserName);

                   $statustext = '������ ��� ��������� ������������ � ��������� ������ ������� �� ��������� �����. 
		                  ���� ������ �� ������ - ��������� ����. ������� ������ ��� ��������� ����� ���� �������.';				     

		   $view = "MainPage";

		 }	     
	   
              // ����� ��������� ������ ������������            
	
           } elseif ($action == 'UserChangeData') {

              // ������ �������� ������������
	   
	     $NowUserId = GetSession($SessionId);
	
             // ���� ������� � ����� ���������, ������ ���� ��������� ��� ������������
             if ($pUserId <= 0 or $NowUserId <= 0)
	     {
	      return;
	     }
	   
	     $AllowEdit = 0;
	     // ����� �� ��������������
             if ($pUserId == $NowUserId or CheckAdmin($SessionId) == 1) 
	     {
		  $AllowEdit = 1;
	     } else {

	       $AllowEdit = 0;
               // �������
	       return;
	     }

             if ($AllowEdit == 1)
	     {
	         $sql = "update  Users set   user_email = trim('".$pUserEmail."'),
		                             user_name = trim('".$pUserName."'),
		                             user_prohibitadd = ".$pUserProhibitAdd.",
					     user_birthyear = ".$pUserBirthYear."
	                 where user_id = ".$pUserId;
                 
		// echo $sql;
		 $rs = MySqlQuery($sql);  
		 mysql_free_result($rs);

		 // ��������� ���������

	         $Sql = "select user_name from  Users where user_id = ".$NowUserId;
		 $Result = MySqlQuery($Sql);  
		 $Row = mysql_fetch_assoc($Result);
		 $ChangeDataUserName = $Row['user_name'];
		 mysql_free_result($Result);
		    
                 $Msg = "��������� ������������ ".$pUserName."!\r\n\r\n";
		 $Msg =  $Msg."� ����� ������� ������ ��������� ��������� - �� ����� ������� � �������� ������������."."\r\n";
		 $Msg =  $Msg."����� ���������: ".$ChangeDataUserName.".\r\n\r\n";
		 $Msg =  $Msg."P.S. ��������� ������ ������� ��, � ����� ������������� ����� ���.";
			   
			    
                  // ���������� ������
		  SendMail(trim($pUserEmail), $Msg, $pUserName);

             } 

	     // ����� ���������� ��������� �������� ������������            
	      	   
	   } else {
	   
	     // ������ ��������� �� ������ ����
             return;
	   }

     // ����� ���������� ������ ��� ���������� ��������� ������� ������������

	   
   } elseif ($action == "SendEmailWithNewPassword")  {
    // �������� ���������� ������� �� ����� ��������� ������ ������������
  
  	     $view = "ViewUserData";

             $pUserId = $_POST['UserId'];
             $SessionId = $_POST['sessionid'];

	     $NowUserId = GetSession($SessionId);

        //     echo 'pUserId '.$pUserId.'now  '.$NowUserId;
	
             // ���� ������� � ����� ���������, ������ ���� ��������� ��� ������������
             if ($pUserId <= 0 or $NowUserId <= 0)
	     {
	      return;
	     }
	   
	     $AllowEdit = 0;
	     // ����� �� ��������������
             if ($pUserId == $NowUserId or CheckAdmin($SessionId) == 1) 
	     {
		  $AllowEdit = 1;
	     } else {

	       $AllowEdit = 0;
               // �������
	       return;
	     }

             if ($AllowEdit == 1)
	     {
	   
		$sql = "select user_email, user_name, user_birthyear from  Users where user_id = ".$pUserId;
		$rs = MySqlQuery($sql);  
                $row = mysql_fetch_assoc($rs);
                mysql_free_result($rs);
     		$UserEmail = $row['user_email'];  
		$UserName = $row['user_name']; 

  		$NewPassword = GeneratePassword(6);
		
		// ����� � ���� ������ � ����� �������� ������ � �������
		//  �������� ������ ��� �������������� � � �����
		$sql = "update   Users  set user_password = '".md5($NewPassword)."',
		                             user_sendnewpassworddt = now(),
					     user_sessionfornewpassword = null,
					     user_sendnewpasswordrequestdt = null
		         where user_id = ".$UserId;
              //   echo $sql;
	        $rs = MySqlQuery($sql);  
                mysql_free_result($rs);

		$statustext = '������ '.$NewPassword.' ������.';

	        $Sql = "select user_name from  Users where user_id = ".$NowUserId;
		$Result = MySqlQuery($Sql);  
		$Row = mysql_fetch_assoc($Result);
		$ChangeDataUserName = $Row['user_name'];
		mysql_free_result($Result);

		$Msg = "��������� ������������ ".$UserName."!\r\n\r\n";
		$Msg =  $Msg."� ����� ������� ������ ������ ������: ".$NewPassword."\r\n";
		$Msg =  $Msg."����� ���������: ".$ChangeDataUserName.".\r\n\r\n";
		$Msg =  $Msg."P.S. ��������� ������ ������� ��, � ����� ������������� ����� ���.";
			    
                // ���������� ������
		SendMail(trim($UserEmail), $Msg, $pUserName);

            }
             // ����� �������� �� ����������� �������� ������

   } elseif ($action == "RestorePasswordRequest")  {
   // �������� ���������� ������� "������ ������"
  
	   $view = "";

           $pUserEmail = $_POST['Login'];

           //echo $pUserEmail;
           if (trim($pUserEmail) == '' or trim($pUserEmail) == 'E-mail') 
	   {
	              $statustext = '�� ������ e-mail.';
		      $alert = 1;
		      return;
	   }

	   $ChangePasswordSessionId = uniqid();
	   
           // ����� � ���� ������ ��� �������������� ������
           $sql = "update   Users  set user_sessionfornewpassword = '".$ChangePasswordSessionId."',
	                               user_sendnewpasswordrequestdt = now()
	           where user_email = '".$pUserEmail."'";
           //echo $sql;
	   $rs = MySqlQuery($sql);  
           mysql_free_result($rs); 	

	   $Msg = "������������!\r\n\r\n";
	   $Msg =  $Msg."���-�� (��������, ��� ���� ��) �������� �������������� ������ �� ����� ��� ��� ����� ������ e-mail."."\r\n";
	   $Msg =  $Msg."��� ��������� ������ ������ ���������� ������� �� ������:"."\r\n";
	   $Msg =  $Msg.$MyHttpLink.$MyPHPScript.'?action=sendpasswordafterrequest&changepasswordsessionid='.$ChangePasswordSessionId."\r\n\r\n";
	   $Msg =  $Msg."P.S. ���� �� �� ����������� �������������� ������ - ������ �������������� ������ - �������� ��������� �� ������������ ����������."."\r\n";

	   //echo $Message;				     
           // ����� ������� �������� "����", ����� ���. ������ � ���������� ����� �� enail
           // ���� �� ���� ������
	   SendMail($pUserEmail, $Msg);	

           $statustext = '������ ��� ��������� ������ ������ ������� �� ��������� �����. ���� ������ �� ������ - ��������� ����.';				     



   } elseif ($action == "sendpasswordafterrequest")  {
     // �������� ���������� �� ������ ��������� �� ������
	   $view = "";

	   $UserId = 0;
	   
	   if (empty($changepasswordsessionid))
	   {
              $action = "";
	      return;
	   }
	   

           $sql = "select user_id, user_email, user_name from  Users where user_sessionfornewpassword = trim('".$changepasswordsessionid."')";
         //  echo $sql;
	   $rs = MySqlQuery($sql);  
	   $Row = mysql_fetch_assoc($rs);
	   mysql_free_result($rs); 
 	   $UserId = $Row['user_id'];
 	   $UserEmail = $Row['user_email'];
 	   $UserName = $Row['user_name'];

            // echo $UserEmail; 
           // ���� �������������� ������� - ������ ������
	   // �������� ����� ����� ����� ���������� ������...
	   if ($UserId > 0)
	   {

		$NewPassword = GeneratePassword(6);
		
		// ����� � ���� ������ � ����� �������� ������ � �������
		//  �������� ������ ��� �������������� � � �����
		$sql = "update   Users  set user_password = '".md5($NewPassword)."',
		                             user_sendnewpassworddt = now(),
					     user_sessionfornewpassword = null,
					     user_sendnewpasswordrequestdt = null
		         where user_id = ".$UserId;
              //   echo $sql;
	        $rs = MySqlQuery($sql);  
                mysql_free_result($rs);

		$statustext = '������ '.$NewPassword.' ������.';

		$Msg = "��������� ������������ ".$UserName."!\r\n\r\n";
		$Msg =  $Msg."�������� �������������� ������� � ������ ������ e-mail,".".\r\n";
		$Msg =  $Msg."��� ����� ������� ������ �� ����� ��� ������ ������: ".$NewPassword."\r\n";
			    
                // ���������� ������
		SendMail(trim($UserEmail), $Msg, $UserName);

                // � ��� ��� �.�. ����� ������������ ������, ����� ��������� ����� �� ����
		$SessionId = StartSession($UserId);
		$view = "MainPage";
              
            }

            
            $changepasswordsessionid = "";
            $action = "";
                
   } elseif ($action == "UserLogout")  {
     // ����� 

	        CloseSession($_POST['sessionid'], 3);
                $SessionId = ""; 
                $_POST['sessionid'] = "";
		$action = "";
		$view = "MainPage";
	
   } elseif ($action == "CancelChangeUserData")  {
    // �������� ���������� ������� ������

           $view = "ViewUserData";

   } elseif ($action == "FindUser")  {
    // �������� ���������� ������� ���������

           $view = "ViewUsers";

   } else {
   // ���� ������� �������� �� ���������

   //  $statustext = "<br/>";
   }

//	print('view = '.$view.' action = '.$action);
   
?>