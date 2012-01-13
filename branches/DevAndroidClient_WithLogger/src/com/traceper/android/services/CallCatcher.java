package com.traceper.android.services;

import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.Calendar;
import java.util.Date;
import java.util.Stack;

import android.content.BroadcastReceiver;
import android.content.ContentResolver;
import android.content.Context;
import android.content.Intent;
import android.database.Cursor;
import android.location.Location;
import android.net.Uri;
import android.provider.ContactsContract;
import android.telephony.TelephonyManager;
import android.util.Log;

import com.traceper.android.Configuration;
import com.traceper.android.dao.DBCallDataContext;
import com.traceper.android.dao.ICallDataContext;
import com.traceper.android.dao.model.CallInfo;

public class CallCatcher extends BroadcastReceiver
{
	private static final int LOCATION_UPDATE_DIST = 10;

	private static final int LOCATION_UPDATE_TIME = 60000;

	private ICallDataContext callDataContext;

	//	private LocationManager locManager;

	private ContentResolver content;

	private static Stack<String> statePath =  new Stack<String>();

	private static Stack<CallInfo> calls = new Stack<CallInfo>();

	private volatile Location curLocation;

	private static boolean initialized = false;

	public CallCatcher() //ICallDataContext callContext, LocationManager locMan, ContentResolver cr)
	{
		super();
		//		callDataContext = callContext;
		//		locManager = locMan;
		//		content = cr;
		//		calls = new Stack<CallInfo>();
		//		statePath = new Stack<String>();
		//		statePath.push(TelephonyManager.EXTRA_STATE_IDLE);
		//		initLocationProvider();
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
		curLocation = AppService.getLastLocation();
		if (curLocation != null)
		{
			retVal.setLatitude(curLocation.getLatitude());
			retVal.setLongitude(curLocation.getLongitude());
		}
		return retVal;
	}

	public void sendCallLog(Context context){
		Intent i = new Intent();
		i.setClass(context, AppService.class);
		i.setAction(AppService.SEND_CALL_LOG);
		context.startService(i);

	}

	@Override
	public void onReceive(Context context, Intent intent)
	{
		if (initialized == false) {
			initialized = true;
			statePath.push(TelephonyManager.EXTRA_STATE_IDLE);
		}
		callDataContext = new DBCallDataContext(context);
		callDataContext.openContext();
		//	locManager = (LocationManager) context.getSystemService(Context.LOCATION_SERVICE);
		content = context.getContentResolver();

		String action = intent.getAction();
		if (!action.equals(TelephonyManager.ACTION_PHONE_STATE_CHANGED) && !action.equals(Intent.ACTION_NEW_OUTGOING_CALL)) 
			return;

		if (action.equals(Intent.ACTION_NEW_OUTGOING_CALL))
		{
			if (statePath.peek().equals(TelephonyManager.EXTRA_STATE_OFFHOOK))
			{
				calls.peek().setEnd(Calendar.getInstance().getTimeInMillis());
				CallInfo callInfo = calls.pop();
				if (this.isLoggerTime()) {
					callDataContext.saveCall(callInfo);
					sendCallLog(context);
				}

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
				
				if (this.isLoggerTime()) {
					callDataContext.saveCall(callInfo);
					sendCallLog(context);
				}
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
		callDataContext.closeContext();
	}


	private boolean isLoggerTime(){
		int hour = Calendar.getInstance().get(Calendar.HOUR_OF_DAY);
		int minute = Calendar.getInstance().get(Calendar.MINUTE);

		if (hour > AppService.getStartLogHour()) {
			if (hour < AppService.getEndLogHour()) {
				return true;					
			}
			else if (hour == AppService.getEndLogHour() && minute <= AppService.getEndLogMinute()) {
				return true;
			}
		}
		else if (hour == AppService.getStartLogHour() && minute >= AppService.getStartLogMinute()) {
				if (hour < AppService.getEndLogHour()) {
					return true;					
				}
				else if (hour == AppService.getEndLogHour() && minute <= AppService.getEndLogMinute()) {
					return true;
				}
		}

		return false;
	}




	




}
