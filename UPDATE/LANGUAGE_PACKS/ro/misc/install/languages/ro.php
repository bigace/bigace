<?php
/**
 * Translation file for the installer.
 * Language: Romanian
 *
 * For further information go to {@link http://www.bigace.de http://www.bigace.de}.
 *
 * @version $Id$
 * @author Vlãgioiu Rãzvan-Marius
 */

if(!defined('_BIGACE_INSTALL_PARENT_')) {
    die('Not runnable alone, go to '.dirname(__FILE__).'/index.php');
}

$LANG['title'] = 'BIGACE '._BIGACE_ID;
$LANG['intro'] = '... editare facilã a conþinutului!';
$LANG['thanks'] = 'Vã mulþumim pentru instalarea BIGACE CMS!';

// -----------------------------------------------------------------------------
// Navigation
$LANG['menu_title'] = 'Instalarea sistemului';
$LANG['menu_step_2'] = 'Verificãri';
$LANG['menu_step_3'] = 'Instalare';
$LANG['menu_step_4'] = 'Crearea comunitãþii';
$LANG['menu_step_5'] = 'Instalarea s-a efectuat cu succes.';
$LANG['menu_step'] = 'Pas';
// -----------------------------------------------------------------------------

// -----------------------------------------------------------------------------
// Welcome Screen
$LANG['install_state'] = 'Status';
$LANG['install_begin'] = 'Pornirea instalãrii';
$LANG['introduction'] = 'Introducere';
// -----------------------------------------------------------------------------

// -----------------------------------------------------------------------------
// Form Tooltip
$LANG['form_tip_close'] = "Închidere";
$LANG['form_tip_hide'] = "Nu mai arãtaþi mesajul.";
// -----------------------------------------------------------------------------

// -----------------------------------------------------------------------------
// LANGUAGES - chooser and names for languages
$LANG['language_choose'] = 'Definiþia de dicþionare';
$LANG['language_text'] = 'Alegeþi limba pe care o veþi folosi în procesul de instalare.';
$LANG['language_button'] = 'Alegeþi limba';
// -----------------------------------------------------------------------------

$LANG['failure'] = 'Erori apãrute';
$LANG['new'] = 'Nou';
$LANG['old'] = 'Vechi';
$LANG['successfull'] = 'Succes';
$LANG['main_menu'] = 'Meniul principal';
$LANG['back'] = 'Înapoi';
$LANG['next'] = 'Urmãtorul';
$LANG['state_no_db'] = 'Baza de date nu este instalatã!';
$LANG['state_not_all_db'] = 'Baza de date este instalatã incomplet!';
$LANG['state_installed'] = 'Core-System a fost instalat cu succes!';
$LANG['help_title'] = 'Ajutor';
$LANG['help_text'] = 'Pentru a vedea informaþii despre paºii urmãtori, mutaþi mouse-ul peste fiecare icoanã de Ajutor din fiecare câmp. Un scurt mesaj cu informaþii va apãrea.<br>Pentru demonstraþie mutaþi mouse-ul peste icoana urmãtoare:';
$LANG['help_demo'] = 'Gãsiþi calea apelând la Ajutor!';
$LANG['db_install'] = 'Instalarea CMS';
$LANG['cid_install'] = 'Uneltele Sstului';
$LANG['install_finish'] = 'Instalare completã';
$LANG['db_install_state'] = 'Status: ';
$LANG['db_install_help'] = 'În acest pas va instala Core CMS. Veþi configura conexiunea cu baza de date';

