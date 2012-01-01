
package com.traceper.android.services;

import java.io.BufferedReader;
import java.io.DataOutputStream;
import java.io.IOException;
import java.io.InputStreamReader;
import java.net.HttpURLConnection;
import java.net.MalformedURLException;
import java.net.URL;
import java.util.ArrayList;
import java.util.Iterator;
import java.util.Timer;
import java.util.TimerTask;

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
import android.location.Location;
import android.location.LocationListener;
import android.location.LocationManager;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;
import android.os.Binder;
import android.os.Bundle;
import android.os.IBinder;
import android.os.Looper;
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

	private final IBinder mBinder = new IMBinder();

	//	private NotificationManager mNM;
	private String email;
	private String password;
	private String authenticationServerAddress;
	private Long lastLocationSentTime;


	private int minDataSentInterval = Configuration.MIN_GPS_DATA_SEND_INTERVAL;
	private int minDistanceInterval = Configuration.MIN_GPS_DISTANCE_INTERVAL;
	//	private XMLHandler xmlHandler;
	private BroadcastReceiver networkStateReceiver;
	//	private boolean gps_enabled = false;
	//	private boolean network_enabled = false;
	private String cookie = null;
	private boolean configurationChanged = false;
	private boolean autoCheckinEnabled;
	private PendingIntent getLocationIntent;
	private AlarmManager am;
	private PendingIntent gpsLocationIntent;
	private PendingIntent networkLocationIntent;
	private Location gpsLocation;
	private Location networkLocation;


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
					gpsLocation = null;
					networkLocation = null;

					Notification notification = new Notification(R.drawable.icon, getString(R.string.ApplicationName), System.currentTimeMillis());

					PendingIntent contentIntent = PendingIntent.getActivity(getApplicationContext(), 0, null, 0);

					notification.setLatestEventInfo(AppService.this,
							getString(R.string.ApplicationName), getString(R.string.waiting_location), contentIntent);	

					mManager.notify(NOTIFICATION_ID , notification);
					locationManager.removeUpdates(gpsLocationIntent);
					locationManager.removeUpdates(networkLocationIntent);
					
					locationManager.requestLocationUpdates(LocationManager.GPS_PROVIDER, 0, 0, gpsLocationIntent);
					locationManager.requestLocationUpdates(LocationManager.NETWORK_PROVIDER, 0, 0, networkLocationIntent);
				
					Intent sendLocationIntent = new Intent(AppService.this, AppService.class);
					sendLocationIntent.setAction(SEND_LOCATION);
					PendingIntent sendLocation = PendingIntent.getService(AppService.this, 0, sendLocationIntent, 0);
					am.set(AlarmManager.ELAPSED_REALTIME_WAKEUP, SystemClock.elapsedRealtime() + 60000, sendLocation);
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
				}
				else if(action.equals(GET_GPS_LOCATION)) {
					gpsLocation = (Location)intent.getExtras().getParcelable(LocationManager.KEY_LOCATION_CHANGED);
					if (gpsLocation != null) {
						locationManager.removeUpdates(gpsLocationIntent);
					}
					sendBestLocation();
				}
				else if(action.equals(GET_NETWORK_LOCATION)) {
					networkLocation = (Location)intent.getExtras().getParcelable(LocationManager.KEY_LOCATION_CHANGED);
					if (networkLocation != null) {
						locationManager.removeUpdates(networkLocationIntent);
					}
					sendBestLocation();
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

	public void sendLocationNow(boolean enable){
		if (enable == true) {
			sendLocation(0, 0);
		}
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

	//TODO: edit the traceper protocol file
	private String sendLocationData(String emailText, String passwordText, Location loc) 
	{		
		double latitude = 0;
		double longitude = 0;
		double altitude = 0;
		if (loc != null) {
			latitude = loc.getLatitude();
			longitude = loc.getLongitude();
			altitude = loc.getLongitude();
		}
		String[] name = new String[8];
		String[] value = new String[8];
		name[0] = "r";
		name[1] = "email";
		name[2] = "password";
		name[3] = "latitude";
		name[4] = "longitude";
		name[5] = "altitude";
		name[6] = "deviceId";
		name[7] = "time";

		value[0] = "users/takeMyLocation";
		value[1] = emailText;
		value[2] = passwordText;
		value[3] = String.valueOf(latitude);
		value[4] = String.valueOf(longitude);
		value[5] = String.valueOf(altitude);
		value[6] = this.deviceId;
		value[7] = String.valueOf((int)(loc.getTime()/1000)); // convert milliseconds to seconds

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
			// TODO Auto-generated catch block
			e.printStackTrace();
		}


		return result;	
	}

	public String sendImage(byte[] image, boolean publicData, String description)
	{
		Location locationGPS = locationManager.getLastKnownLocation(LocationManager.GPS_PROVIDER);
		Location locationNetwork = locationManager.getLastKnownLocation(LocationManager.NETWORK_PROVIDER);
		Location loc = null;
		double latitude = 0;
		double longitude = 0;
		double altitude = 0;
		if (locationGPS == null && locationNetwork != null) {
			loc = locationNetwork;
		}
		else if (locationGPS != null && locationNetwork == null) {
			loc = locationGPS;
		}
		else if (locationGPS != null && locationNetwork != null) {
			if (locationGPS.getTime() > locationNetwork.getTime()) {
				loc = locationGPS;
			}
			else {
				loc = locationNetwork;
			}
		}
		if (loc != null) {
			latitude = loc.getLatitude();
			longitude = loc.getLongitude();
			altitude = loc.getLongitude();
		}
		String params;
		//		try {
		String[] name = new String[8];
		String[] value = new String[8];
		name[0] = "r";
		name[1] = "email";
		name[2] = "password";
		name[3] = "latitude";
		name[4] = "longitude";
		name[5] = "altitude";
		name[6] = "publicData";
		name[7] = "description";

		value[0] = "image/upload";
		value[1] = this.email;
		value[2] = this.password;
		value[3] = String.valueOf(latitude);
		value[4] = String.valueOf(longitude);
		value[5] = String.valueOf(altitude);
		int publicDataInt = 0;
		if (publicData == true) {
			publicDataInt = 1; 
		} 
		value[6] = String.valueOf(publicDataInt);
		value[7] = description;

		String img = new String(image);
		String httpRes = this.sendHttpRequest(name, value, "image", image);
		Log.i("img length: ", String.valueOf(img.length()) );
		String result = getString(R.string.unknown_error_occured);

		try {
			JSONObject jsonObject = new JSONObject(httpRes);
			result = jsonObject.getString("result");
		} catch (JSONException e) {
			// TODO Auto-generated catch block
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
			if (cookie != null) {
				conn.setRequestProperty("Cookie", cookie);
			}

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

			cookie  = conn.getHeaderField("set-cookie");
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

	public void exit() {
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
		String result = AppService.this.sendLocationData(AppService.this.email, AppService.this.password, loc);	

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
		networkLocation = null;
		gpsLocation = null;
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
		}
		if (connected == false || result.equals("1") == false){
			pendingLocations.add(loc);
		}
		
	}

}
