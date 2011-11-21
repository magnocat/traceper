package com.traceper.android;

import android.app.AlertDialog;
import android.content.DialogInterface;
import android.content.DialogInterface.OnClickListener;
import android.os.Bundle;
import android.preference.Preference;
import android.preference.PreferenceActivity;
import android.preference.Preference.OnPreferenceClickListener;
import android.widget.Toast;

import com.traceper.R;
import com.traceper.android.dao.CallLoggContentProvider;
import com.traceper.android.dao.model.GlobalCallHolder;
import com.traceper.android.grouping.BaseGroupingCriteria;


public class CallLoggerPreferencesActivity extends PreferenceActivity 
{
	public static final String clearBeforeId = "clearBeforeId";
	public static final String defaultEMail = "defaultEMail";
	public static final String defaultLocProvider = "defaultLocProvider";
	public static final String defaultGrouping = "defaultGrouping";
	public static final int defaultGroupingVal = BaseGroupingCriteria.GROUPING_BY_TIME;
	
    @Override
    protected void onCreate(Bundle savedInstanceState) {
    	
        super.onCreate(savedInstanceState);
        addPreferencesFromResource(R.xml.preference);
        findPreference("btn_clean_calls").setOnPreferenceClickListener(new OnPreferenceClickListener()
		{
			public boolean onPreferenceClick(Preference pref)
			{
				AlertDialog.Builder builder = new AlertDialog.Builder(CallLoggerPreferencesActivity.this);
				
				builder.setMessage("WARNING!\nAre you sure want clear all calls?")
				       .setCancelable(false)
				       .setPositiveButton("Yes", new OnClickListener() {
				           public void onClick(DialogInterface dialog, int id) {
				        	   getContentResolver().delete(CallLoggContentProvider.CLEAR_CALLS_URI, null, null);
				        	   GlobalCallHolder.getEntireCallList().clear();
								Toast.makeText(getBaseContext(), "Calls list cleared", Toast.LENGTH_LONG).show();
				           }
				       })
				       .setNegativeButton("No", new OnClickListener() {
						public void onClick(DialogInterface dlg, int id)
						{
							dlg.dismiss();
							
						}
				       });
				AlertDialog alert = builder.create();
				alert.show();
				return true;
			}
		});
    }
}

