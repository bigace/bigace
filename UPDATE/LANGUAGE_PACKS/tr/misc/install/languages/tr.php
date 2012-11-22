<?php
/**
* Translation file for the installer.
* Language: Turkish
* Copyright (C) Kevin Papst
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
$LANG['intro'] = '... En kolay içerik yönetimi!';
$LANG['thanks'] = 'Bigace içerik yönetimini seçtiginiz için tesekkürler!';

// -----------------------------------------------------------------------------
// Navigation
$LANG['menu_title'] = 'Sistem Yükleme';
$LANG['menu_step_2'] = 'Sistem Kontrolü';
$LANG['menu_step_3'] = 'Yükleme';
$LANG['menu_step_4'] = 'Web Sayfasi olustur';
$LANG['menu_step_5'] = 'Yükleme tamamlandi';
$LANG['menu_step'] = 'Adim';
// -----------------------------------------------------------------------------

// -----------------------------------------------------------------------------
// Welcome Screen
$LANG['install_state'] = 'Durum';
$LANG['install_begin'] = 'Yüklemeyi başlat';
$LANG['introduction'] = 'Tanitim';
// -----------------------------------------------------------------------------

// -----------------------------------------------------------------------------
// Form Tooltip
$LANG['form_tip_close'] = 'Kapat';
$LANG['form_tip_hide'] = "Bu mesajı tekrar gösterme";
// -----------------------------------------------------------------------------

// -----------------------------------------------------------------------------
// LANGUAGES - chooser and names for languages
$LANG['language_choose'] = 'Dil Seçimi';
$LANG['language_text'] = 'Yükleme sirasinda kullanilacak dili seçin.';
$LANG['language_button'] = 'Dili Degistir';
// -----------------------------------------------------------------------------

$LANG['failure'] = 'Hatalar Olustu';
$LANG['new'] = 'Yeni';
$LANG['old'] = 'Eski';
$LANG['successfull'] = 'Basarili';
$LANG['main_menu'] = 'Ana Menü';
$LANG['back'] = 'Geri';
$LANG['next'] = 'Ileri';
$LANG['state_no_db'] = 'Veri tabani yüklenemez!';
$LANG['state_not_all_db'] = 'Veritabani yüklemesi tamamlanamadi!';
$LANG['state_installed'] = 'Sistem çekirdek bilesenleri basariyla yüklendi!';
$LANG['help_title'] = 'Yardim';
$LANG['help_text'] = 'Her adimda gerekli yardimi görebilmek için, mouse nizi her giris satirinin arkasinda bulunan yardim simgesinin üzerine getirin. Kisa bir yardim mesaji görüntülenecektir.<br>Örnek için takib eden simgenin üzerine gelin:';
$LANG['help_demo'] = 'Yardim mesajlarini görüntülemek için dogru yol :)!';
$LANG['db_install'] = 'Içerik yönetim sistemini yükle';
$LANG['cid_install'] = 'Web sayfasi ayarlari';
$LANG['install_finish'] = 'Yükleme tamamlandi';
$LANG['db_install_state'] = 'Durum: ';
$LANG['db_install_help'] = 'Bu adimda çekirdek bilesenler yüklenecektir bu noktada veritabani ve ilk web sayfaniz için bazi ayarlari yapmaniz gerekiyor';

//-----------------------------------------------------------
// Translation for the System installation Dialog
$LANG['db_value_title'] = 'Veritabani baglantisi';
$LANG['ext_value_title'] = 'Sistem yapilandirmasi';
$LANG['db_type'] = 'Veritabani türü';
$LANG['db_host'] = 'Sunucu/Ana makine';
$LANG['db_database'] = 'Veri tabani';
$LANG['db_user'] = 'Kullanici';
$LANG['db_password'] = 'Sifre';
$LANG['db_prefix'] = 'Tablo Baslangici';
$LANG['mod_rewrite'] = 'Apache MOD-Rewrite';
$LANG['mod_rewrite_yes'] = 'Modul active / Usage possible (.htaccess)';
$LANG['mod_rewrite_no'] = 'Not possible / Do not know';
$LANG['base_dir'] = 'Ana Dizin';
// Translation for the System Installation Help Images
$LANG['base_dir_help'] = 'Yüklemenin yapilacagi anadizinin adini girin. Eger sunucun ana dizinine yükleme yaptiysaniz bos birakin örn (http://www.sitenizinadi.com/). <br><b>Otomatik hesaplanan deger dogru olmalidir!</b><br>Dosya yolu slash (/) ile baslayamaz ancak slash (/) ile Bitmelidir. Örnegin &quot;http://www.sitenizinadi.com/cms/&quot;, belirlenen <b>cms/</b> dogru olmak zorunda.';
$LANG['mod_rewrite_help'] = '<b>Bu ayar basir URL leri hazirlar!</b><br/>Lütfen dogru yapilandirmalari seçtiginizden emin olun. Eger yanlis ayarlamalar yaparsaniz , sayfaniz gezilebilir olmaz. Emin degilseniz lütfen bos birakin!';
$LANG['db_password'] = 'Sifre';
$LANG['def_language'] = 'Varsayilan Dil';
$LANG['def_language_help'] = 'Içerik Yönetim sisteminiz için varsayilan dili seçin.';
$LANG['db_type_help'] = 'Kullanacaginiz veritabani türünü seçin.<br> Yükleme listede yeralan tüm veritabani sistemlerini destekler, çekirdek bilesen sistemi<b>Suan i,çin sadece MySQL veritabanini destekler</b> .<br>Eger MySQL disinda bir veri tabani kullanmaya karar verirseniz HIÇBIR SORUMLULUK BIZE AIT DEGILDIR!';
$LANG['db_host_help'] = 'Veritabani uygulamanizin yüklendigi anamkine bilgisini girin ( <b>localhost</b> yazmayi deneyin genelde oradadir :)!).';
$LANG['db_database_help'] = 'Veritabani adini yazin (PhpMyadminde sol tarafta görünen isim).';
$LANG['db_user_help'] = 'veritabani üzerinde yazma yetkisine sahip kullanici adini girin.';
$LANG['db_prefix_help'] = 'Veritabani tablo önekini belirleyin. Benzersiz bir isim kullanmaya çalisinki herzaman kolayca ayirt edilebilsin. Eger bunun ne anlama geldigini bilmiyorsaniz lütfen varsayilan degeri kullanin.';
$LANG['db_password_help'] = 'Yukariya yazdiginiz kullanici için sifre girin.';
$LANG['db_already_exists'] = 'Veritabani dogru sekilde yüklenmis görünüyor bu adim atlanacak.';

$LANG['htaccess_security'] = 'Apache .htaccess Özelligi';
$LANG['htaccess_security_yes'] = 'Mevcut dosyanin üzerine yazmayi etkinlestirin (.htaccess)';
$LANG['htaccess_security_no'] = 'Mümkün degil / Ne oldugunu bilmiyorum';
$LANG['htaccess_security_help']	= '<b>Bu içeriginizin güvenligi için gerekli bir yardir!</b><br/>:Kullandiginiz server in buna izin verip vermediginden emin olun  .htaccess dosyalari üzerinden. Eger emin degilseniz oldugu gibi birakin!';
//-----------------------------------------------------------

// Translation for Consumer Installation
// First Dialog
$LANG['error_enter_domain'] = 'Web sayfanizin kurulmasi için geçerli bir URL girin';
$LANG['error_enter_adminuser'] = 'Yönetici hesabi için en az 4 karakterden olusn bir isim girin';
$LANG['error_enter_adminpass'] = 'Lütfen yönetici için en az 4 karakterden olusan(8 karakter ve üzeri tavsiye edilir) bir sifre olusturun.';

$LANG['cid_check_failed'] = 'Dosya yazma yetkileri kontrolü basarisiz oldu devam etmeden önce onlari düzeltmelisiniz!';
$LANG['cid_domain'] = 'Domain';
$LANG['cid_id_help'] = 'Yeni sayfaniz için bir kimlik belirleyin bunusadece yönetim panelinden grebileceksiniz';
$LANG['cid_domain_help'] = 'Yeni web sayfanizza tanimlanacak olan domain i girin. Otomatik belirlenen seçenek genelde dogrudur .<br><b>NOT: Sunucu yolu veya slash le biten birseyler eklemeyin!</b>';

$LANG['statistics'] = 'Istatiskler';
$LANG['statistics_on'] = 'Istatistikleri etkinlestir';
$LANG['statistics_off'] = 'Istatistikleri devre disi birak';
$LANG['statistics_help'] = 'Ister etkinlestirin ister devre disi birakin tamamen size bagli etkin olmasi durumnda sistem veritabanina sürekli günlükler yazacaktir.';
$LANG['sitename'] = 'Site adi';
$LANG['sitename_help'] = 'Site basligini girin. Sablonlarda kullanilabilir ve yönetim paneli kullanilarak çok kolay bir sekilde degistirilebilir.';
$LANG['mailserver'] = 'E-posta sunucus';
$LANG['mailserver_help'] = 'E-posta gönderirken kullanilacak posta sunucusunu girin(mail.siteadi.com seklinde olur genelde). Eger sandart PHP proxy kullaniyorsaniz bos birakin(genelde shared hosting lerde kullanilir).';
$LANG['webmastermail'] = 'E-posta Adresi';
$LANG['webmastermail_help'] = "Yöetici hesabui için e-posta adresi girin.";
$LANG['bigace_admin'] = 'Kullanici adi';
$LANG['bigace_password'] = 'Sifre';
$LANG['bigace_check'] = 'Sifre (tekrar)';
$LANG['bigace_admin_help'] = 'Yöenetici hesabi içi kullanici adi girin. Bu yöetici hesabi tüm yetkilere sahip olacaktir.';
$LANG['bigace_password_help'] = 'Yönetici hesabi için sifre belirleyin.';
$LANG['bigace_check_help'] = 'Lütfen sifrenizi dogrulayin eger sifreniz dogrulanamazsa tekrar bu ekrana döneceksiniz.';
$LANG['create_files'] = 'Dosya Sistemi olusturuluyor';
$LANG['save_cconfig'] = 'Sayfa yapilandirmasini kaydet';
$LANG['added_consumer'] = 'Eklenen site';
$LANG['added_consumer'] = 'Mevcut site eklendi';
$LANG['community_exists'] = 'Eklenen domain için bir yükleme zaten var lütfen baska bir domain girin.';

$LANG['check_reload'] = 'Önkontrolü tekrar yap';
$LANG['check_up']               = 'Önkonrol';
$LANG['check_up_help'] = 'DIKKAT: Lütfen BIGACE yüklemesine vaya baska bir site eklemeye baslamadan önce gerekli öçn kontrolleri yapin or add a new Community!';

$LANG['check_yes'] = 'Evet';
$LANG['check_no'] = 'Hayir';
$LANG['check_on'] = 'Açik';
$LANG['check_off'] = 'Kapali';
$LANG['check_status'] = 'Durum';
$LANG['check_setting'] = 'Yapilandirma';
$LANG['check_recommended'] = 'Tavsiye edilen';
$LANG['check_install_help'] = 'Eger bayraklardan biri veya birkaçi kirmiziysa Apache ve PHP yapilandirmanizi düzeltmeniz veya gerekli ayarömalalari yapmaniz gerekir. Yapilmamasi durumunda yükleme tamamlanayabilir veya yanlis/eksik yükleme yapilabilir bu durumda sorumluluk bana ait degildir.';
$LANG['required_settings_title']= 'Zorumlu yapilandirmalar';
$LANG['check_settings_title'] = 'Tavsiye edilen yapilandirmalar';
$LANG['check_settings_help'] = 'BIGACE in saglikli çalismasi için takip eden ayarlamalar yapilmak zorundadir. <br><br>TIçerik yönetim sistemi yapilandirmalarin bazilarinin eksik veya hatali olmasi halindede çalisacaktir. Ancak tavsiyemiz yüklemeye geçmeden önce mevcut problermlerin giderilmesi yönünde olacaktir.';
$LANG['check_files_title'] = 'Dosya ve dizin yetkileri';
$LANG['check_files_help'] = 'BIGace in dogru çalismasi gösterilen doya ve dizinlerde bazi yazma yetkilerine gereksinim duyar &amp; . Eger kirmizi isaret görüyorsaniz yüklemeye geçmeden önce bu dosya veya dizinlerin yazma izinlerini düzeltmeniz gerekecektir.';

$LANG['config_consumer'] = 'Site ayarlari';
$LANG['config_admin'] = 'Yönetici hesabi';

$LANG['community_install_good'] = '
<p>Tebrikler yükleme süreci tamamlandi!</p>
<p>BIGACE le ilgili yardima veya destege ihtiyaç duydugunda unutmaki burda <a href="http://forum.bigace.de" target="_blank">yardim bulabilirsdin</a> Eger istersen :).
<p>Yükleme dizini hala sunucuda bulunuyor güvenliginiz açisindan tamamen silinmesini tavsiye ediyoruz.</p>
<p>Simdi <a href="../../">Yeni kurulan web sitene göz atip</a> kullanmaya baslayabilirsin. Yönetim paneline girmeden önce sisteme giris yaptiginizdan emin olmalisiniz.</p>
<br />
<p>Bol sans!</p>
<br /><br />
<p><a href="../../">Yeni web sayfanizi ziyaret edin</a></p>';

$LANG['community_install_bad'] 	= 'Kurulum sirasinda sorun olustu.';
$LANG['community_install_infos']= 'Sistem mesajlarini gösterir...';

$LANG['error_db_connect'] = 'Veritaani sunucusuna baglanilamadi';
$LANG['error_db_select'] = 'Veritabani seçilemedi';
$LANG['error_db_create'] = 'Veritabani olusturulamadi.';
$LANG['error_read_dir'] = 'Dizin okunamadi';
$LANG['error_created_dir'] = 'Dizin olusturulamadi';
$LANG['error_removed_dir'] = 'Dizin silinemedi';
$LANG['error_copied_file'] = 'Dosya kopyalanamadi';
$LANG['error_remove_file'] = 'Dosya silinemedi';
$LANG['error_close_file'] = 'Dosya kapatilamadi';
$LANG['error_open_file'] = 'Hata: Dosya açilamadi';
$LANG['error_db_statement'] = 'Veritabanainda hata';
$LANG['error_open_cconfig'] = 'Site yapilandirma dosyasi açilamadi';
$LANG['error_double_cconfig'] = 'Hata: Site zaten avar!';
$LANG['could_not_find_consumer'] = 'Hata: Site bulunamadi';
