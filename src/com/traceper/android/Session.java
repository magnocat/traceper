
package com.traceper.android;
import android.content.Context;
import android.content.SharedPreferences;
import android.content.SharedPreferences.Editor;

import com.facebook.android.Facebook;

public class Session {

    private static final String TOKEN = "access_token";
    private static final String EXPIRES = "expires_in";
    private static final String KEY = "facebook-session";
    private static final String UID = "uid";
    private static final String NAME = "name";
    private static final String APP_ID = "app_id";
    private static final String EMAIL = "email";
    private static final String PASS = "password";

    private static Session singleton;
    private static Facebook fbLoggingIn;

    // The Facebook object
    private Facebook fb;

    // The user id of the logged in user
    private String uid;

    // The user name of the logged in user
    private String name;

    // The user email of the logged in user
    private String email;
    
    // The user pass of the logged in user
    private String pass;
    /**
     * Constructor
     * 
     * @param fb
     * @param uid
     * @param name
     */
    public Session(Facebook fb, String uid, String name ,String email, String Pass) {
        this.fb = fb;
        this.uid = uid;
        this.name = name;
        this.email = email;
        this.pass = Pass;
    }

    /**
     * Returns the Facebook object
     */
    public Facebook getFb() {
        return fb;
    }
    /**
     * Returns the session user's id
     */
    public String getPass() {
        return pass;
    }
    /**
     * Returns the session user's id
     */
    public String getUid() {
        return uid;
    }

    /**
     * Returns the session user's name 
     */
    public String getName() {
        return name;
    }
    /**
     * Returns the session user's mail
     */
    public String getEmail() {
        return email;
    }
    /**
     * Stores the session data on disk.
     */
    public boolean save(Context context) {

        Editor editor =
        context.getSharedPreferences(KEY, Context.MODE_PRIVATE).edit();
        editor.putString(TOKEN, fb.getAccessToken());
        editor.putLong(EXPIRES, fb.getAccessExpires());
        editor.putString(UID, uid);
        editor.putString(NAME, name);
        editor.putString(EMAIL, email);
        editor.putString(APP_ID, fb.getAppId());
        if (editor.commit()) {
            singleton = this;
            return true;
        }
        return false;
    }

    /**
     * Loads the session data from disk.
     */
    public static Session restore(Context context) {
        if (singleton != null) {
            if (singleton.getFb().isSessionValid()) {
                return singleton;
            } else {
                return null;
            }
        }

        SharedPreferences prefs =
            context.getSharedPreferences(KEY, Context.MODE_PRIVATE);
        
        String appId = prefs.getString(APP_ID, null);
        
        if (appId == null) {
        	return null;
        }
        
        Facebook fb = new Facebook(appId);
        fb.setAccessToken(prefs.getString(TOKEN, null));
        fb.setAccessExpires(prefs.getLong(EXPIRES, 0));
        String uid = prefs.getString(UID, null);
        String name = prefs.getString(NAME, null);
        String email = prefs.getString(EMAIL, null);
        String pass = prefs.getString(PASS, null);
       
        if (!fb.isSessionValid() || uid == null || name == null ) {
            return null;
        }

        Session session = new Session(fb, uid, name , email, pass);
        singleton = session;
        return session;
    }

    /**
     * Clears the saved session data.
     */
    public static void clearSavedSession(Context context) {
        Editor editor = 
            context.getSharedPreferences(KEY, Context.MODE_PRIVATE).edit();
        editor.clear();
        editor.commit();
        singleton = null;
    }

    /**
     * Facebook object while it's waiting for an auth callback.
     */
    public static void waitForAuthCallback(Facebook fb) {
        fbLoggingIn = fb;
    }

    /**
     * Returns a Facebook object that's been waiting for an auth callback.
     */
    public static Facebook wakeupForAuthCallback() {
        Facebook fb = fbLoggingIn;
        fbLoggingIn = null;
        return fb;
    }

}
