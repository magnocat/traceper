# traceper #
<table>
<tr>
<td width='65%'>
traceper is a GPS tracking system via mobile phones, it is <b>free</b>, it is <b>open source</b>, it is <b>simple</b> and <b>it does not require any GPS tracking device</b>, it only uses mobile phones and we think that all mobile phones will support GPS in the near future.<br>
<br>
Then let's give some info about traceper, there are two softwares in the project. One of them is running at cell phones and sends GPS data to a server by using internet connection.The other one is a web application developed by using <a href='http://code.google.com/apis/maps/'>Google Maps API</a>, PHP, MySQL, Javascript, Ajax to show where the users are.<br>
<br>
Application running at cell phones is now available for <a href='http://www.android.com/'>Android</a> and other versions of this application, running different platforms, will be developed in order to make traceper more usable.<br>
</td>
<td width='35%' align='center'>
<img src='http://farm3.static.flickr.com/2780/4326989313_96a065fd8b_o.png' />
</td>
</tr>
</table>

### Features ###
> traceper consists of two softwares, one is Web Application and the other one is Android Client
> #### Web Application ####
  * Showing users' positions on Earth and listing users
> [![](http://farm3.static.flickr.com/2794/4327692370_d81d706e64_m.jpg)](http://www.flickr.com/photos/38235533@N05/4327692370/in/set-72157623216889001/)
  * Clickable users' icons on the map to show details about that user
> [![](http://farm5.static.flickr.com/4020/4326961165_405478fd70_m.jpg)](http://www.flickr.com/photos/38235533@N05/4326961165/in/set-72157623216889001/)
  * Zooming
> [![](http://farm5.static.flickr.com/4059/4326963189_ec4797522a_m.jpg)](http://www.flickr.com/photos/38235533@N05/4326963189/in/set-72157623216889001/)
  * Searching and tracking a user
> [![](http://farm5.static.flickr.com/4060/4326962673_de4eed3e04_m.jpg)](http://www.flickr.com/photos/38235533@N05/4326962673/in/set-72157623216889001/)


> #### Android Client ####
  * Authenticating the users by communicating the server
> [![](http://farm5.static.flickr.com/4031/4327701894_29d4984edb_m.jpg)](http://www.flickr.com/photos/38235533@N05/4327701894/in/set-72157623216889001/)
  * Sending location data to the server
> [![](http://farm5.static.flickr.com/4044/4327701968_edffc6fdb2_m.jpg)](http://www.flickr.com/photos/38235533@N05/4327701968/in/set-72157623216889001/)
  * Exiting the application
> [![](http://farm5.static.flickr.com/4072/4327702022_8b821b27d0_m.jpg)](http://www.flickr.com/photos/38235533@N05/4327702022/in/set-72157623216889001/)
  * Running the application in background
> [![](http://farm3.static.flickr.com/2781/4327702112_bbf3354a98_m.jpg)](http://www.flickr.com/photos/38235533@N05/4327702112/in/set-72157623216889001/)
  * Getting the minimum time and distance interval from the server to send GPS data

### Install ###
Signed version, source code of traceper android client and web application is in the <a href='http://code.google.com/p/traceper/downloads/list'>traceper.zip</a>.

To install web application, create the following table in MySQL database
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

Edit the database parameters and get a <a href='http://code.google.com/apis/maps/signup.html'>google map api key</a> to edit the GOOGLE\_MAP\_API\_KEY macro  in <a href='http://code.google.com/p/traceper/source/browse/trunk/WebInterface/includes/config.php'>"/includes/config.php"</a> also other parameters can be edited according to your needs.

After web application runs on a http server and traceper android client runs on a emulator or a phone.

  * Click menu button on traceper android client application.
> [![](http://farm5.static.flickr.com/4064/4332642274_468c22ee45_m.jpg)](http://www.flickr.com/photos/38235533@N05/4332642274/)


  * Click settings and enter the http address of web application.
> [![](http://farm3.static.flickr.com/2765/4332642332_0643e0c0ec_m.jpg)](http://www.flickr.com/photos/38235533@N05/4332642332/)

  * Click Register menu item and register yourself.
  * After that you can login to traceper android client with the username and password that you have just entered in registration
  * Lastly here it is where you are!
> [![](http://farm3.static.flickr.com/2719/4332662150_cb63f1ba40_m.jpg)](http://www.flickr.com/photos/38235533@N05/4332662150/)

### Demo ###



### Support ###
If you need support to modify and use this software,
We can share all information we have,
so feel free to <a href='mailto:ahmetmermerkaya@gmail.com'>contact us</a>

### License ###
This software is free.
It can be modified and distributed without notification.

### Disclaimer ###
This software guarantees nothing, use it with your own risk.
No responsilibity is taken for any situation.

### Project Team ###