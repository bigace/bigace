<?php
/**
* Translation file for the installer.
* Language: German
* Copyright (C) Kevin Papst.
*
* For further information go to {@link http://www.bigace.de http://www.bigace.de}.
*
* @version $Id$
* @author Kevin Papst
*/

if(!defined('_BIGACE_INSTALL_PARENT_')) {
    die('Not runnable alone, go to '.dirname(__FILE__).'/index.php');
}

$LANG['title'] = 'BIGACE '._BIGACE_ID;
$LANG['intro'] = '... verwaltet Ihren Content spielend leicht!';
$LANG['thanks'] = 'Danke f&uuml;r die Installation des BIGACE CMS!';

// -----------------------------------------------------------------------------
// Navigation
$LANG['menu_title'] = 'Installations System';
$LANG['menu_step_2'] = '&Uuml;berpr&uuml;fe Einstellungen';
$LANG['menu_step_3'] = 'Installation';
$LANG['menu_step_4'] = 'Community erstellen';
$LANG['menu_step_5'] = 'Installation war erfolgreich';
$LANG['menu_step'] = 'Schritt';
// -----------------------------------------------------------------------------

// -----------------------------------------------------------------------------
// Welcome Screen
$LANG['install_state'] = 'Status';
$LANG['install_begin'] = 'Installation starten';
$LANG['introduction'] = 'Einleitung';
// -----------------------------------------------------------------------------

// -----------------------------------------------------------------------------
// Form Tooltip
$LANG['form_tip_close'] = 'Schlie&szlig;en';
$LANG['form_tip_hide'] = 'Diese Hilfe nicht mehr anzeigen';
// -----------------------------------------------------------------------------

// -----------------------------------------------------------------------------
// LANGUAGES - chooser and names for languages
$LANG['language_choose'] = 'Sprachauswahl';
$LANG['language_text'] = 'Hier k&ouml;nnen Sie die Sprache einstellen, die w&auml;hrend der Installation benutzt wird.';
$LANG['language_button'] = 'Sprache wechseln';

$LANG['language_de'] = 'Deutsch (German)';
$LANG['language_en'] = 'English';
$LANG['language_it'] = 'Italiano (Italian)';
$LANG['language_se'] = 'Svenska (Swedish)';
$LANG['language_fi'] = 'Suomeksi (Finnish)';
$LANG['language_ro'] = 'Românã (Romanian)';
$LANG['language_tr'] = 'Türkçe (Turkish)';
$LANG['language_hr'] = 'Hrvatski (Croatian)';
$LANG['language_ru'] = 'Русский (Russian)';
$LANG['language_pt'] = 'Português (Portuguese)';
$LANG['language_nl'] = 'Nederlandse (Dutch)';
$LANG['language_es'] = 'Español (Spanish)';
$LANG['language_si'] = 'Slovensko (Slovene)';
// -----------------------------------------------------------------------------

$LANG['failure'] = 'Fehler traten auf';
$LANG['new'] = 'Neu';
$LANG['old'] = 'Alt';
$LANG['successfull'] = 'Erfolgreich';
$LANG['main_menu'] = 'Hauptmen&uuml;';
$LANG['back'] = 'Zur&uuml;ck';
$LANG['next'] = 'Weiter';
$LANG['state_no_db'] = 'Die Datenbank scheint noch nicht installiert zu sein!';
$LANG['state_not_all_db'] = 'Die Datenbank scheint nicht vollst&auml;ndig installiert zu sein!';
$LANG['state_installed'] = 'Basissystem erfolgreich installiert!';
$LANG['help_title'] = 'Hilfe';
$LANG['help_text'] = 'Wenn Sie weitere Informationen zu den einzelnen Schritten ben&ouml;tigen, fahren Sie mit Ihrem Mauszeiger &uuml;ber das jeweilige Hilfezeichen hinter dem Eingabefeld, wo eine kurze Hilfemeldung erscheinen wird!<br>Zur Demonstration halten Sie Ihren Mauszeiger jetzt &uuml;ber folgendes Bild:';
$LANG['help_demo'] = 'Genau so sehen Sie Ihre Hilfetexte!';
$LANG['db_install'] = 'CMS installieren';
$LANG['cid_install'] = 'Einstellungen ihrer Webseite';
$LANG['install_finish'] = 'Installation abschliessen';
$LANG['db_install_state'] = 'Status: ';
$LANG['db_install_help'] = 'Hier installieren Sie Ihr CMS. Sie konfigurieren die Datenbankverbindung und grundelegende Einstellungen f&uuml;r die erste Webseite (Community)';

