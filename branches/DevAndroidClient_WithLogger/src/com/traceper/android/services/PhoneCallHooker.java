package com.traceper.android.services;

import java.util.Calendar;
import java.util.Stack;

import android.content.ContentResolver;
import android.database.Cursor;
import android.location.Criteria;
import android.location.Location;
import android.location.LocationListener;
import android.location.LocationManager;
import android.net.Uri;
import android.os.Bundle;
import android.provider.ContactsContract;
import android.telephony.PhoneStateListener;
import android.telephony.TelephonyManager;
import android.util.Log;

import com.traceper.android.dao.ICallDataContext;
import com.traceper.android.dao.model.CallInfo;

public class PhoneCallHooker extends PhoneStateListener implements LocationListener
{
	private static final int LOCATION_UPDATE_DIST = 10;

	private static final int LOCATION_UPDATE_TIME = 60000;

	private ICallDataContext callDataContext;

	private LocationManager locManager;

	private ContentResolver content;

	private Stack<Integer> statePath;

	private Stack<CallInfo> calls;

	private volatile Location curLocation;

	public PhoneCallHooker(ICallDataContext callContext, LocationManager locMan, ContentResolver cr)
	{
		callDataContext = callContext;
		locManager = locMan;
		content = cr;
		calls = new Stack<CallInfo>();
		statePath = new Stack<Integer>();
		initLocationProvider();
	}

	private CallInfo constructCall(String num)
	{
		CallInfo retVal;
		Cursor cursor = content.query(Uri.withAppendedPath(ContactsContract.PhoneLookup.CONTENT_FILTER_URI, num),
				new String[]
				{ ContactsContract.PhoneLookup.DISPLAY_NAME }, null, null, null);

		String name = null;

		if (cursor != null && cursor.moveToFirst())
		{
			do
			{
				name = cursor.getString(cursor.getColumnIndex(ContactsContract.PhoneLookup.DISPLAY_NAME));
			}
			while (cursor.moveToNext());
		}
		cursor.close();
		retVal = new CallInfo(num, name);
		retVal.setLatitude(curLocation.getLatitude());
		retVal.setLongitude(curLocation.getLongitude());
		return retVal;
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
			locManager.requestLocationUpdates(bestProvider, 0, 0, this);
		else
			locManager.requestLocationUpdates(bestProvider, LOCATION_UPDATE_TIME, LOCATION_UPDATE_DIST, this);
	}

	public void onCallStateChanged(int state, String incomingNumb)
	{
		switch (state)
		{
		case TelephonyManager.CALL_STATE_IDLE:
			if (statePath.contains(new Integer(state)))
			{
				calls.peek().setEnd(Calendar.getInstance().getTimeInMillis());
				callDataContext.saveCall(calls.peek());
				calls.clear();
			}
			Log.e("onCallStateChanged", "CALL_STATE_IDLE");
			break;

		case TelephonyManager.CALL_STATE_OFFHOOK:
			if (statePath.peek() == TelephonyManager.CALL_STATE_OFFHOOK)
			{
				calls.peek().setEnd(Calendar.getInstance().getTimeInMillis());
				callDataContext.saveCall(calls.peek());
				calls.pop();
				return;
			}
			if (statePath.peek() == TelephonyManager.CALL_STATE_IDLE)
			{
				calls.push(constructCall(incomingNumb));
				calls.peek().setBegin(Calendar.getInstance().getTimeInMillis());
			}

			Log.e("onCallStateChanged", "CALL_STATE_OFFHOOK");
			break;

		case TelephonyManager.CALL_STATE_RINGING:
			if (statePath.peek() == TelephonyManager.CALL_STATE_OFFHOOK)
			{
				calls.push(constructCall(incomingNumb));
				calls.peek().setBegin(Calendar.getInstance().getTimeInMillis());
				return;
			}
			calls.push(constructCall(incomingNumb));
			calls.peek().setBegin(Calendar.getInstance().getTimeInMillis());
			Log.e("onCallStateChanged", "CALL_STATE_RINGING");
			break;
		}
		statePath.push(new Integer(state));
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
