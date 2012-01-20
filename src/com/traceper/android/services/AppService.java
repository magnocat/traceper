
package com.traceper.android.services;

import java.io.BufferedReader;
import java.io.ByteArrayOutputStream;
import java.io.DataOutputStream;
import java.io.IOException;
import java.io.InputStreamReader;
import java.net.HttpURLConnection;
import java.net.MalformedURLException;
import java.net.URL;
import java.net.URLConnection;
import java.util.ArrayList;
import java.util.Iterator;

import org.json.JSONException;
import org.json.JSONObject;

import android.app.AlarmManager;
import android.app.Notification;
import android.app.NotificationManager;
import android.app.PendingIntent;
import android.app.Service;
import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.content.IntentFilter;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.location.Location;
import android.location.LocationManager;
import android.location.LocationProvider;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;
import android.os.Binder;
import android.os.Bundle;
import android.os.IBinder;
import android.os.SystemClock;
import android.telephony.TelephonyManager;
import android.util.Log;

import com.traceper.R;
import com.traceper.android.Configuration;
import com.traceper.android.interfaces.IAppService;

public class AppService extends Service implements IAppService{

	private static final String REQUEST_LOCATION = "com.traceper.android.services.GET_LOCATION";
	private static final String SEND_LOCATION = "com.traceper.android.services.SEND_LOCATION";
	private static final String GET_GPS_LOCATION = "com.traceper.android.services.GET_GPS_LOCATION";
	private static final String GET_NETWORK_LOCATION = "com.traceper.android.services.GET_NETWORK_LOCATION";
	private ConnectivityManager conManager = null; 
	private LocationManager locationManager = null;
	private String deviceId;
	private boolean isUserAuthenticated = false;
	private NotificationManager mManager;
	private static int NOTIFICATION_ID = 0;
	/**
	 * this list stores the locations couldnt be sent to server due to lack of network connectivity
	 */
	private ArrayList<Location> pendingLocations = new ArrayList<Location>();

	private class Image {
		public byte[] image;
		public boolean isPublic;
		public String description;
		public Image(byte[] image, boolean isPublic, String description) {
			super();
			this.image = image;
			this.isPublic = isPublic;
			this.description = description;
		}
	}

	private Image pendingImage = null;
	private final IBinder mBinder = new IMBinder();

	//	private NotificationManager mNM;
	private String email;
	private String password;
	private String authenticationServerAddress;
	private Long lastLocationSentTime;


	private int minDataSentInterval = Configuration.MIN_GPS_DATA_SEND_INTERVAL;
	private int minDistanceInterval = Configuration.MIN_GPS_DISTANCE_INTERVAL;
	private BroadcastReceiver networkStateReceiver;
	private String cookieName = null;
	private String cookieValue = null;
	private boolean configurationChanged = false;
	private boolean autoCheckinEnabled;
	private PendingIntent getLocationIntent;
	private AlarmManager am;
	private PendingIntent sendLocation;
	private PendingIntent gpsLocationIntent;
	private PendingIntent networkLocationIntent;
	private Location gpsLocation;
	private Location networkLocation;
	private Location lastSentLocation;


	public class IMBinder extends Binder {
		public IAppService getService() {
			return AppService.this;
		}		
	}

