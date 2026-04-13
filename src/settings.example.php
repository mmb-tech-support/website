<?php
  // Основной скрипт
  $MyPHPScript = "/php/mmbscripts_git/";
  $MyLocation = "https://example.com/php/mmbscripts/";
  $MyHttpLink = "https://example.com/";
  $MyShortLink = "example.com";

  // Папка для загрузки файлов
  $MyStoreFileLink = '../../mmbfiles/';
  $MyStoreHttpLink = 'https://example.com/mmbfiles/';

  // Папка и файл для логирования
  $MyLogCatalog = '/var/log/php/';
  $MyLogDefaultFileName = 'mmbscripts.log';
  $MyLogTimestampFileName = 'mmbscripts.tsp';

  // Основные переменные
  $ServerName = '127.0.0.1';
  $WebUserName = 'mmb';
  $WebUserPassword = 'password';
  $DBName = 'mmb';

  // Логгирование
  $MinLogLevel = 'info';          // trace, debug, info, warning, error, critical
  $FatalErrorMail = 'admin@example.com';  // адрес, куда слать сообщения об ошибках, когда нет Sql

  // Режим отладки
  $DebugMode = 1;
  $ReadOnly = 0;            // режим только для чтения $ReadOnly = 1

  // Для желающих скрыть ФИО
  $Anonimus = 'Пользователь скрыл данные';

  // Настройки заголовков исходящей почты
  $MailFrom = 'mmb@example.com';
  $MailFromLabel = 'Робот сайта ММБ';
  $MailReturnPath = 'noreply@example.com';
  $MailReplyTo = 'info@example.com';

  $AcraLogPath = '/var/log/acra.log';
  $AndroidAppSqliteDirectory = '/var/backups/sportiduino-manager/';
?>
