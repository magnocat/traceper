package com.traceper.android;



import java.util.ArrayList;

import android.app.Activity;
import android.app.AlertDialog;
import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.ComponentName;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.ServiceConnection;
import android.content.SharedPreferences;
import android.database.DataSetObserver;
import android.os.Bundle;
import android.os.IBinder;
import android.view.LayoutInflater;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.CheckedTextView;
import android.widget.EditText;
import android.widget.Spinner;
import android.widget.SpinnerAdapter;
import android.widget.TextView;

import com.traceper.R;
import com.traceper.android.interfaces.IAppService;
import com.traceper.android.interfaces.IAppService.CarOptions;
import com.traceper.android.services.AppService;

public class DriverServices extends Activity {
	private IAppService appManager;
	 private CarOptionsAdapter adapter;
	protected static final int FILL_BOTH_USERNAME_AND_PASSWORD = 1;
	protected static final int MAKE_SURE_USERNAME_AND_PASSWORD_CORRECT = 2;
	protected static final int CAR_IN_USE = 3;
	 
    private ServiceConnection mConnection = new ServiceConnection() {
       
		public void onServiceConnected(ComponentName className, IBinder service) {
            appManager = ((AppService.IMBinder)service).getService();  
            appManager.setAuthenticationServerAddress(getSharedPreferences(Configuration.PREFERENCES_NAME, 0).getString(Configuration.PREFERENCES_SERVER_INDEX, Configuration.DEFAULT_SERVER_ADRESS));
            
            ProgressDialog dialog = ProgressDialog.show(DriverServices.this, "", getString(R.string.loading));
            adapter = new CarOptionsAdapter(appManager.getCarOptions(), getApplicationContext());
            spinner.setAdapter(adapter);
            SharedPreferences preferences = getSharedPreferences(Configuration.PREFERENCES_NAME, 0);
            int selectedItem = preferences.getInt(Configuration.PREFRENCES_CAR_SERVICE_SELECTED_ITEM, -1);
            if (selectedItem != -1){     
            	spinner.setSelection(selectedItem);
            }
            dialog.cancel();
    		
        }
        public void onServiceDisconnected(ComponentName className) {
        }
    };

	private Spinner spinner;

