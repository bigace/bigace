
<?php
/**
 * Translation file for the installer.
 * Language: Croatian (Hrvatski)
 * Copyright (C) Kevin Papst
 *
 * For further information go to {@link http://www.bigace.de http://www.bigace.de}.
 *
 * @version $Id$
 * @author Bruno Caleta
 */

if(!defined('_BIGACE_INSTALL_PARENT_')) {
    die('Not runnable alone, go to '.dirname(__FILE__).'/index.php');
}

$LANG['title'] = 'BIGACE '._BIGACE_ID;
$LANG['intro'] = '... lako upravlja vašim Sadržajem!';
$LANG['thanks'] = 'Hvala to instalirate BIGACE CMS!';

// -----------------------------------------------------------------------------
// Navigation
$LANG['menu_title'] = 'Instaliraj Sustav';
$LANG['menu_step_2'] = 'Provjera Postavki';
$LANG['menu_step_3'] = 'Instalacija';
$LANG['menu_step_4'] = 'Napravi Zajednicu';
$LANG['menu_step_5'] = 'Instalacija je uspješna';
$LANG['menu_step'] = 'Korak';
// -----------------------------------------------------------------------------

// -----------------------------------------------------------------------------
// Ekran Dobrosolice
$LANG['install_state'] = 'Status';
$LANG['install_begin'] = 'Start Instalacije';
$LANG['introduction'] = 'Uvod';
// -----------------------------------------------------------------------------

// -----------------------------------------------------------------------------
// Obrazac Tooltip
$LANG['form_tip_close'] = 'Zatvori';
$LANG['form_tip_hide'] = "Ne pokazuj ovu poruku ponovno";
// -----------------------------------------------------------------------------

// -----------------------------------------------------------------------------
// Jezici - Odabir imena za jezike
$LANG['language_choose'] = 'Jezična Definicija';
$LANG['language_text'] = 'Izaberi jezik koji je korišten tijekom instalacije.';
$LANG['language_button'] = 'Promijeni jezik';
// -----------------------------------------------------------------------------

$LANG['failure'] = 'Pogreška prisutna';
$LANG['new'] = 'Novo';
$LANG['old'] = 'Staro';
$LANG['successfull'] = 'Uspješno';
$LANG['main_menu'] = 'Glavni Izbornik';
$LANG['back'] = 'Natrag';
$LANG['next'] = 'Dalje';
$LANG['state_no_db'] = 'Baza podataka čini se da neče biti instalirana!';
$LANG['state_not_all_db'] = 'Instalacija Baze Podataka nije završena!';
$LANG['state_installed'] = 'Core-sustav uspješno instaliran!';
$LANG['help_title'] = 'Pomoć';
$LANG['help_text'] = 'Da pogledaš daljnje informacije za svaki korak, Pomakni miš preko ikone Pomoć  iza svakog polja. Kratka informacijska poruka će se pojaviti.<br>Za demonstracijsku svrhu premjestiti svoj miš iznad sljedeće ikone :';
$LANG['help_demo'] = 'Našli ste pravi način da vidite vašu ikonu Pomoć-Info!';
$LANG['db_install'] = 'Instaliraj CMS';
$LANG['cid_install'] = 'Podešavanja Web stranice';
$LANG['install_finish'] = 'Instalacija Završena';
$LANG['db_install_state'] = 'Stanje: ';
$LANG['db_install_help'] = 'Ovaj korak instalira CMS Core System. Možete konfigurirati povezivanja s bazom podataka i neke postavke za vaš prvi website(Zajednicu)';

