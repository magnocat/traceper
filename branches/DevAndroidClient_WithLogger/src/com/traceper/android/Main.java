package com.traceper.android;

import java.text.DateFormat;
import java.text.SimpleDateFormat;
import java.util.Date;

import android.app.Activity;
import android.content.BroadcastReceiver;
import android.content.ComponentName;
import android.content.Context;
import android.content.Intent;
import android.content.IntentFilter;
import android.content.ServiceConnection;
import android.content.SharedPreferences;
import android.location.Location;
import android.location.LocationManager;
import android.os.Bundle;
import android.os.IBinder;
import android.preference.PreferenceManager;
import android.util.Log;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.Button;
import android.widget.CheckBox;
import android.widget.CompoundButton;
import android.widget.CompoundButton.OnCheckedChangeListener;
import android.widget.TextView;
import android.widget.Toast;

import com.traceper.R;
import com.traceper.android.interfaces.IAppService;
import com.traceper.android.services.AppService;

public class Main extends Activity 
{

	private static final int POST_SUCCES = 5;
	private static final int POST_FAILED = 4;
	private static final int START_SEND_MAIL = 3;
	private static final int UPDATE_EXPANDABLE_LIST = 2;
	private static final int WAIT_SCREEN_OFF = 1;
	private static final int WAIT_SCREEN_ON = 0;

	private static final int TAKE_PICTURE_ID = Menu.FIRST;
	private static final int RESTART_APP_ID = Menu.FIRST + 1;
	private IAppService appService = null;
	private TextView lastDataSentTimeText;
	private Button takePhoto;
//	private Button checkInNow;
	private Button mapviewb;
	private Button call_logger;
	private CheckBox autoSendLocationCheckbox; 
	private Location lastLocation = null;
	private SharedPreferences sharedPrefs;


	public class MessageReceiver extends  BroadcastReceiver  {
		public void onReceive(Context context, Intent intent) {

			Log.i("Broadcast receiver ", "received a message");
			Bundle extra = intent.getExtras();
			if (extra != null)
			{
				String action = intent.getAction();
				if (action.equals(IAppService.LAST_LOCATION_DATA_SENT_TIME))
				{
					Long time = (Long) extra.getLong(IAppService.LAST_LOCATION_DATA_SENT_TIME);
					lastDataSentTimeText.setText(getFormattedDate(time));
					lastLocation = (Location)extra.getParcelable(IAppService.LAST_LOCATION);										
				}
			}

		}

	};
	public MessageReceiver messageReceiver = new MessageReceiver();

	private ServiceConnection mConnection = new ServiceConnection() 
	{
		public void onServiceConnected(ComponentName className, IBinder service) {          
			appService = ((AppService.IMBinder)service).getService();  
			appService.setAuthenticationServerAddress(getSharedPreferences(Configuration.PREFERENCES_NAME, 0).getString(Configuration.PREFERENCES_SERVER_INDEX, Configuration.DEFAULT_SERVER_ADRESS));

			//if (appService.isUserAuthenticated()) 
			{				
				setTitle(appService.getUsername() + " - " + Configuration.APPLICATION_NAME);	
				Long dt = appService.getLastLocationSentTime();
				if (dt != null) {
					lastDataSentTimeText.setText(getFormattedDate(dt));
				}
//				if (autoSendLocationCheckbox.isChecked()==true){
//					appService.setAutoCheckin(true);
//					//			checkInNow.setEnabled(false);
//					Toast.makeText(getBaseContext(), "Auto sending is ready!", Toast.LENGTH_SHORT).show();
//				};
				appService.sendLogTServer(true);
			}
//			else {
//				Intent i = new Intent(Main.this, Login.class);																
//				startActivity(i);
//				Main.this.finish();
//			}
		}
		public void onServiceDisconnected(ComponentName className) {          
			appService = null;
			Toast.makeText(Main.this, R.string.local_service_stopped,
					Toast.LENGTH_SHORT).show();
		}
	};



	/*
	private boolean isCallServiceRun()
	{
		ActivityManager activMan = (ActivityManager) getSystemService(ACTIVITY_SERVICE);
		List<ActivityManager.RunningServiceInfo> servList = activMan.getRunningServices(Integer.MAX_VALUE);

		if (servList != null && servList.size()>0)
		{
			for (RunningServiceInfo runningServiceInfo : servList)
			{
				if (runningServiceInfo.service.getClassName().equalsIgnoreCase(CallLoggerService.class.getName()))
				{
					return true;
				}
			}
		}
		return false;
	}
	*/