	private Button sendCarServicesButton;
	private EditText carPasswordEditText;
	private EditText carLoginEditText;
	private Button carLoginButton;
	
	
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.driver_services);
		
		 spinner = (Spinner) findViewById(R.id.spinner);
		 sendCarServicesButton = (Button) findViewById(R.id.car_services_selected);
		 carPasswordEditText = (EditText) findViewById(R.id.car_password_text);
		 carLoginEditText = (EditText) findViewById(R.id.car_login_text);
		 carLoginButton = (Button) findViewById(R.id.car_login);
		 
		 sendCarServicesButton.setOnClickListener(new OnClickListener() {
			@Override
			public void onClick(View v) {
				int selectedItemPosition = spinner.getSelectedItemPosition();
				int result = appManager.sendCarServices(adapter.getCarOptions().get(selectedItemPosition).getId());
				if (result == IAppService.HTTP_RESPONSE_SUCCESS) {
					SharedPreferences.Editor editor = getSharedPreferences(Configuration.PREFERENCES_NAME, 0).edit();
					editor.putInt(Configuration.PREFRENCES_CAR_SERVICE_SELECTED_ITEM, selectedItemPosition);
					editor.commit();
					Intent i = new Intent(DriverServices.this, DriverMain.class);
					startActivity(i);
		
				}
			}
		});
		 
		 carLoginButton.setOnClickListener(new OnClickListener() {
			@Override
			public void onClick(View v) {
				String carId = carLoginEditText.getText().toString();
				String carPass = carPasswordEditText.getText().toString();
				if (carId.length() == 0 || carPass.length() == 0) {
					showDialog(FILL_BOTH_USERNAME_AND_PASSWORD);
				}
				else {
					ProgressDialog dialog = ProgressDialog.show(DriverServices.this, "", getString(R.string.loading));
					int result = appManager.authenticateCar(carId, carPass);
					dialog.cancel();
					if (result == IAppService.HTTP_RESPONSE_ERROR_UNAUTHORIZED_ACCESS) {
						showDialog(MAKE_SURE_USERNAME_AND_PASSWORD_CORRECT);
					}
					else if (result == IAppService.HTTP_RESPONSE_ERROR_CAR_IN_USE){
						showDialog(CAR_IN_USE);
					}
					else if (result == IAppService.HTTP_RESPONSE_SUCCESS) {
						Intent i = new Intent(DriverServices.this, DriverMain.class);
						startActivity(i);
		
					}
				}
			}
		});
	}
	
	@Override
	protected void onResume() {
		super.onResume();
		bindService(new Intent(DriverServices.this, AppService.class), mConnection , Context.BIND_AUTO_CREATE);
	}
	
	@Override
	protected void onPause() {
		unbindService(mConnection);
		super.onPause();
	}
	
	@Override
    protected Dialog onCreateDialog(int id) 
    {    	
    
    	switch (id) 
    	{
    		case FILL_BOTH_USERNAME_AND_PASSWORD:
    			return new AlertDialog.Builder(DriverServices.this)       
        		.setMessage(R.string.fill_both_carname_and_password)
        		.setPositiveButton(R.string.OK, new DialogInterface.OnClickListener() {
        			public void onClick(DialogInterface dialog, int whichButton) {
        				/* User clicked OK so do some stuff */
        			}
        		})        
        		.create();  
     		case MAKE_SURE_USERNAME_AND_PASSWORD_CORRECT:

    			return new AlertDialog.Builder(DriverServices.this)       
        		.setMessage(R.string.make_sure_carname_and_password_correct)
        		.setPositiveButton(R.string.OK, new DialogInterface.OnClickListener() {
        			public void onClick(DialogInterface dialog, int whichButton) {
        				/* User clicked OK so do some stuff */
        			}
        		})        
        		.create();	
     		case CAR_IN_USE:

    			return new AlertDialog.Builder(DriverServices.this)       
        		.setMessage(R.string.car_in_use)
        		.setPositiveButton(R.string.OK, new DialogInterface.OnClickListener() {
        			public void onClick(DialogInterface dialog, int whichButton) {
        				/* User clicked OK so do some stuff */
        			}
        		})        
        		.create();		
    		default:
    			return null;
    	}
    	
    	
    }
	
	public class CarOptionsAdapter implements SpinnerAdapter {
		
		private ArrayList<CarOptions> carOptions;
		private Context context;

		public CarOptionsAdapter(ArrayList<CarOptions> carOptions, Context context) {
			this.carOptions = carOptions;
			this.context = context;
		}
		
		public ArrayList<CarOptions> getCarOptions() {
			return carOptions;
		}

		@Override
		public int getCount() {
			return this.carOptions.size();
		}

		@Override
		public Object getItem(int index) {
			return this.carOptions.get(index);
		}

		@Override
		public long getItemId(int index) {
			return this.carOptions.get(index).getId();
		}

		@Override
		public View getView(int position, View view, ViewGroup parent) {
			TextView textView;
			if (view == null) {
				textView = (TextView)LayoutInflater.from(context).inflate(android.R.layout.simple_spinner_item, parent, false);
			}
			else {
				textView = (TextView) view;
			}
			textView.setText(this.carOptions.get(position).getTitle());			
			
			return textView;
		}

		@Override
		public int getItemViewType(int position) {
			return 0;
		}

		@Override
		public int getViewTypeCount() {
			return 1;
		}

		@Override
		public boolean hasStableIds() {
			return false;
		}

		@Override
		public boolean isEmpty() {
			return false;
		}

		@Override
		public void registerDataSetObserver(DataSetObserver observer) {
		}

		@Override
		public void unregisterDataSetObserver(DataSetObserver observer) {
		}

		@Override
		public View getDropDownView(int position, View convertView,
				ViewGroup parent) {
			CheckedTextView textView;
			if (convertView == null) {
				textView = (CheckedTextView)LayoutInflater.from(context).inflate(android.R.layout.simple_spinner_dropdown_item, parent, false);
			}
			else {
				textView = (CheckedTextView) convertView;
			}
			textView.setText(this.carOptions.get(position).getTitle());			
			
			return textView;
		}
		
	}

}
