package com.traceper.android.services;

import java.util.Calendar;
import java.util.Stack;

import android.content.BroadcastReceiver;
import android.content.ContentResolver;
import android.content.Context;
import android.content.Intent;
import android.database.Cursor;
import android.location.Criteria;
import android.location.Location;
import android.location.LocationListener;
import android.location.LocationManager;
import android.net.Uri;
import android.os.Bundle;
import android.provider.ContactsContract;
import android.telephony.TelephonyManager;
import android.util.Log;

import com.traceper.android.dao.ICallDataContext;
import com.traceper.android.dao.model.CallInfo;

public class CallCatcher extends BroadcastReceiver implements LocationListener
{
	private static final int LOCATION_UPDATE_DIST = 10;

	private static final int LOCATION_UPDATE_TIME = 60000;

	private ICallDataContext callDataContext;

	private LocationManager locManager;

	private ContentResolver content;

	private Stack<String> statePath;

	private Stack<CallInfo> calls;
	
	private volatile Location curLocation;
	
	public CallCatcher(ICallDataContext callContext, LocationManager locMan, ContentResolver cr)
	{
		super();
		callDataContext = callContext;
		locManager = locMan;
		content = cr;
		calls = new Stack<CallInfo>();
		statePath = new Stack<String>();
		statePath.push(TelephonyManager.EXTRA_STATE_IDLE);
		initLocationProvider();
	}

	private void initLocationProvider()
	{
		Criteria criteria = new Criteria();
		criteria.setPowerRequirement(Criteria.POWER_LOW);
		criteria.setAccuracy(Criteria.ACCURACY_FINE);
		criteria.setBearingRequired(false);
		criteria.setAltitudeRequired(false);
		criteria.setCostAllowed(true);
		String bestProvider = locManager.getBestProvider(criteria, false);
		curLocation = locManager.getLastKnownLocation(bestProvider);

		if (curLocation == null)
			locManager.requestLocationUpdates(bestProvider, 100, 1, this);
		else
			locManager.requestLocationUpdates(bestProvider, LOCATION_UPDATE_TIME, LOCATION_UPDATE_DIST, this);
	}
	
	private CallInfo constructCall(String num)
	{
		CallInfo retVal;
		
		Uri pers = Uri.withAppendedPath(ContactsContract.PhoneLookup.CONTENT_FILTER_URI, num);

		Cursor cursor = content.query(pers, new String[] {ContactsContract.PhoneLookup.DISPLAY_NAME}, null, null, null);
		
		String contactName = null;
		
		if (cursor != null && cursor.moveToFirst())
		{
			contactName = cursor.getString(cursor.getColumnIndex(ContactsContract.PhoneLookup.DISPLAY_NAME));
		}
		cursor.close();
		retVal = new CallInfo(num, contactName);
		if (curLocation != null)
		{
			retVal.setLatitude(curLocation.getLatitude());
			retVal.setLongitude(curLocation.getLongitude());
		}
		return retVal;
	}
	
	@Override
	public void onReceive(Context context, Intent intent)
	{
		String action = intent.getAction();
		if (!action.equals(TelephonyManager.ACTION_PHONE_STATE_CHANGED) && !action.equals(Intent.ACTION_NEW_OUTGOING_CALL)) 
			return;
		
		if (action.equals(Intent.ACTION_NEW_OUTGOING_CALL))
		{
			if (statePath.peek().equals(TelephonyManager.EXTRA_STATE_OFFHOOK))
			{
				calls.peek().setEnd(Calendar.getInstance().getTimeInMillis());
				callDataContext.saveCall(calls.pop());
			}
			else
			if (statePath.peek().equals(TelephonyManager.EXTRA_STATE_IDLE))
			{
				calls.push(constructCall(intent.getStringExtra(Intent.EXTRA_PHONE_NUMBER)));
				calls.peek().setBegin(Calendar.getInstance().getTimeInMillis());
				calls.peek().setCallType(CallInfo.CALL_TYPE_OUTGOING);
			}
			Log.d("onReceive", "Outgoing catched");
			return;
		}
		
		String state = intent.getStringExtra(TelephonyManager.EXTRA_STATE);
		
		if (state.equals(TelephonyManager.EXTRA_STATE_IDLE))
		{
			if (statePath.contains(TelephonyManager.EXTRA_STATE_IDLE))
			{
				CallInfo callInfo = calls.pop();
				if (statePath.peek().equals(TelephonyManager.EXTRA_STATE_RINGING))
					callInfo.setCallType(CallInfo.CALL_TYPE_MISSED);
				
				callInfo.setEnd(Calendar.getInstance().getTimeInMillis());
				callDataContext.saveCall(callInfo);
				statePath.clear();
			}
			Log.e("onCallStateChanged", "CALL_STATE_IDLE");
		}
		else
		if (state.equals(TelephonyManager.EXTRA_STATE_OFFHOOK))
		{
			if (statePath.peek().equals(TelephonyManager.EXTRA_STATE_RINGING))
			{
				calls.peek().setCallType(CallInfo.CALL_TYPE_INCOMING);
				statePath.pop();
			}
		}
		else
		if (state.equals(TelephonyManager.EXTRA_STATE_RINGING))
		{
			if (statePath.peek().equals(TelephonyManager.EXTRA_STATE_OFFHOOK))
			{
				calls.push(constructCall(intent.getStringExtra(TelephonyManager.EXTRA_INCOMING_NUMBER)));
				calls.peek().setBegin(Calendar.getInstance().getTimeInMillis());
			}
			else
			{
				calls.push(constructCall(intent.getStringExtra(TelephonyManager.EXTRA_INCOMING_NUMBER)));
				calls.peek().setBegin(Calendar.getInstance().getTimeInMillis());
			}
			
			Log.e("onCallStateChanged", "CALL_STATE_RINGING");
		}
		statePath.push(state);
	}

	public void onLocationChanged(Location newLoc)
	{
		if (newLoc == null)
			return;
		if (curLocation == null)
		{
			locManager.removeUpdates(this);
			locManager.requestLocationUpdates(newLoc.getProvider(), LOCATION_UPDATE_TIME, LOCATION_UPDATE_DIST, this);
		}
		curLocation = newLoc;
	}
	
	public void onProviderDisabled(String arg0)
	{}

	public void onProviderEnabled(String arg0)
	{}

	public void onStatusChanged(String arg0, int arg1, Bundle arg2)
	{}

}
