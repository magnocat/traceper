package com.traceper.android;

import android.app.Activity;
import android.app.ProgressDialog;
import android.content.ComponentName;
import android.content.Context;
import android.content.Intent;
import android.content.ServiceConnection;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.os.IBinder;
import android.view.View;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemSelectedListener;
import android.widget.ArrayAdapter;
import android.widget.Spinner;
import android.widget.TextView;
import android.widget.Toast;

import com.traceper.R;
import com.traceper.android.interfaces.IAppService;
import com.traceper.android.services.AppService;

public class DriverMain extends Activity{
	private IAppService appManager;

    private ServiceConnection mConnection = new ServiceConnection() {
        
		public void onServiceConnected(ComponentName className, IBinder service) {
            appManager = ((AppService.IMBinder)service).getService();  
            appManager.setAuthenticationServerAddress(getSharedPreferences(Configuration.PREFERENCES_NAME, 0).getString(Configuration.PREFERENCES_SERVER_INDEX, Configuration.DEFAULT_SERVER_ADRESS));
    		
        }
        public void onServiceDisconnected(ComponentName className) {
        }
    };
	
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.driver_main);

		Spinner userStatusSpinner = (Spinner) findViewById(R.id.user_status);
		ArrayAdapter<CharSequence> adapter = ArrayAdapter.createFromResource(
				this, R.array.user_status, android.R.layout.simple_spinner_item);
		adapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
		userStatusSpinner.setAdapter(adapter);

		SharedPreferences preferences = getSharedPreferences(Configuration.PREFERENCES_NAME, 0);
		int selectedItem = preferences.getInt(Configuration.PREFRENCES_STATUS_SELECTED_ITEM, -1);
        if (selectedItem != -1){     
        	userStatusSpinner.setSelection(selectedItem);
        }


		userStatusSpinner.setOnItemSelectedListener(new OnItemSelectedListener() {
			boolean firstEntrace = true;
			@Override
			public void onItemSelected(AdapterView<?> arg0, View view,
					int position, long id) {
				if (firstEntrace == true){
					firstEntrace = false;
					return;
				}
				SharedPreferences.Editor editor = getSharedPreferences(Configuration.PREFERENCES_NAME, 0).edit();
				editor.putInt(Configuration.PREFRENCES_STATUS_SELECTED_ITEM, position);
				editor.commit();
				
				String text = ((TextView) view).getText().toString();
				boolean statusOnline = false;
				if (text.equals("Online")) {
					statusOnline = true;
				}
				ProgressDialog dialog = ProgressDialog.show(DriverMain.this, "", getString(R.string.loading));
				appManager.setUserStatus(statusOnline);
				dialog.cancel();
			}

			@Override
			public void onNothingSelected(AdapterView<?> arg0) {
				// TODO Auto-generated method stub
				
			}
		});
		
		Spinner withInDistanceSpinner = (Spinner) findViewById(R.id.withInDistance);
		ArrayAdapter<CharSequence> distanceAdapter = ArrayAdapter.createFromResource(
				this, R.array.distance, android.R.layout.simple_spinner_item);
		distanceAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
		withInDistanceSpinner.setAdapter(distanceAdapter);
		
		selectedItem = preferences.getInt(Configuration.PREFRENCES_WITHIN_DISTANCE_SELECTED_ITEM, -1);
        if (selectedItem != -1){     
        	withInDistanceSpinner.setSelection(selectedItem);
        }
		
		
		withInDistanceSpinner.setOnItemSelectedListener(new OnItemSelectedListener() {
			boolean firstEntrace = true;
			@Override
			public void onItemSelected(AdapterView<?> arg0, View view, int position, long id) {
				if (firstEntrace == true){
					firstEntrace = false;
					return;
				}
				SharedPreferences.Editor editor = getSharedPreferences(Configuration.PREFERENCES_NAME, 0).edit();
				editor.putInt(Configuration.PREFRENCES_WITHIN_DISTANCE_SELECTED_ITEM, position);
				editor.commit();
				
				String distance = ((TextView) view).getText().toString();
				ProgressDialog dialog = ProgressDialog.show(DriverMain.this, "", getString(R.string.loading));
				appManager.setUserWithInDistance(distance);
				dialog.cancel();				
			}

			@Override
			public void onNothingSelected(AdapterView<?> arg0) {
			}
		});


	}
	
	@Override
	protected void onResume() {
		bindService(new Intent(DriverMain.this, AppService.class), mConnection , Context.BIND_AUTO_CREATE);
		
		super.onResume();
	}
	
	@Override
	protected void onPause() {
		unbindService(mConnection);
		super.onPause();
	}

}
