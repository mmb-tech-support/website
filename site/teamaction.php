<?php
   // ���������� ��������, ��������� � ��������   

   
// ��� ���� ��������� ����� ��������� ����������� ������� (� ����� � ��� ��)
// �������, ����� ������������ ����� � �������
   

	   if (empty($SessionId))
	   {
           $SessionId =  $_POST['sessionid'];
	   } 

	   $UserId = GetSession($SessionId);
/*
	   if (empty($UserId))
	   {
		//$statustext = "�� �������� ������������.";
	       // $alert = 0; 
		//return; 
	   }
*/
	   if (empty($TeamId))
	   {
             $TeamId = 0;
	   }  
	   if (empty($RaidId))
	   {
             $RaidId = 0;
	   }  

   

   if ($action == "RegisterNewTeam")  {
    // ��������� ����������� �������
        $view = "ViewTeamData";
        $viewmode = "Add";	
	//$statustext = "������������: ".$UserId.", ������: ".$SessionId;


	   if (empty($_POST['RaidId']))	   
	   {
		$statustext = "�� ������ ��� (�������� �� ������).";
	        $alert = 0; 
		return; 
	   }

           // ������� ��� � �������� ���������� � ��, ����������� ��� �������� ����������� �������������� �������
	   	   $sql = "select raid_id, raid_name, raid_registrationbegdate, raid_registrationbegdate, 
		                  raid_registrationenddate, raid_resultpublicationdate, now() as nowdt,
				  CASE WHEN raid_registrationenddate is not null and YEAR(raid_registrationenddate) <= 2011 
				      THEN 1 
				      ELSE 0 
				  END as oldmmb 
		           from  Raids where  raid_id = ".$_POST['RaidId'];
           //echo $sql;
	   $rs = MySqlQuery($sql);  
           $Row = mysql_fetch_assoc($rs);
           mysql_free_result($rs);      
           $RaidId =  $Row['raid_id'];
   	   $RaidPublicationResultDate = $Row['raid_resultpublicationdate'];
           $RaidRegistrationEndDate = $Row['raid_registrationenddate'];
           //echo $RaidId.' '.$RaidRegistrationEndDate;
           $OldMmb = $Row['oldmmb'];
 	   $NowDt = $row['nowdt'];
 
	   if (empty($RaidId) or empty($RaidRegistrationEndDate))
           {
            $statustext = "��� �� ������";
	    $alert = 0;		
	    return;
	   }

           // ��������, ��� ������������ �� ������� � ������� �� ��������� ��� 
	   $sql = "select t.team_num 
	           from  TeamUsers tu 
		         inner join  Teams  t on  tu.team_id = t.team_id 
		         inner join  Distances d on  t.distance_id = d.distance_id
		   where d.raid_id = '.$RaidId.' and  tu.teamuser_hide = 0 and tu.user_id = ".$UserId;
	   $rs = MySqlQuery($sql);  
           $row = mysql_fetch_assoc($rs);
           mysql_free_result($rs);      
           $TeamNum =  $row['team_num'];


	   if ($TeamNum > 0)
	   {
            $statustext = "��� ���� ������� c ����� �������� (N ".$row['team_num'].")";
	    $alert = 0;		
	    return;
	   }

         //   echo 'ok';

   } elseif  ($action == 'TeamChangeData' or $action == "AddTeam")  {
   // ��������� ������ �������

       
        if ($action == "AddTeam")
        {
            $viewmode = "Add";
        } else {
	    $viewmode = "";
        } 
 
	$view = "ViewTeamData";

           // ���� ����� �� � ���� ���� - ��������� ����
           $pDistanceId = $_POST['DistanceId'];
           $RaidId = $_POST['RaidId'];
           $pTeamNum = (int) $_POST['TeamNum'];
           $pTeamName = $_POST['TeamName'];
           $pTeamUseGPS = ($_POST['TeamUseGPS'] == 'on' ? 1 : 0);
           $pTeamMapsCount = $_POST['TeamMapsCount'];
           $pTeamGreenPeace = ($_POST['TeamGreenPeace'] == 'on' ? 1 : 0);
           $pTeamConfirmResult = ($_POST['TeamConfirmResult'] == 'on' ? 1 : 0);
           $pModeratorConfirmResult = ($_POST['ModeratorConfirmResult'] == 'on' ? 1 : 0);
           $pNewTeamUserEmail = $_POST['NewTeamUserEmail'];
           $pTeamNotOnLevelId = (int)$_POST['TeamNotOnLevelId'];

          //  echo $pTeamUseGPS;

	   if ($action <> "AddTeam" and $TeamId <= 0)
	   {
		$statustext = "�� ������ ������������� �������.";
	        $alert = 1; 
                $viewsubmode = "ReturnAfterError"; 
		return; 
	   }  

	   if (empty($pDistanceId))
	   {
		$statustext = "�� ������� ���������.";
	        $alert = 1; 
                $viewsubmode = "ReturnAfterError"; 
		return; 
	   }  


	   if (empty($RaidId))
	   {
		$statustext = "�� ������ ���.";
	        $alert = 1; 
                $viewsubmode = "ReturnAfterError"; 
		return; 
	   }  

           if (trim($pTeamName) == '')
	   {
		$statustext = "�� ������� ��������.";
	        $alert = 1; 
                $viewsubmode = "ReturnAfterError"; 
		return; 
	   }

           if ($pTeamMapsCount <= 0 or $pTeamMapsCount > 15)
	   {
		$statustext = "�� ������� ����� ���� ��� ������������ ����� ����.";
	        $alert = 1; 
                $viewsubmode = "ReturnAfterError"; 
		return; 
	   }


           $sql = "select count(*) as resultcount 
                   from  Teams t
                         inner join Distances d
                         on t.distance_id = d.distance_id 
                    where d.raid_id = ".$RaidId." and trim(team_name) = '".$pTeamName."' and team_hide = 0 and team_id <> ".$TeamId;
           //echo $sql;
	   $rs = MySqlQuery($sql);  
	   $Row = mysql_fetch_assoc($rs);
           mysql_free_result($rs);      
	   if ($Row['resultcount'] > 0)
	   {
   		$statustext = "��� ���� ������� � ����� ���������.";
	        $alert = 1; 
                $viewsubmode = "ReturnAfterError"; 
                return; 
	   }

           // ��������� ����� �������: ���� ����� - 0 � ������ ������ �� ������ ����
           $sql = "select count(*) as resultcount 
                   from  Teams t inner join Distances d on t.distance_id = d.distance_id 
                   where d.raid_id = '.$RaidId.' and  team_num = '.$pTeamNum.' and team_hide = 0 and team_id <> ".$TeamId;
           //echo $sql;
	   $rs = MySqlQuery($sql);  
	   $Row = mysql_fetch_assoc($rs);
           mysql_free_result($rs);      
	   if ($Row['resultcount'] > 0)
	   {
   		$statustext = "��� ���� ������� � ����� �������.";
	        $alert = 1; 
                $viewsubmode = "ReturnAfterError"; 
                return; 
	   }

    
    
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
      
	    if  ($action <> "AddTeam" and CheckTeamUser($SessionId, $TeamId))
	    {
		  $TeamUser = 1;
	    } else {
		  $TeamUser = 0;
	    }



	    if ($OldMmb and $pTeamNum <= 0)
 	    {
   		$statustext = "��� ��� �� 2012 ���� ����� �������� ����� �������.";
	        $alert = 1; 
                $viewsubmode = "ReturnAfterError"; 
                return; 
	    }


               // echo $pNewTeamUserEmail;
           // ��������� email ������ ��������� �������
	   if (!empty($pNewTeamUserEmail) and trim($pNewTeamUserEmail) <> 'Email ������ ���������')
	   {

                 
	        $sql = "select user_id, user_prohibitadd from  Users where ltrim(COALESCE(user_password, '')) <> '' and user_hide = 0 and trim(user_email) = trim('".$pNewTeamUserEmail."')";
		//   echo $sql;
		$rs = MySqlQuery($sql);  
		$Row = mysql_fetch_assoc($rs);
	        mysql_free_result($rs);
                $NewUserId = $Row['user_id']; 
                $UserProhibitAdd = $Row['user_prohibitadd']; 
		
		if (empty($NewUserId))
		{
	                $statustext = '������������ � ����� email �� ������.';
			$alert = 1;
		        $viewsubmode = "ReturnAfterError"; 
			return;
		}

                // �������� �� ������ ��������� � �������
		if ($UserProhibitAdd and $NewUserId <> $UserId and !$Moderator)
		{
			$NewUserId = 0;
	                $statustext = '������������ �������� ��������� ���� � ������� ������ �������������.';
			$alert = 1;
		        $viewsubmode = "ReturnAfterError"; 
			return;
		}

	        $sql = "select count(*) as result 
		        from  TeamUsers tu 
		            inner join  Teams t  on tu.team_id = t.team_id
			    inner join  Distances d  on t.distance_id = d.distance_id
		        where teamuser_hide = 0 and d.raid_id = ".$RaidId." and user_id = ".$NewUserId;

//		echo 's1 '.$sql;
		$rs = MySqlQuery($sql);  
		$Row = mysql_fetch_assoc($rs);
	        mysql_free_result($rs);
                if ($Row['result'] > 0)
		{
			$NewUserId = 0;
	                $statustext = '������������ � ����� email ��� ������� � �������.';
			$alert = 1;
		        $viewsubmode = "ReturnAfterError"; 
			return;
		} 

            } else  {
	    
	        // ���������, ��� ��� ����� ������� �������� email ���������
		if ($action == "AddTeam")
		{
			$NewUserId = 0;
			$statustext = "��� ����� ������� ������ ���� ������ email ���������.";
		        $alert = 1; 
		        $viewsubmode = "ReturnAfterError"; 
	                return; 
		}
		
		$NewUserId = 0;

	    } // ����� �������� �� ���������� �������� ������
	    

            // ����� �������� ����������� ��������������

	    if ($viewmode == "Add" or $Moderator or ($TeamUser and !$TeamModeratorConfirmResult))
	    {
	      $AllowEdit = 1;
	    } else { 
	      $AllowEdit = 0;
	    }
 
            // ��� �� ���������� ����� ��� ������ ���� ���������, �� �� ������ ������ ���������
            if (!$AllowEdit)
            {
   	       $statustext = "����������� ���������.";
               return;

	    }

            $TeamActionTextForEmail = "";

	    if ($action == "AddTeam")
	    {
	      // ����� �������
                 
		 $sql = "insert into  Teams (team_num, team_name, team_usegps, team_mapscount, distance_id, 
                                             team_registerdt, team_greenpeace, team_confirmresult, 
                                             team_moderatorconfirmresult, level_id) 
		                       values  (";

                 if ($OldMmb)
                 {
                    $sql = $sql.$pTeamNum;
		 } else {
		    $sql = $sql."	       (
						 select COALESCE(MAX(t.team_num), 0) +1
						 from  Teams t
						      inner join  Distances d on t.distance_id = d.distance_id
						 where d.raid_id = ".$RaidId."
						)";
                 }
                 $sql = $sql.", '".$pTeamName."',".$pTeamUseGPS.",".$pTeamMapsCount.", ".$DistanceId.",
                                NOW(), ".$pTeamGreenPeace.", ".$pTeamConfirmResult.",
                               ".$pModeratorConfirmResult.", ".$pTeamNotOnLevelId." )";


              //   echo $sql;  
                 // ��� insert ������ ��������� �������� id - ��� ����������� �  MySqlQuery
		 $TeamId = MySqlQuery($sql);  
	
