
package com.traceper.android.services;

import java.io.BufferedReader;
import java.io.ByteArrayInputStream;
import java.io.DataOutputStream;
import java.io.IOException;
import java.io.InputStreamReader;
import java.net.HttpURLConnection;
import java.net.MalformedURLException;
import java.net.URL;
import java.util.ArrayList;
import java.util.Iterator;

import javax.xml.parsers.FactoryConfigurationError;
import javax.xml.parsers.ParserConfigurationException;
import javax.xml.parsers.SAXParser;
import javax.xml.parsers.SAXParserFactory;

import org.xml.sax.SAXException;

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
import android.telephony.TelephonyManager;
import android.util.Log;

import com.traceper.android.Configuration;
import com.traceper.android.interfaces.IAppService;
import com.traceper.android.tools.XMLHandler;

public class AppService extends Service implements IAppService{

	private ConnectivityManager conManager = null; 
	private LocationManager locationManager = null;
	private String deviceId;
	private boolean isUserAuthenticated = false;

	/**
	 * this list stores the locations couldnt be sent to server due to lack of network connectivity
	 */
	private ArrayList<Location> pendingLocations = new ArrayList<Location>();

	private final static String HTTP_ACTION_TAKE_MY_LOCATION = "DeviceTakeMyLocation";
	private final static String HTTP_ACTION_AUTHENTICATE_ME = "DeviceAuthenticateMe";
	private final static String HTTP_ACTION_AUTHENTICATE_CAR = "DeviceAuthenticateCar";
	private final static String HTTP_ACTION_GET_CAR_OPTIONS = "DeviceGetCarOptions";
	private final static String HTTP_ACTION_SET_CAR_SERVICES = "DeviceSetCarServices";
	private final static String HTTP_ACTION_SET_USER_STATUS = "DeviceSetUserStatus";
	private final static String HTTP_ACTION_SET_USER_WITHIN_DISTANCE = "DeviceSetUserWithInDistance";	
	private final static String HTTP_ACTION_REGISTER_ME = "DeviceRegisterMe";
	private static final String LOCATION_CHANGED = "location changed";
	private static final String HTTP_ACTION_GET_IMAGE = "DeviceGetImage";
	


	private final IBinder mBinder = new IMBinder();

	//	private NotificationManager mNM;
	private String email;
	private String password;
	private String authenticationServerAddress;
	private Long lastLocationSentTime;

	private LocationHandler locationHandler;
	private int minDataSentInterval = Configuration.MIN_GPS_DATA_SEND_INTERVAL;
	private int minDistanceInterval = Configuration.MIN_GPS_DISTANCE_INTERVAL;
	private XMLHandler xmlHandler;
	private BroadcastReceiver networkStateReceiver;
	private boolean regularUpdateFlag;


	public class IMBinder extends Binder {
		public IAppService getService() {
			return AppService.this;
		}		
	}

	public void onCreate() 
	{   	
		conManager = (ConnectivityManager) getSystemService(Context.CONNECTIVITY_SERVICE);

		locationManager = (LocationManager) getSystemService(Context.LOCATION_SERVICE);

		deviceId = ((TelephonyManager) getSystemService(TELEPHONY_SERVICE)).getDeviceId();

		xmlHandler = new XMLHandler();
		locationHandler = new LocationHandler();

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
	
	
	private void sendPendingLocations(){
		Iterator<Location> iterator = pendingLocations.iterator();
		while (iterator.hasNext()) {
			Location location = (Location) iterator.next();
			int result = sendLocationDataAndParseResult(location);
			if (result == HTTP_RESPONSE_SUCCESS) {
				iterator.remove();
			}
		}
	}

	public IBinder onBind(Intent intent) 
	{
		return mBinder;
	}

	//TODO: edit the traceper protocol file
	private int sendLocationData(String emailText, String passwordText, Location loc) 
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
		name[0] = "action";
		name[1] = "email";
		name[2] = "password";
		name[3] = "latitude";
		name[4] = "longitude";
		name[5] = "altitude";
		name[6] = "deviceId";
		name[7] = "time";

		value[0] = HTTP_ACTION_TAKE_MY_LOCATION;
		value[1] = emailText;
		value[2] = passwordText;
		value[3] = String.valueOf(latitude);
		value[4] = String.valueOf(longitude);
		value[5] = String.valueOf(altitude);
		value[6] = this.deviceId;
		value[7] = String.valueOf((int)(loc.getTime()/1000)); // convert milliseconds to seconds

		String httpRes = this.sendHttpRequest(name, value, null, null);

		int result = this.evaluateResult(httpRes);
		if (result == HTTP_RESPONSE_SUCCESS)
		{			
			lastLocationSentTime = System.currentTimeMillis();
			Intent i = new Intent(IAppService.LAST_LOCATION_DATA_SENT_TIME);
			i.setAction(IAppService.LAST_LOCATION_DATA_SENT_TIME);
			i.putExtra(IAppService.LAST_LOCATION_DATA_SENT_TIME, lastLocationSentTime);
			sendBroadcast(i);
			Log.i("broadcast sent", "sendLocationData broadcast sent");			
		}
		return result;	
	}

