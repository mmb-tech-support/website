<?php
// +++++++++++ Обработчик действий, связанных с результатом +++++++++++++++++++
// Предполагается, что он вызывается после обработчика teamaction,
// поэтому не проверяем сессию и прочее

// Выходим, если файл был запрошен напрямую, а не через include
if (!isset($MyPHPScript)) return;


function teamAddError($error)
{
	global $view, $viewmode;
	CMmb::setError($error, $view, 'ReturnAfterErrorTlp');
	$viewmode = "AddTlp";
}

function teamEditError($error)
{
	global $view, $viewmode;
	CMmb::setError($error, $view, 'ReturnAfterErrorTlp');
	$viewmode = "EditTlp";
}

function getLevelPointIdByName($pLevelPointName, $distanceId) {
	global $RaidId;
	$sql = "select levelpoint_id from LevelPoints where distance_id = ".intval($distanceId)." and levelpoint_name = ". CSql::quote($pLevelPointName);
        $Result = MySqlQuery($sql);
	$row_cnt = mysqli_num_rows($Result);
	if ($row_cnt != 1) {
		throw new Exception("точка не найдена по названию [$pLevelPointName], results_cnt: $row_cnt");
	}
	$row = mysqli_fetch_assoc($Result);
	return intval($row['levelpoint_id']);
}

function getDistanceIdAndTeamIdForTeamNum($pTeamNum) {
	global $RaidId;
	$sql = "select team_id, distance_id from Teams where distance_id in (select distance_id from Distances where raid_id = ".intval($RaidId).") and team_num = ". intval($pTeamNum);
        $Result = MySqlQuery($sql);
	$row_cnt = mysqli_num_rows($Result);
	if ($row_cnt != 1) {
		throw new Exception("команда [$pTeamNum] не найдена, results_cnt: $row_cnt");
	}
	$row = mysqli_fetch_assoc($Result);
	return ['teamId' => intval($row['team_id']), 'distanceId' => intval($row['distance_id'])];
}

function parseDate($dateStr) {
	// 2025-10-24 21:26:49
	$format = 'Y-m-d H:i:s';
	$date = DateTime::createFromFormat($format, $dateStr);
	if ($date === false || $date->format($format) !== $dateStr) {
		throw new Exception("неверный формат даты");
	}
	return CSql::timeString($date->format('Y'), $date->format('dm'), $date->format('His'), false);
}

function AddTlp($pTeamId, $pLevelPointId, $pTlpComment, $pErrorId, $TlpYDTs) {
	global $UserId, $RaidID;
	$errs = [];
        // тут по-хорошему нужны проверки
	if ($pTeamId <= 0 or $pLevelPointId <= 0)
	{
		array_push($errs, 'Не определён ключ команды или ключ точки для результата.');
		//$viewmode = "EditTlp";
		return $errs;
	}


        $sql = " select count(*) as countresult
	          from TeamLevelPoints
	          where team_id = $pTeamId
		        and levelpoint_id = $pLevelPointId";

	if  (((int) CSql::singleValue($sql, 'countresult')) > 0)
	{
		array_push($errs, 'Результаты по точке уже есть.');
		return $errs;
	}


        // потом добавить время макс. и мин.

	$sql = "insert into TeamLevelPoints (team_id, levelpoint_id,
                device_id,
		teamlevelpoint_datetime, teamlevelpoint_comment, error_id, teamlevelpoint_date)
		values ($pTeamId, $pLevelPointId, 1,
		        $TlpYDTs, '$pTlpComment', $pErrorId, NOW())";


	//  echo $sql;
	// При insert должен вернуться послений id - это реализовано в MySqlQuery
	$TeamLevelPointId = MySqlQuery($sql);

	if ($TeamLevelPointId <= 0)
	{
		array_push($errs, 'Ошибка записи нового результата для точки.');
		return $errs;
	}

/*
	 $statustext = CheckLevelPoints($DistanceId);
	 if (!empty($error))
	 {
		$alert = 1;
	 }
*/

	// Временно отключил, так как при больших правках тормозит сильно.
	// Пересчет результатов срабатывает при редактировании данных, ну и общий пересчет по всем командам, конечно
        // RecalcTeamResultFromTeamLevelPoints(0,  $pTeamId);


	//$view = $_POST['view'];
	//if (empty($view)) $view = "ViewTeamData";
	return $errs;
}

//echo 'action '.$action;

