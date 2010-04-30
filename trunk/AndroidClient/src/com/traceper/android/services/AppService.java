
package com.traceper.android.services;

import java.io.BufferedReader;
import java.io.BufferedWriter;
import java.io.ByteArrayInputStream;
import java.io.DataOutputStream;
import java.io.FileInputStream;
import java.io.IOException;
import java.io.InputStreamReader;
import java.io.PrintWriter;
import java.io.StringReader;
import java.io.UnsupportedEncodingException;
import java.net.HttpURLConnection;
import java.net.MalformedURLException;
import java.net.URL;
import java.net.URLConnection;
import java.net.URLEncoder;
import java.nio.ByteBuffer;
import java.security.spec.EncodedKeySpec;
import java.util.HashMap;
import java.util.Iterator;
import java.util.Map;

import javax.xml.parsers.FactoryConfigurationError;
import javax.xml.parsers.ParserConfigurationException;
import javax.xml.parsers.SAXParser;
import javax.xml.parsers.SAXParserFactory;

import org.xml.sax.SAXException;

import android.app.Service;
import android.content.Context;
import android.content.Intent;
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
	
	private final static String HTTP_ACTION_TAKE_MY_LOCATION = "DeviceTakeMyLocation";
	private final static String HTTP_ACTION_REGISTER_ME = "DeviceRegisterMe";
	private static final String LOCATION_CHANGED = "location changed";
	private static final String HTTP_ACTION_GET_IMAGE = "DeviceGetImage";


	private final IBinder mBinder = new IMBinder();
	
//	private NotificationManager mNM;
	private String username;
	private String password;
	private String authenticationServerAddress;
	private Long lastLocationSentTime;
	
	private LocationHandler locationHandler;
	private int minDataSentInterval = Configuration.MIN_GPS_DATA_SEND_INTERVAL;
	private int minDistanceInterval = Configuration.MIN_GPS_DISTANCE_INTERVAL;
	private XMLHandler xmlHandler;
	
	
	public class IMBinder extends Binder {
		public IAppService getService() {
			return AppService.this;
		}		
	}
	   
    public void onCreate() 
    {   	
 //       mNM = (NotificationManager)getSystemService(NOTIFICATION_SERVICE);

        conManager = (ConnectivityManager) getSystemService(Context.CONNECTIVITY_SERVICE);
    	
        locationManager = (LocationManager) getSystemService(Context.LOCATION_SERVICE);
        
//        locationManager.setTestProviderEnabled(LocationManager.GPS_PROVIDER, true);
//		locationManager.setTestProviderStatus(LocationManager.GPS_PROVIDER,
//		           LocationProvider.AVAILABLE, null, System.currentTimeMillis()); 
        
        deviceId = ((TelephonyManager) getSystemService(TELEPHONY_SERVICE)).getDeviceId();
        
        xmlHandler = new XMLHandler();
        locationHandler = new LocationHandler();
    
    }

	public IBinder onBind(Intent intent) 
	{
		return mBinder;
	}

	/**
	 * Show a notification while this service is running.
	 * @param msg 
	 **/
