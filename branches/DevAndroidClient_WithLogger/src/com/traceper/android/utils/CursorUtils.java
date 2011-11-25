package com.traceper.android.utils;

import java.util.List;
import java.util.Stack;

import android.database.Cursor;
import android.text.format.DateFormat;

import com.traceper.android.dao.db.CallInfoTable;
import com.traceper.android.dao.model.CallInfo;

public final class CursorUtils
{
	public static List<CallInfo> getLimitCalls(Cursor c, long limit)
	{
    	List<CallInfo> retVal = new Stack<CallInfo>();
    	long index = 0;
    	
    	if(c == null){
    		return retVal;
    	}
    	if (c.moveToFirst())
	    	do
			{
	    		String action = null;
	    		String callNumber;
	    		String callName;
	    		int callType;
	    		long callBegin;
	    		long callEnd;
	    		int callLat;
	    		int callLon;
	    		
	    		if (limit<0?false:index==limit)
	    		{
	    			return retVal;
	    		}
	    		index++;
	    		
	    		
	    		callNumber = c.getString(c.getColumnIndex(CallInfoTable.KEY_NUMBER));
	    		callName = c.getString(c.getColumnIndex(CallInfoTable.KEY_CONTACT_NAME));
	    		callType = c.getInt(c.getColumnIndex(CallInfoTable.KEY_TYPE));
	    		callBegin = c.getLong(c.getColumnIndex(CallInfoTable.KEY_START));
	    		callEnd = c.getLong(c.getColumnIndex(CallInfoTable.KEY_END));
	    		
	    		callLat = c.getInt(c.getColumnIndex(CallInfoTable.KEY_LAT));
	    		callLon = c.getInt(c.getColumnIndex(CallInfoTable.KEY_LONG));
	    		
	    		
	    		CallInfo ci = new CallInfo(callNumber, callName);
	    		ci.setLatitude(callLat);
	    		ci.setLongitude(callLon);
	    		ci.setCallType(callType);
	    		ci.setBegin(callBegin);
	    		ci.setEnd(callEnd);
	    		retVal.add(0, ci);
			}while (c.moveToNext());
    	c.close();
    	return retVal;
	}
    
    public static List<CallInfo> getAllCalls(Cursor c)
	{
    	return getLimitCalls(c, -1);
	}
    
    public static final int SHARE_AS_PLAIN_TEXT = 0;
    public static final int SHARE_AS_TEXT_HTML = 1;
    
    public static String getCallsForSharing(Cursor c, int msgFormat)
    {
    	String retVal = "", retValFotter="";
    	final String format;
    	
    	switch (msgFormat)
		{
			case SHARE_AS_TEXT_HTML:
				retVal += "<html><head>" +
				"<meta http-equiv=\"content-type\" content=\"text/html;charset=ISO-8859-1\"> " +
				"<meta content=\"width = device-width\" name=\"viewport\"></head>" +
				"<body><table style='width:100%'><tbody>" +
				"<tr>" +
					"<td>Number:</td>" +
					"<td>Contact:</td>" +
					"<td>Begin:</td>" +
					"<td>End:</td>" +
					"<td>Type:</td>" +
					"<td>Location:</td>" +
				"</tr>";
				format = "<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>";
				retValFotter = "</tbody></table></html>";
				break;
	
			default:
				format = "Number:%s " + "Contact:%s " + "Begin:%s " + "End:%s " + "Type:%s " + "Location:%s\n\n";
				break;
		}
		
		String number, contact, callType = "", begin, end, position = "Unknown";
		double lat, lon;

    	if(c == null){
    		return retVal;
    	}
    	
    	
		while (c.moveToNext())
		{	
    		number = c.getString(c.getColumnIndex(CallInfoTable.KEY_NUMBER));
    		contact = c.getString(c.getColumnIndex(CallInfoTable.KEY_CONTACT_NAME));
    		contact = contact == null ? "Unknown" : contact;
    		
    		switch (c.getInt(c.getColumnIndex(CallInfoTable.KEY_TYPE)))
			{
				case CallInfo.CALL_TYPE_INCOMING:
					callType = "INCOMING";
					break;
				case CallInfo.CALL_TYPE_OUTGOING:
					callType = "OUTGOING";
					break;
				case CallInfo.CALL_TYPE_MISSED:
					callType = "MISSED";
					break;
			}
			begin = DateFormat.format("dd/MM/yyyy hh:mm:ss", c.getLong(c.getColumnIndex(CallInfoTable.KEY_START)))
					.toString();
			end = DateFormat.format("dd/MM/yyyy hh:mm:ss", c.getLong(c.getColumnIndex(CallInfoTable.KEY_END)))
					.toString();

    		lat = c.getDouble(c.getColumnIndex(CallInfoTable.KEY_LAT));
    		lon = c.getDouble(c.getColumnIndex(CallInfoTable.KEY_LONG));
    		if (lat != 0.0 || lon != 0.0)
				position = "(" + String.valueOf(lat) + ", " + String.valueOf(lon) + ")";

			retVal += String.format(format, number, contact, begin, end, callType, position);
		};
    	c.close();
    	
    	retVal += retValFotter;
    	
		return retVal;
    }
}