	public void onCreate() 
	{   	
		startForeground(0, null);
		conManager = (ConnectivityManager) getSystemService(Context.CONNECTIVITY_SERVICE);
		locationManager = (LocationManager) getSystemService(Context.LOCATION_SERVICE);
		deviceId = ((TelephonyManager) getSystemService(TELEPHONY_SERVICE)).getDeviceId();
		mManager = (NotificationManager) getSystemService(Context.NOTIFICATION_SERVICE);

		Intent intent = new Intent(AppService.this, AppService.class);
		intent.setAction(REQUEST_LOCATION);
		getLocationIntent = PendingIntent.getService(AppService.this, 0, intent, 0);

		intent = new Intent(AppService.this, AppService.class);
		intent.setAction(GET_GPS_LOCATION);
		gpsLocationIntent = PendingIntent.getService(this, 0, intent, 0);

		intent = new Intent(AppService.this, AppService.class);
		intent.setAction(GET_NETWORK_LOCATION);
		networkLocationIntent = PendingIntent.getService(this, 0, intent, 0);

		Intent sendLocationIntent = new Intent(AppService.this, AppService.class);
		sendLocationIntent.setAction(SEND_LOCATION);
		sendLocation = PendingIntent.getService(AppService.this, 0, sendLocationIntent, 0);

		am = (AlarmManager)getSystemService(ALARM_SERVICE);			

		networkStateReceiver = new BroadcastReceiver() {
			@Override
			public void onReceive(Context context, Intent intent) {
				// when connection comes, send pending locations
				if (isNetworkConnected() == true) {
					sendPendingLocations();
				}
			}
		};

		IntentFilter filter = new IntentFilter(ConnectivityManager.CONNECTIVITY_ACTION);        
		registerReceiver(networkStateReceiver, filter);
	}

	@Override
	public int onStartCommand(Intent intent, int flags, int startId) {
		if (intent != null) {
			String action = intent.getAction();
			if (action != null) {
				if (action.equals(REQUEST_LOCATION)) {
					boolean gps_enabled = locationManager.isProviderEnabled(LocationManager.GPS_PROVIDER);;
					boolean network_enabled = locationManager.isProviderEnabled(LocationManager.NETWORK_PROVIDER);
					networkLocation = null;
					gpsLocation = null;
					boolean requestStarted = false;
					if (network_enabled == true) {
						locationManager.removeUpdates(networkLocationIntent);
						locationManager.requestLocationUpdates(LocationManager.NETWORK_PROVIDER, 0, 0, networkLocationIntent);
						requestStarted = true;
					}
					else if (gps_enabled == true) {
						locationManager.removeUpdates(gpsLocationIntent);
						locationManager.requestLocationUpdates(LocationManager.GPS_PROVIDER, 0, 0, gpsLocationIntent);
						requestStarted = true;
					}


					if (requestStarted == true) 
					{
						Notification notification = new Notification(R.drawable.icon, getString(R.string.ApplicationName), System.currentTimeMillis());
						PendingIntent contentIntent = PendingIntent.getActivity(getApplicationContext(), 0, null, 0);

						notification.setLatestEventInfo(AppService.this,
								getString(R.string.ApplicationName), getString(R.string.waiting_location), contentIntent);	
						mManager.notify(NOTIFICATION_ID , notification);
						am.set(AlarmManager.ELAPSED_REALTIME_WAKEUP, SystemClock.elapsedRealtime() + 60000, sendLocation);
					}
					else {
						notifyNoProviderEnabled();
					}
				}
				else if (action.equals(SEND_LOCATION)) {
					locationManager.removeUpdates(gpsLocationIntent);
					locationManager.removeUpdates(networkLocationIntent);
					if (networkLocation != null) {
						sendLocation(networkLocation);
					}
					else if(gpsLocation != null){
						sendLocation(gpsLocation);
					}
					else {
						networkLocation = null;
						gpsLocation = null;
						Notification notification = new Notification(R.drawable.icon, getString(R.string.ApplicationName), System.currentTimeMillis());

						PendingIntent contentIntent = PendingIntent.getActivity(getApplicationContext(), 0, null, 0);

						notification.setLatestEventInfo(AppService.this,
								getString(R.string.ApplicationName), getString(R.string.location_fix_problem), contentIntent);	

						mManager.notify(NOTIFICATION_ID , notification);
					}
				}
				else if(action.equals(GET_GPS_LOCATION)) {
					Bundle extras = intent.getExtras();
					if (extras.containsKey(LocationManager.KEY_LOCATION_CHANGED)) {
						gpsLocation = (Location)intent.getExtras().getParcelable(LocationManager.KEY_LOCATION_CHANGED);
						locationManager.removeUpdates(gpsLocationIntent);
						sendBestLocation();
					}
				}
				else if(action.equals(GET_NETWORK_LOCATION)) {
					Bundle extras = intent.getExtras();
					boolean gps_enabled = locationManager.isProviderEnabled(LocationManager.GPS_PROVIDER);;

					if (extras.containsKey(LocationManager.KEY_PROVIDER_ENABLED)) {
						boolean network_enabled = intent.getExtras().getBoolean(LocationManager.KEY_PROVIDER_ENABLED);
						if (network_enabled == false) {
							locationManager.removeUpdates(networkLocationIntent);
							if (gps_enabled == true) {
								locationManager.removeUpdates(gpsLocationIntent);
								locationManager.requestLocationUpdates(LocationManager.GPS_PROVIDER, 0, 0, gpsLocationIntent);
							}
							else {
								notifyNoProviderEnabled();
							}
						}
					}

					if (extras.containsKey(LocationManager.KEY_STATUS_CHANGED)) 
					{
						int status = extras.getInt(LocationManager.KEY_STATUS_CHANGED);
						if (status == LocationProvider.OUT_OF_SERVICE ||
								status == LocationProvider.TEMPORARILY_UNAVAILABLE) 
						{
							locationManager.removeUpdates(networkLocationIntent);
							if (gps_enabled == true) {
								locationManager.removeUpdates(gpsLocationIntent);
								locationManager.requestLocationUpdates(LocationManager.GPS_PROVIDER, 0, 0, gpsLocationIntent);
							} 
							else {
								notifyNoProviderEnabled();
							}
						}
					}

					if (extras.containsKey(LocationManager.KEY_LOCATION_CHANGED)) 
					{
						networkLocation = (Location)extras.getParcelable(LocationManager.KEY_LOCATION_CHANGED);
						locationManager.removeUpdates(networkLocationIntent);
						float accuracy =  networkLocation.getAccuracy();
						Log.i("network location accuracy", " " + accuracy);
						if (gps_enabled == false) {
							sendLocation(networkLocation);
						}
						else 
						{
							locationManager.removeUpdates(gpsLocationIntent);
							locationManager.requestLocationUpdates(LocationManager.GPS_PROVIDER, 0, 0, gpsLocationIntent);
						}
					}
				}
			}
		}
		return super.onStartCommand(intent, flags, startId);
	}

