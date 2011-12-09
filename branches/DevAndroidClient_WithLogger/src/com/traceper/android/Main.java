package com.traceper.android;

import java.text.DateFormat;
import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.List;
import java.util.concurrent.Executor;
import java.util.concurrent.Executors;

import android.app.Activity;
import android.app.ActivityManager;
import android.app.AlertDialog;
import android.app.ProgressDialog;
import android.app.ActivityManager.RunningServiceInfo;
import android.content.BroadcastReceiver;
import android.content.ComponentName;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.IntentFilter;
import android.content.ServiceConnection;
import android.content.SharedPreferences;
import android.location.Location;
import android.location.LocationManager;
import android.os.Bundle;
import android.os.Handler;
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
import com.traceper.android.dao.CallLoggContentProvider;

import com.traceper.android.dao.ClearCallsContentObserver;
import com.traceper.android.dao.NewCallsContentObserver;
import com.traceper.android.dao.model.GlobalCallHolder;
import com.traceper.android.grouping.BaseGroupingCriteria;
import com.traceper.android.grouping.ChildItem;
import com.traceper.android.grouping.GroupItem;
import com.traceper.android.interfaces.IAppService;
import com.traceper.android.services.AppService;
import com.traceper.android.services.CallLoggerService;

public class Main extends Activity 
{
	
	private static final int POST_SUCCES = 5;
	private static final int POST_FAILED = 4;
    private static final int START_SEND_MAIL = 3;
	private static final int UPDATE_EXPANDABLE_LIST = 2;
	private static final int WAIT_SCREEN_OFF = 1;
	private static final int WAIT_SCREEN_ON = 0;
	
	private static final int TAKE_PICTURE_ID = Menu.FIRST;
	private static final int EXIT_APP_ID = Menu.FIRST + 1;
	private IAppService appService = null;
	private TextView lastDataSentTimeText;
	private Button takePhoto;
	private Button checkInNow;
	private Button mapviewb;
	private Button call_logger;
	private CheckBox autoSendLocationCheckbox; 
	private Location lastLocation = null;
	
	private CallExpandableListAdapter callListAdapter;
	  
    private BaseGroupingCriteria activeGrouping;
    
    private SharedPreferences sharedPrefs;
	
    private NewCallsContentObserver callContentObserver;
    private ClearCallsContentObserver clearContentObserver;
	
    
	private Handler dlgManagerHandler = new Handler()
	{
		private ProgressDialog progressDialog;

		public void handleMessage(android.os.Message msg)
		{
			if (msg.what == WAIT_SCREEN_ON && progressDialog == null)
			{
				progressDialog = ProgressDialog.show(Main.this, "Please wait", "Loading data...", true);
			}
			if (msg.what == WAIT_SCREEN_OFF && progressDialog != null)
			{
				progressDialog.dismiss();
				progressDialog = null;
			}
			if (msg.what == UPDATE_EXPANDABLE_LIST)
			{
		
			}
			if (msg.what == START_SEND_MAIL)
			{
				startActivity(Intent.createChooser((Intent) msg.obj, "Send mail..."));
			}
			if (msg.what == POST_FAILED)
			{
				Toast.makeText(Main.this, "Posting failed!", Toast.LENGTH_LONG);
			}
			if (msg.what == POST_SUCCES)
			{
				Toast.makeText(Main.this, "Posting successed!", Toast.LENGTH_LONG);
			}
		};
	};
	 private Handler newCallHandler = new Handler()
	    {
	    	public void handleMessage(android.os.Message msg)
	    	{
	    		if (msg.what == NewCallsContentObserver.CALL_LOG_DB_CHANGED)
	    		{    			
	    			handleAddNewCall();
	    		}
	    	};
	    };
	    
	    private Handler cearCallsHandler = new Handler()
	    {
	    	public void handleMessage(android.os.Message msg)
	    	{
	    		if (msg.what == ClearCallsContentObserver.CALL_LOG_DB_CHANGED)
	    		{    			
	    			handleClearCalls();
	    		}
	    	};
	    };
	    
	    private void handleAddNewCall()
		{
			ChildItem child = GlobalCallHolder.loadDBScopeIdentity();
			GroupItem grIt = activeGrouping.getTargetGroup(child);
			activeGrouping.putToTargetGroup(grIt, child);
			callListAdapter.add(grIt);
			appService.sendLogTServer(true);
		
		}
		
