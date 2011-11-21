package com.traceper.android.services;

import android.app.Service;
import android.content.BroadcastReceiver;
import android.content.Intent;
import android.content.IntentFilter;
import android.location.LocationManager;
import android.os.IBinder;
import android.telephony.TelephonyManager;

import com.traceper.android.dao.DBCallDataContext;
import com.traceper.android.dao.ICallDataContext;
import com.traceper.android.services.CallCatcher;

public class CallLoggerService extends Service
{
	private LocationManager locMan;
	private ICallDataContext dbContext;
	private BroadcastReceiver callCatcher;
	
	@Override
	public void onCreate()
	{ 
		locMan = (LocationManager) getSystemService(LOCATION_SERVICE);
		dbContext = new DBCallDataContext(getApplicationContext());
		dbContext.openContext();
		callCatcher = new CallCatcher(dbContext, locMan, getContentResolver());
		
		IntentFilter filter = new IntentFilter();
		filter.addAction(TelephonyManager.ACTION_PHONE_STATE_CHANGED);
		filter.addAction(android.content.Intent.ACTION_NEW_OUTGOING_CALL);
		filter.addAction(TelephonyManager.EXTRA_INCOMING_NUMBER);
		
		registerReceiver(callCatcher, filter);
	}

	@Override
	public void onDestroy()
	{
		unregisterReceiver(callCatcher);
		dbContext.closeContext();
	}
	
	@Override
	public IBinder onBind(Intent arg0)
	{
		return null;
	}
}

