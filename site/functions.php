

<? 



 function MySqlQuery($SqlString,$SessionId,$NonSecure) {
 // ����� ���������� ���������� �� ������ &$ConnectionId  MySqlQuery($SqlString,&$ConnectionId, $SessionId,$NonSecure);
 //
 // �����  MySqlQuety('...',&$ConnectionId, ...);

   $NewConnection = 0;
   if (empty($ConnectionId))
   {
	$NewConnectionId = 1;
    
        // ������ ���� �� settings
	include("settings.php");
	
    	$ConnectionId = mysql_connect($ServerName, $WebUserName, $WebUserPassword);

         // ������ ����������
         if ($ConnectionId <= 0)
	 {
	    echo mysql_error();
            die(); 
	    return -1; 
	 }

	 //  ������������� ��������� ����
	 mysql_query('set time_zone = \'+4:00\'', $ConnectionId);  
         // �������� �� ���

     //    echo $DBName;
	 
	 $rs = mysql_select_db($DBName, $ConnectionId);

	 if (!$rs)
	 {
	    echo mysql_error();
	    die(); 
	    return -1; 
	 }
	 
   }
 
  // echo $ConnectionId;
   
   $rs = mysql_query($SqlString, $ConnectionId);  
 
 
   if (!$rs)
   {
	    echo mysql_error();
            die(); 
	    return -1; 
   }
   
   // ���� ��� insert - ���������� ��������� id 
   if (strpos($SqlString, 'insert') !== false)
   {
     $rs = mysql_insert_id($ConnectionId);
   //  echo ' NewId '.$rs;
   }
 
 
 
   if ($NewConnection == 1)
   {
	mysql_close($ConnectionId);
   } 

   return $rs;	
 
 }


  function StartSession($UserId) {

      if ($UserId > 0) 
      {
	      $SessionId = uniqid();
	      $Result = MySqlQuery("insert into  Sessions (session_id, user_id, session_starttime, session_updatetime, session_status)
	                            values ('".$SessionId ."',".$UserId.", now(), now(), 0)");

	      mysql_free_result($Result); 

              // ���������� ����� ��������� �����������
	      $Result = MySqlQuery("update Users set user_lastauthorizationdt = now()
	                            where user_id = ".$UserId);

	      mysql_free_result($Result); 



      }  else {
          $SessionId = '';
      }   

      return $SessionId;

  }


   // ��������� ���������� ������
  function CloseInactiveSessions($TimeOutInMinutes) {
  //  $TimeOut ����� � ������� � ���������� ����������, ��� �������� ����������� ������ 
  
        // �.�. ����� ��� ����� ����� ��������� �������� ���������� � ��
       $Result = MySqlQuery("update  Sessions set session_status = 1 
			    where session_status = 0 and session_updatetime < date_add(now(), interval -".$TimeOutInMinutes." MINUTE)");
       mysql_free_result($Result); 


      return;
  }

  // �������� ������ ������
  function GetSession($SessionId) {


      if (empty($SessionId))
      {
        return 0;
      } 

      // ��������� ��� ������, ������� ��������� 20 �����
      CloseInactiveSessions(20);
      
  //   echo $SessionId;
      $Result = MySqlQuery("select user_id, connection_id, session_updatetime, session_starttime
                            from   Sessions 
			    where session_id = '".$SessionId ."'");

      $Row = mysql_fetch_assoc($Result);
      mysql_free_result($Result); 

      // ��� ����� �������� �� ���������� �������, �� ���������� ������ � �.�.
      
      $UserId = $Row['user_id'];

      // ��������� ����� ������, ���� �� ��
      if ($UserId > 0)
      {
       $Result = MySqlQuery("update  Sessions set session_updatetime = now()
			    where session_status = 0 and session_id = '".$SessionId ."'");
       mysql_free_result($Result); 
      }
      
      return $UserId;

  }


  // ��������� ������
  function CloseSession($SessionId, $CloseStatus) {
  //  $CloseStatus  1 - ���������� ��������� � ���������� ����������
  //                3 - ������������ ����� �� �������

      if (empty($SessionId) or $CloseStatus <= 0)
      {
        return 0;
      } 

        // �.�. ����� ��� ����� ����� ��������� �������� ���������� � ��
       $Result = MySqlQuery("update  Sessions set session_updatetime = now(), session_status = ".$CloseStatus."
			    where session_status = 0 and session_id = '".$SessionId ."'");

       mysql_free_result($Result); 

      return;
   }


    // ����������� ������
    function GeneratePassword($PasswordLength) {
   // ���������� �������� � ������.$PasswordLength
	
	 $CharsArr="qazxswnhyujmedcvfrtgbkiolp1234567890QAZCVFXSWEDRTGBNHYUJMKIOLP";

		 // ���������� ���������� �������� � $chars
		 $CharsArrLen = StrLen($CharsArr) - 1;

		 // ���������� ������ ����������, � ������� � ����� ���������� �������.
 		 $Password = '';

		 // ������ ������.
		 while($PasswordLength--) {  $Password.=$CharsArr[rand(0, $CharsArrLen)]; }

       // echo $Password;
      return $Password;
    }



  // �������� ������
    function SendMail($Email, $Message, $ToName='', $Subject='���������� � ����� ���') {
   //
   
   // 20.01.2012 ������� ������� ������� �� ����� �������  send_mime_mail (��. ����)
	//$Headers = 'From: mmb@progressor.ru' . "\r\n" .
	//		'Reply-To: mmb@progressor.ru' . "\r\n" .
	//	    'X-Mailer:  /';
   
//           mail($Email, '���������� � ����� ���', $Message, $Headers);

    send_mime_mail('mmbsite',
		  'mmb@progressor.ru',
		  $ToName,
		  $Email,
		  'CP1251',  // ���������, � ������� ��������� ������������ ������
		  'CP1251', // ���������, � ������� ����� ���������� ������
		  $Subject,
		  $Message);


    return ;
    }



    // ��������, ��� ������� ������ ����������� ��������������
    function CheckAdmin($SessionId) {

        if ($SessionId <= 0) 
	{
	  return 0;
	}
   
	$UserId = GetSession($SessionId);

        if ($UserId <= 0) 
	{
	  return 0;
	}

	
	$sql = "select user_admin  
		        from  Users
			where user_hide = 0 and user_id = ".$UserId; 
      //  echo 'sql '.$sql;
	$Result = MySqlQuery($sql);
	$Row = mysql_fetch_assoc($Result);
	mysql_free_result($Result); 
		

    return $Row['user_admin'];
    }


    // ��������, ��� ������� ������ ����������� ���������� �������� ���
    function CheckModerator($SessionId, $RaidId) {
   
   
        if ($RaidId <= 0) 
	{
	  return 0;
	}

        if ($SessionId <= 0) 
	{
	  return 0;
	}

	$UserId = GetSession($SessionId);

        if ($UserId <= 0) 
	{
	  return 0;
	}
        
	$sql = "select CASE WHEN count(*) > 0 THEN 1 ELSE 0 END as user_moderator 
		        from  RaidModerators
			where raidmoderator_hide = 0 and raid_id = ".$RaidId." and user_id = ".$UserId; 

	$Result = MySqlQuery($sql);
	$Row = mysql_fetch_assoc($Result);
	mysql_free_result($Result); 

    return $Row['user_moderator'];
    }


    // ��������, ��� ������� ������ ����������� ��������� �������
    function CheckTeamUser($SessionId, $TeamId) {
   
   
        if ($TeamId <= 0) 
	{
	  return 0;
	}

        if ($SessionId <= 0) 
	{
	  return 0;
	}

	$UserId = GetSession($SessionId);

        if ($UserId <= 0) 
	{
	  return 0;
	}
        
	$sql = "select CASE WHEN count(*) > 0 THEN 1 ELSE 0 END as userinteam 
		        from  TeamUsers tu
			where teamuser_hide = 0 and team_id = ".$TeamId." and user_id = ".$UserId; 

	$Result = MySqlQuery($sql);
	$Row = mysql_fetch_assoc($Result);
	mysql_free_result($Result); 

    return $Row['userinteam'];
    }


    // ������� 
    // �����: �������� ������ [rgbeast]  
    // �� ������ �������� e-mail � ������� ��������� ���������� PHP
/*
������:
send_mime_mail('����� ������',
               'sender@site.ru',
               '���������� ������',
               'recepient@site.ru',
               'CP1251',  // ���������, � ������� ��������� ������������ ������
               'KOI8-R', // ���������, � ������� ����� ���������� ������
               '������-�����������',
               "������������, � ���� ���������!");
*/
    function send_mime_mail($name_from, // ��� �����������
                        $email_from, // email �����������
                        $name_to, // ��� ����������
                        $email_to, // email ����������
                        $data_charset, // ��������� ���������� ������
                        $send_charset, // ��������� ������
                        $subject, // ���� ������
                        $body, // ����� ������
                        $html = FALSE // ������ � ���� html ��� �������� ������
                        ) 
    {
      $to = mime_header_encode($name_to, $data_charset, $send_charset)
		  . ' <' . $email_to . '>';
      $subject = mime_header_encode($subject, $data_charset, $send_charset);
      $from =  mime_header_encode($name_from, $data_charset, $send_charset)
                     .' <' . $email_from . '>';
      if($data_charset != $send_charset) 
      {
	$body = iconv($data_charset, $send_charset, $body);
      }
      $headers = "From: $from\r\n";
      $type = ($html) ? 'html' : 'plain';
      $headers .= "Content-type: text/$type; charset=$send_charset\r\n";
      $headers .= "Mime-Version: 1.0\r\n";

      return mail($to, $subject, $body, $headers);
     }

     function mime_header_encode($str, $data_charset, $send_charset) 
     {
      if($data_charset != $send_charset) 
      {
	$str = iconv($data_charset, $send_charset, $str);
      }
      return '=?' . $send_charset . '?B?' . base64_encode($str) . '?=';
     }
     // ����� ������� ��� �������� ������

     // ������� ������������� ��������� �������
     function RecalcTeamResult($teamid)
     {
	  // ���� ���� �� ���� ���� ������������ = ����� �������� - ������
           // MySql ��� ������������� ����� �������� SUM() ������ ���������� ������ � NULL, �� ��������� - �������
           //  ��-�� ����� ���������� ������ ��������� ������ � ���������������
 	   $sql = "update Teams t
			  inner join
			  (
			    select  tl.team_id,
				    SUM(TIME_TO_SEC(timediff(tl.teamlevel_endtime, 
					CASE l.level_starttype 
					    WHEN 1 THEN tl.teamlevel_begtime 
					    WHEN 2 THEN l.level_begtime 
					    WHEN 3 THEN (select MAX(tl2.teamlevel_endtime) 
							 from TeamLevels tl2
							      inner join Levels l2 
							      on tl2.level_id = l2.level_id
							 where tl2.team_id = tl.team_id 
							       and l2.level_order < l.level_order
							) 
					    ELSE NULL 
					END
				      )) + COALESCE(tl.teamlevel_penalty, 0)*60) as team_resultinsec 
			    from  TeamLevels tl 
				  inner join Levels l 
				  on tl.level_id = l.level_id 
			    where tl.teamlevel_hide = 0 and tl.team_id = ".$teamid." 
			    group by  tl.team_id
			   )  a
			  on  t.team_id = a.team_id
		  set t.team_result = SEC_TO_TIME(a.team_resultinsec)";

             // echo $sql;
              MySqlQuery($sql);  

              // ������ ���������� � NULL ���������� ��� �������, � �������, ���� ���� �� ������ ��� NULL
	      $sql = "update Teams t
			  inner join
			  (
			    select  tl.team_id
			    from  TeamLevels tl 
				  inner join Levels l 
				  on tl.level_id = l.level_id 
			    where tl.teamlevel_hide = 0 and tl.team_id = ".$teamid." 
				  and timediff(tl.teamlevel_endtime, 
					CASE l.level_starttype 
					    WHEN 1 THEN tl.teamlevel_begtime 
					    WHEN 2 THEN l.level_begtime 
					    WHEN 3 THEN (select MAX(tl2.teamlevel_endtime) 
							 from TeamLevels tl2
							      inner join Levels l2 
							      on tl2.level_id = l2.level_id
							 where tl2.team_id = tl.team_id 
							       and l2.level_order < l.level_order
							) 
					    ELSE NULL 
					END
				      ) is NULL
			   )  a
			  on  t.team_id = a.team_id
		  set t.team_result = NULL";

             // echo $sql;
              MySqlQuery($sql);  


     }
     // ����� ������� ��������� ���������� ������� 
?>

