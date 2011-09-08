package com.traceper.android.interfaces;

import java.util.ArrayList;



public interface IAppService {
	
	public class CarOptions {
		private int id;
		private String title;
		public CarOptions(int id, String title) {
			super();
			this.id = id;
			this.title = title;
		}
		public long getId() {
			return this.id;
		}
		public CharSequence getTitle() {
			return this.title;
		}
		
	}
	
	public static final int HTTP_REQUEST_FAILED = 0;	
	//protocol defined statics
	public static final int HTTP_RESPONSE_SUCCESS = 1;
	public static final int HTTP_RESPONSE_ERROR_UNKNOWN = -1;
	public static final int HTTP_RESPONSE_ERROR_MISSING_PARAMETER = -2;
	public static final int HTTP_RESPONSE_ERROR_UNSUPPORTED_ACTION = -3;
	public static final int HTTP_RESPONSE_ERROR_UNAUTHORIZED_ACCESS = -4;
	public static final int HTTP_RESPONSE_ERROR_EMAIL_EXISTS = -5;
	public static final int HTTP_RESPONSE_ERROR_EMAIL_NOT_VALID = -9;
	public static final int HTTP_RESPONSE_ERROR_USER_NOT_VALID = -10;
	public static final int HTTP_RESPONSE_ERROR_USER_ACCOUNT_EXPIRED = -11;
	public static final int HTTP_RESPONSE_ERROR_CAR_IN_USE = -12;
	//self-defined error	
	public static final int HTTP_RESPONSE_ERROR_UNKNOWN_RESPONSE = -100;

	
//	public static final int ACTION_LAST_LOCATION_DATA_SENT_TIME = 1001;
	public static final String LAST_LOCATION_DATA_SENT_TIME = "LAST_LOCATION_DATA_SENT_TIME";
	
	
	
	public String getUsername();
	
	public boolean isNetworkConnected();
	
	public boolean isUserAuthenticated();
	
	public Long getLastLocationSentTime();
	
	public void exit();
	
	public int registerUser(String password, String email, String realname);
	
	public int authenticateUser(String username, String password);
	
	public void setAuthenticationServerAddress(String address);
	
	public int sendImage(byte[] image, boolean publicData);
	
	public ArrayList<CarOptions> getCarOptions();

	public int sendCarServices(long id);
	
	public int authenticateCar(String carname, String password);

	public int setUserStatus(boolean userOnline);

	public int setUserWithInDistance(String distance);
	
}