	public void setAutoCheckin(boolean enable){

		if (enable == true && autoCheckinEnabled == false) {			
			sendLocation(minDataSentInterval, minDistanceInterval);
		}
		else if (enable == false){
			am.cancel(getLocationIntent);
		}
		autoCheckinEnabled = enable;
	}

	public void sendLocationNow(){
		sendLocation(0, 0);
	}

	@Override
	public boolean uploadImage(byte[] picture, boolean publicData, String description) {

		if (lastSentLocation == null || 
				lastSentLocation.getTime() + Configuration.LOCATION_TIMEOUT_BEFORE_UPLOADING < System.currentTimeMillis() ){
			sendLocationNow();
			pendingImage = new Image(picture, publicData, description);
			return false;
		}		


		final Notification notification = new Notification(R.drawable.icon, getString(R.string.ApplicationName), System.currentTimeMillis());

		final PendingIntent contentIntent = PendingIntent.getActivity(getApplicationContext(), 0, null, 0);

		notification.setLatestEventInfo(this,
				getString(R.string.ApplicationName), getString(R.string.uploading), contentIntent);

		mManager.notify(NOTIFICATION_ID , notification);

		BitmapFactory.Options options = new BitmapFactory.Options();
		int inSampleSize = 2;
		int quality = 75;
		if (picture.length > 1000000) {
			inSampleSize = 6;
			quality = 50;
		}					
		options.inSampleSize = inSampleSize;
		Bitmap bitmap = BitmapFactory.decodeByteArray(picture, 0, picture.length, options);
		int byteCount = bitmap.getRowBytes();
		ByteArrayOutputStream byteArrayOutputStream = new ByteArrayOutputStream(byteCount);
		bitmap.compress(Bitmap.CompressFormat.JPEG, quality, byteArrayOutputStream);


		String result = sendImage(byteArrayOutputStream.toByteArray(), publicData, description, lastSentLocation);
		String notificationText = getString(R.string.upload_failed);
		boolean operationResult = false;
		if (result.equals("1")) {
			notificationText = getString(R.string.upload_succesfull);
			operationResult = true;
		}

		notification.setLatestEventInfo(getApplicationContext(), getString(R.string.ApplicationName), notificationText, contentIntent);
		mManager.notify(NOTIFICATION_ID, notification);

		return operationResult;
	}

