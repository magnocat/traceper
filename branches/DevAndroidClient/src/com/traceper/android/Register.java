package com.traceper.android;

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
import android.os.Bundle;
import android.os.Handler;
import android.os.IBinder;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.Button;
import android.widget.EditText;
import android.widget.Toast;

import com.traceper.R;
import com.traceper.android.interfaces.IAppService;
import com.traceper.android.services.AppService;

public class Register extends Activity {
	
	private static final int FILL_ALL_FIELDS = 0;
	protected static final int TYPE_SAME_PASSWORD_IN_PASSWORD_FIELDS = 1;
	private static final int SIGN_UP_SUCCESSFULL = 2;
	protected static final int EMAIL_AND_PASSWORD_LENGTH_SHORT = 3;
	protected static final int CUSTOM_MESSAGE_DIALOG = 4;
	private static final int NOT_CONNECTED_TO_NETWORK = 5;
	
	public static final String EXTRA_NAME = "INTENT_NAME_KEY";
	public static final String EXTRA_EMAIL = "INTENT_EMAIL_KEY";
	public static final String EXTRA_FACEBOOK_ID = "INTENT_FACEBOOK_ID_KEY";
	public static final String ACTION_REGISTER_FACEBOOK_USER = "com.traceper.android.Register.FacebookUser";
	
	private EditText passwordText;
	private EditText eMailText;
	private EditText passwordAgainText;
	private EditText realnameText;
	private IAppService appService;
	private ProgressDialog progressDialog;
	private Handler handler = new Handler();
	
	private String serverResponse;
	
	private ServiceConnection mConnection = new ServiceConnection() {
        

		public void onServiceConnected(ComponentName className, IBinder service) {
            // This is called when the connection with the service has been
            // established, giving us the service object we can use to
            // interact with the service.  Because we have bound to a explicit
            // service that we know is running in our own process, we can
            // cast its IBinder to a concrete class and directly access it.
			
            appService = ((AppService.IMBinder)service).getService(); 
            appService.setAuthenticationServerAddress(getSharedPreferences(Configuration.PREFERENCES_NAME, 0).getString(Configuration.PREFERENCES_SERVER_INDEX, Configuration.DEFAULT_SERVER_ADRESS));
            
            if (appService.isNetworkConnected() == false)
    		{
    			showDialog(NOT_CONNECTED_TO_NETWORK);					
    		}
            
        }

        public void onServiceDisconnected(ComponentName className) {
            // This is called when the connection with the service has been
            // unexpectedly disconnected -- that is, its process crashed.
            // Because it is running in our same process, we should never
            // see this happen.
        	appService = null;
            Toast.makeText(Register.this, R.string.local_service_stopped,
                    Toast.LENGTH_SHORT).show();
        }
    };
	protected String dialogMessage;
	private String facebookId = null;

	public void onCreate(Bundle savedInstanceState) {
	        super.onCreate(savedInstanceState);    

	        // it is a precaution to disable registration if it is not enabled
	        if (Configuration.REGISTRATION_ENABLED == true) {
	            setContentView(R.layout.sign_up_screen);
	        }
	        setTitle("Register - " + Configuration.APPLICATION_NAME);
	        
	        Button signUpButton = (Button) findViewById(R.id.register);
	        Button cancelButton = (Button) findViewById(R.id.cancel_signUp);
	        passwordText = (EditText) findViewById(R.id.password);  
	        passwordAgainText = (EditText) findViewById(R.id.passwordAgain);  
	        eMailText = (EditText) findViewById(R.id.email);
	        realnameText = (EditText) findViewById(R.id.realname);
	        
	        Intent i = getIntent();
	        String action = i.getAction();
	        if (action != null && action.equals(ACTION_REGISTER_FACEBOOK_USER)) {
	        	Bundle extras = i.getExtras();
	        	String email =  extras.getString(EXTRA_EMAIL);
	        	eMailText.setText(email);
	        	eMailText.setEnabled(false);
	        	
	        	String name = extras.getString(EXTRA_NAME);
	        	realnameText.setText(name);
	        	realnameText.setEnabled(false);
	        	
	        	facebookId  = extras.getString(EXTRA_FACEBOOK_ID);
	        }
	        
	        signUpButton.setOnClickListener(new OnClickListener(){
				public void onClick(View arg0) 
				{						
					
					if (passwordText.length() > 0 && 
						passwordAgainText.length() > 0 &&
						eMailText.length() > 0 &&
						realnameText.length() > 0
						)
					{
						//TODO check email adress is valid
						
						if (passwordText.getText().toString().equals(passwordAgainText.getText().toString())){
						
							if (eMailText.length() >= 5 && passwordText.length() >= 5) {
							
									progressDialog = ProgressDialog.show(Register.this, "", getString(R.string.saving));
								
									Thread registerThread = new Thread(){
										private Handler handler = new Handler();
										
										

										public void run() {
											String password = null;
//											password = AeSimpleMD5.MD5(passwordText.getText().toString());
											password = passwordText.getText().toString();
											
											try {
											
											serverResponse = appService.registerUser(password, 
																			 eMailText.getText().toString(),
																			 realnameText.getText().toString(),
																			 facebookId);
											if (serverResponse == null){
											
												serverResponse = appService.registerUser(password, 
														 eMailText.getText().toString(),
														 realnameText.getText().toString(),
														 facebookId);
												
											}
											
											}catch(Exception e){
												
												e.printStackTrace();
											
											}
											
		
											handler.post(new Runnable(){

												public void run() {
													progressDialog.dismiss();
													System.out
															.println("Server response "  + serverResponse);
													if (serverResponse.equals("1") == true) {
														if (facebookId != null && facebookId.equals("") == false) {
															Toast.makeText(Register.this, getString(R.string.signup_not_required_activation_successfull), Toast.LENGTH_LONG).show();
															SharedPreferences.Editor editor = getSharedPreferences(Configuration.PREFERENCES_NAME,0).edit();
															editor.putString(Configuration.PREFERENCES_USEREMAIL, eMailText.getText().toString());
															editor.putString(Configuration.PREFERENCES_PASSWORD, passwordText.getText().toString());
															editor.putString(Configuration.FB_EMAIL, eMailText.getText().toString());
															editor.putString(Configuration.FB_NAME, realnameText.getText().toString());
															editor.putString(Configuration.FB_ID, facebookId);
															
															editor.commit();
															finish();
														}
														else {
															showDialog(SIGN_UP_SUCCESSFULL);														
														}
													}
													else {
														Register.this.dialogMessage = serverResponse;
														showDialog(CUSTOM_MESSAGE_DIALOG);
													}
												}
	
											});
										}
		
									};
									registerThread.start();
							}
							else{
								showDialog(EMAIL_AND_PASSWORD_LENGTH_SHORT);
							}							
						}
						else {
							showDialog(TYPE_SAME_PASSWORD_IN_PASSWORD_FIELDS);
						}						
					}
					else {
						showDialog(FILL_ALL_FIELDS);
						
					}				
				}       	
	        });
	        
	        cancelButton.setOnClickListener(new OnClickListener(){
				public void onClick(View arg0) 
				{						
					finish();					
				}	        	
	        });
	        
	        
	    }
	