//-----------------------------------------------------------
// Translation for the System Installation Dialog
$LANG['db_value_title'] = 'Datenbank Verbindung';
$LANG['ext_value_title'] = 'System Konfiguration';
$LANG['db_type'] = 'Datenbank Typ';
$LANG['db_host'] = 'Server/Host';
$LANG['db_database'] = 'Datenbank';
$LANG['db_user'] = 'Benutzer';
$LANG['db_password'] = 'Passwort';
$LANG['db_prefix'] = 'Tabellen Prefix';
$LANG['mod_rewrite'] = 'Apache MOD-Rewrite';
$LANG['mod_rewrite_yes'] = 'Nutzung m&ouml;glich / Mod-Rewrite aktiv (.htaccess)';
$LANG['mod_rewrite_no'] = 'Nutzung nicht m&ouml;glich / Unbekannt';
$LANG['base_dir'] = 'Basis Verzeichnis';
// Translation for the System Installation Help Images
$LANG['base_dir_help'] = 'Hier geben Sie den Basis Pfad der BIGACE Installation ein. Wenn Sie BIGACE im Hauptverzeichniss Ihres Webauftritts installiert haben (z.B. direkt unter &quot;http://www.example.com/&quot;) super, lassen Sie diesen Punkt leer. Andernfalls m&uuml;ssen Sie den Pfad von Domain zur BIGACE Installation angeben.<br><b>Das System berechnet den Pfad selbstst&auml;ndig, Sie sollten daher diesen Punkt nicht manuell &auml;ndern zu m&uuml;ssen, falls er vorgef&uuml;llt ist.</b><br>Der Pfad darf NICHT mit einem Slash beginnen und MUSS mit einem Slash enden (ansonsten wird es Fehler geben)!<br>Nehmen wir an, Sie haben unter &quot;http://www.example.com/cms/&quot; installiert, dann geben Sie &quot;cms/&quot; ein.<br><b>Richtig: cms/</b><br><b>Falsch: /cms</b>';
$LANG['mod_rewrite_help'] = '<b>Diese Einstellung erm&ouml;glicht freundliche URLs!</b><br/>Stellen Sie hier bitte ein, ob Ihr System das Apache Modul Mod-Rewrite aktiviert ist und diese Funktion via .htaccess Dateien genutzt werden kann. Zus&auml;tzlich ist es notwendig, das .htaccess Dateien geparst werden. Sollte dies nicht der Fall sein, und sie w&auml;hlen dennoch das es m&ouml;glich ist, werden Sie Ihr System nicht benutzen k&ouml;nnen (andersherum ist kein Problem)! Diese Einstellung ist &uuml;ber einen Konfigurationseintrag anpassbar. Sollten Sie sich nicht sicher sein, lassen Sie diese Einstellung wie sie ist.';
$LANG['def_language'] = 'Standard Sprache';
$LANG['def_language_help'] = 'Hier stellen Sie die Standard Sprache Ihres CMS ein. Sie k&ouml;nnen diesen Wert sp&auml;ter &uuml;ber eine Konfigurations Eintrag anpassen.';
$LANG['db_type_help'] = 'W&auml;hlen Sie hier die eingesetzte Datenbank aus.<br>Die Installation unterst&uuml;tzt alle angezeigten Datenbanken, das Core System <b>unterst&uuml;tzt momentan NUR MySQL</b> vollst&auml;ndig.<br>Sollten Sie sich f&uuml;r eine andere Datenbank entscheiden, passiert dies ausdr&uuml;klich auf Ihr eigenes Risiko!';
$LANG['db_host_help'] = 'Tragen Sie hier den Namen des Server ein, auf dem Ihre Datenbank installiert ist.';
$LANG['db_database_help'] = 'Tragen Sie hier den Namen Ihrer Datenbank ein. In phpMyAdmin ist dies der Name den Sie im linken Frame ausw&auml;hlen!';
$LANG['db_user_help'] = 'Tragen Sie hier den Benutzer ein, der Schreibzugriffe auf Ihre Datenbank hat.';
$LANG['db_prefix_help'] = 'Tragen Sie hier das Tabellen Prefix ein, den die DB Tabellen tragen sollen. So sind diese stets eindeutig identifizierbar und sie k&ouml;nnten sogar mehrere Installationen parallel betreiben. Wenn Sie nicht wissen was das soll, &uuml;bernehmen Sie einfach die Standardwerte!';
$LANG['db_password_help'] = 'Tragen Sie hier das Passwort ein, welches Ihrem Datenbankbenutzer zugeordnet ist.';
$LANG['db_already_exists'] = 'Die Datenbank scheint bereits zu existieren, Schritt wird &uuml;bersprungen.';

