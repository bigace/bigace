<?php
/**
* Translation file for the installer.
* Language: Slovenski (Slovenian)
* Copyright (C) Kevin Papst
*
* For further information go to {@link http://www.bigace.de http://www.bigace.de}.
*
* @version $Id$
* @author V.I.A. DOM d.o.o.
*/

if(!defined('_BIGACE_INSTALL_PARENT_')) {
    die('Not runnable alone, go to '.dirname(__FILE__).'/index.php');
}

$LANG['title'] = 'BIGACE '._BIGACE_ID;
$LANG['intro'] = '... enostavno urejanje vasih vsebin!';
$LANG['thanks'] = 'Hvala ker ste instalirali BIGACE CMS!';

// -----------------------------------------------------------------------------
// Navigation
$LANG['menu_title'] = 'Instalacija sistema';
$LANG['menu_step_2'] = 'Preveri nastavitve';
$LANG['menu_step_3'] = 'Instalacija';
$LANG['menu_step_4'] = 'Ustvari forum';
$LANG['menu_step_5'] = 'Instalacija uspesna';
$LANG['menu_step'] = 'Korak';
// -----------------------------------------------------------------------------

// -----------------------------------------------------------------------------
// Welcome Screen
$LANG['install_state'] = 'Status';
$LANG['install_begin'] = 'Zacni z instalacijo';
$LANG['introduction'] = 'Uvod';
// -----------------------------------------------------------------------------

// -----------------------------------------------------------------------------
// Form Tooltip
$LANG['form_tip_close'] = 'Zapri';
$LANG['form_tip_hide'] = "Tega sporocila ne prikazuj vec";
// -----------------------------------------------------------------------------

// -----------------------------------------------------------------------------
// LANGUAGES - chooser and names for languages
$LANG['language_choose'] = 'Definicija jezika';
$LANG['language_text'] = 'Izberite jezik ki ga zelite uoprabljati med instalcijskim procesom.';
$LANG['language_button'] = 'Spremeni jezik';

$LANG['language_de'] = 'Deutsch (German)';
$LANG['language_en'] = 'English';
$LANG['language_se'] = 'Svenska (Swedish)';
$LANG['language_ro'] = 'Românã (Romanian)';
$LANG['language_it'] = 'Italiano (Italian)';
$LANG['language_fi'] = 'Suomeksi (Finnish)';
$LANG['language_tr'] = 'Türkçe (Turkish)';
$LANG['language_si'] = 'Slovensko (Slovene)';
// -----------------------------------------------------------------------------

$LANG['failure'] = 'Napaka';
$LANG['new'] = 'Nov';
$LANG['old'] = 'Star';
$LANG['successfull'] = 'Uspesno';
$LANG['main_menu'] = 'Glavni meni';
$LANG['back'] = 'Nazaj';
$LANG['next'] = 'Naslednji';
$LANG['state_no_db'] = 'Podatkovna baza ni instalirana!';
$LANG['state_not_all_db'] = 'Instalacija podatkovne baze ni bila dokoncana!';
$LANG['state_installed'] = 'Core-System uspesno namescen!';
$LANG['help_title'] = 'Pomoc';
$LANG['help_text'] = 'Za informacije naslednjih korakov, premaknite misko na Pomoc ikono za vsakim vnosnim poljem. Pojavijo se kratke informacije.<br>Za demonstracijo premaknite misko na ikono:';
$LANG['help_demo'] = 'Nasli ste pravo pot za ogled pomoci!';
$LANG['db_install'] = 'Instaliraj CMS';
$LANG['cid_install'] = 'Nastavitve internetne strani';
$LANG['install_finish'] = 'Koncaj instalacijo';
$LANG['db_install_state'] = 'Stanje: ';
$LANG['db_install_help'] = 'Ta korak instalira CMS Core Sistem. Konfiguriraj povezavo podatkovne baze in nekatere druge nastavitve za vaso prvo internetno stran(Forum)';