//-----------------------------------------------------------
// Translation for the System installation Dialog
$LANG['db_value_title'] = 'Conectarea bazei de date';
$LANG['ext_value_title'] = 'Configurarea sistemului';
$LANG['db_type'] = 'Tipul bazei de date';
$LANG['db_host'] = 'Server/Host';
$LANG['db_database'] = 'Baza de date';
$LANG['db_user'] = 'Utilizator';
$LANG['db_password'] = 'Parolã';
$LANG['db_prefix'] = 'Prefix tabelã';
$LANG['mod_rewrite'] = 'Apache MOD-Rewrite';
$LANG['mod_rewrite_yes'] = 'Modul active(.htaccess)';
$LANG['mod_rewrite_no'] = 'Nu este posibil';
$LANG['base_dir'] = 'Directorul de bazã';
// Translation for the System Installation Help Images
$LANG['base_dir_help'] = 'Introduceþi directorul de instalare relativã. Lsaþi liber daca instalarea se face în directorul rãdãcinã (http://www.situldvs.com/). <br>';
$LANG['mod_rewrite_help'] = '<b>Aceastã uneltã permite URL-uri!</b><br/Daca folosiþi unealta fãrã opþiunea de Rescriere, sistemul nu va mai gãsi situl. Daca nu sunteþi sigur de setãri lasaþi câmpul aºa cum este!';
$LANG['db_password'] = 'Parolã';
$LANG['def_language'] = 'Limba implicitã';
$LANG['def_language_help'] = 'Alegeþi limba implicitã pentru CMS-ul dvs.';
$LANG['db_type_help'] = 'Alegeþi tipul de bazã de date folositã.<br>Instalarea suportã tipurile de baze de date arãtat mai jos, dar sistemul <b>suportã MySQL</b>.<br>Folosirea altei baze date decât MySQL, se face pe propriul dvs. risc!';
$LANG['db_host_help'] = 'Introduceþi numele serverului unde baza de date va fi instalatã (încercaþi sã folosiþi <b>localhost</b> care funcþioneazã in 99% din cazuri!).';
$LANG['db_database_help'] = 'Introduceri numele bazei de date (aceeaºi creatã cu phpMyAdmin).';
$LANG['db_user_help'] = 'Introduceþi numele de utilizator ce are permisiune de scriere în baza de date.';
$LANG['db_prefix_help'] = 'Introduceþi prefixul pentru tabelele Bigface. Daca nu sunteþi sigur lãsaþi câmpul necompletat.';
$LANG['db_password_help'] = 'Introduceþi parola asociatã utilizatorului.';
$LANG['db_already_exists'] = 'Baza de date a fost instalatã corect. Puteþi trece la pasul urmãtor.';

$LANG['htaccess_security'] = 'Apache .htaccess Feature';
$LANG['htaccess_security_yes'] = 'Suprascrierea este activã (.htaccess)';
$LANG['htaccess_security_no'] = 'Nu este posibil';
$LANG['htaccess_security_help'] = '<b>Aceasta este o unealtã pentru securitatea dvs!</b><br/>Fiþi sigur cã serverul permite <b>SUPRASCRIEREA TUTUROR </b> by .htaccess fiºierelor. Dacã nu sunteþi sigur lãsaþi câmpul necompletat!';
//-----------------------------------------------------------

// Translation for Consumer Installation
// First Dialog
$LANG['error_enter_domain'] = 'Vã rugãm sã introduceþi un nume de Domeniu, care sã fie valid.';
$LANG['error_enter_adminuser'] = 'Vã rugãm sã introduceþi un nume pentru contul de Administrator (minim 4 caractere).';
$LANG['error_enter_adminpass'] = 'Vã rugãm sã introduceþi o parolã pentru contul de Administrator (minim 6 caractere).';

$LANG['cid_check_failed'] = 'Verificarea permisiunii fiºierelor a EªUAT! Trebuie sã CORECTAÞI înainte de a continua!';
$LANG['cid_domain'] = 'Comunitatea de Domenii';
$LANG['cid_id_help'] = 'Alegeþi ID-ul pentru comunitatea dvs. Dacã doriþi sã migraþi la instalarea existentã, cea mai bunã idee este sã folosiþi ID-ul vechii comunitãþi.';
$LANG['cid_domain_help'] = 'Introduceþi numele de Domeniu care va fi mapat. Valoarea auto-detectatã va fi corectã.<br><b></b>';

$LANG['statistics'] = 'Statistici';
$LANG['statistics_on'] = 'Activarea statisticilor';
$LANG['statistics_off'] = 'Dezactivarea statisticilor';
$LANG['statistics_help'] = 'Alegeþi dacã doriþi sã activaþi sau nu statiscile.';
$LANG['sitename'] = 'Numele sitului dvs. ';
$LANG['sitename_help'] = 'Introduceþi titlul. Poate fi schimbat usor din secþiunea de Administrare.';
$LANG['mailserver'] = 'Server-ul de E-Mail';
$LANG['mailserver_help'] = 'Introduceþi datele de Server-ului de E-Mail (mail.yourdomain.com), care va trimite e-mailurile dvs. Lãsaþi gol dacã folosiþi PHP default proxy.';
$LANG['webmastermail'] = 'Adresa de E-mail';
$LANG['webmastermail_help'] = "Introduceþi adresa de e-mail care va administra comunitatea dvs.";
$LANG['bigace_admin'] = 'Utilizator';
$LANG['bigace_password'] = 'Parola';
$LANG['bigace_check'] = 'Parola din nou';
$LANG['bigace_admin_help'] = 'Introduceþi utilizatorul pentru contul de administrator. Va avea drept general de administrare.';
$LANG['bigace_password_help'] = 'Introduceþi parola pentru contul de administrator.';
$LANG['bigace_check_help'] = 'Verificaþi dacã parolele introduse sunt identice.';
$LANG['create_files'] = 'Crearea fiºierelor de sistem';
$LANG['save_cconfig'] = 'Salveazã comunitatea';
$LANG['added_consumer'] = 'Comunitate adãugatã';
$LANG['added_consumer'] = 'Comunitate existentã adãugatã';
$LANG['community_exists'] = 'Consumatorul existã deja, vã rugãm sã introduceþi un Domeniu nou.';

