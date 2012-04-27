package com.traceper.android;

import android.app.Activity;
import android.app.AlertDialog;
import android.app.Dialog;
import android.app.ProgressDialog;
import android.app.AlertDialog.Builder;
import android.content.ComponentName;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.ServiceConnection;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.os.Handler;
import android.os.IBinder;
import android.view.LayoutInflater;
import android.view.View;
import android.widget.CheckBox;
import android.widget.EditText;
import android.widget.Toast;

import com.facebook.android.Facebook;

import com.traceper.R;
import com.traceper.android.interfaces.IAppService;
import com.traceper.android.services.AppService;


public class LoginControl extends Activity {
    protected static final int NOT_CONNECTED_TO_SERVICE = 0;
	protected static final int FILL_BOTH_USERNAME_AND_PASSWORD = 1;
	private static final int MAKE_SURE_USERNAME_AND_PASSWORD_CORRECT = 2 ;
	private static final int NOT_CONNECTED_TO_NETWORK = 3;
	private static final int UNKNOWN_ERROR_OCCURED = 4;
	private static final int SETTINGS_DIALOG = 5;
	private static final int HTTP_REQUEST_FAILED = 6;
	private static final int HTTP_MISSING_PARAMETER = 7;
	private static final int CUSTOM_MESSAGE_DIALOG = 8;
	private boolean FB_LOGIN=false;

    public static final String FB_APP_ID = "370934372924974";
    private CheckBox rememberMeCheckBox;
    private String dialogMessage;
    private IAppService appManager;
    private ProgressDialog progressDialog;
    private String usermail;
    private String userid;
    
    
    private ServiceConnection mConnection = new ServiceConnection() {
        public void onServiceConnected(ComponentName className, IBinder service) {
            // This is called when the connection with the service has been
            // established, giving us the service object we can use to
            // interact with the service.  Because we have bound to a explicit
            // service that we know is running in our own process, we can
            // cast its IBinder to a concrete class and directly access it.
            appManager = ((AppService.IMBinder)service).getService();  
            appManager.setAuthenticationServerAddress(getSharedPreferences(Configuration.PREFERENCES_NAME, 0).getString(Configuration.PREFERENCES_SERVER_INDEX, Configuration.DEFAULT_SERVER_ADRESS));
           
            if (appManager.isNetworkConnected() == false)
    		{
    			showDialog(NOT_CONNECTED_TO_NETWORK);					
    		}
            else if((appManager.isNetworkConnected()==true) && (FB_LOGIN == true)){
            	fb_login();
            }
            else if (appManager.isUserAuthenticated() == true)
            {
            	Intent i = new Intent(LoginControl.this, Main.class);																
				startActivity(i);
				LoginControl.this.finish();
            }
        }

        public void onServiceDisconnected(ComponentName className) {
            // This is called when the connection with the service has been
            // unexpectedly disconnected -- that is, its process crashed.
            // Because it is running in our same process, we should never
            // see this happen.
        	appManager = null;
            Toast.makeText(LoginControl.this, R.string.local_service_stopped,
                    Toast.LENGTH_SHORT).show();
        }
    };
    
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        if (FB_APP_ID == null) {
            Builder alertBuilder = new Builder(this);
            alertBuilder.setTitle("Warning");
            alertBuilder.setMessage("A Facebook Applicaton ID must be " +
                    "specified before running this example: see App.java");
            alertBuilder.create().show();
        }
        