//	         echo $TeamId; 
//                 $UserId = mysql_insert_id($Connection);
		 if ($TeamId <= 0)
		 {
                        $statustext = '������ ������ ����� �������.';
			$alert = 1;
		        $viewsubmode = "ReturnAfterError"; 
			return;
		 } else {

		         $sql = "insert into  TeamUsers (team_id, user_id) values  (".$TeamId.", ".$NewUserId.")";
			 $TeamUserId = MySqlQuery($sql);  
   
                         $TeamActionTextForEmail = "������� �������";

		   // ������ ����� ������� �� ��������
		   $viewmode = "";

		 }	     
	

   	    } else {
              // ��������� � ��� ������������ �������

                 $TeamActionTextForEmail = "��������� ������ �������";
		 
	         $sql = "update  Teams set team_name = trim('".$pTeamName."'), 
		                              distance_id = ".$pDistanceId.", 
					      team_usegps = ".$pTeamUseGPS.", 
					      team_greenpeace = ".$pTeamGreenPeace.", 
					      team_confirmresult = ".$pTeamConfirmResult.", 
					      team_moderatorconfirmresult = ".$pModeratorConfirmResult.", 
					      level_id = ".$pTeamNotOnLevelId.", 
					      team_mapscount = ".$pTeamMapsCount."
	                 where team_id = ".$TeamId;
         
	        // echo $sql;
	 
                 $rs = MySqlQuery($sql);  
 	         mysql_free_result($rs);

                 // ��������� ����� ������� � ����� ������� �� ����������
  		 $sql = "SELECT level_order FROM Levels where level_id = ".$pTeamNotOnLevelId;
                 $rs = MySqlQuery($sql);  
                 $Row = mysql_fetch_assoc($rs);
 	         mysql_free_result($rs);
               

                 if ($Row['level_order'] > 0)
                 {   

		    $sql = "update TeamLevels tl
				   join Levels l1
				   on tl.level_id = l1.level_id 
			    set teamlevel_hide = 0 
			    where team_id = ".$TeamId." 
				  and l1.level_order < ".$Row['level_order'];
                       
		 //   echo $sql;
		    $rs = MySqlQuery($sql);  
		    mysql_free_result($rs);

                    // ������� ���� ��� � �� �� ��� ������, ���� ����� �������������� ���-�� ���������
                    // ��������, ��������� �������
		    $sql = "update  TeamLevels tl
				    join Levels l1
				    on tl.level_id = l1.level_id 
                            set teamlevel_hide = 1, 
                                teamlevel_begtime = NULL,
                                teamlevel_endtime = NULL,
                                teamlevel_points = NULL,
                                teamlevel_penalty = NULL,
                                teamlevel_comment = NULL
			    where team_id = ".$TeamId." 
				  and l1.level_order >= ".$Row['level_order'];
                       
		   // echo $sql;
		    $rs = MySqlQuery($sql);  
		    mysql_free_result($rs);


                  } else {

		    $sql = "update  TeamLevels set teamlevel_hide = 0 
			    where team_id = ".$TeamId; 

		   // echo $sql;
		    $rs = MySqlQuery($sql);  
		    mysql_free_result($rs);


                  }
                  // ����� ���������  ����� ������� � ������� ����� �� ����������
                


	         // ���� ��������� ���������
		 if ($NewUserId > 0)
		 {
		   $sql = " insert into  TeamUsers (team_id, user_id) values  (".$TeamId.", ".$NewUserId.")";
  		   //echo $sql;
		   $TeamUserId = MySqlQuery($sql);  

		   $Sql = "select user_name from  Users where user_id = ".$NewUserId;
		   $Result = MySqlQuery($Sql);  
		   $Row = mysql_fetch_assoc($Result);
  	           mysql_free_result($Result);
		   $NewUserName = $Row['user_name'];
		 
		   $TeamActionTextForEmail = "�������� �������� ".$NewUserName;
		 }        


            }
	    // ����� �������� ����� ��� ������������ �������

            // ��������� ��������� ������� (������� ����� ������ ��� ��������� ����� �������� �������)
	    if ($UserId > 0 and $TeamId > 0)
	    {
	      RecalcTeamResult($TeamId);
            }

            // ��������� ������ ���� ���������� ������� �� ����������
	    if ($UserId > 0 and $TeamId > 0)
	    {
	    	    $Sql = "select user_name from  Users where user_id = ".$UserId;
		    $Result = MySqlQuery($Sql);  
		    $Row = mysql_fetch_assoc($Result);
    	            mysql_free_result($Result);
		    $ChangeDataUserName = $Row['user_name'];
		   
		    
		    $sql = "select u.user_email, u.user_name, t.team_num, d.distance_name, r.raid_name
		            from  Users u 
			         inner join  TeamUsers tu
				 on tu.user_id = u.user_id
				 inner join  Teams t
				 on tu.team_id =  t.team_id
				 inner join  Distances d
				 on t.distance_id = d.distance_id
				 inner join  Raids r
				 on d.raid_id = r.raid_id
			    where tu.teamuser_hide = 0 and tu.team_id = ".$TeamId."
			    order by  tu.teamuser_id asc"; 
                //echo 'sql '.$sql;
		$Result = MySqlQuery($sql);

		while ($Row = mysql_fetch_assoc($Result))
		{
                   // ��������� ���������
                   $Msg = "��������� �������� ".$Row['user_name']."!\r\n\r\n";
		   $Msg =  $Msg."��������: ".$TeamActionTextForEmail.".\r\n";
		   $Msg =  $Msg."������� N ".$Row['team_num'].", ���������: ".$Row['distance_name'].", ���: ".trim($Row['raid_name']).".\r\n";
		   $Msg =  $Msg."����� ���������: ".$ChangeDataUserName.".\r\n";
		   $Msg =  $Msg."�� ������ ������� ��������� �� ����� � ��� ������������� ������ ���� ���������.\r\n\r\n";
		   $Msg =  $Msg."P.S. ��������� ����� ������� ����� �� ���������� �������, � ����� ��������� ���.";
			   
			    
                  // ���������� ������
		  SendMail($Row['user_email'], $Msg, $Row['user_name']);
		}
 	        mysql_free_result($Result);

	    }
	    // ����� �������� ������������ ���������� ���������

	    // ���� �������� �������������� ��������, �� ������� ���������� (���� ������ ���� ����������� - �� ������ ������)
	    $view = $_POST['view'];
	
	    if (empty($view))
	    {
		$view = "ViewTeamData";
	    } 	



   } elseif  ($action == 'FindTeam')  {
   // ���������� � ������� �� ������
   
	
		$sql = "select team_id 
		        from  Teams t
			     inner join  Distances d on t.distance_id = d.distance_id
		        where d.raid_id = ".$RaidId." and t.team_hide = 0 and t.team_num = ".$TeamNum;
          // echo $sql;
	   $rs = MySqlQuery($sql);  
	   $Row = mysql_fetch_assoc($rs);
           mysql_free_result($rs);
           $TeamId = $Row['team_id'];
	   
		 if ($TeamId <= 0)
		 {
                        
                        $statustext = '������� � ������� '.$TeamNum.' �� �������.';
			$alert = 1;
			return;


		 }  else {

			$view = "ViewTeamData";
		 }


   } elseif  ($action == 'TeamInfo')  {
   // ���������� � ������� �� Id
   
		 if ($TeamId <= 0)
		 {
                        
                        $statustext = '������� �� �������.';
			$alert = 1;
			return;


		 }  else {

			$view = "ViewTeamData";
                        $viewmode = ""; 
		 }


   } elseif  ($action == 'HideTeamUser')  {
   // �������� ��������� �������
   
	     $TeamUserId = $_POST['HideTeamUserId'];
	     if ($TeamUserId <= 0)
	     {
	         $statustext = '�������� �� ������.';
		 $alert = 1;
		 return;
	     }	 

	     $TeamId = $_POST['TeamId'];
	     if ($TeamId <= 0)
	     {
	         $statustext = '������� �� �������.';
		 $alert = 1;
		 return;
	     }	 

	     $RaidId = $_POST['RaidId'];
	     if ($RaidId <= 0)
	     {
	         $statustext = '�� ������ ���.';
		 $alert = 1;
		 return;
	     }	 

	     $SessionId = $_POST['sessionid'];
	     if ($SessionId <= 0)
	     {
	         $statustext = '�� ������� �����.';
		 $alert = 1;
		 return;
	     }	 

	      if (CheckModerator($SessionId, $RaidId))
	      {
	        $Moderator = 1;
	      } else {
	        $Moderator = 0;
	      }
      
	      if  (CheckTeamUser($SessionId, $TeamId))
	      {
		  $TeamUser = 1;
	      } else {
		  $TeamUser = 0;
	      }

             // �������� ����. ���� ��� - �������
             if ($Moderator or $TeamUser)
             {
               $AllowEdit = 1;
             } else {
               $AllowEdit = 0;     
                              
               return;
             }


             // �������, ��� �� ��� ��������� �������� ��� ���
 	     $sql = "select count(*) as result from  TeamUsers  where teamuser_hide = 0 and team_id = ".$TeamId;
	          // echo $sql;
	     $rs = MySqlQuery($sql);  
	     $Row = mysql_fetch_assoc($rs);
             mysql_free_result($rs);
	     $TeamUserCount = $Row['result'];
             if ($TeamUserCount > 1)
	     {
                // ���-�� ��� �������� 
		$sql = "update  TeamUsers set teamuser_hide = 1 where teamuser_id = ".$TeamUserId; 
        	// echo $sql;
	        $rs = MySqlQuery($sql);  
                mysql_free_result($rs);
		
		$view = "ViewTeamData";

		
	     } else {
	       // ��� ��� ��������� ��������
	   
	   
		$sql = "update  TeamUsers set teamuser_hide = 1 where teamuser_id = ".$TeamUserId; 
		$rs = MySqlQuery($sql);  
                mysql_free_result($rs);
		$sql = "update  Teams set team_hide = 1 where team_id = ".$TeamId;
		$rs = MySqlQuery($sql);  
                mysql_free_result($rs);

		$view = "";
	   
	     } // ����� �������� �� ���������� ���������

	     // ��������� ������ ���� ���������� ������� �� ��������
	    if ($UserId > 0 and $TeamId > 0)
	    {
	    	    $Sql = "select user_name from  Users where user_id = ".$UserId;
		    $Result = MySqlQuery($Sql);  
		    $Row = mysql_fetch_assoc($Result);
		    $ChangeDataUserName = $Row['user_name'];
		    mysql_free_result($Result);
		    
	    	    $Sql = "select user_name from  Users u inner join  TeamUsers tu on tu.user_id = u.user_id where tu.teamuser_id = ".$TeamUserId;
		    $Result = MySqlQuery($Sql);  
		    $Row = mysql_fetch_assoc($Result);
		    $DelUserName = $Row['user_name'];
		    mysql_free_result($Result);
                    

		    $sql = "select u.user_email, u.user_name, t.team_num, d.distance_name, r.raid_name
		            from  Users u 
			         inner join  TeamUsers tu
				 on tu.user_id = u.user_id
				 inner join  Teams t
				 on tu.team_id =  t.team_id
				 inner join  Distances d
				 on t.distance_id = d.distance_id
				 inner join  Raids r
				 on d.raid_id = r.raid_id
			    where tu.teamuser_id = ".$TeamUserId." or (tu.teamuser_hide = 0 and tu.team_id = ".$TeamId.")
			    order by  tu.teamuser_id asc"; 
                     //echo 'sql '.$sql;
		     $Result = MySqlQuery($sql);

		     if ($TeamUserCount > 1)
		     {
			     while ($Row = mysql_fetch_assoc($Result))
			     {
		                   // ��������� ���������
			           if (trim($DelUserName) <> trim($Row['user_name']))
				   {
					$Msg = "��������� �������� ".$Row['user_name']."!\r\n\r\n�� ����� ������� (N ".$Row['team_num'].", ���������: ".trim($Row['distance_name']).", ���: ".trim($Row['raid_name']).") ��� ������ ��������: ".$DelUserName.".\r\n����� ���������: ".$ChangeDataUserName.".\r\n�� ������ ������� ��������� �� ����� � ��� ������������� ������ ���� ���������.\r\n\r\nP.S. ��������� ����� ������� ����� �� ���������� �������, � ����� ��������� ���.";
				   } else {
					$Msg = "��������� �������� ".$Row['user_name']."!\r\n\r\n�� ���� ������� �� ������� (N ".$Row['team_num'].", ���������: ".trim($Row['distance_name']).", ���: ".trim($Row['raid_name']).")\r\n����� ���������: ".$ChangeDataUserName.".\r\n�� ������ ������� ��������� �� ����� � ��� ������������� ������ ���� ���������.\r\n\r\nP.S. ��������� ����� ������� ����� �� ���������� �������, � ����� ��������� ���.";
				   }	
	 	                   // ���������� ������
				   SendMail($Row['user_email'], $Msg, $Row['user_name']);
			     }
		    } else {
			$Row = mysql_fetch_assoc($Result);
	                $Msg = "��������� �������� ".$Row['user_name']."!\r\n\r\n���� ������� (N ".$Row['team_num'].", ���������: ".trim($Row['distance_name']).", ���: ".trim($Row['raid_name']).") ���� �������.\r\n����� ���������: ".$ChangeDataUserName.".\r\n�� ������ ������� ��������� �� ����� � ��� ������������� ������ ���� ���������.\r\n\r\nP.S. ��������� ����� ������� ����� �� ���������� �������, � ����� ��������� ���.";
	 	        // ���������� ������
			SendMail($Row['user_email'], $Msg, $Row['user_name']);
		    }			     
		    mysql_free_result($Result);

	    }
	    // ����� �������� ������������ ���������� ���������


 } elseif  ($action == 'TeamUserOut')  {
   // ����� ����� �����  ��������� �������
   
	     $TeamUserId = $_POST['HideTeamUserId'];
	     if ($TeamUserId <= 0)
	     {
	         $statustext = '�������� �� ������.';
		 $alert = 1;
		 return;
	     }	 

             // ����� ����� ���� 0 ���� - ������, ��� �������� ����� �� ������
	     $LevelId = $_POST['UserOutLevelId'];
	     if ($LevelId < 0)
	     {
	         $statustext = '�� ������ ����.';
		 $alert = 1;
		 return;
	     }	 

	     $TeamId = $_POST['TeamId'];
	     if ($TeamId <= 0)
	     {
	         $statustext = '������� �� �������.';
		 $alert = 1;
		 return;
	     }	 

	     $RaidId = $_POST['RaidId'];
	     if ($RaidId <= 0)
	     {
	         $statustext = '�� ������ ���.';
		 $alert = 1;
		 return;
	     }	 

	     $SessionId = $_POST['sessionid'];
	     if ($SessionId <= 0)
	     {
	         $statustext = '�� ������� �����.';
		 $alert = 1;
		 return;
	     }	 

	      if (CheckModerator($SessionId, $RaidId))
	      {
	        $Moderator = 1;
	      } else {
	        $Moderator = 0;
	      }
      
	      if  (CheckTeamUser($SessionId, $TeamId))
	      {
		  $TeamUser = 1;
	      } else {
		  $TeamUser = 0;
	      }

             // �������� ����. ���� ��� - �������
             if ($Moderator or $TeamUser)
             {
               $AllowEdit = 1;
             } else {
               $AllowEdit = 0;     
                              
               return;
             }

           
		$sql = "update  TeamUsers set level_id = ".($LevelId > 0 ?  $LevelId : 'null' )." where teamuser_id = ".$TeamUserId; 
        	// echo $sql;
	        $rs = MySqlQuery($sql);  
                mysql_free_result($rs);

	
		$view = "ViewTeamData";

                // ������ �� ����������		

		    $Sql = "select user_name from  Users where user_id = ".$UserId;
		    $Result = MySqlQuery($Sql);  
		    $Row = mysql_fetch_assoc($Result);
    	            mysql_free_result($Result);
		    $ChangeDataUserName = $Row['user_name'];
		   
		    
		    $sql = "select u.user_email, u.user_name, t.team_num, d.distance_name, r.raid_name
		            from  Users u 
			         inner join  TeamUsers tu
				 on tu.user_id = u.user_id
				 inner join  Teams t
				 on tu.team_id =  t.team_id
				 inner join  Distances d
				 on t.distance_id = d.distance_id
				 inner join  Raids r
				 on d.raid_id = r.raid_id
			    where tu.teamuser_hide = 0 and tu.team_id = ".$TeamId."
			    order by  tu.teamuser_id asc"; 
                //echo 'sql '.$sql;
		$Result = MySqlQuery($sql);

		while ($Row = mysql_fetch_assoc($Result))
		{
                   // ��������� ���������
                   $Msg = "��������� �������� ".$Row['user_name']."!\r\n\r\n";
		   $Msg =  $Msg."��������: ��������� ������ �������.\r\n";
		   $Msg =  $Msg."������� N ".$Row['team_num'].", ���������: ".$Row['distance_name'].", ���: ".trim($Row['raid_name']).".\r\n";
		   $Msg =  $Msg."����� ���������: ".$ChangeDataUserName.".\r\n";
		   $Msg =  $Msg."�� ������ ������� ��������� �� ����� � ��� ������������� ������ ���� ���������.\r\n\r\n";
		   $Msg =  $Msg."P.S. ��������� ����� ������� ����� �� ���������� �������, � ����� ��������� ���.";
			   
		   	    
                  // ���������� ������
		   SendMail($Row['user_email'], $Msg, $Row['user_name']);
		}
 	        mysql_free_result($Result);


   } elseif  ($action == 'HideTeam')  {
   // ���������� � ������� �� ������
     

	     $TeamId = $_POST['TeamId'];
	     if ($TeamId <= 0)
	     {
	         $statustext = '������� �� �������.';
		 $alert = 1;
		 return;
	     }	 

	  if ($RaidId <= 0)
	     {
	         $statustext = '�� ������ ���.';
		 $alert = 1;
		 return;
	     }	 

	     $SessionId = $_POST['sessionid'];
	     if ($SessionId <= 0)
	     {
	         $statustext = '�� ������� �����.';
		 $alert = 1;
		 return;
	     }	 

	      if (CheckModerator($SessionId, $RaidId))
	      {
	        $Moderator = 1;
	      } else {
	        $Moderator = 0;
	      }
      
	      if  (CheckTeamUser($SessionId, $TeamId))
	      {
		  $TeamUser = 1;
	      } else {
		  $TeamUser = 0;
	      }

             // �������� ����. ���� ��� - �������
             if ($Moderator or $TeamUser)
             {
               $AllowEdit = 1;
             } else {
               $AllowEdit = 0;     
                              
               return;
             }



	     if ($UserId > 0 and $TeamId > 0)
	     {
		    $Sql = "select user_name from  Users where user_id = ".$UserId;
		    $Result = MySqlQuery($Sql);  
		    $Row = mysql_fetch_assoc($Result);
		    $ChangeDataUserName = $Row['user_name'];
		    mysql_free_result($Result);
	     }

		    $sql = "select u.user_email, u.user_name, t.team_num, d.distance_name, r.raid_name
		            from  Users u 
			         inner join  TeamUsers tu
				 on tu.user_id = u.user_id
				 inner join  Teams t
				 on tu.team_id =  t.team_id
				 inner join  Distances d
				 on t.distance_id = d.distance_id
				 inner join  Raids r
				 on d.raid_id = r.raid_id
			    where tu.teamuser_hide = 0 and tu.team_id = ".$TeamId."
			    order by  tu.teamuser_id asc"; 
                     //echo 'sql '.$sql;
		     $Result = MySqlQuery($sql);

		     while ($Row = mysql_fetch_assoc($Result))
		     {
	                   // ��������� ���������
	                   $Msg = "��������� �������� ".$Row['user_name']."!\r\n\r\n���� ������� (N ".$Row['team_num'].", ���������: ".trim($Row['distance_name']).", ���: ".trim($Row['raid_name']).") ���� �������.\r\n����� ���������: ".$ChangeDataUserName.".\r\n\r\n\r\nP.S. ��������� ����� ������� ����� �� ���������� �������, � ����� ��������� ���.";
 	                   // ���������� ������
			   SendMail($Row['user_email'], $Msg, $Row['user_name']);
		     }
                     mysql_free_result($Result);

		$sql = "update  TeamUsers set teamuser_hide = 1 where team_id = ".$TeamId; 
		$rs = MySqlQuery($sql);  
                mysql_free_result($rs);
                $sql = "update  Teams set team_hide = 1 where team_id = ".$TeamId;
		$rs = MySqlQuery($sql);  
                mysql_free_result($rs);
		
	      $view = "ViewRaidTeams";

   } elseif ($action == "CancelChangeTeamData")  {
    // �������� ���������� ������� ������

           $view = "ViewTeamData";

   } elseif ($action == "ViewRaidTeams")  {
    // �������� ���������� ������� ������

           $view = "ViewRaidTeams";


   } else {
   // ���� ������� �������� �� ���������

   //  $statustext = "<br/>";
   }

	//print('view = *'.$view.'* action = '.$action);
   
?>