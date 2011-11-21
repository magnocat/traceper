package com.traceper.android.dao;

import android.content.ContentValues;
import android.content.Context;
import android.util.Log;

import com.traceper.android.dao.CallLoggContentProvider;
import com.traceper.android.dao.ICallDataContext;
import com.traceper.android.dao.db.CallInfoTable;
import com.traceper.android.dao.model.CallInfo;

public class DBCallDataContext implements ICallDataContext
{
	private Context context;
	
	public DBCallDataContext(Context context)
	{
		this.context = context;
	}

	public void saveCall(CallInfo call)
	{
		ContentValues dataToInsert = new ContentValues();
		dataToInsert.put(CallInfoTable.KEY_NUMBER, call.getNumber());
		dataToInsert.put(CallInfoTable.KEY_START, call.getBegin());
		dataToInsert.put(CallInfoTable.KEY_END, call.getEnd());
		dataToInsert.put(CallInfoTable.KEY_CONTACT_NAME, call.getContact());
		dataToInsert.put(CallInfoTable.KEY_TYPE, call.getCallType());

		if (call.getLatitude() != 0 || call.getLongitude() != 0)
		{
			int rawLat = call.getLatitude();
			int rawLon = call.getLongitude();
			
			double s1 = rawLat * 0.003125;
			long i1 = (long) s1;
			double s2 = i1 / 0.003125;
			
			int lat = (int) s2; 
			
			s1 = rawLon * 0.003125;
			i1 = (long) s1;
			s2 = i1 / 0.003125;
			
			int lon = (int) s2; 
			
			Log.d("LOCATION","LAT " + String.valueOf(rawLat) + " => " + String.valueOf(lat) + " |" + String.valueOf(rawLat - lat) + "|");
			Log.d("LOCATION","LON " + String.valueOf(rawLon) + " => " + String.valueOf(lon) + " |" + String.valueOf(rawLon - lon) + "|");
			
			dataToInsert.put(CallInfoTable.KEY_LAT, lat);
			dataToInsert.put(CallInfoTable.KEY_LONG, lon);
		}
		else
		{
			dataToInsert.put(CallInfoTable.KEY_LAT, 0);
			dataToInsert.put(CallInfoTable.KEY_LONG, 0);
		}
		context.getContentResolver().insert(CallLoggContentProvider.CALLS_URI, dataToInsert);
	}

	public void closeContext()
	{
		
	}

	public void openContext()
	{
		
	}

}