	@Override
	protected void onPrepareDialog(int id, Dialog dialog) {
		
		if (id == CUSTOM_MESSAGE_DIALOG) {
			((AlertDialog)dialog).setMessage(this.dialogMessage);
		}
		
		super.onPrepareDialog(id, dialog);
	}
	
	protected Dialog onCreateDialog(int id) 
	{    	
		  	
		switch (id) 
		{
			case TYPE_SAME_PASSWORD_IN_PASSWORD_FIELDS:			
				return new AlertDialog.Builder(Register.this)       
				.setMessage(R.string.signup_type_same_password_in_password_fields)
				.setPositiveButton(R.string.OK, new DialogInterface.OnClickListener() {
					public void onClick(DialogInterface dialog, int whichButton) {
						/* User clicked OK so do some stuff */
					}
				})        
				.create();			
			case FILL_ALL_FIELDS:				
				return new AlertDialog.Builder(Register.this)       
				.setMessage(R.string.signup_fill_all_fields)
				.setPositiveButton(R.string.OK, new DialogInterface.OnClickListener() {
					public void onClick(DialogInterface dialog, int whichButton) {
						/* User clicked OK so do some stuff */
					}
				})        
				.create();
			case SIGN_UP_SUCCESSFULL:
				return new AlertDialog.Builder(Register.this)       
				.setMessage(R.string.signup_successfull)
				.setPositiveButton(R.string.OK, new DialogInterface.OnClickListener() {
					public void onClick(DialogInterface dialog, int whichButton) {
						finish();
					}
				})        
				.create();	
			case EMAIL_AND_PASSWORD_LENGTH_SHORT:
				return new AlertDialog.Builder(Register.this)       
				.setMessage(R.string.email_and_password_length_short)
				.setPositiveButton(R.string.OK, new DialogInterface.OnClickListener() {
					public void onClick(DialogInterface dialog, int whichButton) {
						/* User clicked OK so do some stuff */
					}
				})        
				.create();
			case CUSTOM_MESSAGE_DIALOG:
    			return new AlertDialog.Builder(Register.this)       
        		.setMessage(this.dialogMessage)
        		.setPositiveButton(R.string.OK, new DialogInterface.OnClickListener() {
        			public void onClick(DialogInterface dialog, int whichButton) {
        				Register.this.finish();
        			}
        		})        
        		.create(); 
			case NOT_CONNECTED_TO_NETWORK:

    			return new AlertDialog.Builder(Register.this)       
        		.setMessage(R.string.not_connected_to_network)
        		.setPositiveButton(R.string.OK, new DialogInterface.OnClickListener() {
        			public void onClick(DialogInterface dialog, int whichButton) {
        				finish();
        			}
        		})        
        		.create(); 
			default:
				return null;
				
		}

	
	}
	
	@Override
	protected void onResume() {
		bindService(new Intent(Register.this, AppService.class), mConnection , Context.BIND_AUTO_CREATE);
		   
		super.onResume();
	}
	
	@Override
	protected void onPause() 
	{
		unbindService(mConnection);
		super.onPause();
	}
	
	

}