$LANG['check_reload'] = 'Executa Pre-verificarea din nou';
$LANG['check_up']               = 'Pre-verificare';
$LANG['check_up_help'] = 'ATENÞIE: Efectuaþi pre-verificarea înainte de instalarea platformei CMS!';

$LANG['check_yes'] = 'Da';
$LANG['check_no'] = 'Nu';
$LANG['check_on'] = 'Pornit';
$LANG['check_off'] = 'Oprit';
$LANG['check_status'] = 'Status';
$LANG['check_setting'] = 'Unelte';
$LANG['check_recommended'] = 'Recomandare';
$LANG['check_install_help'] = 'Dacã unul din steguleþe este marcat cu roºu, trebuie sã ajustaþi configuraþiile PHP ºi Apache. Daca nu efectuaþi ajustãrile instalarea nu va continua.';
$LANG['required_settings_title']= 'Unelte obligatorii';
$LANG['check_settings_title'] = 'Unelte recomandate';
$LANG['check_settings_help'] = 'Urmãtoarele unelte PHP sunt recomandate. <br><br>';
$LANG['check_files_title'] = 'Directorul si permisia fiºierelor';
$LANG['check_files_help'] = 'Pentru a putea lucra, Bigace trebuie sã schimbe permisia urmãtoarelor directoare &amp; fiºiere. Dacã vedeþi fiºiere cu roºu va trebui sã le atribuiþi permisia la 0777.';

$LANG['config_consumer'] = 'Uneltele comuniþãþii';
$LANG['config_admin'] = 'Contul de administrator';

$LANG['community_install_good'] = '
<p>Felicitãri. Instalarea s-a efectuat cu succes!</p>
<p>Dacã în orice moment aveþi nevoie de suport, sau BIGACE nu mai funcþioneazã corespunzãtor, nu uitaþi forumul nostru <a href="http://forum.bigace.de" target="_blank">secþiunea de ajutor este disponibilã</a> .
<p>Directorul de instalare încã mai existã. Din motive de securitate este bine sã îl ºtergeþi.</p>
<p>Acum puteþi <a href="../../">vizualiza noul dvs. site</a> ºi începeþi sã îl folosiþi. Trebuie sã vã asiguraþi cã sunteþi logat ºi cã aveþi acces la secþiunea de administrare.</p>
<br />
<p>Mult noroc!</p>
<br /><br />
<p><a href="../../">Vizitaþi-vã noul site</a></p>';

$LANG['community_install_bad']  = 'Au apãrut probleme pe parcursul instalãrii.';
$LANG['community_install_infos']= 'Afiºaþi sistemul de mesaje...';

$LANG['error_db_connect'] = 'Nu se poate conecta la hostul bazei de date';
$LANG['error_db_select'] = 'Nu poate selecta baza de date';
$LANG['error_db_create'] = 'Nu poate crea baza de date.';
$LANG['error_read_dir'] = 'Nu poate citi directorul';
$LANG['error_created_dir'] = 'Nu poate crea directorul';
$LANG['error_removed_dir'] = 'Nu poate ºterge directorul';
$LANG['error_copied_file'] = 'Nu poate copia fiºierul';
$LANG['error_remove_file'] = 'Nu poate ºterge fiºierul';
$LANG['error_close_file'] = 'Nu poate închide fiºierul';
$LANG['error_open_file'] = 'Eroare: Nu se poate deschide fiºierul';
$LANG['error_db_statement'] = 'Eroare în statusul bazei de date';
$LANG['error_open_cconfig'] = 'Nu se poate deschide fiºierul de configurare a Comunitãþii';
$LANG['error_double_cconfig'] = 'Eroare: Comunitatea existã deja!';
$LANG['could_not_find_consumer'] = 'Eroare: Nu se poate gãsi Comunitatea!';