	protected void onCreate(Bundle savedInstanceState) 
	{		
		super.onCreate(savedInstanceState);   
		setContentView(R.layout.main);
//		autoSendLocationCheckbox = (CheckBox) findViewById(R.id.auto_check);
		takePhoto = (Button) findViewById(R.id.take_upload_photo_button);
//		checkInNow = (Button) findViewById(R.id.send_location);

		LocationManager locManager = (LocationManager) getSystemService(LOCATION_SERVICE);

		takePhoto.setCompoundDrawablesWithIntrinsicBounds(android.R.drawable.ic_menu_camera,0,0,0);
//		checkInNow.setCompoundDrawablesWithIntrinsicBounds(android.R.drawable.ic_menu_mylocation,0,0,0);
		sharedPrefs = PreferenceManager.getDefaultSharedPreferences(this);

		if (!locManager.isProviderEnabled(LocationManager.GPS_PROVIDER)){
			Toast.makeText(this, R.string.gps_disabled_message, Toast.LENGTH_SHORT).show();
		}

		takePhoto.setOnClickListener(new OnClickListener() {
			public void onClick(View arg0) {
				Intent i = new Intent(Main.this, CameraController.class);
				startActivity(i);
			}
		});
		lastDataSentTimeText = (TextView)findViewById(R.id.lastLocationDataSentAtTime);

//		autoSendLocationCheckbox.setOnCheckedChangeListener(new OnCheckedChangeListener() { 
//
//			@Override 
//			public void onCheckedChanged(CompoundButton buttonView, boolean isChecked) { 
//				SharedPreferences.Editor editor = getSharedPreferences(Configuration.PREFERENCES_NAME,0).edit();
//				editor.putBoolean(Configuration.PREFRENCES_AUTO_SEND_CHECKBOX,isChecked);
//				editor.commit();
//				if (isChecked) { 
//					appService.setAutoCheckin(true);
//					Toast.makeText(getBaseContext(), "Auto sending is ready!", Toast.LENGTH_SHORT).show(); 
//				} 
//				else 
//				{ 
//					appService.setAutoCheckin(false);
//					Toast.makeText(getBaseContext(), "Auto sending is disabled!", Toast.LENGTH_SHORT).show(); 
//				} 
//
//			} 
//		}); 

//		checkInNow.setOnClickListener(new OnClickListener() {
//
//			@Override
//			public void onClick(View v) {
//				appService.sendLocationNow(true);				
//			}
//
//		});

	


	}

	@Override
	protected void onPause() 
	{
		unregisterReceiver(messageReceiver);		
		unbindService(mConnection);
		super.onPause();
	}

	@Override
	protected void onResume() 
	{			
		super.onResume();
		bindService(new Intent(Main.this, AppService.class), mConnection , Context.BIND_AUTO_CREATE);
		IntentFilter i = new IntentFilter();
		i.addAction(IAppService.LAST_LOCATION_DATA_SENT_TIME);
		//i.addAction(IMService.FRIEND_LIST_UPDATED);
		registerReceiver(messageReceiver, i);	
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {		
		boolean result = super.onCreateOptionsMenu(menu);		

		//		menu.add(0, TAKE_PICTURE_ID, 0, R.string.take_photo).setIcon(android.R.drawable.ic_menu_camera);

		menu.add(0, RESTART_APP_ID, 0, R.string.restart_application); //.setIcon(R.drawable.exit);	

		return result;
	}




	@Override
	public boolean onMenuItemSelected(int featureId, MenuItem item) 
	{		

		switch(item.getItemId()) 
		{	  
		case TAKE_PICTURE_ID:
		{
			Intent i = new Intent(Main.this, CameraController.class);
			startActivity(i);
			return true;
		}		
		case RESTART_APP_ID:
		{
			appService.restart();
			
			//startService(i);
			//finish();
			return true;
		}			
		}

		return super.onMenuItemSelected(featureId, item);		
	}	

	@Override
	protected void onActivityResult(int requestCode, int resultCode, Intent data) {		
		super.onActivityResult(requestCode, resultCode, data);		
	}

	public String getFormattedDate(Long time) 
	{
		DateFormat df = new SimpleDateFormat("dd MMM yyyy HH:mm");
		return df.format(new Date(time));
	}
}
