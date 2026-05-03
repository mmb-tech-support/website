<?php
// +++++++++++ Пакетная загрузка данных ++++++++++++++++++++++++++++++++++++++++

// Выходим, если файл был запрошен напрямую, а не через include
if (!isset($MyPHPScript)) return;

?>

<style>
textarea#multiinput-data {
  width: 100%;
  min-height: 300px;
  display: inline-block;
}
</style>
<form name="BatchTlpForm" action="<?php echo $MyPHPScript; ?>" method="POST" enctype="multipart/form-data">
<input type="hidden" name="action" value="AddTlpBatch" />
<input type="hidden" name="view" value="" />
<input type="hidden" name="RaidId" value="<?php echo $RaidId; ?>" />

<div>
	<button type="submit" id="multiinput-button">Загрузить данные</button>
</div>
<div>
<label>Выберите CSV-файл: <input type="file" name="csvFile"></label></div>
<div><b>ИЛИ вставить содержимое в форму:</b></div>
<div>
	<span>Разделитель: <b>;</b> или <b>TAB</b>, начиная с # -- комментарий</span>
</div>
<textarea name="punches" id="multiinput-data" placeholder="team_num;levelpoint_name;teamlevelpoint_datetime
411;1;2025-10-24 21:26:49"><?php
	if (isset($processed_punches)) {
		echo implode("\n", $processed_punches);
	}
?></textarea>
</form>
<br/>



