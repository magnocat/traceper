package com.traceper.android;

public class Configuration {
	
	public static final boolean REGISTRATION_ENABLED = true;
	public static final int MIN_GPS_DATA_SEND_INTERVAL = 0;  //in milliseconds
	public static final int MIN_GPS_DISTANCE_INTERVAL = 0; // in meters
	
	/**
	 * When uploading an image, it looks the difference between the last location time and current time
	 * if it is more than the value above, it requests new location update.
	 */
	public static final int LOCATION_TIMEOUT_BEFORE_UPLOADING = 180000;
	public static final String PREFERENCES_NAME = "Traceper_Preferences";
	public static final String PREFERENCES_SERVER_INDEX = "Traceper_Preferences_Server";
	public static final String PREFERENCES_USEREMAIL = "PREFERENCES_USEREMAIL";
	public static final String PREFERENCES_PASSWORD = "PREFERENCES_PASSWORD";
	public static final String PREFRENCES_REMEMBER_ME_CHECKBOX = "PREFRENCES_REMEMBER_ME_CHECKBOX";
//	public static final String DEFAULT_SERVER_ADRESS = "http://192.168.1.4/Dev_Traceper_WebInterface/";
	public static final String DEFAULT_SERVER_ADRESS = "http://www.mekya.com/labs/traceper/";

	public static final String PREFRENCES_AUTO_SEND_CHECKBOX = "PREFRENCES_AUTO_SEND_CHECKBOX";
	
	public static final String APPLICATION_NAME = "Traceper Tracking System";
	

	
}
