<?

print('<div align = "center" class = "HeadText">���������� ����-������<br>
c����������� �� ����������� ��������������</div>'."\r\n");

print('<p align = "left" style = "text-align: justify;">�������� ����: ��������� ���������� ����������� ������� �������� �
�������� �� ����������� �������. ����� ���� ������������ � ������
������ ����� �� ������������� <a href = "http://www.kandid.ru">���������� ����-������</a> </p>'."\r\n");

print('<p align = "left" style = "text-align: justify;">���������� ����-������ ���������� ������ � ���: �
�������� ��� � � ����� �������. ������, ��� ������� �� ���� ������ �������������� � �������� (������������
��������� ��������) ����� ����. ������������ ��������� �������� �
������������ ������ �������������� � ��������� ������������� �� ���������.</p>'."\r\n");


//print(''."\r\n");

print('<table cellpadding = "10" border = "0" width = "100%">'."\r\n");
print('<tr class = "gray">'."\r\n");
print('<td>����-������</td>'."\r\n");
print('<td colspan = "2">����� � �����</td>'."\r\n");
print('<td>���������� � �����, ��������� ��������� (<a href = "#help">?</a>)</td>'."\r\n");
print('</tr>'."\r\n");

// ����������� ���������� � ����-�������
$resultRaids = MySqlQuery('SELECT * FROM Raids r WHERE raid_registrationenddate is not null ORDER BY raid_id  DESC');

$RaidsCount = mysql_num_rows($resultRaids);
while ($rowRaids = mysql_fetch_assoc($resultRaids)) {

        
	$RaidId = $rowRaids['raid_id'];
	$RaidName = trim($rowRaids['raid_name']);
	$RaidPeriod = trim($rowRaids['raid_period']);
	$RaidRulesLink = $rowRaids['raid_ruleslink'];
	$RaidStartLink = $rowRaids['raid_startlink'];
	$RaidStartPoint = $rowRaids['raid_startpoint'];
	$RaidFinishPoint = $rowRaids['raid_finishpoint'];
 
        if ($RaidsCount%2 == 0) {
	
	  $TrClass = 'yellow';
	
	} else {
	  
	  $TrClass = 'green';
	
	} 
	
	$RaidsCount--;
	
	//echo $i;
	print('<tr class = "'.$TrClass.'">'."\r\n");

 
	//class = "yellow"
        print('<td><a href = "javascript:document.FindTeamForm.RaidId.value='.$RaidId.';RaidTeams();">'.$RaidName.'</a></td>'."\r\n");

	print('<td><a href = "'.$RaidRulesLink.'">'.$RaidPeriod.'</td>'."\r\n");
        
	if (empty($RaidStartLink))
	{
		print('<td>'.$RaidStartPoint.' - '.$RaidFinishPoint.'</td>'."\r\n");
	} else {
		print('<td><a href = "'.$RaidStartLink.'">'.$RaidStartPoint.'</a> - '.$RaidFinishPoint.'</td>'."\r\n");
	
	}	
        
	print('<td>'."\r\n");
	 
	// ����������� ���������� � ����������
	$resultDistance = MySqlQuery('SELECT d.*,
					(
					 select count(team_id) 
					 from  Teams t 
					 where t.distance_id = d.distance_id
					            and t.team_hide = 0
					) as teamscount,
					COALESCE((
					 select sum(COALESCE(team_mapscount, 0)) 
					 from  Teams t 
					 where t.distance_id = d.distance_id
					            and t.team_hide = 0
					), 0) as teammapscount,
					(
					 select count(tu.teamuser_id) 
					 from  Teams t 
					          inner join  TeamUsers tu
					          on t.team_id = tu.team_id
					 where t.distance_id = d.distance_id
					            and t.team_hide = 0
					            and tu.teamuser_hide = 0
					) as teamuserscount
			            FROM  Distances d 
				    WHERE raid_id = '.$RaidId.'  ORDER BY distance_name ASC');
	
        $DistancesCount = 0;
         
	while ($rowDistance = mysql_fetch_assoc($resultDistance)) {
	
	        $DistancesCount++;
		
		if ($DistancesCount > 1) {
	
			print('<br>'."\r\n");
	
		}
	
		$DistanceName = $rowDistance['distance_name'];
		$DistanceLink = $rowDistance['distance_resultlink'];
		$DistanceData = $rowDistance['distance_data'];
		$DistanceCounters = '('.$rowDistance['teamscount'].'/'.$rowDistance['teammapscount'].'/'.$rowDistance['teamuserscount'].')';
	
	        print('<a href = "'.$DistanceLink.'">'.$DistanceName.'</a>: '.$DistanceCounters.' '.$DistanceData."\r\n");
	
	}
	mysql_free_result($resultDistance);
        print('</td>'."\r\n");
	print('</tr>'."\r\n");

}
mysql_free_result($resultRaids);
print('</table>'."\r\n");
//print('</body></html>'."\r\n");

print('<hr>'."\r\n");
print('<p align = "left" style = "text-align: justify;">'."\r\n");
print('<a name = "help">������ �� ����� �� ���������� ��� � ���� ������������� ���������, �� ������ - ������������ ����� ������ � ��������� ������� ���������:
 ���������� - �� ������ ����� ��������� ��,  � �������  - ���������� ��, ����� ���� - �����. 
 ���� ��������� ������� �� � � � - �������������� ������ ������ �� ���������� � �������.
 ����� ������ ��������� �� ����� �� ������ ���������� ����� ������ ������� ��������������, ���� �� ���� ����������� ���������, ���� �� ������� ���� � �.�.
 ����������� ����� ��������� (��� ���������) � ���������, � ����� ��������� ����� - �������� � ������� <a href="http://slazav.mccme.ru/maps/">"�����"</a>
'."\r\n");
print('</p>'."\r\n");

?>