// =============== Изменение/добавление результатов команды ===================
if ($action == "ChangeTeamResult")
{
	//$view = "ViewTeamData";
	$viewmode = "";
	if ($TeamId <= 0) return;

	// Проверка возможности редактировать результаты
	if (!CanEditResults($Administrator, $Moderator, $TeamUser, $OldMmb, $RaidStage, $TeamOutOfRange))
	{
		$statustext = 'Изменение результатов команды запрещено';
		return;
	}

	// Получаем информацию об этапах, которые могла проходить команда
	$sql = "select l.level_id, l.level_name, l.level_pointnames, l.level_starttype, l.level_pointpenalties,
		l.level_begtime, l.level_maxbegtime, l.level_minendtime, l.level_endtime,
		tl.teamlevel_begtime, tl.teamlevel_endtime,
		tl.teamlevel_points, tl.teamlevel_progress, tl.teamlevel_penalty,
		tl.teamlevel_id
		from Teams t
			inner join Distances d on t.distance_id = d.distance_id
			inner join Levels l on d.distance_id = l.distance_id
			left outer join TeamLevels tl on l.level_id = tl.level_id and t.team_id = tl.team_id and tl.teamlevel_hide = 0
		where l.level_hide = 0  and t.team_id = $TeamId";
	$rs = MySqlQuery($sql);

	// ================ Цикл обработки данных по этапам
	$statustext = "";
	while ($Row = mysqli_fetch_assoc($rs))
	{
		// По этому ключу потом определяем, есть ли уже строчка в TeamLevels или её нужно создать
		$TeamLevelId = $Row['teamlevel_id'];

		$levelPfx = 'Level'.$Row['level_id'];

		// Обрабатываем сход с этапа
		$TeamLevelProgress = mmb_validateInt($_POST, $levelPfx.'_progress', 0);

		// Вычисляем время выхода на этап
		$BegYear = mmb_validate($_POST, $levelPfx.'_begyear', '');
		$BegDate = mmb_validate($_POST, $levelPfx.'_begdate', '');
		$BegTime = mmb_validate($_POST, $levelPfx.'_begtime', '');
		$BegYDTs = CSql::timeString($BegYear, $BegDate, $BegTime);

		// Конвертируем в даты php для сравнения
		$BegYDT = strtotime(substr(trim($BegYDTs), 1, -1));
		$BegMinYDT = strtotime(substr(trim($Row['level_begtime']), 1, -1));
		$BegMaxYDT = strtotime(substr(trim($Row['level_maxbegtime']), 1, -1));
		// Обнуляем время старта, если он не в порядке готовности
		if ($Row['level_starttype'] <> 1)
		{
			$BegYDTs = "NULL";
		}
		else
		// Проверка границ старта
		{
			if ($BegYDT < $BegMinYDT or $BegYDT > $BegMaxYDT)
			{
				if ($TeamLevelProgress > 0)
					$statustext = $statustext."</br> старта '{$Row['level_name']}'";
				// Теперь не выходим, а ставим время NULL - вдруг пользователь сохранить хотел КП
				$BegYDTs = "NULL";
			}
		}

		// Вычисляем время финиша на этапе
		$EndYear = $_POST[$levelPfx.'_endyear'];
		$EndDate = $_POST[$levelPfx.'_enddate'];
		$EndTime = $_POST[$levelPfx.'_endtime'];
		$EndYDTs = CSql::timeString($EndYear, $EndDate, $EndTime);

		// Конвертируем в даты php для сравнения
		$EndYDT = strtotime(substr(trim($EndYDTs), 1, -1));
		$EndMinYDT = strtotime(substr(trim($Row['level_minendtime']), 1, -1));
		$EndMaxYDT = strtotime(substr(trim($Row['level_endtime']), 1, -1));
		// Проверка границ финиша
		if ($EndYDT < $EndMinYDT or $EndYDT > $EndMaxYDT)
		{
			$EndYDTs = "NULL";
			if ($TeamLevelProgress > 1)
				$statustext = $statustext."<br/> финиша '{$Row['level_name']}'";
		}

		// Ставим флаг "Дошла до конца этапа", если у нас есть корректное время финиша команды
		if (($EndYDTs <> "NULL") && !$TeamLevelProgress) $TeamLevelProgress = 2;

		// Получаем отметки о невзятых КП переводим его в строку
               // Штраф и результат считаем для всех этапов отдельно (после записи данных)
		$ArrLen = count(explode(',', $Row['level_pointnames']));
		//$Penalties = explode(',', $Row['level_pointpenalties']);
		$TeamLevelPoints = '';
		$PenaltyTime = 0;
		$points = array();
		for ($i = 0; $i < $ArrLen; $i++)
			$points[] = mmb_isOn($_POST, $levelPfx.'_chk'.$i);

		$TeamLevelPoints = implode(", ", $points);

		// Обрабатываем комментарии
		$Comment = $_POST[$levelPfx.'_comment'];
		$connectionId = CSql::getConnection();

		if ($Comment == "") $Comment = "NULL"; else $Comment = "'" . mysqli_real_escape_string($connectionId, $Comment) . "'";


		// Если есть запись в базе - изменяем, нет - вставляем
		if ($TeamLevelId > 0)
		{
			$sql = "update TeamLevels set
					teamlevel_begtime = $BegYDTs,
					teamlevel_endtime = $EndYDTs,
					teamlevel_points = '$TeamLevelPoints',
					teamlevel_progress = '$TeamLevelProgress',
					teamlevel_comment = $Comment,
					error_id = NULL
				where teamlevel_id = $TeamLevelId";
		}
		else
		{
			$sql = "insert into TeamLevels (team_id, level_id, teamlevel_begtime, teamlevel_endtime, teamlevel_points, teamlevel_comment, teamlevel_progress)
					values ($TeamId, {$Row['level_id']}, $BegYDTs, $EndYDTs, '$TeamLevelPoints', $Comment, $TeamLevelProgress)";
		}
		MySqlQuery($sql);
	}
	// Конец цикла по этапам
	mysqli_free_result($rs);

	if (trim($statustext) <> "")
		$statustext = "Выход за допустимые границы: ".trim($statustext);

	// Пересчет врмени нахождения команды на этапах
	RecalcTeamLevelDuration($TeamId);
	// Пересчет штрафов
	RecalcTeamLevelPenalty($TeamId);

        //  10/06/2014 если старцый ММБ. то не обновляем результат
	// Обновляем результат команды
	if (!$OldMmb) {
		RecalcTeamResult($TeamId);
	}

        //Если передали альтернативную страницу, на которую переходить (пока только одна возможность - на список команд)
	$view = $_POST['view'];
	if (empty($view)) $view = "ViewTeamData";


}
// ============ Добавить точку  =============
elseif ($action == 'AddTlp')
{
	if (!CRights::canEditPointResult($UserId, $RaidId, $TeamId))
	{
		CMmb::setMessage('Нет прав на ввод  результата для точки');
		return;
	}

	$pTeamId = mmb_validateInt($_POST, 'TeamId');
	$pLevelPointId = mmb_validateInt($_POST, 'LevelPointId');
        $pTlpComment = $_POST['TlpComment'];
	$pErrorId = mmb_validateInt($_POST, 'ErrorId', -1);
	$TlpYDTs = CSql::timeString2($_POST, 'TlpYear', 'TlpDate', 'TlpTime', false);

	$res = AddTlp($pTeamId, $pLevelPointId, $pTlpComment, $pErrorId, $TlpYDTs);
	foreach($res as $err) {
		teamAddError($err);
	}

 }
 elseif ($action == 'AddTlpBatch')
{
        CMmb::setViews('ViewAdminMultiInputPage', '');
	if (!CRights::canEditPointResult($UserId, $RaidId, 0))
	{
		CMmb::setMessage('Нет прав на ввод  результата для точки');
		return;
	}
	$punches = stripcslashes(stripcslashes($_POST['punches']));
	if (isset($_FILES['csvFile'])) {
		$file = $_FILES['csvFile'];
		$fileError = $file['error'];
		$fileTmpPath = $file['tmp_name'];
		if ($fileError === UPLOAD_ERR_OK) {
	        	$punches = file_get_contents($fileTmpPath);
		}
	}
	$processed_punches = [];
	foreach(explode("\n", $punches) as $punch) {
		$punch = trim($punch);
		$punch_copy = $punch;
		$punch = trim(explode("#", $punch)[0]);
		$punch_data = explode("\t", str_replace(";", "\t", $punch));
		if (sizeof($punch_data) == 0 || ! is_numeric($punch_data[0])) {
			// header
			array_push($processed_punches, $punch_copy);
		} else {
			try {
				if (count($punch_data) < 3) {
					throw new Exception('Недостаточно данных');
				}
				$pTeamNum = intval($punch_data[0]);
				$pLevelPointName = $punch_data[1];
				$pLevelPointDateTime = $punch_data[2];
				$distanceIdAndTeamId = getDistanceIdAndTeamIdForTeamNum($pTeamNum);
				$pTeamId = $distanceIdAndTeamId['teamId'];
				$distanceId = $distanceIdAndTeamId['distanceId'];
				$pLevelPointId = getLevelPointIdByName($pLevelPointName, $distanceId);
				$TlpYDTs = parseDate($punch_data[2]);
				$res = AddTlp($pTeamId, $pLevelPointId, '', 0, $TlpYDTs);
				if (sizeof($res) == 0) {
					array_push($processed_punches, "#" . $punch);
				} else {
					array_push($processed_punches, $punch . ' # ' . implode(', ', $res));
				}
			} catch(Exception $e) {
				array_push($processed_punches, $punch . ' # ' . $e->getMessage());
			}
		}
	}
}
 elseif ($action == "TlpInfo")
 {
    // Действие вызывается кнопокй "Править" в таблице точек

	$view = $_POST['view'];
	if (empty($view)) $view = "ViewTeamData";
	$viewmode = "EditTlp";
 }
 // ============ Правка точки  =============