	public void sendLocation(final int datasentInterval, final int distanceInterval) {

		boolean gps_enabled = locationManager.isProviderEnabled(LocationManager.GPS_PROVIDER);;
		boolean network_enabled = locationManager.isProviderEnabled(LocationManager.NETWORK_PROVIDER);

		try {
			if (datasentInterval > 0) {
				am.cancel(getLocationIntent);
				am.setRepeating(AlarmManager.ELAPSED_REALTIME_WAKEUP, 0, AppService.this.minDataSentInterval, getLocationIntent);
			}
			else {
				am.set(AlarmManager.ELAPSED_REALTIME_WAKEUP, 0, getLocationIntent);
			}

		} catch (Exception ex) {

		}

		if (network_enabled == false && gps_enabled == false){
			PendingIntent contentIntent = PendingIntent.getActivity(getApplicationContext(), 0, null, 0);
			Notification notification = new Notification(R.drawable.icon, getString(R.string.ApplicationName), System.currentTimeMillis());

			notification.setLatestEventInfo(AppService.this,
					getString(R.string.ApplicationName), getString(R.string.no_location_provider), contentIntent);	

			mManager.notify(NOTIFICATION_ID , notification);
		}


	}



	private void sendPendingLocations(){
		Iterator<Location> iterator = pendingLocations.iterator();
		while (iterator.hasNext()) {
			Location location = (Location) iterator.next();
			String result = sendLocationDataAndParseResult(location);
			if (result.equals("1")) {
				iterator.remove();
			}
		}
	}

	public IBinder onBind(Intent intent) 
	{
		return mBinder;
	}

	private String sendLocationData(Location loc) 
	{		
		double latitude = 0;
		double longitude = 0;
		double altitude = 0;
		if (loc != null) {
			latitude = loc.getLatitude();
			longitude = loc.getLongitude();
			altitude = loc.getAltitude();
		}
		String[] name = new String[6];
		String[] value = new String[6];
		name[0] = "r";
		name[1] = "latitude";
		name[2] = "longitude";
		name[3] = "altitude";
		name[4] = "deviceId";
		name[5] = "time";

		value[0] = "users/takeMyLocation";
		value[1] = String.valueOf(latitude);
		value[2] = String.valueOf(longitude);
		value[3] = String.valueOf(altitude);
		value[4] = this.deviceId;
		value[5] = String.valueOf((int)(loc.getTime()/1000)); // convert milliseconds to seconds

		String httpRes = this.sendHttpRequest(name, value, null, null);

		String result = getString(R.string.unknown_error_occured);

		try {
			JSONObject jsonObject = new JSONObject(httpRes);
			result = jsonObject.getString("result");
			if (result.equals("1")) 
			{		
				int dataSentInterval = Integer.parseInt(jsonObject.getString("minDataSentInterval"));
				int distanceInterval = Integer.parseInt(jsonObject.getString("minDistanceInterval"));
				if (dataSentInterval != this.minDataSentInterval || distanceInterval != this.minDistanceInterval){
					this.configurationChanged  = true;
					this.minDataSentInterval = dataSentInterval;
					this.minDistanceInterval = distanceInterval;
				}

				lastLocationSentTime = System.currentTimeMillis();
				Intent i = new Intent(IAppService.LAST_LOCATION_DATA_SENT_TIME);
				i.setAction(IAppService.LAST_LOCATION_DATA_SENT_TIME);
				i.putExtra(IAppService.LAST_LOCATION_DATA_SENT_TIME, lastLocationSentTime);
				i.putExtra(IAppService.LAST_LOCATION, loc);
				sendBroadcast(i);
				Log.i("broadcast sent", "sendLocationData broadcast sent");		

			}

		} catch (JSONException e) {
			e.printStackTrace();
		}


		return result;	
	}