//-----------------------------------------------------------
// Translation for the System installation Dialog
$LANG['db_value_title'] = 'Povezava podatkovne baze';
$LANG['ext_value_title'] = 'Konfiguracija sistema';
$LANG['db_type'] = 'Tip baze podatkov';
$LANG['db_host'] = 'Server/Gostitelj';
$LANG['db_database'] = 'Baza podatkov';
$LANG['db_user'] = 'Uporabnik';
$LANG['db_password'] = 'Geslo';
$LANG['db_prefix'] = 'Table Prefix';
$LANG['mod_rewrite'] = 'Apache MOD-Rewrite';
$LANG['mod_rewrite_yes'] = 'Modul aktiviran / Uporaba mozna (.htaccess)';
$LANG['mod_rewrite_no'] = 'Ni mogoce / Ne vem';
$LANG['base_dir'] = 'Direktorij baze';
// Translation for the System Installation Help Images
$LANG['base_dir_help'] = 'Vnesite instalacijski direktorij povezan z vaso bazo podatkov (BaseDir). Pustite prazno ce instalirate v web root-u (http://www.example.com/). <br><b>Avtomatsko izracunavanje vrednosti mora biti pravilno!</b><br>Pot se NE SME zaceti, koncati se mora z slashom. Na primer &quot;http://www.example.com/cms/&quot;, vrednost <b>cms/</b> mora biti tocna.';
$LANG['mod_rewrite_help'] = '<b>Te nastavitve dovoljujejo prijateljske URL-je!</b><br/>Prosimo prepricajte se da ste izbrali prave nastavitve. Ce ste izbrali uporabljeno moznost brez Rewrite Support-a, Vas sistem ne bo your System may not be brkan. Te nastavitve se konfigurirajo preko Config Entry-a. If you are not sure leave this setting as is!';
$LANG['db_password'] = 'Geslo';
$LANG['def_language'] = 'Privzet jezik';
$LANG['def_language_help'] = 'Izberite privzet jezik za vas CMS.';
$LANG['db_type_help'] = 'Izberite tip baze podatkov katero boste uporabljali.<br>Instalacijski postopek podpira vse prikazane podatkovne baze, ampak Core System <b>trenutno podpira SAMO MySQL</b> popolnoma.<br>Ce ste se odlocili za drugo podatkovno bazo, ste to naredili na svojo odgovornost!';
$LANG['db_host_help'] = 'Vnesite ime serverja kjer je vasa baza podatkov instalirana (poizkusite uporabiti <b>localhost</b> kateri pogosto deluje!).';
$LANG['db_database_help'] = 'Vnesite ime vase baze podatkov (Primer: enako kot jo vidite v levem okvirju phpMyAdmin-a).';
$LANG['db_user_help'] = 'Vnesite uporabnika, ki ima pravice pisanja za vaso bazo podatkov.';
$LANG['db_prefix_help'] = 'Vnesite prefix za BIGACE-ovo podatkovno tabelo. Uporabite unikatno ime, da jo bo vedno mogoce neposredno identificirati. Če ne boste razumeli pomen tega, uporabite privzeto vrednost.';
$LANG['db_password_help'] = 'Vnesite geslo za uporabnika.';
$LANG['db_already_exists'] = 'zdi se da je Baza podatkov pravilno namescena, preskoči ta korak namestitve.';

$LANG['htaccess_security'] = 'Apache .htaccess Feature';
$LANG['htaccess_security_yes'] = 'Dopusti override aktivnim (.htaccess)';
$LANG['htaccess_security_no'] = 'Ni mogoce / Ne vem';
$LANG['htaccess_security_help']	= '<b>To so nastavitve za varnost vasih podatkov!</b><br/>Prepricajte se da server dopusca<b>Override vse</b> z .htaccess datotekami. Ce niste prepricani pustite te nastavitve take kot so!';
//-----------------------------------------------------------

