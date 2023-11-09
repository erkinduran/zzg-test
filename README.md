## About ZZG Test Projesi

- Env dosyasında XML_SOURCE elemanı eklenmiş olmalı.
- Komutu çalıştırmak için konsolda `php artisan get-xml-data` çalıştırılmalı.
- Komut, `app/Console/Kernel.php` schedule methoduna saatlik şedül olarak eklendi.
- XML dosyalarını kayıttan önce `storage/app/XmlData` klasörüne kayıt edilmektedir.
- Datanın json tipli dosyaları `storage/app/JsonData` klasöründe kayıt edilmektedir.
- Test için `.env.testing` gerekmektedir.
