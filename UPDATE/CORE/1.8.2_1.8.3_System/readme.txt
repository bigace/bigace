 
ATTENTION: Before updating, you have to make sure, that you update from the BIGACE Version 1.8.2!
If you have an earlier Version, update Step-by-Step to the Version 1.8.2.

-------------------------------------------------------------------------------------

###### Steps to fully perform the Update ######

1. Upload this Directory to your Installation at "/misc/install/update/".

2. Set delete rights for the following directorys recursive:

    /consumer/cid{CID}/modul/contactMail
    /consumer/cid{CID}/modul/guestbook
    /consumer/cid{CID}/modul/photoAlbum
    /consumer/cid{CID}/modul/sitemap
    /consumer/cid{CID}/modul/submenuPreview

   Those directorys wil be deleted, cause all Modules are available as 
   Updates now, you are admitted to use those!
   If those Directorys will not be deleted, each new Consumer will have all 
   (possibly old and not working) Modules. Your actual Modules will be overwritten 
   with the old version, each time you perform the Update "UpdateConsumerFiles"!

   It is really important that those directorys will be deleted.

3. Perform the Update from ONE of your Consumer.

4. If successful, delete the Update Directory "/misc/install/update/1.8.2_1.8.3_System".

5. Add the new Key:
      define ('_BIGACE_DIR_PATH',    '{BASE_DIR}');
   to  "system/config/config.system.php"
   where '{BASE_DIR}' should be replaced by your BaseDirectory Setting.
   BaseDirectory is the Directory where your Bigace Installation resists under your WebserverRoot.

   For example:
   a. You installed directly beneath under http://www.example.com/, take an empty String:
      define ('_BIGACE_DIR_PATH',    '');
   b. You installed under http://www.example.com/cms/, take the cms/ folder:
      define ('_BIGACE_DIR_PATH',    'cms/');
 
6. Install and perform the Update "1.8.2_1.8.3_Consumer" on each installed Consumer.

7. If successful, delete the Update Directory "/misc/install/update/1.8.2_1.8.3_Consumer".

8. Update all Files by uploading them to your FTP location, but do NOT
   overwrite "/system/config/config.system.php" and "/system/config/consumer.ini".

9. Perform the Update "UpdateConsumerFiles" on each installed Consumer.

10. Download, install and update all modules on each Consumer that uses a Modul.

Have fun!

-------------------------------------------------------------------------------------

P.S.: The next Update will be easier, promise ;-)
 