        // If a session already exists, render the stream page
        // immediately. Otherwise, render the login page.
        Session session = Session.restore(this);
        if (session != null) {
          
        	usermail = session.getEmail();
        	userid = session.getUid();
        	FB_LOGIN = true;
        	
        } else {
			Intent i = new Intent(LoginControl.this, Login.class);			
			startActivity(i);
			LoginControl.this.finish();
        	
        }
    }
    
    
    @Override
    protected Dialog onCreateDialog(int id) 
    {    	
    
    	switch (id) 
    	{
    		case NOT_CONNECTED_TO_SERVICE:
    		
    			return new AlertDialog.Builder(LoginControl.this)       
        		.setMessage(R.string.not_connected_to_service)
        		.setPositiveButton(R.string.OK, new DialogInterface.OnClickListener() {
        			public void onClick(DialogInterface dialog, int whichButton) {
        				/* User clicked OK so do some stuff */
        			}
        		})        
        		.create();    			
    			
    		case FILL_BOTH_USERNAME_AND_PASSWORD:
    		
    			return new AlertDialog.Builder(LoginControl.this)       
        		.setMessage(R.string.fill_both_username_and_password)
        		.setPositiveButton(R.string.OK, new DialogInterface.OnClickListener() {
        			public void onClick(DialogInterface dialog, int whichButton) {
        				/* User clicked OK so do some stuff */
        				Session.clearSavedSession(getApplicationContext());
        				finish();
        			}
        		})        
        		.create();    
    			
    		case MAKE_SURE_USERNAME_AND_PASSWORD_CORRECT:

    			return new AlertDialog.Builder(LoginControl.this)       
        		.setMessage(R.string.make_sure_username_and_password_correct)
        		.setPositiveButton(R.string.OK, new DialogInterface.OnClickListener() {
        			public void onClick(DialogInterface dialog, int whichButton) {
        				/* User clicked OK so do some stuff */
        			
        			}
        		})        
        		.create();  
    			
    		case NOT_CONNECTED_TO_NETWORK:

    			return new AlertDialog.Builder(LoginControl.this)       
        		.setMessage(R.string.not_connected_to_network)
        		.setPositiveButton(R.string.OK, new DialogInterface.OnClickListener() {
        			public void onClick(DialogInterface dialog, int whichButton) {
        				finish();
        			}
        		})        
        		.create(); 
    			
    		case UNKNOWN_ERROR_OCCURED:

    			return new AlertDialog.Builder(LoginControl.this)       
        		.setMessage(R.string.unknown_error_occured)
        		.setPositiveButton(R.string.OK, new DialogInterface.OnClickListener() {
        			public void onClick(DialogInterface dialog, int whichButton) {
        				/* User clicked OK so do some stuff */
        			}
        		})        
        		.create(); 
    		
    		case SETTINGS_DIALOG:
    			
    			LayoutInflater factory = LayoutInflater.from(this);
                View textEntryView = factory.inflate(R.layout.server_address_entry_dialog, null);
                
                final EditText server_address_edit = (EditText) textEntryView.findViewById(R.id.server_address_edit);
              
                server_address_edit.setText(getSharedPreferences(Configuration.PREFERENCES_NAME, 0).
                									getString(Configuration.PREFERENCES_SERVER_INDEX, Configuration.DEFAULT_SERVER_ADRESS));
                
                return new AlertDialog.Builder(LoginControl.this)
                   // .setIcon(R.drawable.alert_dialog_icon)
                    .setTitle(R.string.alert_dialog_settings)
                    .setView(textEntryView)
                    .setPositiveButton(R.string.alert_dialog_ok, new DialogInterface.OnClickListener() {
                        public void onClick(DialogInterface dialog, int whichButton) {
                        	SharedPreferences.Editor editor = getSharedPreferences(Configuration.PREFERENCES_NAME, 0).edit();
                        	editor.putString(Configuration.PREFERENCES_SERVER_INDEX, server_address_edit.getText().toString());
                        	editor.commit();
                        	appManager.setAuthenticationServerAddress(server_address_edit.getText().toString());
                        }
                    })
                    .setNegativeButton(R.string.alert_dialog_cancel, new DialogInterface.OnClickListener() {
                        public void onClick(DialogInterface dialog, int whichButton) {
                        	server_address_edit.setText(getSharedPreferences(Configuration.PREFERENCES_NAME, 0).
									getString(Configuration.PREFERENCES_SERVER_INDEX, Configuration.DEFAULT_SERVER_ADRESS));
                            /* User clicked cancel so do some stuff */
                        }
                    })
                    .create(); 
    		case HTTP_REQUEST_FAILED:
    			return new AlertDialog.Builder(LoginControl.this)       
        		.setMessage(R.string.http_request_failed)
        		.setPositiveButton(R.string.OK, new DialogInterface.OnClickListener() {
        			public void onClick(DialogInterface dialog, int whichButton) {
        				/* User clicked OK so do some stuff */
        			}
        		})        
        		.create(); 
    		case HTTP_MISSING_PARAMETER:
    			return new AlertDialog.Builder(LoginControl.this)       
        		.setMessage(R.string.http_missing_parameter)
        		.setPositiveButton(R.string.OK, new DialogInterface.OnClickListener() {
        			public void onClick(DialogInterface dialog, int whichButton) {
        				/* User clicked OK so do some stuff */
        			}
        		})        
        		.create(); 
    		case CUSTOM_MESSAGE_DIALOG:
    			return new AlertDialog.Builder(LoginControl.this)       
        		.setMessage(this.dialogMessage)
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
    
    private void fb_login(){
	
	startService(new Intent(LoginControl.this,  AppService.class));
	
	if (appManager == null) {
		showDialog(NOT_CONNECTED_TO_SERVICE);
		return;
	}
	else if (appManager.isNetworkConnected() == false)
	{
		showDialog(NOT_CONNECTED_TO_NETWORK);					
	}
	//TODO: check whether email format is valid.
	else if (usermail.length() > 0 && userid.length() > 0)
	{					
		progressDialog = ProgressDialog.show(LoginControl.this, "", getString(R.string.loading), true, false);	
		
		Thread loginThread = new Thread(){
			private Handler handler = new Handler();
			String result;
			@Override
			public void run() {
				result = appManager.authenticateUser(usermail , userid);
				
				handler.post(new Runnable(){
					public void run() {										
						progressDialog.dismiss();
						
						if (result.equals("1")) // == IAppService.HTTP_RESPONSE_SUCCESS)
						{
													
							Intent i = new Intent(LoginControl.this, Main.class);																	
							startActivity(i);	
							LoginControl.this.finish();										
						}
						else{
							LoginControl.this.dialogMessage = result;
							showDialog(CUSTOM_MESSAGE_DIALOG);
						}
					}									
				});
											
			}
		};
		
		loginThread.start();
		
	}
	else {
		// Username or Password is not filled, alert the user					 
		showDialog(FILL_BOTH_USERNAME_AND_PASSWORD);
	}
}
@Override
protected void onPause() 
{
	unbindService(mConnection);
	super.onPause();
}

@Override
protected void onResume() 
{		
	bindService(new Intent(LoginControl.this, AppService.class), mConnection , Context.BIND_AUTO_CREATE);
	super.onResume();
}  
    protected void onActivityResult(int requestCode, int resultCode,
                                    Intent data) {
        Facebook fb = Session.wakeupForAuthCallback();
        fb.authorizeCallback(requestCode, resultCode, data);
    }

}