// Translation for Consumer Installation
// First Dialog
$LANG['error_enter_domain'] = 'Prosim vnesite tocno domeno, kjer bo vas novi forum dosegljiv.';
$LANG['error_enter_adminuser'] = 'Prosim vnesite ime za vas nov administratorski racun (vsaj 4 znaki).';
$LANG['error_enter_adminpass'] = 'Prosim vnesite geslo za vas nov administratorski racun (vsaj 6 znakov) ter ga spodaj potrdite.';

$LANG['cid_check_failed'] = 'Preverba vasega dovoljenja datotek FAILED! Moras jih popravi, preden lahko nadaljujete!';
$LANG['cid_domain'] = 'Domena foruma';
$LANG['cid_id_help'] = 'Izberite ID za novo Skupnosti (ki ga bodo uporabljali samo notranji). Ce boste preseliti obstoječe instalacije, je najboljša ideja, da izbere ID starih Skupnosti.';
$LANG['cid_domain_help'] = 'Vpisite ime domene, ki bodo preslikane v novo skupnost. Avto-odkrite vrednosti morajo biti pravilne.<br><b>OPOMBA: NE NASAJTE POT ALI TRAILING SLASH!</b>';

$LANG['statistics'] = 'Statistika';
$LANG['statistics_on'] = 'Aktivirajte statistiko';
$LANG['statistics_off'] = 'Deaktivirajte statistiko';
$LANG['statistics_help'] = 'Izberite, ali zelite vkljuciti statisticne podatke ali ne. ce jih vkljucite, se bo vsak klic srani zapisal v podatkovno bazo.';
$LANG['sitename'] = 'Ime spletne strani';
$LANG['sitename_help'] = 'Vnesite ime ali naslov vase strani. Ta vrednost se lahko uporabi v Predlogi in se zlahka spremeni v Administratorskem panelu.';
$LANG['mailserver'] = 'Streznik za e-posto';
$LANG['mailserver_help'] = 'Vpisite svoje Mail streznike (mail.yourdomain.com), ki bo posiljal e-posto. Pustite prazno, ce uporabljate proxy default PHP (za vecino skupnih sistemov).';
$LANG['webmastermail'] = 'E-postni naslov';
$LANG['webmastermail_help'] = "Vnesite e-mail naslov za administratorski racun vasega novega foruma.";
$LANG['bigace_admin'] = 'Uporabnisko ime';
$LANG['bigace_password'] = 'Geslo';
$LANG['bigace_check'] = 'Geslo [ponovni vnos]';
$LANG['bigace_admin_help'] = 'Vpišite uporabnisko ime za adminstratorski racun. Ta administrator bo lastnik vseh dovoljenj za Items in administratovne naloge.';
$LANG['bigace_password_help'] = 'Vnesite geslo za administratorski racun.';
$LANG['bigace_check_help'] = 'Prosim potrdite izbrano geslo. Ce se geslo katerega ste vpisali, vas avtomatsko vrne nazaj na to stran.';
$LANG['create_files'] = 'Ustvarjanje datotecnega sistema';
$LANG['save_cconfig'] = 'Shranite konfiguracijo foruma';
$LANG['added_consumer'] = 'Dodan forum';
$LANG['added_consumer'] = 'Dodan obstojec forum';
$LANG['community_exists'] = 'Uporabnik obstajaza to domeno, prosim vnesite drugo domeno.';

$LANG['check_reload'] = 'Ponovno se izvaja Pre-Check';
$LANG['check_up']               = 'Pre-Check';
$LANG['check_up_help'] = 'POZOR: Prosimo, da opravite Pre-Check, preden začnete BIGACE-u namestiti ali dodati nov forum!';