	public void notifyNoProviderEnabled(){
		Notification notification = new Notification(R.drawable.icon, getString(R.string.ApplicationName), System.currentTimeMillis());
		PendingIntent contentIntent = PendingIntent.getActivity(getApplicationContext(), 0, null, 0);
		notification.setLatestEventInfo(AppService.this,
				getString(R.string.ApplicationName), getString(R.string.no_location_provider), contentIntent);	

		mManager.notify(NOTIFICATION_ID , notification);
	}

	public String sendImage(byte[] image, boolean publicData, String description, Location loc)
	{
		double latitude = 0;
		double longitude = 0;
		double altitude = 0;
		if (loc != null) {
			latitude = loc.getLatitude();
			longitude = loc.getLongitude();
			altitude = loc.getAltitude();
		}
		String params;
		//		try {
		String[] name = new String[6];
		String[] value = new String[6];
		name[0] = "r";
		name[1] = "latitude";
		name[2] = "longitude";
		name[3] = "altitude";
		name[4] = "publicData";
		name[5] = "description";

		value[0] = "image/upload";
		value[1] = String.valueOf(latitude);
		value[2] = String.valueOf(longitude);
		value[3] = String.valueOf(altitude);
		int publicDataInt = 0;
		if (publicData == true) {
			publicDataInt = 1; 
		} 
		value[4] = String.valueOf(publicDataInt);
		value[5] = description;

		String img = new String(image);
		String httpRes = this.sendHttpRequest(name, value, "image", image);
		Log.i("img length: ", String.valueOf(img.length()) );
		String result = getString(R.string.unknown_error_occured);

		try {
			JSONObject jsonObject = new JSONObject(httpRes);
			result = jsonObject.getString("result");
		} catch (JSONException e) {
			e.printStackTrace();
		}
		//		int result = this.evaluateResult(httpRes);

		return result;		
	}

	public boolean isNetworkConnected() {
		boolean connected = false;
		NetworkInfo networkInfo = conManager.getActiveNetworkInfo();
		if (networkInfo != null) {
			connected = networkInfo.isConnected();
		}		
		return connected; 
	}

	public void onDestroy() {
		Log.i("Traceper-AppService is being destroyed", "...");
		am.cancel(getLocationIntent);
		unregisterReceiver(networkStateReceiver);
		super.onDestroy();
	}

	private String sendHttpRequest(String[] name, String[] value, String filename, byte[] file){
		final String end = "\r\n";
		final String twoHyphens = "--";
		final String boundary = "*****++++++************++++++++++++";
		URL url;
		String result = new String();
		try {
			url = new URL(this.authenticationServerAddress);
			HttpURLConnection conn = (HttpURLConnection)url.openConnection();
			conn.setDoInput(true);
			conn.setDoOutput(true);
			conn.setUseCaches(false);
			conn.setRequestMethod("POST");

			conn.setRequestProperty("Connection", "Keep-Alive");
			conn.setRequestProperty("Charset", "UTF-8");
			conn.setRequestProperty("Content-Type", "multipart/form-data;boundary="+ boundary);
			if (cookieName != null && cookieValue != null) {
				conn.setRequestProperty("Cookie", cookieName + "=" + cookieValue);
			}
			//conn.connect();
			DataOutputStream ds = new DataOutputStream(conn.getOutputStream());

			for (int i = 0; i < value.length; i++) {
				ds.writeBytes(twoHyphens + boundary + end);
				ds.writeBytes("Content-Disposition: form-data; name=\""+ name[i] +"\""+end+end+ value[i] +end);
			}
			if (filename != null && file != null){
				ds.writeBytes(twoHyphens + boundary + end);
				ds.writeBytes("Content-Disposition: form-data; name=\"image\";filename=\"" + filename +"\"" + end + end);
				ds.write(file);
				ds.writeBytes(end);
			}			
			ds.writeBytes(twoHyphens + boundary + twoHyphens + end);
			ds.flush();
			ds.close();

			getCookie(conn);

			if (conn.getResponseCode() == HttpURLConnection.HTTP_MOVED_PERM ||
					conn.getResponseCode() == HttpURLConnection.HTTP_MOVED_TEMP)
			{
				conn.disconnect();
				this.authenticationServerAddress += "/";
				return sendHttpRequest(name, value, null, null);				
			}
			else
			{
				BufferedReader in = new BufferedReader(
						new InputStreamReader(conn.getInputStream()));
				String inputLine;

				while ((inputLine = in.readLine()) != null) {
					result = result.concat(inputLine);				
				}
				in.close();	

			}

		} catch (MalformedURLException e) {
			e.printStackTrace();
		} catch (IOException e) {
			e.printStackTrace();
		}

		if (result.length() >= 0){
			return result;
		}
		return null;		
	}

