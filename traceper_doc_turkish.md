# traceper #
<table>
<tr>
<td width='65%'>
traceper cep telefonları üzerinden çalışan bir GPS takip sistemidir. traceper <b>ücretsizdir</b>, <b>açık kaynak kodludur</b>, <b>basittir</b> ve <b>herhangi bir GPS vericisine ihtiyaç duymaz</b>, sadece GPS özelliği olan cep telefonlarını kullanır. (yakın zamanda tüm cep telefonlarının GPS özelliği olacağını düşünüyoruz.)<br>
<br>
Şimdi traceper hakkında biraz bilgi verelim, traceper da iki tane yazılım bulunmaktadır. Bunlardan bir tanesi cep telefonlarında çalışır ve GPS verisini sunucuya ayarlanabilen<br>
aralıklarla gönderir, diğeri ise web uygulaması olup <a href='http://code.google.com/apis/maps/'>Google Maps API</a>, PHP, MySQL, Javascript, Ajax kullanarak geliştirilmiştir ve  kullanıcıların nerede olduğunu google map üzerinde gösterir.<br>
<br>
Cep telefonlarında çalışan uygulama <a href='http://www.android.com/'>Android</a> işletim sistemi için yazılmış olup diğer platformlarda(J2ME) çalışan versiyonlarının da yazılması planlanmaktadır.<br>
</td>
<td width='35%' align='center'>
<img src='http://farm3.static.flickr.com/2780/4326989313_96a065fd8b_o.png' />
</td>
</tr>
</table>

### Özellikler ###
> traceper iki yazılımdan oluştur, birisi web uygulaması,diğeri ise Android istemcisidir.
> #### Web Uygulaması ####
  * Kullanıcıların listesini ve yeryüzünde nerede olduğunu gösterebilme
> [![](http://farm3.static.flickr.com/2794/4327692370_d81d706e64_m.jpg)](http://www.flickr.com/photos/38235533@N05/4327692370/in/set-72157623216889001/)
  * Tıklanabilen kullanıcı ikonları ilgili kullanıcı hakkında detaylı bilgi verebilme
> [![](http://farm5.static.flickr.com/4020/4326961165_405478fd70_m.jpg)](http://www.flickr.com/photos/38235533@N05/4326961165/in/set-72157623216889001/)
  * Yakınlaştırabilme
> [![](http://farm5.static.flickr.com/4059/4326963189_ec4797522a_m.jpg)](http://www.flickr.com/photos/38235533@N05/4326963189/in/set-72157623216889001/)
  * Kullanıcı arayabilme ve takip edebilme
> [![](http://farm5.static.flickr.com/4060/4326962673_de4eed3e04_m.jpg)](http://www.flickr.com/photos/38235533@N05/4326962673/in/set-72157623216889001/)


> #### Android İstemcisi ####
  * Sunucu ile bağlantıya geçerek kullanıcı yetkilendirme
> [![](http://farm5.static.flickr.com/4031/4327701894_29d4984edb_m.jpg)](http://www.flickr.com/photos/38235533@N05/4327701894/in/set-72157623216889001/)
  * GPS Konum bilgisini sunucuya gönderme
> [![](http://farm5.static.flickr.com/4044/4327701968_edffc6fdb2_m.jpg)](http://www.flickr.com/photos/38235533@N05/4327701968/in/set-72157623216889001/)
  * Uygulamayı kapatma
> [![](http://farm5.static.flickr.com/4072/4327702022_8b821b27d0_m.jpg)](http://www.flickr.com/photos/38235533@N05/4327702022/in/set-72157623216889001/)
  * Uygulamayı arka planda çalıştırabilme
> [![](http://farm3.static.flickr.com/2781/4327702112_bbf3354a98_m.jpg)](http://www.flickr.com/photos/38235533@N05/4327702112/in/set-72157623216889001/)
  * Sunucudan hangi aralıklarda GPS konum bilgisini göndereceğini alabilme

### Kurulum ###
İşaretlenmiş traceper Android istemcisi, kaynak kodu ve web uygulaması <a href='http://code.google.com/p/traceper/downloads/list'>traceper_DDMMMYYY.zip</a> dosyasında bulunmaktadır.

Web uygulamasını kurmak için aşağıdaki tabloyu MySQL veritabanında oluşturunuz
```
CREATE TABLE  `tracker_users` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(12) DEFAULT NULL,
  `password` char(32) NOT NULL,
  `group` int(10) unsigned NOT NULL DEFAULT '0',
  `latitude` decimal(8,6) NOT NULL DEFAULT '0.000000',
  `longitude` decimal(9,6) NOT NULL DEFAULT '0.000000',
  `altitude` decimal(15,6) NOT NULL DEFAULT '0.000000',
  `realname` varchar(80) NOT NULL,
  `email` varchar(100) NOT NULL,
  `dataArrivedTime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deviceId` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `username` (`username`) USING BTREE,
  KEY `realname_email` (`realname`,`email`) USING BTREE,
  KEY `dataArrivedTime` (`dataArrivedTime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8
```

<a href='http://code.google.com/p/traceper/source/browse/trunk/WebInterface/includes/config.php'>"/includes/config.php"</a> dosyası içinde veritabanı parametrelerini kendi veritabanınıza göre düzenleyiniz ve  <a href='http://code.google.com/apis/maps/signup.html'>google map api key</a> alarak GOOGLE\_MAP\_API\_KEY makrosunu düzenleyiniz, diğer parametreleri de ihtiyaçlarınıza göre  düzenleyebilirsiniz.

Web uygulaması bir http sunucusunda ve android istemcisi bir emulatorde veya cep telefonunda çalıştıktan sonra

  * traceper Android istemcisinde menü düğmesine tıklayınız.
> [![](http://farm5.static.flickr.com/4064/4332642274_468c22ee45_m.jpg)](http://www.flickr.com/photos/38235533@N05/4332642274/)


  * Settings düğmesine basınız ve http sunucusunun adresini giriniz.
> [![](http://farm3.static.flickr.com/2765/4332642332_0643e0c0ec_m.jpg)](http://www.flickr.com/photos/38235533@N05/4332642332/)

  * Register menü seçeneğinine tıklayınız ve kayıt olunuz.
  * traceper Android istemcisine kayıt olurkenki kullanıdığınız kullanıcı adı ve şifre ile giriş yaptıktan sonra nerede olduğunuzu görebilirsiniz!
> [![](http://farm3.static.flickr.com/2719/4332662150_cb63f1ba40_m.jpg)](http://www.flickr.com/photos/38235533@N05/4332662150/)

### Demo ###



### Destek ###
Bu yazılımın kullanımı veya geliştirilmesi hakkında bilgiye ihtiyacınız olursa sahip olduğumuz
tüm bilgileri sizinle paylaşmaya hazırız, bu yüzden iletişime geçmek için
tereddüt yaşamayınız.

### Lisans ###
Bu yazılım ücretsizdir, haber vermeden dağıtılabilir veya değiştirilebilir.

### Tekzip ###
Bu yazılım hiç bir şeyi garanti etmez, yazılımı kendi riskinizle kullanınız.
Herhangi bir durumda sorumluluk kabul edilmez.

### Proje Takımı ###