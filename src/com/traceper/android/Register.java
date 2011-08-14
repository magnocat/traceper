package com.traceper.android;

import java.io.UnsupportedEncodingException;
import java.security.NoSuchAlgorithmException;

import android.app.Activity;
import android.app.AlertDialog;
import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.ComponentName;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.ServiceConnection;
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
import com.traceper.android.tools.AeSimpleMD5;

public class Register extends Activity {
	
	private static final int FILL_ALL_FIELDS = 0;
	protected static final int TYPE_SAME_PASSWORD_IN_PASSWORD_FIELDS = 1;
	private static final int SIGN_UP_FAILED = 2;
	private static final int SIGN_UP_EMAIL_CRASHED = 3;
	private static final int SIGN_UP_SUCCESSFULL = 4;
	protected static final int EMAIL_AND_PASSWORD_LENGTH_SHORT = 5;
	protected static final int SIGN_UP_EMAIL_NOT_VALID = 6;
	private EditText passwordText;
	private EditText eMailText;
	private EditText passwordAgainText;
	private EditText realnameText;
	private IAppService appService;
	private Handler handler = new Handler();
	
	private ServiceConnection mConnection = new ServiceConnection() {
        

		public void onServiceConnected(ComponentName className, IBinder service) {
            // This is called when the connection with the service has been
            // established, giving us the service object we can use to
            // interact with the service.  Because we have bound to a explicit
            // service that we know is running in our own process, we can
            // cast its IBinder to a concrete class and directly access it.
			
            appService = ((AppService.IMBinder)service).getService(); 
            appService.setAuthenticationServerAddress(getSharedPreferences(Configuration.PREFERENCES_NAME, 0).getString(Configuration.PREFERENCES_SERVER_INDEX, Configuration.DEFAULT_SERVER_ADRESS));
            
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
							
									Thread thread = new Thread(){
										int result;
										ProgressDialog progressDialog = ProgressDialog.show(Register.this, getString(R.string.traceper_register), getString(R.string.saving));
										public void run() {
											String password = null;
//											password = AeSimpleMD5.MD5(passwordText.getText().toString());
											password = passwordText.getText().toString();
											
											result = appService.registerUser(password, 
																			 eMailText.getText().toString(),
																			 realnameText.getText().toString());
											progressDialog.dismiss();
		
											handler.post(new Runnable(){
		
												public void run() {
													if (result == IAppService.HTTP_RESPONSE_SUCCESS) {
														showDialog(SIGN_UP_SUCCESSFULL);
													}
													else if (result == IAppService.HTTP_RESPONSE_ERROR_EMAIL_EXISTS){
														showDialog(SIGN_UP_EMAIL_CRASHED);
													}
													else if (result == IAppService.HTTP_RESPONSE_ERROR_EMAIL_NOT_VALID){
														showDialog(SIGN_UP_EMAIL_NOT_VALID);
													}
													else  //if (result.equals(SERVER_RES_SIGN_UP_FAILED)) 
													{
														showDialog(SIGN_UP_FAILED);
													}			
												}
		
											});
										}
		
									};
									thread.start();
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
			case SIGN_UP_FAILED:
				return new AlertDialog.Builder(Register.this)       
				.setMessage(R.string.signup_failed)
				.setPositiveButton(R.string.OK, new DialogInterface.OnClickListener() {
					public void onClick(DialogInterface dialog, int whichButton) {
						/* User clicked OK so do some stuff */
					}
				})        
				.create();
			case SIGN_UP_EMAIL_CRASHED:
				return new AlertDialog.Builder(Register.this)       
				.setMessage(R.string.signup_email_crashed)
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
			case SIGN_UP_EMAIL_NOT_VALID:
				return new AlertDialog.Builder(Register.this)       
				.setMessage(R.string.email_not_valid)
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