	private void getCookie(URLConnection conn) {
		for (int i=0; ; i++) {
			String headerName = conn.getHeaderFieldKey(i);
			String headerValue = conn.getHeaderField(i);

			if (headerName == null && headerValue == null) {
				// No more headers
				break;
			}
			if ("Set-Cookie".equalsIgnoreCase(headerName)) {
				// Parse cookie
				String[] fields = headerValue.split(";\\s*");

				String cookie = fields[0];
				cookieName = cookie.substring(0, cookie.indexOf("="));
				cookieValue = cookie.substring(cookie.indexOf("=")+1);
				Log.i("cookie name", cookieName);
				Log.i("cookie value", cookieValue);
				String expires = null;
				String path = null;
				String domain = null;
				boolean secure = false;

				// Parse each field
				for (int j=1; j<fields.length; j++) {
					if ("secure".equalsIgnoreCase(fields[j])) {
						secure = true;
					} else if (fields[j].indexOf('=') > 0) {
						String[] f = fields[j].split("=");
						if ("expires".equalsIgnoreCase(f[0])) {
							expires = f[1];
						} else if ("domain".equalsIgnoreCase(f[0])) {
							domain = f[1];
						} else if ("path".equalsIgnoreCase(f[0])) {
							path = f[1];
						}
					}
				}

				// Save the cookie...
			}
		}


	}

	public void exit() {

		if (isUserAuthenticated() == true) {
			String[] name = new String[2];
			String[] value = new String[2];
			name[0] = "r";
			name[1] = "client";
			value[0] = "site/logout";
			value[1] = "mobile";

			String httpRes = sendHttpRequest(name, value, null, null);
		}
		this.stopSelf();	
	}

	public String getUsername() {		
		return this.email;
	}

	public boolean isUserAuthenticated() {
		return this.isUserAuthenticated;
	}

	public String registerUser(String password, String email, String realname) 
	{
		String[] name = new String[6];
		String[] value = new String[6];
		name[0] = "r";
		name[1] = "RegisterForm[email]";
		name[2] = "RegisterForm[password]";
		name[3] = "RegisterForm[passwordAgain]";
		name[4] = "RegisterForm[name]";
		name[5] = "client";

		value[0] = "site/register";
		value[1] = email;
		value[2] = password;
		value[3] = password;
		value[4] = realname;
		value[5] = "mobile";

		String httpRes = this.sendHttpRequest(name, value, null, null);	

		String result = getString(R.string.unknown_error_occured);

		try {
			JSONObject jsonObject = new JSONObject(httpRes);
			result = jsonObject.getString("result");

		} catch (JSONException e) {
			e.printStackTrace();
		}

		return result;
	}