$LANG['htaccess_security'] = 'Apache .htaccess Funktion';
$LANG['htaccess_security_yes'] = 'Nutzung m&ouml;glich / Allow override aktiv (.htaccess)';
$LANG['htaccess_security_no'] = 'Nicht m&ouml;glich / Unbekannt';
$LANG['htaccess_security_help'] = '<b>Diese Einstellung dient der Sicherheit Ihrer Daten!</b><br/>Stellen Sie sicher das Ihr Webserver Override All mit .htaccess Dateien unterst&uuml;tzt. Wenn Sie sich nicht sicher sind, lassen Sie diese Einstellung wie sie ist!';
//-----------------------------------------------------------

// Translation for Consumer Installation
// First Dialog
$LANG['error_enter_domain'] = 'Bitte geben Sie eine korrekte Domain an, unter der die neue Community erreichbar sein soll.';
$LANG['error_enter_adminuser'] = 'Bitte geben Sie einen Namen f&uuml;r den Administrator ein (mindestens 4 Zeichen).';
$LANG['error_enter_adminpass'] = 'Bitte geben Sie ein Passwort f&uuml;r den Administrator ein (mindestens 6 Zeichen), best&auml;tigen Sie Ihre Eingabe durch Wiederholung.';

$LANG['cid_check_failed'] = 'Die &Uuml;berpr&uuml;fung der Dateirechte war nicht erfolgreich! Sie m&uuml;ssen diese unbedingt korrigieren BEVOR Sie fortfahren k&ouml;nnen!';$LANG['cid_id'] = 'Community ID';
$LANG['cid_domain'] = 'Community Domain';
$LANG['cid_id_help'] = 'W&auml;hlen Sie die ID f&uuml;r die neue Community aus (diese wird nur intern verwendet). Sollten Sie eine bestehende Community migrieren wollen, ist es am besten hier dessen alte Community ID auszuw&auml;hlen!';
$LANG['cid_domain_help'] = 'Tragen Sie hier die Domain ein, auf der die neue Community laufen soll. Der automatisch gefundene Wert sollte korrekt sein.<br><b>HINWEIS: TRAGEN SIE KEINEN PFAD ODER ABSCHLIESSENDES SLASH EIN!</b>';

$LANG['statistics'] = 'Statistiken';
$LANG['statistics_on'] = 'Statistiken schreiben';
$LANG['statistics_off'] = 'Statistiken NICHT schreiben';
$LANG['statistics_help'] = 'Hier w&auml;hlen Sie, ob Sie das Schreiben von Statistiken einschalten wollen oder nicht. Wenn Sie Statistiken einschalten, wird bei jedem Seitenaufruf ein schreibender Datenbankzugriff ausgef&uuml;hrt.';
$LANG['sitename'] = 'Webseiten Name';
$LANG['sitename_help'] = 'Tragen Sie hier den Namen Ihrer Webseite oder deren Titel ein. Dieser Wert kann sp&auml;ter im Template genutzt und &uuml;ber die Administration einfach ge&auml;ndert werden.';
$LANG['mailserver'] = 'Mail Server';
$LANG['mailserver_help'] = 'Tragen Sie hier den Mailserver ein, &uuml;ber den Sie Emails verschicken werden. Lassen Sie dieses Feld leer, wenn Sie den PHP konfigurierten Standard Proxy benutzen (f&uuml;r die meisten Shared-hosting Systeme).';
$LANG['webmastermail'] = 'Email Adresse';
$LANG['webmastermail_help'] = "Tragen Sie hier die Email Adresse des Admistrators der neuen Community ein.";
$LANG['bigace_admin'] = 'Benutzername';
$LANG['bigace_password'] = 'Passwort';
$LANG['bigace_check'] = 'Passwort [best&auml;tigen]';
$LANG['bigace_admin_help'] = 'Tragen Sie hier den Benutzernamen des neuen Administrator Accounts ein.';
$LANG['bigace_password_help'] = 'Tragen Sie hier das Passwort f&uuml;r Ihren Administrator Zugang ein.';
$LANG['bigace_check_help'] = 'Bitte best&auml;tigen Sie das gew&auml;hlte Passwort. Sollte sich keine &Uuml;bereinstimmung ergeben, kehren Sie hierher zur&uuml;ck.';
$LANG['create_files'] = 'Erstelle Dateisystem';
$LANG['save_cconfig'] = 'Speichere Community Konfiguration';
$LANG['added_consumer'] = 'Community hinzugef&uuml;gt';
$LANG['added_consumer'] = 'Bestehende Community hinzugef&uuml;gt';
$LANG['community_exists'] = 'Es existiert bereits eine Community f&uuml;r die angegebene Domain, bitte geben Sie eine andere Domain an.';

$LANG['check_reload'] = 'Vorab Check erneut ausf&uuml;hren';
$LANG['check_up'] = 'Vor-Check';
$LANG['check_up_help'] = 'BEACHTEN SIE: Bitte f&uuml;hren Sie den Vorab Check aus, BEVOR Sie damit beginnen das CMS zu installieren oder eine Community anzulegen!';

