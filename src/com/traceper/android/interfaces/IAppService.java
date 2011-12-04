package com.traceper.android.interfaces;



public interface IAppService {
	
	public static final int HTTP_REQUEST_FAILED = 0;	
	//protocol defined statics
	public static final int HTTP_RESPONSE_SUCCESS = 1;
	public static final int HTTP_RESPONSE_ERROR_UNKNOWN = -1;
	public static final int HTTP_RESPONSE_ERROR_MISSING_PARAMETER = -2;
	public static final int HTTP_RESPONSE_ERROR_UNSUPPORTED_ACTION = -3;
	public static final int HTTP_RESPONSE_ERROR_UNAUTHORIZED_ACCESS = -4;
	public static final int HTTP_RESPONSE_ERROR_EMAIL_EXISTS = -5;
	public static final int HTTP_RESPONSE_ERROR_EMAIL_NOT_VALID = -9;
	//self-defined error	
	public static final int HTTP_RESPONSE_ERROR_UNKNOWN_RESPONSE = -100;

		
//	public static final int ACTION_LAST_LOCATION_DATA_SENT_TIME = 1001;
	public static final String LAST_LOCATION_DATA_SENT_TIME = "LAST_LOCATION_DATA_SENT_TIME";
	public static final String LAST_LOCATION = "LOCATION";
	
	public String getUsername();
	
	public boolean isNetworkConnected();
	
	public boolean isUserAuthenticated();
	
	public void setAutoCheckin(boolean enable);
	
	public void sendLocationNow(boolean enable);
	
	public Long getLastLocationSentTime();
	
	public void exit();
	
	public String registerUser(String password, String email, String realname);
	
	public String authenticateUser(String username, String password);
	
	public void setAuthenticationServerAddress(String address);
	
	public String sendImage(byte[] image, boolean publicData);
	

	
}