//-----------------------------------------------------------
// Translation for the System installation Dialog
$LANG['db_value_title'] = 'Povezivanje sa Bazom podataka';
$LANG['ext_value_title'] = 'Konfiguracija sustava';
$LANG['db_type'] = 'Tip Baze Podataka';
$LANG['db_host'] = 'Poslužitelj/Domain';
$LANG['db_database'] = 'Baza Podataka';
$LANG['db_user'] = 'Korisnik';
$LANG['db_password'] = 'Zaporka';
$LANG['db_prefix'] = 'Tablica Prefiks';
$LANG['mod_rewrite'] = 'Apache MOD-Prepiši';
$LANG['mod_rewrite_yes'] = 'Modul aktivan / Korištenje je moguče(.htaccess)';
$LANG['mod_rewrite_no'] = 'Nije Moguče/ Ne Znam';
$LANG['base_dir'] = 'Baza Direktorij';
// Translation for the System Installation Help Images
$LANG['base_dir_help'] = 'Unesite instalacijski direktorij povezan sa tvojom BaseDir. Ostavite prazno polje ako instalirate u web rootu(http://www.example.com/). <br><b>Auto izračunata vrijednost bi trebala biti točna!</b><br>Put ne smije započeti, ali  kraj je sa crticom. Za primjer evo instalacije&quot;http://www.example.com/cms/&quot;, vrijednost<b>cms/</b> trebala bi biti ispravna.';
$LANG['mod_rewrite_help'] = '<b>Ova postavka omogučuje prijateljski URLs!</b><br/>Molimo Vas da odaberete prava Podešavanja. Ako se odlučite za korištenje bez podrške Mogučeg ponovnog upisa , vaš sustav neće moći pretraživati u vašem pretraživaču. Ova postavka je podesiva putem Config Ulaza. Ako niste sigurni ostavite ova podešavanja kakva jesu!';
$LANG['db_password'] = 'Zaporka';
$LANG['def_language'] = 'Zadani Jezik';
$LANG['def_language_help'] = 'Izaberite svoj zadani Jezik za vaš CMS.';
$LANG['db_type_help'] = 'Izaberite Tip Baze podataka koju ćete koristiti.<br>Instalacija podržava sve napisane Baze Podataka, ali Core System <b>Trenutno SAMO podržava MySQL</b> u potpunosti.<br>Ako se odlučite koristiti drugu Basu podataka osim MySQL, to je onda na vaš rizik!';
$LANG['db_host_help'] = 'Unesite Ime Servera gdje je Basa podataka instalirana(Pokušaj koristiti <b>localhost</b> koje obično funkcionira!).';
$LANG['db_database_help'] = 'Unesite ime vaše Baze podataka(primjer isto koje vidite u lijevom okviru pored phpMyAdmin).';
$LANG['db_user_help'] = 'Unesite Korisnika koji ima Dozvole za pisanje u vašoj Bazi podataka.';
$LANG['db_prefix_help'] = 'Unesite prefiks za tablice BIGACE  baze podataka. Koristeči jedinstveno Ime, ona će uvijek biti izravno identificirana. Ako ne znate značenje ovoga koristite Zadanu Vrijednost.';
$LANG['db_password_help'] = 'Unesite Zaporku za Unesenog Korisnika.';
$LANG['db_already_exists'] = 'Baza podataka je pravilno instalirana, preskačemo ovaj korak instalacije.';

$LANG['htaccess_security'] = 'Apache .htaccess Feature';
$LANG['htaccess_security_yes'] = 'Dopusti prebrisati aktivni(.htaccess)';
$LANG['htaccess_security_no'] = 'Nije Moguče/ Ne Znam';
$LANG['htaccess_security_help'] = '<b>Ovo su postavke za sigurnost vaših podataka!</b><br/>Budite sigurni da vaš server dopušta <b>prebrisavanje svega</b> by .htaccess Files. Ako niste sigurni ostavite kako trenutno stoji!';
//-----------------------------------------------------------

// Translation for Consumer Installation
// First Dialog
$LANG['error_enter_domain'] = 'Molim unesite ispravnu Domenu, gdje je nova Zajednica Dostupna.';
$LANG['error_enter_adminuser'] = 'Molim unesite Ime za novi Administracijski Račun(minimum 4 znaka).';
$LANG['error_enter_adminpass'] = 'Molim unesite Zaporku za novi Administracijski Račun(minimum 6 znakova) i verificirajte ispod.';

$LANG['cid_check_failed'] = 'Pregled Vaših dozvola datoteka  PROPAO! Morate to popraviti PRIJE nego što nastavite!';
$LANG['cid_domain'] = 'Domena Zajednice';
$LANG['cid_id_help'] = 'Izaberi ID za svoju novu Zajednicu(koja će se koristi samo interno). Ako ćete seliti postoječu instalaciju, najbolja ideja je da izaberete ID svoje stare Zajednice.';
$LANG['cid_domain_help'] = 'Unesite naziv Domene, koji će se preslikati u novu zajednicu. Auto-otkrivena vrijednost bi trebala biti točna.<br><b>NOTE: NE UNOSITE PUT ILI BILO KOJU CRTICU!</b>';

$LANG['statistics'] = 'Statistika';
$LANG['statistics_on'] = 'aktivirati Statistiku';
$LANG['statistics_off'] = 'Deaktivirati Statistiku';
$LANG['statistics_help'] = 'Izaberi dali će aktivirati statistiku ili ne. ako je aktivira, sa svakog pojedinog poziva pisanja baze podataka poziv će biti izvršen.';
$LANG['sitename'] = 'Ime Web Stranice';
$LANG['sitename_help'] = 'Unesite ime ili Naslov vaše web stranice. Ova vrijednost se može koristiti u Predlošcima i lako mijenjati koristeći Administraciju.';
$LANG['mailserver'] = 'Mail Server';
$LANG['mailserver_help'] = 'Unesi svoj Mail Server (mail.yourdomain.com), gdje će biti poslani vaši e-mailovi. Ostavi prazno ako koristi PHP Zadani proxy (uglavnom za sve zajedničke sustave).';
$LANG['webmastermail'] = 'Email adresa';
$LANG['webmastermail_help'] = "unesite email adresu za Administracijski Račun vaše nove Zajednice.";
$LANG['bigace_admin'] = 'Korisničko Ime';
$LANG['bigace_password'] = 'Zaporka';
$LANG['bigace_check'] = 'Zaporka [ponovno upisati]';
$LANG['bigace_admin_help'] = 'Unesite korisničko ime za vaš administracijski račun. Ovaj administrator imat će sve svoje dozvole na Predmete i administrativne funkcije.';
$LANG['bigace_password_help'] = 'Unesite zaporku za vaš administracijski Račun.';
$LANG['bigace_check_help'] = 'Molim verificirajte vašu odabranu zaporku.Ako unesena Zaporka ne odgovara prvom upisu biti ćete vračeni ovdje.';
$LANG['create_files'] = 'Pravljenje sustava datoteka';
$LANG['save_cconfig'] = 'Spremi konfiguraciju Zajednice';
$LANG['added_consumer'] = 'Zajednica dodana';
$LANG['added_consumer'] = 'Postojeća Zajednica dodana';
$LANG['community_exists'] = 'Postoji osoba koja posjeduje ovu domenu i koristi je, molim unesite drugu domenu.';