$LANG['required_empty_dirs'] = 'Benötigte Verzeichnisse';
$LANG['empty_dirs_description'] = 'Die folgenden Verzeichnisse werden von BIGACE benötigt, konnten jedoch nicht automatisch erstellt werden. Bitte erstellen Sie diese manuell:';
$LANG['check_yes'] = 'Ja';
$LANG['check_no'] = 'Nein';
$LANG['check_on'] = 'Ein';
$LANG['check_off'] = 'Aus';
$LANG['check_status'] = 'Status';
$LANG['check_setting'] = 'Einstellung';
$LANG['check_recommended'] = 'Empfohlen';
$LANG['check_install_help'] = 'Wenn eines dieser Einstellungen rot markiert ist, dann m&uuml;ssen Sie Ihre Apache/PHP Konfiguration korrigieren. Sollte dies nicht geschehen, wird es zu einer fehlerhaften Installation f&uuml;hren.';
$LANG['required_settings_title'] = 'Ben&ouml;tigte Einstellungen';
$LANG['check_settings_title'] = 'Empfohlene Einstellungen';
$LANG['check_settings_help'] = 'Dies sind die empfohlenen Einstellungen, f&uuml;r eine reibungslose BIGACE Installation. BIGACE wird grunds&auml;tzlich funktionieren, auch wenn nicht alle Werte &uuml;bereinstimmen. <br><br>Wir empfehlen jedoch immer, angezeigte Probleme, wenn m&ouml;glich, vor der Installation zu korrigieren.';
$LANG['check_files_title'] = 'Verzeichnis- und Dateirechte';
$LANG['check_files_help'] = 'Damit das System richtig funktioniert, sind Schreibrechte zu folgenden Verzeichnissen/Dateien erforderlich. Wenn Sie &quot;unbeschreibbar&quot; (Nein) sehen, m&uuml;ssen Sie die Zugriffsrechte korrigieren.';

$LANG['config_consumer'] = 'Community Einstellungen';
$LANG['config_admin'] = 'Administrator Account';

$LANG['community_install_good'] = '
<p>Glückwunsch, die Installation ist fertig!</p>
<p>Wenn Sie zu irgendeinem Zeitpunkt Hilfe benötigen, oder BIGACE nicht mehr wie gewohnt reagiert, denken Sie bitte daran, das <a href="http://forum.bigace.de" target="_blank">Hilfe immer vorhanden ist</a> wenn Sie sie benötigen.
<p>Das Installations Verzeichnis existiert noch. Es ist aus Sicherheitsgründen eine gute Idee, dieses komplett zu löschen.</p>
<p>Jetzt können Sie <a href="../../">Ihre neue Webseite ansehen</a> und damit beginnen sie zu nutzen. Sie sollten sich zuerst am System anmelden, wonach Sie dann Zugang zum Administrations Bereich erlangen.</p>
<br />
<p>Viel Spaß und Erfolg!</p>
<br /><br />
<p><a href="../../">Weiter zu Ihrer Webseite</a></p>';

$LANG['community_install_bad'] 	    = 'Es traten Fehler w&auml;hrend der Community Installation auf.';
$LANG['community_install_infos'] = 'System Meldungen anzeigen...';

$LANG['error_db_connect'] = 'Abbruch: Konnte keine Verbindung zum Datenbank-Host herstellen';
$LANG['error_db_select'] = 'Abbruch: Konnte Datenbank nicht selektieren';
$LANG['error_db_create'] = 'Konnte Datenbank nicht erstellen.';
$LANG['error_read_dir'] = 'Konnte Verzeichniss nicht lesen. (Rechte korrekt gesetzt?)';
$LANG['error_created_dir'] = 'Konnte Verzeichniss nicht erstellen';
$LANG['error_removed_dir'] = 'Konnte Verzeichniss nicht l&ouml;schen';
$LANG['error_copied_file'] = 'Konnte Datei nicht kopieren';
$LANG['error_remove_file'] = 'Konnte Datei nicht l&ouml;schen';
$LANG['error_close_file'] = 'Konnte Datei nicht schliessen';
$LANG['error_open_file'] = 'Fehler: Konnte Datei nicht &ouml;ffnen';
$LANG['error_db_statement'] = 'Fehler in DB Statement';
$LANG['error_open_cconfig'] = 'Konnte Community Konfigurations Datei nicht &ouml;ffnen';
$LANG['error_double_cconfig'] = 'Fehler: Community besteht bereits!';
$LANG['could_not_find_consumer'] = 'Fehler: Konnte Community nicht finden';
