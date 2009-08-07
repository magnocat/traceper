
package com.traceper.android.services;

import java.io.BufferedReader;
import java.io.ByteArrayInputStream;
import java.io.IOException;
import java.io.InputStreamReader;
import java.io.PrintWriter;
import java.net.HttpURLConnection;
import java.net.MalformedURLException;
import java.net.URL;

import javax.xml.parsers.FactoryConfigurationError;
import javax.xml.parsers.ParserConfigurationException;
import javax.xml.parsers.SAXParser;
import javax.xml.parsers.SAXParserFactory;

import org.xml.sax.SAXException;

import android.app.Service;
import android.content.Intent;
import android.location.Location;
import android.location.LocationListener;
import android.location.LocationManager;
import android.location.LocationProvider;
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

        conManager = (ConnectivityManager) getSystemService(CONNECTIVITY_SERVICE);
    	
        locationManager = (LocationManager) getSystemService(LOCATION_SERVICE);
        
        locationManager.setTestProviderEnabled(LocationManager.GPS_PROVIDER, true);
		locationManager.setTestProviderStatus(LocationManager.GPS_PROVIDER,
		           LocationProvider.AVAILABLE, null, System.currentTimeMillis()); 
        
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
		String params = "action="+ HTTP_ACTION_TAKE_MY_LOCATION + 
						"&username=" + usernameText + 
						"&password=" + passwordText + 
						"&latitude="+ latitude + 
						"&longitude=" + longitude + 
						"&altitude=" + altitude +
						"&deviceId=" + this.deviceId + 
						"&";
		
		String httpRes = this.sendHttpRequest(params);	
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
			
			PrintWriter out = new PrintWriter(connection.getOutputStream());			
			out.println(params);
			out.close();
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