	public int sendImage(byte[] image, boolean publicData){
		Location loc = locationManager.getLastKnownLocation(LocationManager.GPS_PROVIDER);
		double latitude = 0;
		double longitude = 0;
		double altitude = 0;
		if (loc != null) {
			latitude = loc.getLatitude();
			longitude = loc.getLongitude();
			altitude = loc.getLongitude();
		}
		String params;
		//		try {
		String[] name = new String[7];
		String[] value = new String[7];
		name[0] = "action";
		name[1] = "email";
		name[2] = "password";
		name[3] = "latitude";
		name[4] = "longitude";
		name[5] = "altitude";
		name[6] = "publicData";

		value[0] = HTTP_ACTION_GET_IMAGE;
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


		String img = new String(image);
		String httpRes = this.sendHttpRequest(name, value, "image", image);
		Log.i("img length: ", String.valueOf(img.length()) );
		int result = this.evaluateResult(httpRes);

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
		locationManager.removeUpdates(locationHandler);
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

	public int registerUser(String password, String email, String realname) 
	{
		String[] name = new String[4];
		String[] value = new String[4];
		name[0] = "action";
		name[1] = "email";
		name[2] = "password";
		name[3] = "realname";

		value[0] = HTTP_ACTION_REGISTER_ME;
		value[1] = email;
		value[2] = password;
		value[3] = realname;

		String result = this.sendHttpRequest(name, value, null, null);		

		return this.evaluateResult(result);
	}


	public int authenticateUser(String email, String password) 
	{			
		this.password = password;
		this.email = email;

		String[] name = new String[4];
		String[] value = new String[4];
		name[0] = "action";
		name[1] = "email";
		name[2] = "password";
		name[3] = "deviceId";

		value[0] = HTTP_ACTION_AUTHENTICATE_ME;
		value[1] = this.email;
		value[2] = this.password;
		value[3] = this.deviceId;

		String httpRes = this.sendHttpRequest(name, value, null, null);

		int result = this.evaluateResult(httpRes); // this.sendLocationData(this.email, this.password, locationManager.getLastKnownLocation(LocationManager.GPS_PROVIDER));	

		if (result == HTTP_RESPONSE_SUCCESS) 
		{			
			this.isUserAuthenticated = true;
			this.minDataSentInterval = xmlHandler.getGpsMinDataSentInterval();
			this.minDistanceInterval = xmlHandler.getGpsMinDistanceInterval();

			Thread locationUpdates = new Thread() {
				public void run() {
					Looper.prepare();

					locationManager.requestLocationUpdates(LocationManager.GPS_PROVIDER, minDataSentInterval, minDistanceInterval, 
							locationHandler);				       

					Looper.loop();
				}
			};
		if(this.regularUpdateFlag)
			locationUpdates.start();
	
		}
		else {
			this.isUserAuthenticated = false;
		}
		return result;
	}	
	
	@Override
	public int authenticateCar(String carname, String password) {

		String[] name = new String[5];
		String[] value = new String[5];
		name[0] = "action";
		name[1] = "email";
		name[2] = "password";
		name[3] = "car_login";
		name[4] = "car_password";

		value[0] = HTTP_ACTION_AUTHENTICATE_CAR;
		value[1] = this.email;
		value[2] = this.password;
		value[3] = carname;
		value[4] = password;

		String httpRes = this.sendHttpRequest(name, value, null, null);

		int result = this.evaluateResult(httpRes); // this.sendLocationData(this.email, this.password, locationManager.getLastKnownLocation(LocationManager.GPS_PROVIDER));	

		if (result == HTTP_RESPONSE_SUCCESS) 
		{			
	
		}
		else {
		}
		return result;
	}

	private int evaluateResult(String result)
	{
		int iresult = HTTP_RESPONSE_ERROR_UNKNOWN_RESPONSE;
		if (result == null){
			iresult = HTTP_REQUEST_FAILED;
		}
		else {
			SAXParser sp;
			try {
				sp = SAXParserFactory.newInstance().newSAXParser();
				sp.parse(new ByteArrayInputStream(result.getBytes()), xmlHandler);
			} catch (ParserConfigurationException e) {
				e.printStackTrace();
			} catch (SAXException e) {
				e.printStackTrace();
			} catch (FactoryConfigurationError e) {
				e.printStackTrace();
			} catch (IOException e) {
				e.printStackTrace();
			}		

			iresult = xmlHandler.getActionResult();
			switch (iresult)
			{
			case HTTP_RESPONSE_SUCCESS:
				Log.i("HTTP_RESPONSE", "successfull.");
				break;
			case HTTP_REQUEST_FAILED:
				Log.w("HTTP_RESPONSE", "failed: http request failed.");
				break;
			case HTTP_RESPONSE_ERROR_MISSING_PARAMETER:
				Log.w("HTTP_RESPONSE", "failed: http request failed.");
				break;
			case HTTP_RESPONSE_ERROR_UNAUTHORIZED_ACCESS:
				Log.w("HTTP_RESPONSE", "failed: unauthorized access");				
				break;
			case HTTP_RESPONSE_ERROR_UNKNOWN:
				Log.w("HTTP_RESPONSE", "failed: unknown error");
				break;
			case HTTP_RESPONSE_ERROR_UNSUPPORTED_ACTION:
				Log.w("HTTP_RESPONSE", "failed: unsupported action");
				break;
			case HTTP_RESPONSE_ERROR_EMAIL_EXISTS:
				Log.w("HTTP_RESPONSE", "failed registration: email already exists");
				break;
			case HTTP_RESPONSE_ERROR_EMAIL_NOT_VALID:
				Log.w("HTTP_RESPONSE", "failed registration: email is not valid");
				break;
			case HTTP_RESPONSE_ERROR_USER_NOT_VALID:
				Log.w("HTTP_RESPONSE", "HTTP_RESPONSE_ERROR_USER_NOT_VALID");
				break;
			case HTTP_RESPONSE_ERROR_USER_ACCOUNT_EXPIRED:
				Log.w("HTTP_RESPONSE", "HTTP_RESPONSE_ERROR_USER_ACCOUNT_EXPIRED");
				break;	
			case HTTP_RESPONSE_ERROR_CAR_IN_USE:
				Log.w("HTTP_RESPONSE", "HTTP_RESPONSE_ERROR_CAR_IN_USE");
				break;		
			default:
				iresult = HTTP_RESPONSE_ERROR_UNKNOWN_RESPONSE;
				Log.w("HTTP_RESPONSE", "failed: unknown response returned from server");
				break;
			}			
		}

		return iresult;
	}
	
	public void CancelRegularUpdate(){
		this.regularUpdateFlag  = false;
		locationManager.removeUpdates(locationHandler);
	}

	public void setAuthenticationServerAddress(String address) {
		this.authenticationServerAddress = address;
	}

	public Long getLastLocationSentTime() {
		return lastLocationSentTime;
	}
	
	private int sendLocationDataAndParseResult(Location loc) {
		int result = AppService.this.sendLocationData(AppService.this.email, AppService.this.password, loc);	

		int dataSentInterval = AppService.this.xmlHandler.getGpsMinDataSentInterval();
		int distanceInterval = AppService.this.xmlHandler.getGpsMinDistanceInterval();

		// if configuration is changed in server, then arrange itself below by
		// adding using new params in locationManager. requestLocationUpdates
		if (dataSentInterval != AppService.this.minDataSentInterval ||
				distanceInterval != AppService.this.minDistanceInterval)
		{
			AppService.this.minDataSentInterval = dataSentInterval;
			AppService.this.minDistanceInterval = distanceInterval;

			locationManager.removeUpdates(locationHandler);
			Thread locationUpdates = new Thread() {
				public void run() {
					Looper.prepare();

					locationManager.requestLocationUpdates(LocationManager.GPS_PROVIDER, 
							minDataSentInterval, 
							minDistanceInterval, 
							locationHandler);			       

					Looper.loop();
				}

			};		      

			locationUpdates.start();
		}
		return result;
	}

	private class LocationHandler implements LocationListener{
		public void onLocationChanged(Location loc){	
			if (loc != null) {
				Log.i("location listener", "onLocationChanged");
				boolean connected = isNetworkConnected();
				Integer result = null;
				if (connected == true) {
					// send pending locations if any...
					sendPendingLocations();
					// send last location data
					result = sendLocationDataAndParseResult(loc);					
				}
				if (connected == false || result != HTTP_RESPONSE_SUCCESS){
					pendingLocations.add(loc);
				}

			}
		}
		public void onProviderDisabled(String provider){
			Log.i("location listener", "onProviderDisabled");	
		}
		public void onProviderEnabled(String provider){					
			Log.i("location listener", "onProviderEnabled");	
		}
		public void onStatusChanged(String provider, int status, Bundle extras){															
			Log.i("location listener", "onProviderEnabled");	
		}	
		
	}
	
	public int updateLocation(String status){
		locationManager.requestLocationUpdates(LocationManager.GPS_PROVIDER, minDataSentInterval, minDistanceInterval, locationHandler);
		int retval = sendLocationData(this.email, this.password, locationManager.getLastKnownLocation(LocationManager.GPS_PROVIDER));
		//this.status = status;
		locationManager.removeUpdates(locationHandler);
		return retval;
	}

	@Override
	public ArrayList<CarOptions> getCarOptions() {

		String[] name = new String[1];
		String[] value = new String[1];
		name[0] = "action";

		value[0] = HTTP_ACTION_GET_CAR_OPTIONS;

		String httpRes = this.sendHttpRequest(name, value, null, null);
		
		SAXParser sp;
		try {
			sp = SAXParserFactory.newInstance().newSAXParser();
			xmlHandler.cleanCarOptions();
			sp.parse(new ByteArrayInputStream(httpRes.getBytes()), xmlHandler);
		} catch (ParserConfigurationException e) {
			e.printStackTrace();
		} catch (SAXException e) {
			e.printStackTrace();
		} catch (FactoryConfigurationError e) {
			e.printStackTrace();
		} catch (IOException e) {
			e.printStackTrace();
		}
		
		return xmlHandler.getCarOptions();
	}
	@Override
	public int sendCarServices(long id) {
		String[] name = new String[4];
		String[] value = new String[4];
		name[0] = "action";
		name[1] = "email";
		name[2] = "password";
		name[3] = "serviceId";
		
		value[0] = HTTP_ACTION_SET_CAR_SERVICES;
		value[1] = this.email;
		value[2] = this.password;
		value[3] = String.valueOf(id);
		

		String httpRes = this.sendHttpRequest(name, value, null, null);
		
		return this.evaluateResult(httpRes);
		
	}


	@Override
	public int setUserStatus(boolean userOnline) {
		String[] name = new String[4];
		String[] value = new String[4];
		name[0] = "action";
		name[1] = "email";
		name[2] = "password";
		name[3] = "statusOnline";
		
		value[0] = HTTP_ACTION_SET_USER_STATUS;
		value[1] = this.email;
		value[2] = this.password;
		if (userOnline == true) value[3] = "1";
		else value[3] = "0";
		

		String httpRes = this.sendHttpRequest(name, value, null, null);
		
		return this.evaluateResult(httpRes);
	}
	
	@Override
	public int setUserWithInDistance(String distance) 
	{
		String[] name = new String[4];
		String[] value = new String[4];
		name[0] = "action";
		name[1] = "email";
		name[2] = "password";
		name[3] = "withInDistance";
		
		value[0] = HTTP_ACTION_SET_USER_WITHIN_DISTANCE;
		value[1] = this.email;
		value[2] = this.password;
		value[3] = distance;

		String httpRes = this.sendHttpRequest(name, value, null, null);
		
		return this.evaluateResult(httpRes);
	}

}