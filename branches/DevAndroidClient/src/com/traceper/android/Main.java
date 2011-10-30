package com.traceper.android;

import java.text.DateFormat;
import java.text.SimpleDateFormat;
import java.util.Date;

import android.app.Activity;
import android.app.AlertDialog;
import android.content.BroadcastReceiver;
import android.content.ComponentName;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.IntentFilter;
import android.content.ServiceConnection;
import android.content.SharedPreferences;
import android.location.LocationManager;
import android.os.Bundle;
import android.os.IBinder;
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
import android.os.Handler;


import com.traceper.R;
import com.traceper.android.interfaces.IAppService;
import com.traceper.android.services.AppService;

public class Main extends Activity 
{



	private static final int TAKE_PICTURE_ID = Menu.FIRST;
	private static final int EXIT_APP_ID = Menu.FIRST + 1;
	private IAppService appService = null;
	private TextView lastDataSentTimeText;
	private Button takePhoto;
	private Button sendLocation;
	private CheckBox autoSendLocationCheckbox; 
	private Handler handler = new Handler();


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
					sendLocation.setEnabled(false);
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




	protected void onCreate(Bundle savedInstanceState) 
	{		
		super.onCreate(savedInstanceState);   
		setContentView(R.layout.main);
		autoSendLocationCheckbox = (CheckBox) findViewById(R.id.auto_check);
		takePhoto = (Button) findViewById(R.id.take_upload_photo_button);
		sendLocation = (Button) findViewById(R.id.send_location);
		LocationManager locManager = (LocationManager) getSystemService(LOCATION_SERVICE);
		   
		takePhoto.setCompoundDrawablesWithIntrinsicBounds(android.R.drawable.ic_menu_camera,0,0,0);
		sendLocation.setCompoundDrawablesWithIntrinsicBounds(android.R.drawable.ic_menu_mylocation,0,0,0);
		SharedPreferences preferences = getSharedPreferences(Configuration.PREFERENCES_NAME, 0);
		autoSendLocationCheckbox.setChecked(preferences.getBoolean(Configuration.PREFRENCES_AUTO_SEND_CHECKBOX, false));

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
					//sendLocation.setEnabled(false);
					Toast.makeText(getBaseContext(), "Auto sending is ready!", Toast.LENGTH_SHORT).show(); 
				} 
				else 
				{ 
					appService.setAutoCheckin(false);
					//sendLocation.setEnabled(true);
					Toast.makeText(getBaseContext(), "Auto sending is disable!", Toast.LENGTH_SHORT).show(); 
				} 

			} 
		}); 
		
		sendLocation.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				         if (autoSendLocationCheckbox.isChecked()) {
				        	autoSendLocationCheckbox.setChecked(false);
		         }
				appService.sendLocationNow(true);
				
			}
	
		});



	}

//Gps provider status control
	private void createGpsDisabledAlert(){
		AlertDialog.Builder builder = new AlertDialog.Builder(this);
		builder.setMessage("Your GPS is disabled! Would you like to enable it?")
		     .setCancelable(false)
		     .setPositiveButton("Enable GPS",
		          new DialogInterface.OnClickListener(){
		          public void onClick(DialogInterface dialog, int id){
		               showGpsOptions();
		          }
		     });
		     builder.setNegativeButton("Do nothing",
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