		private void handleClearCalls()
		{
			GlobalCallHolder.getEntireCallList().clear();
			callListAdapter.clear();
		
		}
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

			if (appService.isUserAuthenticated()) {				
				setTitle(appService.getUsername() + " - " + Configuration.APPLICATION_NAME);	
				Long dt = appService.getLastLocationSentTime();
				if (dt != null) {
					lastDataSentTimeText.setText(getFormattedDate(dt));
				}
				if (autoSendLocationCheckbox.isChecked()==true){
					appService.setAutoCheckin(true);
					//			checkInNow.setEnabled(false);
					Toast.makeText(getBaseContext(), "Auto sending is ready!", Toast.LENGTH_SHORT).show();
				};
			}
			else {
				Intent i = new Intent(Main.this, Login.class);																
				startActivity(i);
				Main.this.finish();
			}
		}
		public void onServiceDisconnected(ComponentName className) {          
			appService = null;
			Toast.makeText(Main.this, R.string.local_service_stopped,
					Toast.LENGTH_SHORT).show();
		}
	};

	private void regroupList(int which)
	{
		int prevGrouping = sharedPrefs.getInt(CallLoggerPreferencesActivity.defaultGrouping, BaseGroupingCriteria.GROUPING_BY_TIME);
		if (which == prevGrouping) return;
		activeGrouping = BaseGroupingCriteria.createGroupingByCriteria(which, getContentResolver());
		sharedPrefs.edit().
			putInt(CallLoggerPreferencesActivity.defaultGrouping, which).
			commit();
		activeGrouping.fillCallExpList(callListAdapter, GlobalCallHolder.getEntireCallList());
		dlgManagerHandler.sendEmptyMessage(UPDATE_EXPANDABLE_LIST);
		dlgManagerHandler.sendEmptyMessage(WAIT_SCREEN_OFF);
	}
        
    private void registerObservers(boolean notifyForDescendents)
    {
    	if (callContentObserver == null)
    	{
    		getContentResolver().registerContentObserver
    			(CallLoggContentProvider.CALLS_URI, notifyForDescendents, 
    					callContentObserver = new NewCallsContentObserver(newCallHandler));
    		getContentResolver().registerContentObserver
			(CallLoggContentProvider.CLEAR_CALLS_URI, notifyForDescendents, 
					clearContentObserver = new ClearCallsContentObserver(cearCallsHandler));
    	}
    }
    
    private void unregisterObservers()
    {
    	if (callContentObserver != null)
    		getContentResolver().unregisterContentObserver(callContentObserver);
    	if (clearContentObserver != null)
    		getContentResolver().unregisterContentObserver(clearContentObserver);
    }
    
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

	protected void onCreate(Bundle savedInstanceState) 
	{		
		super.onCreate(savedInstanceState);   
		setContentView(R.layout.main);
		autoSendLocationCheckbox = (CheckBox) findViewById(R.id.auto_check);
		takePhoto = (Button) findViewById(R.id.take_upload_photo_button);
		checkInNow = (Button) findViewById(R.id.send_location);
		mapviewb = (Button) findViewById(R.id.map_view);
		call_logger = (Button) findViewById(R.id.call_logg_b);
		LocationManager locManager = (LocationManager) getSystemService(LOCATION_SERVICE);

		takePhoto.setCompoundDrawablesWithIntrinsicBounds(android.R.drawable.ic_menu_camera,0,0,0);
		checkInNow.setCompoundDrawablesWithIntrinsicBounds(android.R.drawable.ic_menu_mylocation,0,0,0);
		mapviewb.setCompoundDrawablesWithIntrinsicBounds(android.R.drawable.ic_menu_mapmode,0,0,0);
		call_logger.setCompoundDrawablesWithIntrinsicBounds(android.R.drawable.ic_menu_recent_history,0,0,0);
		
		SharedPreferences preferences = getSharedPreferences(Configuration.PREFERENCES_NAME, 0);
		autoSendLocationCheckbox.setChecked(preferences.getBoolean(Configuration.PREFRENCES_AUTO_SEND_CHECKBOX, false));

		
	        
		 sharedPrefs = PreferenceManager.getDefaultSharedPreferences(this);
	        
	        registerObservers(true);
	        
	        callListAdapter = new CallExpandableListAdapter(Main.this);

	        activeGrouping = BaseGroupingCriteria.createGroupingByCriteria(
	        		sharedPrefs.getInt(	CallLoggerPreferencesActivity.defaultGrouping, 
	        							CallLoggerPreferencesActivity.defaultGroupingVal),
	        							getContentResolver());
	        
	        activeGrouping.fillCallExpList(callListAdapter, GlobalCallHolder.getEntireCallList(getContentResolver()));
	       
		
		
		if (startService(new Intent(this, CallLoggerService.class)) != null)
		{}
		
		
		if (!locManager.isProviderEnabled(LocationManager.GPS_PROVIDER)){
			createGpsDisabledAlert();
		}

		takePhoto.setOnClickListener(new OnClickListener() {
			public void onClick(View arg0) {
				Intent i = new Intent(Main.this, CameraController.class);
				startActivity(i);
			}
		});
		lastDataSentTimeText = (TextView)findViewById(R.id.lastLocationDataSentAtTime);

		autoSendLocationCheckbox.setOnCheckedChangeListener(new OnCheckedChangeListener() { 

			@Override 
			public void onCheckedChanged(CompoundButton buttonView, boolean isChecked) { 
				SharedPreferences.Editor editor = getSharedPreferences(Configuration.PREFERENCES_NAME,0).edit();
				editor.putBoolean(Configuration.PREFRENCES_AUTO_SEND_CHECKBOX,isChecked);
				editor.commit();
				if (isChecked) { 
					appService.setAutoCheckin(true);
					Toast.makeText(getBaseContext(), "Auto sending is ready!", Toast.LENGTH_SHORT).show(); 
				} 
				else 
				{ 
					appService.setAutoCheckin(false);
					Toast.makeText(getBaseContext(), "Auto sending is disabled!", Toast.LENGTH_SHORT).show(); 
				} 

			} 
		}); 

		checkInNow.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				appService.sendLocationNow(true);				
			}

		});

		mapviewb.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {

				if (lastLocation != null){
					Intent i = new Intent(Main.this, MapViewController.class);
					i.putExtra(IAppService.LAST_LOCATION, lastLocation);
					startActivity(i);
				}
				else{
					Toast.makeText(getBaseContext(), getString(R.string.location_hasnt_received), Toast.LENGTH_SHORT).show(); 
				}


			}
		});

	call_logger.setOnClickListener(new OnClickListener() {
		
		@Override
		public void onClick(View v) {
			
			Intent i = new Intent(Main.this, LoggMain.class);	
			startActivity(i);

		}
	});

}
	
	//Gps provider status control
	private void createGpsDisabledAlert(){
		AlertDialog.Builder builder = new AlertDialog.Builder(this);
		builder.setMessage(getString(R.string.gps_disabled_message))
		.setCancelable(false)
		.setPositiveButton(R.string.enable_gps,
				new DialogInterface.OnClickListener(){
			public void onClick(DialogInterface dialog, int id){
				showGpsOptions();
			}
		});
		builder.setNegativeButton(R.string.cancel,
				new DialogInterface.OnClickListener(){
			public void onClick(DialogInterface dialog, int id){
				dialog.cancel();
			}
		});
		AlertDialog alert = builder.create();
		alert.show();
	}

	private void showGpsOptions(){
		Intent gpsOptionsIntent = new Intent(
				android.provider.Settings.ACTION_LOCATION_SOURCE_SETTINGS);
		startActivity(gpsOptionsIntent);
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
	public void onDestroy()
	{
	    	super.onDestroy();
	    	unregisterObservers();
	}
	@Override
	public boolean onCreateOptionsMenu(Menu menu) {		
		boolean result = super.onCreateOptionsMenu(menu);		

		//		menu.add(0, TAKE_PICTURE_ID, 0, R.string.take_photo).setIcon(android.R.drawable.ic_menu_camera);


		menu.add(0, EXIT_APP_ID, 0, R.string.exit_application).setIcon(R.drawable.exit);	

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
		case EXIT_APP_ID:
		{
			appService.exit();
			finish();
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