/*
    private void showNotification(String username, String msg) 
	{       
        // Set the icon, scrolling text and timestamp
    	String title = username + ": " + 
     				((msg.length() < 5) ? msg : msg.substring(0, 5)+ "...");
        Notification notification = new Notification(R.drawable.stat_sample, 
        					title,
                System.currentTimeMillis());

        Intent i = new Intent(this, Messaging.class);
        i.putExtra(FriendInfo.USERNAME, username);
        i.putExtra(FriendInfo.MESSAGE, msg);	
        
        // The PendingIntent to launch our activity if the user selects this notification
        PendingIntent contentIntent = PendingIntent.getActivity(this, 0,
                i, 0);

        // Set the info for the views that show in the notification panel.
        // msg.length()>15 ? msg : msg.substring(0, 15);
        notification.setLatestEventInfo(this, "New message from " + username,
                       						msg, 
                       						contentIntent);
        
        //TODO: it can be improved, for instance message coming from same user may be concatenated 
        // next version
        
        // Send the notification.
        // We use a layout id because it is a unique number.  We use it later to cancel.
        mNM.notify((username+msg).hashCode(), notification);
    }	
*/

	private int sendLocationData(String usernameText, String passwordText, Location loc) 
	{		
		double latitude = 0;
		double longitude = 0;
		double altitude = 0;
		if (loc != null) {
			latitude = loc.getLatitude();
			longitude = loc.getLongitude();
			altitude = loc.getLongitude();
		}
		String[] name = new String[7];
		String[] value = new String[7];
		name[0] = "action";
		name[1] = "username";
		name[2] = "password";
		name[3] = "latitude";
		name[4] = "longitude";
		name[5] = "altitude";
		name[6] = "deviceId";
		
		value[0] = HTTP_ACTION_TAKE_MY_LOCATION;
		value[1] = usernameText;
		value[2] = passwordText;
		value[3] = String.valueOf(latitude);
		value[4] = String.valueOf(longitude);
		value[5] = String.valueOf(altitude);
		value[6] = this.deviceId;
		
		String httpRes = this.sendHttpRequest(name, value, null, null);
		
		String params = "action="+ URLEncoder.encode(HTTP_ACTION_TAKE_MY_LOCATION) + 
						"&username=" + URLEncoder.encode(usernameText) + 
						"&password=" + URLEncoder.encode(passwordText) + 
						"&latitude=" + latitude + 
						"&longitude=" + longitude + 
						"&altitude=" + altitude +
						"&deviceId=" + URLEncoder.encode(this.deviceId) + 
						"&";
		
	//	String httpRes = this.sendHttpRequest(params);
		
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
	
	public int sendImage(byte[] image){
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
		String[] name = new String[6];
		String[] value = new String[6];
		name[0] = "action";
		name[1] = "username";
		name[2] = "password";
		name[3] = "latitude";
		name[4] = "longitude";
		name[5] = "altitude";
		
		value[0] = HTTP_ACTION_GET_IMAGE;
		value[1] = this.username;
		value[2] = this.password;
		value[3] = String.valueOf(latitude);
		value[4] = String.valueOf(longitude);
		value[5] = String.valueOf(altitude);
		
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
	
	private String sendHttpRequest(String params)
	{		
		URL url;
		String result = new String();
		try 
		{
			url = new URL(this.authenticationServerAddress);
			HttpURLConnection connection;
			
			connection = (HttpURLConnection) url.openConnection();
			connection.setDoOutput(true);	
			connection.setDoInput(true);
//			PrintWriter out = new PrintWriter(connection.getOutputStream());	
			
//			connection.setRequestProperty("Connection", "Keep-Alive");
			DataOutputStream outputStream = new DataOutputStream(connection.getOutputStream());
			
			outputStream.writeBytes(params);
			outputStream.close();
			//out.close();
			
			// if the / character is not written to end of the address, 
			// it arises temp or permanent moved error, adding / character may solve this problem
			if (connection.getResponseCode() == HttpURLConnection.HTTP_MOVED_PERM ||
				connection.getResponseCode() == HttpURLConnection.HTTP_MOVED_TEMP)
			{
				connection.disconnect();
				this.authenticationServerAddress += "/";
				return sendHttpRequest(params);				
			}
			else
			{
				BufferedReader in = new BufferedReader(
									new InputStreamReader(connection.getInputStream()));
				String inputLine;

				while ((inputLine = in.readLine()) != null) {
					result = result.concat(inputLine);				
				}
				in.close();	
			}
			
			
		} 
		catch (MalformedURLException e) {
			e.printStackTrace();
		} 
		catch (IOException e) {
			e.printStackTrace();
		}	
		
		String response = null;
		if (result.length() != 0) {
			response = result;
//			response = HTTP_REQUEST_FAILED;
		}
		else {
			
//			try {
//				response = Integer.parseInt(result);
//			}catch(NumberFormatException ex) {
//				response = HTTP_RESPONSE_ERROR_UNKNOWN_RESPONSE;
//			}
		}		
		return response;
	}

	public void exit() {
		this.stopSelf();	
	}

	public String getUsername() {		
		return this.username;
	}

	public boolean isUserAuthenticated() {
		return this.isUserAuthenticated;
	}

	public int registerUser(String username, String password, String email, String realname) {
		
		String params = "action="+ HTTP_ACTION_REGISTER_ME + 
						"&username=" + username + 
						"&password=" + password + 
						"&email="+ email + 
						"&realname=" + realname + 
						"&";
		
		String result = this.sendHttpRequest(params);		
		
		return this.evaluateResult(result);
	}
	

	public int authenticateUser(String username, String password) 
	{			
		this.password = password;
		this.username = username;
		int result = this.sendLocationData(this.username, this.password, locationManager.getLastKnownLocation(LocationManager.GPS_PROVIDER));	
		
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
						  
			locationUpdates.start();
			
						
		}
		else {
			this.isUserAuthenticated = false;
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
				case HTTP_RESPONSE_ERROR_USERNAME_EXISTS:
					Log.w("HTTP_RESPONSE", "failed registration: username alread exists");
					break;
				default:
					iresult = HTTP_RESPONSE_ERROR_UNKNOWN_RESPONSE;
					Log.w("HTTP_RESPONSE", "failed: unknown response returned from server");
					break;
			}			
		}
		
		return iresult;
	}

	public void setAuthenticationServerAddress(String address) {
		this.authenticationServerAddress = address;
	}

	public Long getLastLocationSentTime() {
		return lastLocationSentTime;
	}
	
	private class LocationHandler implements LocationListener{
		public void onLocationChanged(Location loc){	
			if (loc != null) {
				Log.i("location listener", "onLocationChanged");
				AppService.this.sendLocationData(AppService.this.username, AppService.this.password, loc);	
				
				int dataSentInterval = AppService.this.xmlHandler.getGpsMinDataSentInterval();
				int distanceInterval = AppService.this.xmlHandler.getGpsMinDistanceInterval();
				
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

}