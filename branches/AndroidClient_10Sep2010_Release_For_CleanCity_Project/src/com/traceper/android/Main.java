package com.traceper.android;

import java.text.DateFormat;
import java.text.SimpleDateFormat;
import java.util.Date;

import android.app.Activity;
import android.app.AlertDialog;
import android.app.Dialog;
import android.content.BroadcastReceiver;
import android.content.ComponentName;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.IntentFilter;
import android.content.ServiceConnection;
import android.os.Bundle;
import android.os.IBinder;
import android.util.Log;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.Button;
import android.widget.Toast;

import com.traceper.R;
import com.traceper.android.interfaces.IAppService;
import com.traceper.android.services.AppService;

public class Main extends Activity 
{
	private static final int TAKE_PICTURE_ID = Menu.FIRST;
	private static final int EXIT_APP_ID = Menu.FIRST + 1;
	protected static final int DIALOG_ENABLE_GPS = 0;
	private IAppService appService = null;
//	private TextView lastDataSentTimeText;
	private Button takePhoto;
	public class MessageReceiver extends  BroadcastReceiver  {
		public void onReceive(Context context, Intent intent) {
			
			Log.i("Broadcast receiver ", "received a message");
			Bundle extra = intent.getExtras();
			if (extra != null)
			{
				String action = intent.getAction();
				if (action.equals(IAppService.LAST_LOCATION_DATA_SENT_TIME))
				{
					//Long time = (Long) extra.getLong(IAppService.LAST_LOCATION_DATA_SENT_TIME);
					//lastDataSentTimeText.setText(getFormattedDate(time));
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
//				lastDataSentTimeText.setText(getFormattedDate(appService.getLastLocationSentTime()));
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
		takePhoto = (Button) findViewById(R.id.take_upload_photo_button);
		takePhoto.setCompoundDrawablesWithIntrinsicBounds(android.R.drawable.ic_menu_camera,0,0,0);
		takePhoto.setOnClickListener(new OnClickListener() {
			public void onClick(View arg0) {
				if (appService.isGPSEnabled() == true) { 
					Intent i = new Intent(Main.this, CameraController.class);
					startActivity(i);
				}
				else {
					showDialog(DIALOG_ENABLE_GPS);
				}
			}
		});
	//	lastDataSentTimeText = (TextView)findViewById(R.id.lastLocationDataSentAtTime);
		
//		AdView adView = (AdView)findViewById(R.id.adView);
//	   adView.loadAd(new AdRequest());
    }
	
	@Override
	protected Dialog onCreateDialog(int id) {
		switch (id) 
    	{
    		case DIALOG_ENABLE_GPS:
    		
    			return new AlertDialog.Builder(Main.this)       
        		.setMessage(R.string.enable_gps)
        		.setPositiveButton(R.string.OK, new DialogInterface.OnClickListener() {
        			public void onClick(DialogInterface dialog, int whichButton) {
        				/* User clicked OK so do some stuff */
        			}
        		})        
        		.create();    
    	}
		return super.onCreateDialog(id);
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