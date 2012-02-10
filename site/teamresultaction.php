<?php

  
   // ���������� ��������, ���������  � �����������
   // ��������������, ��� �� ���������� ����� ����������� teamaction ������� �� ��������� ������ � ������

   // ���� ������������ ������ ���� ������� - ��������� �����������

   if ($action == "ChangeTeamResult")  {
    // ��������� ����������� �������
        $view = "ViewTeamData";
        $viewmode = "";	


	   if ($TeamId <= 0)	   
	   {
		return; 
	   }

	  $sql = "select r.raid_resultpublicationdate, r.raid_registrationenddate, 
                        CASE WHEN r.raid_registrationenddate is not null and YEAR(r.raid_registrationenddate) <= 2011 
                             THEN 1 
                             ELSE 0 
                        END as oldmmb,
                        r.raid_id,
                        t.team_moderatorconfirmresult
		 from   Raids r
                        inner join Distances  d
                        on r.raid_id = d.raid_id
                        inner join Teams t
                        on d.distance_id = t.distance_id
		 where t.team_id = ".$TeamId; 
		//echo 'sql '.$sql;
                $Result = MySqlQuery($sql);
		$Row = mysql_fetch_assoc($Result);
                mysql_free_result($Result);
		$RaidPublicationResultDate = $Row['raid_resultpublicationdate'];
		$RaidId = $Row['raid_id'];
                $OldMmb = $Row['oldmmb'];
		$ModeratorConfirmResult = $Row['team_moderatorconfirmresult'];

            // ��� ���� ��������� ���������� ������ �� ������ ������ �� ���

            // �����, ��������, ��� �������� �� �����,����� �� ���������� ������ ���������� � ������� �����

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


       // ����� �������� ����������� ��������������

	    if ($OldMmb or $Moderator or ($TeamUser and !$TeamModeratorConfirmResult))
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

           //  ������� ������ ������, ����� ����������� ����� ������ ����� ������ ��� ����� ��������
	   	   $sql = "select l.level_id, l.level_name, l.level_pointnames, l.level_starttype, l.level_pointpenalties, 
                                  l.level_begtime, l.level_maxbegtime, l.level_minendtime, l.level_endtime,
                                  tl.teamlevel_begtime, tl.teamlevel_endtime,
                                  tl.teamlevel_points, tl.teamlevel_penalty, 
                                  tl.teamlevel_id
			    from  Teams t 
                                 inner join Distances d 
                                 on t.distance_id = d.distance_id 
                                 inner join Levels l  
                                 on d.distance_id = l.distance_id 
                                 left outer join Levels l1
                                 on t.level_id = l1.level_id
                                 left outer join TeamLevels tl
                                 on l.level_id =  tl.level_id
                                    and t.team_id = tl.team_id   
                                    and tl.teamlevel_hide = 0
                           where l.level_order < COALESCE(l1.level_order, l.level_order + 1) and  t.team_id = ".$TeamId;

       		           
             //"and l.level_minbegtime <= now() ";


          // echo $sql;
	   $rs = MySqlQuery($sql);  
     
           // ������ ���� ��������� ������ �� ������ 
           $statustext = "";
	   while ($Row = mysql_fetch_assoc($rs))
	   {
              
              // �� ����� ����� ����� ����������, ���� �� ��� ������� � teamLevels ��� � ����� ������� 
              $TeamLevelId = $Row['teamlevel_id'];


		    $Index = 'Level'.$Row['level_id'].'_begyear';
                    $BegYear = $_POST[$Index];
                    $Index = 'Level'.$Row['level_id'].'_begdate';
                    $BegDate = $_POST[$Index];
                    $Index = 'Level'.$Row['level_id'].'_begtime';
                    $BegTime = $_POST[$Index];

                    $BegYDTs =  "'".$BegYear."-".substr(trim($BegDate), -2)."-".substr(trim($BegDate), 0, 2)." ".substr(trim($BegTime), 0, 2).":".substr(trim($BegTime), -2).":00'";
      //              echo 'BegYDTs';
  
                    // ������������ � ���� php ��� ���������
                    $BegYDT  = strtotime(substr(trim($BegYDTs), 1, -1));
                    $BegMinYDT = strtotime(substr(trim($Row['level_begtime']), 1, -1));
                    $BegMaxYDT = strtotime(substr(trim($Row['level_maxbegtime']), 1, -1));
//                    echo ' BegYDT'.$BegYDT.' '.date("Y/m/d H/i/s", $BegYDT);
  //                  echo ' BegMinYDT'.$BegMinYDT.' '.date("Y/m/d H/i/s", $BegMinYDT);
    //                echo ' BegMaxYDT'.$BegMaxYDT.' '.date("Y/m/d H/i/s", $BegMaxYDT);

                    
                    // �������� ����� ������, ���� �� �� � ������� ���������� ��� ������ ����
                    if ($Row['level_starttype'] <> 1) // or empty($BegYear) or empty($BegDate) or empty($BegTime) or $BegYDTs == '0000-00-00 00:00:00')
                    {
		      $BegYDTs = "NULL";
                    }  else {
                      // �������� ������
		      if ($BegYDT < $BegMinYDT or $BegYDT > $BegMaxYDT)
		      {
			$statustext = $statustext."</br> ������ '".$Row['level_name']."'";
  		        $BegYDTs = "NULL";
                        // ������ �� �������, � ������ ����� NULL - ����� ����������� ��������� ����� ��
			//return;
		      } 
                    }




                    $Index = 'Level'.$Row['level_id'].'_endyear';
                    $EndYear = $_POST[$Index];
                    $Index = 'Level'.$Row['level_id'].'_enddate';
                    $EndDate = $_POST[$Index];
                    $Index = 'Level'.$Row['level_id'].'_endtime';
                    $EndTime = $_POST[$Index];

                    $EndYDTs =  "'".$EndYear."-".substr(trim($EndDate), -2)."-".substr(trim($EndDate), 0, 2)." ".substr(trim($EndTime), 0, 2).":".substr(trim($EndTime), -2).":00'";
                    //echo 'EndYDTs'.$EndYDTs;


                    $EndYDT  = strtotime(substr(trim($EndYDTs), 1, -1));
                    $EndMinYDT = strtotime(substr(trim($Row['level_minendtime']), 1, -1));
                    $EndMaxYDT = strtotime(substr(trim($Row['level_endtime']), 1, -1));

              //      echo ' EndYDT'.$EndYDT.' '.date("Y/m/d H/i/s", $EndYDT);
              //      echo ' EndMinYDT'.$EndMinYDT.' '.date("Y/m/d H/i/s", $EndMinYDT);
              //      echo ' EndMaxYDT'.$EndMaxYDT.' '.date("Y/m/d H/i/s", $EndMaxYDT);


                    // �������� ������
		    if ($EndYDT < $EndMinYDT or $EndYDT > $EndMaxYDT)
		    {
                        $EndYDTs  = "NULL";
			$statustext = $statustext."</br> ������ '".$Row['level_name']."'";
	//		return;
		    }

                    // �������� ������� � �������� �� ��������� ��� � ������ � ������� �����
                    $ArrLen = count(explode(',', $Row['level_pointnames']));
                    $Penalties = explode(',', $Row['level_pointpenalties']);
                    $TeamLevelPoints = '';
                    $PenaltyTime = 0;
		    for ($i = 0; $i < $ArrLen; $i++) 
		    {
			 $Index = 'Level'.$Row['level_id'].'_chk'.$i;
		         $Point = $_POST[$Index];

                         if  ($Point == 'on')
                         {
			    $TeamLevelPoints = $TeamLevelPoints.',1';
                            $PenaltyTime = $PenaltyTime + (int)$Penalties[$i];
                         } else {
			    $TeamLevelPoints = $TeamLevelPoints.',0';
                         }
                    }
                    $TeamLevelPoints = substr(trim($TeamLevelPoints), 1);


		    $Index = 'Level'.$Row['level_id'].'_comment';
                    $Comment = $_POST[$Index];


              // ���� ���� ������ - ��������, ��� - ��������� 
              if ($TeamLevelId > 0)
              {                

		  $sql = " update  TeamLevels  set teamlevel_begtime = ".$BegYDTs.", 
                                                   teamlevel_endtime = ".$EndYDTs.",
                                                   teamlevel_penalty = ".$PenaltyTime.",
                                                   teamlevel_points = '".$TeamLevelPoints."',  
                                                   teamlevel_comment = '".$Comment."'  
                           where teamlevel_id = ".$TeamLevelId."";

              } else {

		  $sql = " insert into  TeamLevels (team_id, level_id, teamlevel_begtime, teamlevel_endtime,
                                                    teamlevel_penalty, teamlevel_points, teamlevel_comment) 
					 values  (".$TeamId.", ".$Row['level_id'].", ".$BegYDTs.", 
                                                  ".$EndYDTs.", ".$PenaltyTime.", '".$TeamLevelPoints."', '".$Comment."')";
	      }

 	      //  echo $sql;
              // ��������� ����������� ������
              MySqlQuery($sql);  





           }
           // ����� ����� �� ������     
           mysql_free_result($rs);      

           if (trim($statustext) <> "") 
	   {
	      $statustext = "����� �� ���������� �������: ".trim($statustext);
           } 
           // ��������� ��������� �������
           RecalcTeamResult($TeamId);


   } else {
   // ���� ������� �������� �� ���������

   //  $statustext = "<br/>";
   }

	//print('view = *'.$view.'* action = '.$action);  


   
?>