<?php


   $alert = 0; 
   $statustext = ""; //"�������: ".date("d.m.Y")."  &nbsp; �����: ".date("H:i:s");
  echo $action;
   // ���������� ���� ��������   
   

   // ��������� ������������ ������ � ���������� ����� �� �������� � ������������
  
   if ($action == "UserLogin") {
       // ��������� �����������

	 $action = "";
          
         // ��������� �������� ������ 
	 if ($_POST['Login'] == "") {

           $statustext = "�� ������ e-mail.";
           $alert = 1; 
           return;

         } elseif ($_POST['Password']== "") {

           $statustext = "�� ������ ������.";
           $alert = 1; 
           return;

         } 
         // ����� ��������� �������� ������� ������

                $Sql = "select user_id, user_name from mmb.Users where trim(user_email) = trim('".$_POST['Login']."') and user_password = '".md5($_POST['Password'])."'";
		
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
		//$statustext = "������������: ".$UserId.", ������: ".$SessionId;
		

   } elseif ($action == "")  {

         $view = "MainPage";
//         $statustext = "���������: ".$employeename.", ��������� �����: ".$tabnum ;

   } elseif ($action == "UserInfo")  {
    // �������� ���������� ������� ��� ������� ������������ 

  	  $view = "ViewUserData";
	  //$statustext = "�������������: ".$_SESSION['user_name'].", ����: ".$_SESSION['user_id']. " ������". session_id(); 

   } elseif ($action == "ViewNewUserForm")  {
    // �������� ���������� ������� ����� ������������

           $view = "ViewUserData";
		

   } elseif ($action == "UserChangeData")  {
     // �������� ���������� ���� ��� ����������� ������ ������������ ���� ��� ������ ������ �������

   	   $view = "ViewUserData";

           $pUserEmail = $_POST['UserEmail'];
           $pUserName = $_POST['UserName'];
           $pUserBirthYear = $_POST['UserBirthYear'];


           if (trim($pUserEmail) == '')
	   {
		$statustext = "�� ������ e-mail.";
	        $alert = 1; 
		return; 
	   }

           if (trim($pUserName) == '')
	   {
		$statustext = "�� ������� ���.";
	        $alert = 1; 
		return; 
	   }

           if ($pUserBirthYear < 1930 or $pUserBirthYear > date("Y"))
	   {
		$statustext = "��� �� ������ ��� ������ ������������.";
	        $alert = 1; 
		return; 
	   }

	   $UserId = 0;

	   if (empty($SessionId))
	   {
	        $SessionId =  $_POST['sessionid'];
	   } 

	   $UserId = GetSession($SessionId);

           $sql = "select count(*) as resultcount from mmb.Users where trim(user_email) = '".$pUserEmail."' and user_id <> ".$UserId;
           //echo $sql;
	   $rs = MySqlQuery($sql);  
	   $Row = mysql_fetch_assoc($rs);
	   if ($Row['resultcount'] > 0)
	   {
   		$statustext = "��� ���� ������������ � ����� email.";
	        $alert = 1; 
                return; 
	   }


           $sql = "select count(*) as resultcount from mmb.Users where trim(user_name) = '".$pUserName."' and user_birthyear = ".$pUserBirthYear." and user_id <> ".$UserId;
           //echo $sql;
	   $rs = MySqlQuery($sql);  
	   $Row = mysql_fetch_assoc($rs);
	   if ($Row['resultcount'] > 0)
	   {
   		$statustext = "��� ���� ������������ � ����� ������ � ����� ��������.";
	        $alert = 1; 
                return; 
	   }


      //     echo $UserId; 
	   if ($UserId > 0)
	   {
 	    // ��������� � ��� ������������� ������������

             // ��������, ��� ��� ����� ������
	         $sql = "update mmb.Users set user_email = trim('".$pUserEmail."'), 
		                             user_name = trim('".$pUserName."'),
					     user_birthyear = ".$pUserBirthYear."
	                 where user_id = ".$UserId;
                 
	//	 echo $sql;
		 $rs = MySqlQuery($sql);  
	   } else {
	     // ����� ������������

                 // ������ �����
                  $NewPassword = GeneratePassword(6);

                //  echo         $NewPassword;           
                  // ������ ������� �� �����, �������� �� ����� � ����� ��� ��� � ����
                 // ���������� ������ ������������
		 $sql = "insert into mmb.Users (user_email, user_name, user_birthyear, user_password) values ('".$pUserEmail."', '".$pUserName."', ".$pUserBirthYear.", '".md5($NewPassword)."')";
//                 echo $sql;  
                 // ��� insert ������ ��������� �������� id - ��� ����������� �  MySqlQuery
		 $UserId = MySqlQuery($sql);  
	
//	         echo $UserId; 
//                 $UserId = mysql_insert_id($Connection);
		 if ($UserId <= 0)
		 {
                        $statustext = '������ ������ ������ ������������.';
			$alert = 1;
			return;
		 } else {

                   $statustext = '������: '.$NewPassword;
		   SendMail($pUserEmail,'New password: '.$NewPassword);
		 
		   $SessionId = StartSession($UserId);
		 }	     
	   
	    }
	   
   } elseif ($action == "SendEmailWithNewPassword")  {
    // �������� ���������� ������� �� ����� ��������� ������ ������������
  
	   $view = "ViewUserData";

           $pUserEmail = $_POST['UserEmail'];

	   $UserId = 0;

	   if (empty($SessionId))
	   {
	        $SessionId =  $_POST['sessionid'];
	   } 

	   $UserId = GetSession($SessionId);

         //  echo $UserId; 
	   if ($UserId > 0)
	   {
  		$NewPassword = GeneratePassword(6);
		
                 // ����� � ����
	         $sql = "update  mmb.Users  set user_password = '".md5($NewPassword)."' where user_id = ".$UserId;
              //   echo $sql;
		 $rs = MySqlQuery($sql);  

		$statustext = '������: '.$NewPassword;
		
		SendMail($pUserEmail,'New password: '.$NewPassword);

            }
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

	   $ChangePasswordSessionId = StartSession();

           // ����� � ���� ������ ��� �������������� ������
           $sql = "update  mmb.Users  set user_sessionfornewpassword = '".$ChangePasswordSessionId."' where user_email = '".$pUserEmail."'";
           //echo $sql;
	   $rs = MySqlQuery($sql);  
	
	   $Message =  '���� �� ������ �������� ������ - ��������� �� ������: '.
	               'http://mmb.progressor.ru'.$MyPHPScript.'?action=sendpasswordafterrequest&changepasswordsessionid='.$ChangePasswordSessionId;

	   //echo $Message;				     
	   SendMail($pUserEmail, $Message);	

           $statustext = '������ ��� ��������� ������ ������ ������� �� ��������� �����. ���� ������ �� ������ - ��������� ����.';				     



   } elseif ($action == "sendpasswordafterrequest")  {
     // �������� ���������� �� ������ ��������� �� ������
	   $view = "";

	   $UserId = 0;

           $sql = "select user_id, user_email from mmb.Users where user_sessionfornewpassword = '".$changepasswordsessionid."'";
         //  echo $sql;
	   $rs = MySqlQuery($sql);  
	   $Row = mysql_fetch_assoc($rs);
 	   $UserId = $Row['user_id'];
 	   $UserEmail = $Row['user_email'];

            // echo $UserEmail; 
           // ���� �������������� ������� - ������ ������
	   if ($UserId > 0)
	   {
  		$NewPassword = GeneratePassword(6);
		
                 // ����� � ����
	         $sql = "update  mmb.Users  set user_password = '".md5($NewPassword)."', user_sessionfornewpassword = '' where user_id = ".$UserId;
              //   echo $sql;
		 $rs = MySqlQuery($sql);  

		$statustext = '������: '.$NewPassword;
		
		SendMail($UserEmail,'New password: '.$NewPassword);

                
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
	

   } elseif ($action == "SendRequestForSendPassword")  {
   // ��������� ������ �������� ������ ������������

         if ($login == "") {

            $statustext = "�� �� ������ ��� ������������!";
            $alert = 1; 
            return; 

         } 

         $Connection = mssql_connect($ServerName, $WebUserName, $WebUserPassword);

         if ($Connection <= 0) {
               $statustext = "������ ���������� � ��������.";
               $alert = 1; 
               return; 
         }

         $mail = trim($login).'@garant.ru';
 	     $tempemployeeid = uniqid(""); 
         mssql_select_db($ScifDBName, $Connection);
         $sql = "declare @nResult tinyint exec p_SendRequestForSendPassword '".$login."', 
		            '".$MyPHPScript."', '".$tempemployeeid."', @nResult OUTPUT select @nResult as result";

//		 echo $sql;

		 $rs = mssql_query($sql, $Connection);  
         $Result =  mssql_result($rs, 0, 'result');  

         // ������� ���������
         if ($Result <= 0) {

           $statustext = "������������ ".$login." �� ������!" ;
           $alert = 1; 

         } elseif ($Result == 1 ) {

              $statustext = "�� ������ �������� �����!";
              $alert = 1; 
  
         } elseif ($Result == 2 ) {

           $statustext = "�������� ����� �������� ������ �� �����" ;
           $alert = 1; 

         } elseif ($Result == 3 ) {

           $statustext = "������ �������." ;
           $alert = 1; 

         } 
         // ����� ��������

         mssql_close($Connection);

         // ���������� ����� 
	 $action = "";


   } elseif ($action == "SendPassword")  {
   // ��������� ������ �������� ������ ������������

         if ($tempemployeeid == "") {

            $statustext = "��� ���������� �������������� ������������!";
            $alert = 1; 
            return; 

         } 

         $Connection = mssql_connect($ServerName, $WebUserName, $WebUserPassword);

         if ($Connection <= 0) {
               $statustext = "������ ���������� � ��������.";
               $alert = 1; 
               return; 
         }

         $mail = trim($login).'@garant.ru';
         mssql_select_db($ScifDBName, $Connection);
         $sql = "declare @nResult tinyint exec p_SendPassword '".$tempemployeeid."', NULL, @nResult OUTPUT select @nResult as result";
         $rs = mssql_query($sql, $Connection);  
         $Result =  mssql_result($rs, 0, 'result');  

         // ������� ���������
         if ($Result <= 0) {

           $statustext = "������������ ".$login." �� ������!" ;
           $alert = 1; 

         } elseif ($Result == 1 ) {

              $statustext = "�� ������ �������� �����!";
              $alert = 1; 
  
         } elseif ($Result == 2 ) {

           $statustext = "�������� ����� �������� ������ �� �����" ;
           $alert = 1; 

         } elseif ($Result == 3 ) {

           $statustext = "������ ��������� ������! " ;
           $alert = 1; 

         } elseif ($Result == 4 ) {

           $statustext = "������ ������. ������������� ������� ��� ������ ����� ���������." ;
           $alert = 1; 

         } elseif ($Result == 5) {

           $statustext = "����� ������ ������ � ������. ������������� ������� ��� ������ ����� ���������." ;
           $alert = 1; 

         } 
         // ����� ��������

         mssql_close($Connection);

         // ���������� ����� 
	 $action = "";



   } elseif ($action == "ViewFormForChangePassword")  {

         // ��������� ������ ������ ����� ��� ����� ������
        
         $view = "ChangePassword";
         $menupad = "ChangePassword";
         $statustext = "���������: ".$employeename.", ��������� �����: ".$tabnum ;



   } elseif ($action == "ChangePassword")  {

         // ��������� ������ ����� ������ �������������
        

         if ($password == "") {

           $statustext = "�� �� ����� ������!";
           $alert = 1; 
           return;

         } elseif ($newpassword == "") {

           $statustext = "������ �� ����� ���� ������!";
           $alert = 1; 
           return;

         } elseif ($newpassword == $login) {

           $statustext = "������ �� ����� ��������� � ������� ������������!";
           $alert = 1; 
           return;

         } elseif (strlen(trim($newpassword)) < 6) {

           $statustext = "������ �� ����� ��������� ������ 6 ��������!";
           $alert = 1; 
           return;

         } 
         // ����� ��������� �������� ������

         $Connection = mssql_connect($ServerName, $WebUserName, $WebUserPassword);

         if ($Connection <= 0) {
              $statustext = "������ ���������� � ��������.";
              $alert = 1; 
              return; 
         }

         mssql_select_db($ScifDBName, $Connection);
         $sql = "declare @nResult tinyint  exec p_ChangePassword '".$sessionid."', '".$password."', '".$newpassword."', @nResult OUTPUT select @nResult as result";

		 $rs = mssql_query($sql, $Connection);  


         $Result =  mssql_result($rs, 0, 'result');  

         // ������� ���������
         if ($Result <= 0) {

              $statustext = "������������ ".$login." �� ������!";
              $alert = 1; 
 
         } elseif ($Result == 1 ) {

           $statustext = "������������ ������" ;
           $alert = 1; 

         } elseif ($Result >= 2 and $Result <= 4) {

           $statustext = "������ �������." ;
           $alert = 1; 

         } elseif ($Result >= 5) {

           $statustext = "������ �������. ������� ������ � ����� �������." ;
           $alert = 1; 
         } 
         // ����� ��������

         $password = ""; 
         $newpassword = ""; 

         mssql_close($Connection);

   } elseif ($action == "ViewFormForSecurity")  {

         // ��������� ������ ������ ����� ��� �������� ������������
        
         $view = "ViewSecurity";
         $menupad = "Security";
         $statustext = "���������: ".$employeename.", ��������� �����: ".$tabnum ;



   } elseif ($action == "ChangeIp")  {

         // ��������� ������ ����� ip �������������
        


         $Connection = mssql_connect($ServerName, $WebUserName, $WebUserPassword);

         if ($Connection <= 0) {
              $statustext = "������ ���������� � ��������.";
              $alert = 1; 
              return; 
         }

         mssql_select_db($ScifDBName, $Connection);
         $sql = "declare @nResult tinyint  exec p_ChangeIp '".$sessionid."', '".$employeeip."', @nResult OUTPUT select @nResult as result";
         $rs = mssql_query($sql, $Connection);  


         $Result =  mssql_result($rs, 0, 'result');  

         // ������� ���������
         if ($Result <= 0) {

              $statustext = "������������ �� ������!";
              $alert = 1; 
  
         } elseif ($Result == 1 ) {


//         $statustext = "�������� ���������. " ;

         } 
         // ����� ��������


         mssql_close($Connection);

         $view = "ViewSecurity";
         $menupad = "Security";
         $statustext = "���������: ".$employeename.", ��������� �����: ".$tabnum ;


   } elseif ($action == "ChangeSendPasswordFlag")  {

         // ��������� ������ ����� ������� �� �������� ������ �� �����
        


         $Connection = mssql_connect($ServerName, $WebUserName, $WebUserPassword);

         if ($Connection <= 0) {
              $statustext = "������ ���������� � ��������.";
              $alert = 1; 
              return; 
         }

         mssql_select_db($ScifDBName, $Connection);
         $sql = "declare @nResult tinyint  exec p_ChangeSendPasswordFlag '".$sessionid."', ".$employeenosendpassword.", @nResult OUTPUT select @nResult as result";
         $rs = mssql_query($sql, $Connection);  


         $Result =  mssql_result($rs, 0, 'result');  

         // ������� ���������
         if ($Result <= 0) {

              $statustext = "������������ �� ������!";
              $alert = 1; 
 
         } elseif ($Result == 1 ) {


//         $statustext = "�������� ���������. " ;

         } 
         // ����� ��������


         mssql_close($Connection);

         $view = "ViewSecurity";
         $menupad = "Security";
         $statustext = "���������: ".$employeename.", ��������� �����: ".$tabnum ;


   } else {
   // ���� ������� �������� �� ���������

   //  $statustext = "<br/>";
   }

//	print('view = '.$view.' action = '.$action);
   
?>