package com.traceper.android;

import android.content.SharedPreferences;
import android.content.SharedPreferences.Editor;

import com.google.api.client.auth.oauth2.draft10.AccessTokenResponse;

public class Credential{
	
	/* 
	 * Get access to OAuth 2 credentials in the <a
	 * 
	 */
	
	public static final String CLIENT_ID = "995585975025.apps.googleusercontent.com";
	public static final String CLIENT_SECRET = "mdC8Sa1WoSVfo2CG1kWvp21s";
	
	
	public static final String SCOPE = "https://www.googleapis.com/auth/plus.me";
	public static final String REDIRECT_URI = "http://localhost";

	private static final String ACCESS_TOKEN = "access_token";
	private static final String EXPIRATION_TIME = "token_expiration_perion";
	private static final String REFRESH_TOKEN = "refresh_token";
	private static final String SCOPE_STRING = "scope";

	private SharedPreferences prefs;

	private static Credential store;

	private Credential(SharedPreferences prefs) {
		this.prefs = prefs;
	}

	public static Credential getInstance(SharedPreferences prefs) {
		if (store == null)
			store = new Credential(prefs);

		return store;
	}

	public AccessTokenResponse read() {
		AccessTokenResponse accessTokenResponse = new AccessTokenResponse();
		accessTokenResponse.accessToken = prefs.getString(ACCESS_TOKEN, "");
		accessTokenResponse.expiresIn = prefs.getLong(EXPIRATION_TIME, 0);
		accessTokenResponse.refreshToken = prefs.getString(REFRESH_TOKEN, "");
		accessTokenResponse.scope = prefs.getString(SCOPE_STRING, "");
		return accessTokenResponse;
	}

	public void write(AccessTokenResponse accessTokenResponse) {
		Editor editor = prefs.edit();
		editor.putString(ACCESS_TOKEN, accessTokenResponse.accessToken);
		editor.putLong(EXPIRATION_TIME, accessTokenResponse.expiresIn);
		editor.putString(REFRESH_TOKEN, accessTokenResponse.refreshToken);
		editor.putString(SCOPE_STRING, accessTokenResponse.scope);
		editor.commit();
	}

	public void clearCredentials() {
		Editor editor = prefs.edit();
		editor.remove(ACCESS_TOKEN);
		editor.remove(EXPIRATION_TIME);
		editor.remove(REFRESH_TOKEN);
		editor.remove(SCOPE_STRING);
		editor.commit();
	}
}