$LANG['check_reload'] = 'Izvršiti Provjerite ponovo';
$LANG['check_up']               = 'provjerite ponovno';
$LANG['check_up_help'] = 'PANJA: Molimo izvršiti Ponovnu provjeru prije nego što počnete da instalirate BIGACE ili dodajte novu Zajednicu!';

$LANG['required_empty_dirs'] = 'Potreban direktorij';
$LANG['empty_dirs_description'] = 'postojeći direktorij je potreban za BIGACE, ali ga se nemože kreirati automatski. Molim kreirajte ga Ručno:';
$LANG['check_yes'] = 'Da';
$LANG['check_no'] = 'Ne';
$LANG['check_on'] = 'Uključen';
$LANG['check_off'] = 'Isključen';
$LANG['check_status'] = 'Stanje';
$LANG['check_setting'] = 'Postavke';
$LANG['check_recommended'] = 'Preporučeno';
$LANG['check_install_help'] = 'ako je jedna od zastavica označena crvenom bojom, morate prilagoditi/ispraviti vaš Apache i PHP konfiguraciju. Ako to ne napravite, biti će na kraju pogreška u instalaciji.';
$LANG['required_settings_title']= 'Potrebne postavke';
$LANG['check_settings_title'] = 'Preporučene Postavke';
$LANG['check_settings_help'] = 'Pratite PHP preporučene postavke , da dobijete bezbrižan i gladak rad sa BIGACeom. <br><br> CMS može raditi iako neke postavke se ne podudaraju. ipak Preporučamo, Popraviti problem koji se pojavio, prije nego što nastavite sa instalacijom.';
$LANG['check_files_title'] = 'Direktorij- i Dozvole datoteka';
$LANG['check_files_help'] = 'Za ispravan rad, BIGACE treba pisane(write) dozvole u slijedečim direktorijima&amp; datoteke. Ako vidite crvenu točku, morate popraviti Dozvole prije nego što nastavite.';

$LANG['config_consumer'] = 'Postavke Zajednice';
$LANG['config_admin'] = 'Administracijski Račun';

$LANG['community_install_good'] = '
<p>estitamo, instalacija je završena!</p>
<p>Ako u bilo koje vrijeme treba podršku, ili BIGACE ima pogrešku za ispravan rad, molim zapamtite ovo<a href="http://forum.bigace.de" target="_blank">Pomoć je dostupna</a> ako je želite.
<p>Vaš instalacijski direktorij još postoji. Dobro bi bilo da ga uklonite potpuno zbog sigurnosnih razloga.</p>
<p>Sada ti moe<a href="../../">Pogledaj svoju novo napravljenu web stranicu</a> i počni je koristiti. Moraš prvo biti siguran da si se Prijavio, jer nakon toga si u mogučnosti da pristupiš Administracijskom centru.</p>
<br />
<p>Sretno!</p>
<br /><br />
<p><a href="../../">Posjeti svoju stranicu</a></p>';

$LANG['community_install_bad']  = 'Problem prisutan prilikom instalacije.';
$LANG['community_install_infos']= 'Prikaži Sustavnu poruku...';

$LANG['error_db_connect'] = 'Ne mogu se spojiti sa domainom Baze podataka';
$LANG['error_db_select'] = 'Ne mogu se spojiti sa Bazom podataka';
$LANG['error_db_create'] = 'Ne mogu kreirati Bazu podataka.';
$LANG['error_read_dir'] = 'ne mogu pročitati Direktorij';
$LANG['error_created_dir'] = 'ne mogu napraviti Direktorij';
$LANG['error_removed_dir'] = 'Ne mogu izbrisati Direktorij';
$LANG['error_copied_file'] = 'Ne mogu kopirati datoteku';
$LANG['error_remove_file'] = 'Ne mogu izbrisati datoteku';
$LANG['error_close_file'] = 'Ne mogu zatvoriti datoteku';
$LANG['error_open_file'] = 'Pogreška: Ne mogu otvoriti datoteku';
$LANG['error_db_statement'] = 'Pogreška u DB izjavi';
$LANG['error_open_cconfig'] = 'Ne mogu otvoriti Zajednicu konfiguracijske datoteke';
$LANG['error_double_cconfig'] = 'Pogreška: Zajednica več postoji!';
$LANG['could_not_find_consumer'] = 'Pogreška: Ne mogu nači Zajednicu';