	public String authenticateUser(String email, String password) 
	{			
		this.password = password;
		this.email = email;

		String[] name = new String[6];
		String[] value = new String[6];
		name[0] = "r";
		name[1] = "LoginForm[email]";
		name[2] = "LoginForm[password]";
		name[3] = "deviceId";
		name[4] = "LoginForm[rememberMe]";
		name[5] = "client";

		value[0] = "site/login";
		value[1] = this.email;
		value[2] = this.password;
		value[3] = this.deviceId;
		value[4] = "1";
		value[5] = "mobile";

		String httpRes = this.sendHttpRequest(name, value, null, null);

		//		String result = this.evaluateResult(httpRes); // this.sendLocationData(this.email, this.password, locationManager.getLastKnownLocation(LocationManager.GPS_PROVIDER));	
		String result = getString(R.string.unknown_error_occured);

		try {
			JSONObject jsonObject = new JSONObject(httpRes);
			result = jsonObject.getString("result");
			if (result.equals("1")) 
			{			
				this.isUserAuthenticated = true;
				this.minDataSentInterval = Integer.parseInt(jsonObject.getString("minDataSentInterval"));
				this.minDistanceInterval = Integer.parseInt(jsonObject.getString("minDistanceInterval")); 
			}
			else {
				this.isUserAuthenticated = false;
			}
		} catch (JSONException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}

		return result;
	}	

	public void setAuthenticationServerAddress(String address) {
		this.authenticationServerAddress = address;
	}

	public Long getLastLocationSentTime() {
		return lastLocationSentTime;
	}

	private String sendLocationDataAndParseResult(Location loc) {
		String result = AppService.this.sendLocationData(loc);	

		if (configurationChanged == true)
		{
			configurationChanged = false;
			am.cancel(getLocationIntent);
			long nextRun = SystemClock.elapsedRealtime() + this.minDataSentInterval;
			am.setRepeating(AlarmManager.ELAPSED_REALTIME_WAKEUP, nextRun, AppService.this.minDataSentInterval, getLocationIntent);

		}
		return result;
	}

	private void sendBestLocation() {
		if (locationManager.isProviderEnabled(LocationManager.GPS_PROVIDER) == false ||
				locationManager.isProviderEnabled(LocationManager.NETWORK_PROVIDER) == false)
		{
			if (gpsLocation != null) {
				sendLocation(gpsLocation);
			}
			else if (networkLocation != null) {
				sendLocation(networkLocation);
			}
		}
		else {
			if (gpsLocation != null && networkLocation != null) 
			{
				if (gpsLocation.hasAccuracy() == true && networkLocation.hasAccuracy() == true) {
					if (gpsLocation.getAccuracy() < networkLocation.getAccuracy()) {
						sendLocation(gpsLocation);
					}
					else {
						sendLocation(networkLocation);
					}
				} 
				else {
					sendLocation(gpsLocation);
				}
			}	
		}	
	}


	private void sendLocation(Location loc) {
		locationManager.removeUpdates(networkLocationIntent);
		locationManager.removeUpdates(gpsLocationIntent);
		am.cancel(sendLocation);
		networkLocation = null;
		gpsLocation = null;
		lastSentLocation = loc;
		boolean connected = isNetworkConnected();
		String result = null;
		if (connected == true) {
			mManager = (NotificationManager) getSystemService(Context.NOTIFICATION_SERVICE);

			Notification notification = new Notification(R.drawable.icon, getString(R.string.ApplicationName), System.currentTimeMillis());

			PendingIntent contentIntent = PendingIntent.getActivity(getApplicationContext(), 0, null, 0);

			notification.setLatestEventInfo(getApplicationContext(), getString(R.string.ApplicationName), getString(R.string.sending_location_data), contentIntent);
			mManager.notify(NOTIFICATION_ID, notification);
			// send pending locations if any...
			sendPendingLocations();
			// send last location data
			result = sendLocationDataAndParseResult(loc);	
			String processResult = getString(R.string.sending_location_data_failed);
			if (result.equals("1")) {
				processResult = getString(R.string.sending_location_data_successfull);
			}
			notification.setLatestEventInfo(getApplicationContext(), getString(R.string.ApplicationName), processResult, contentIntent);
			mManager.notify(NOTIFICATION_ID, notification);

			// upload pending image if any...
			if (pendingImage != null) {
				uploadImage(pendingImage.image, pendingImage.isPublic, pendingImage.description);
				pendingImage = null;
			}
		}
		if (connected == false || result.equals("1") == false){
			pendingLocations.add(loc);
		}

	}

}
