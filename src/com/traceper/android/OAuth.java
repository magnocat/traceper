package com.traceper.android;

import java.io.IOException;
import java.net.MalformedURLException;
import java.util.LinkedList;
import java.util.List;

import org.json.JSONException;

import android.accounts.Account;
import android.accounts.AccountManager;
import android.app.Activity;
import android.app.ProgressDialog;
import android.content.ComponentName;
import android.content.Context;
import android.content.Intent;
import android.content.IntentFilter;
import android.content.ServiceConnection;
import android.content.SharedPreferences;
import android.graphics.drawable.Drawable;
import android.os.Bundle;
import android.os.IBinder;
import android.preference.PreferenceManager;
import android.util.Log;
import android.view.View;
import android.webkit.WebView;
import android.webkit.WebViewClient;
import android.widget.Toast;

import com.google.api.client.auth.oauth2.draft10.AccessTokenResponse;
import com.google.api.client.googleapis.auth.oauth2.draft10.GoogleAccessProtectedResource;
import com.google.api.client.googleapis.auth.oauth2.draft10.GoogleAuthorizationRequestUrl;
import com.google.api.client.googleapis.auth.oauth2.draft10.GoogleAccessTokenRequest.GoogleAuthorizationCodeGrant;
import com.google.api.client.http.HttpTransport;
import com.google.api.client.http.javanet.NetHttpTransport;
import com.google.api.client.json.JsonFactory;
import com.google.api.client.json.jackson.JacksonFactory;
import com.google.api.services.plus.Plus;
import com.google.api.services.plus.Plus.Builder;
import com.google.api.services.plus.model.Person;
import com.google.api.services.plus.model.PersonEmails;
import com.traceper.R;
import com.traceper.android.interfaces.IAppService;
import com.traceper.android.services.AppService;

public class OAuth extends Activity {

	private SharedPreferences prefs;
	private Person profile;
	private IAppService appService = null;
	private ProgressDialog progressDialog;
	
	private ServiceConnection mConnection = new ServiceConnection() 
	{
		public void onServiceConnected(ComponentName className, IBinder service) {  
		            
			appService = ((AppService.IMBinder)service).getService();    
			appService.setAuthenticationServerAddress(getSharedPreferences(Configuration.PREFERENCES_NAME, 0).getString(Configuration.PREFERENCES_SERVER_INDEX, Configuration.DEFAULT_SERVER_ADRESS));
           
		
		}
		public void onServiceDisconnected(ComponentName className) {          
			appService = null;
		
		}
	};

	
	@Override
	public void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		this.prefs = PreferenceManager.getDefaultSharedPreferences(this);

		// new OAuthRequestTokenTask(this).execute();
	}

	@Override
	protected void onResume() {		
		super.onResume();

		
		WebView webview = new WebView(this);
		webview.setVisibility(View.VISIBLE);
		webview.getSettings().setJavaScriptEnabled(true);
		setContentView(webview);
		
		String googleAuthorizationRequestUrl = new GoogleAuthorizationRequestUrl(
				Credential.CLIENT_ID,
				Credential.REDIRECT_URI,
				Credential.SCOPE).build();

		webview.setWebViewClient(new WebViewClient() {
 
			@Override
			public void onPageFinished(WebView view, String url) {

				if (url.startsWith(Credential.REDIRECT_URI)) {
					try {
						if (url.indexOf("code=") != -1) {
							// Url is like http://localhost/?code=4/Z5DgC1IxNL-muPsrE2Sjy9zQn2pF
							String code = url.substring(
									Credential.REDIRECT_URI.length() + 7,
									url.length());

							AccessTokenResponse accessTokenResponse = new GoogleAuthorizationCodeGrant(
									new NetHttpTransport(),
									new JacksonFactory(),
									Credential.CLIENT_ID,
									Credential.CLIENT_SECRET,
									code, Credential.REDIRECT_URI)
									.execute();
							
							Credential credentialStore = Credential.getInstance(prefs);
							credentialStore.write(accessTokenResponse);
							view.setVisibility(View.INVISIBLE);
							startActivity(new Intent(
									OAuth.this,
									Login.class));
							retrieveProfile();
							
							finish();
						} else if (url.indexOf("error=") != -1) {
							view.setVisibility(View.INVISIBLE);
							Credential.getInstance(prefs)
									.clearCredentials();
							startActivity(new Intent(
									OAuth.this,
									Login.class));
							finish();
						}

					} catch (IOException e) {
						e.printStackTrace();
					}
				}
			}
		});

		webview.loadUrl(googleAuthorizationRequestUrl);
		bindService(new Intent(OAuth.this, AppService.class), mConnection , Context.BIND_AUTO_CREATE);
		IntentFilter i = new IntentFilter();
		
	//retrieveProfile();
	}


private void retrieveProfile() {
	try {
		JsonFactory jsonFactory = new JacksonFactory();
		HttpTransport transport = new NetHttpTransport();

		Credential credentialStore = Credential
				.getInstance(prefs);

		AccessTokenResponse accessTokenResponse = credentialStore.read();

		GoogleAccessProtectedResource accessProtectedResource = new GoogleAccessProtectedResource(
				accessTokenResponse.accessToken, transport, jsonFactory,
				Credential.CLIENT_ID,
				Credential.CLIENT_SECRET,
				accessTokenResponse.refreshToken);

		Builder b = Plus.builder(transport, jsonFactory)
				.setApplicationName("Simple-Google-Plus/1.0");
		b.setHttpRequestInitializer(accessProtectedResource);
		Plus plus = b.build();
		profile = plus.people().get("me").execute();
		gp_save();
		
	} catch (Exception ex) {
		ex.printStackTrace();
	}
}
private void gp_save(){
	// save google account 
	String name = profile.getDisplayName();
	String uid = profile.getId();
	String gender = profile.getGender();
	String email = "";
	String result = "";
	
	   AccountManager manager = AccountManager.get(this); 
	    Account[] accounts = manager.getAccountsByType("com.google"); 
	    List<String> possibleEmails = new LinkedList<String>();

	    for (Account account : accounts) {
	      possibleEmails.add(account.name);
	    }
	   email = possibleEmails.get(0);
	if (email ==null)
	{
		email = "nomail@gmail.com";
	}
		try{
			result = appService.registerGPUser(uid, email, name);
			
			SharedPreferences.Editor editor = getSharedPreferences(Configuration.PREFERENCES_NAME, 0).edit();
			editor.putBoolean(Configuration.PREFRENCES_REMEMBER_ME_CHECKBOX, true);
        	editor.putString(Configuration.PREFERENCES_USEREMAIL, email);
        	editor.putString(Configuration.PREFERENCES_PASSWORD, uid);
        	editor.commit();	
        	
			
		} catch (Exception e) {
			e.printStackTrace();
		}

}
@Override
protected void onPause() 
{	
	unbindService(mConnection);
	super.onPause();
}


}