$LANG['required_empty_dirs'] = 'Zahtevani direktoriji';
$LANG['empty_dirs_description'] = 'Sledeci dorektoriji so zahtevani za BIGACE, vendar se ne morejo ustvariti avtomatsko. Prosim ustvarite jih rocno:';
$LANG['check_yes'] = 'Da';
$LANG['check_no'] = 'Ne';
$LANG['check_on'] = 'Vklop';
$LANG['check_off'] = 'Izklop';
$LANG['check_status'] = 'State';
$LANG['check_setting'] = 'Nastavitve';
$LANG['check_recommended'] = 'Priporocljivo';
$LANG['check_install_help'] = 'Če je ena izmed zastav označena z rdeco barvo, boste morali prilagoditi / popravi Apache in PHP konfiguracijo. Če je ne, popravite bo verjetno na koncu instalacije napaka.';
$LANG['required_settings_title']= 'Zahtevanje nastavitve';
$LANG['check_settings_title'] = 'Priporocene nastavitve';
$LANG['check_settings_help'] = 'Te PHP nastavitve so priporocljive za nemoteno delovanje BIGACE-a. <br><br>CMS naj bi tudi delal, če nekatere nastavitve ne ustrezajo. Kljub temu priporocamo, da se odpravi vsaka omenjena težava, preden nadaljujete z namestitvijo.';
$LANG['check_files_title'] = 'Dovoljenja za datoteke in direktorije';
$LANG['check_files_help'] = 'Za dobro delo, BIGACE potrebuje dovoljenja za pisanje za naslednje direktorije in datoteke. Ce vidite rdečo piko, morate popraviti dovoljenje pred nadaljevanjem.';

$LANG['config_consumer'] = 'Nastavitev foruma';
$LANG['config_admin'] = 'Administratorski racun';

$LANG['community_install_good'] = '
<p>Cestitek instalacijski proces je zakljucen!</p>
<p>Ce boste kadarkoli rabili pomoc ali pa BIGACE ne deluje pravilno, ne pozabite da <a href="http://forum.bigace.de" target="_blank">pomoc na voljo</a> ce jo potrebujete.</p>
<p>Vaša namestitvena datoteka še vedno obstaja. Prosimo, da jo odstranite povsem zaradi varnostnih razlogov.</p>
<p>Zdaj lahko <a href="../../">obiscete vaso novo spletno stran</a> ter jo zacnete uporabljati. Prepricajte se ce ste prijavljeni, kajti le tako boste lahko dostopali do administratorskega panela.</p>
<br />
<p>Srecno!</p>
<br /><br />
<p><a href="../../">Obiscite vasno novo spletno stran</a></p>';

$LANG['community_install_bad'] 	= 'Napaka med instalacijo.';
$LANG['community_install_infos']= 'Prikazi sistemska sporocila...';

$LANG['error_db_connect'] = 'Ne morem se povezati z gostujoco podatkovno bazo';
$LANG['error_db_select'] = 'Ne moreme izbrati baze podatkov';
$LANG['error_db_create'] = 'Ne morem ustvariti baze podatkov.';
$LANG['error_read_dir'] = 'Ne morem brati direktorija';
$LANG['error_created_dir'] = 'Ne morem ustvariti direktorija';
$LANG['error_removed_dir'] = 'Ne morem izbrisati direktorija';
$LANG['error_copied_file'] = 'Ne morem kopirati datoterke';
$LANG['error_remove_file'] = 'Ne morem izbrisati datoteke';
$LANG['error_close_file'] = 'Ne morem sapreti datoteke';
$LANG['error_open_file'] = 'Napaka: Ne morem odpreti datoteke';
$LANG['error_db_statement'] = 'Napaka v DB Statement-u';
$LANG['error_open_cconfig'] = 'Ne morem odpreti konfiguracijsko datoteko foruma';
$LANG['error_double_cconfig'] = 'Napaka: forum obstaja!';
$LANG['could_not_find_consumer'] = 'Napaka: ne morem najti foruma';