elseif ($action == 'ChangeTlp')
{
//	if (!$Administrator && !$Moderator)
	if (!CRights::canEditPointResult($UserId, $RaidId, $TeamId))
	{
		CMmb::setMessage('Нет прав на правку результата для точки');
		return;
	}

        $pTeamLevelPointId = mmb_validateInt($_POST, 'TeamLevelPointId');


	if ($pTeamLevelPointId <= 0)
	{
		teamEditError('Не определён ключ результата для точки.');
		return;
	}



	$pLevelPointId = mmb_validateInt($_POST, 'LevelPointId');
	$pTeamId = mmb_validateInt($_POST, 'TeamId');
        $pTlpComment = $_POST['TlpComment'];
	$pErrorId = mmb_validateInt($_POST, 'ErrorId', -1);

	$TlpYDTs = CSql::timeString2($_POST, 'TlpYear', 'TlpDate', 'TlpTime', false);

	if ($pTeamId <= 0 or $pLevelPointId <= 0)
	{
		teamEditError('Не определён ключ команды или ключ точки для результата.');
		return;
	}


	$sql = " select count(*) as countresult
	          from TeamLevelPoints
	          where team_id = $pTeamId
		        and levelpoint_id = $pLevelPointId
			and teamlevelpoint_id <> $pTeamLevelPointId";

	if  (CSql::singleValue($sql, 'countresult') > 0)
	{
		CMmb::setErrorSm('Результаты по точке уже есть.', 'ReturnAfterErrorTlp');
		return;
	}






        $sql = "update TeamLevelPoints  set levelpoint_id = $pLevelPointId
	                                ,team_id = $pTeamId
	                                ,error_id = $pErrorId
	                                ,teamlevelpoint_comment = '$pTlpComment'
	                                ,teamlevelpoint_datetime = $TlpYDTs
	                                ,teamlevelpoint_date = NOW()
	        where teamlevelpoint_id = $pTeamLevelPointId";

	//echo $sql;

	 MySqlQuery($sql);


         RecalcTeamResultFromTeamLevelPoints(0,  $pTeamId);
//         RecalcTeamResultFromTeamLevelPoints(25, 0);

    /*
	 $statustext = CheckLevelPoints($DistanceId);
	 if (!empty($error))
	 {
		$alert = 1;
	 }
	*/

}
// ============ Удаление точки  =============
elseif ($action == 'HideTlp')
{
//	if (!$Administrator && !$Moderator)
	if (!CRights::canEditPointResult($UserId, $RaidId, $TeamId))
	{
		CMmb::setMessage('Нет прав на правку результата для точки');
		return;
	}

        $pTeamLevelPointId = mmb_validateInt($_POST, 'TeamLevelPointId');
        $pTeamId = mmb_validateInt($_POST, 'TeamId');


	if ($pTeamId <= 0)
	{
		teamEditError('Не определён ключ команды.');
		return;
	}



	if ($pTeamLevelPointId <= 0)
	{
		teamEditError('Не определён ключ результата для точки.');
		return;
	}


        $sql = "delete from TeamLevelPoints where teamlevelpoint_id = $pTeamLevelPointId";


	 MySqlQuery($sql);


         RecalcTeamResultFromTeamLevelPoints(0,  $pTeamId);


/*
	 $statustext = CheckLevelPoints($DistanceId);
	 if (!empty($error))
	 {
		$alert = 1;
	 }
*/



}
else
{
}

?>
