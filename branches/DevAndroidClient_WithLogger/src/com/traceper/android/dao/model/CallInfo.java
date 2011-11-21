package com.traceper.android.dao.model;

import java.util.HashMap;
import java.util.Map;

import android.content.Intent;
import android.telephony.PhoneNumberUtils;

public class CallInfo
{
	public final static String FIELD_NUMBER = "number";
	public final static String FIELD_CONTACT = "contact";
	public final static String FIELD_CALL_TYPE = "type";
	public final static String FIELD_LOCATION_LAT = "xlocation";
	public final static String FIELD_LOCATION_LON = "ylocation";
	public final static String FIELD_BEGIN = "begin";
	public final static String FIELD_END = "end";
	
	public final static int CALL_TYPE_OUTGOING = 0;
	public final static int CALL_TYPE_INCOMING = 1;
	public final static int CALL_TYPE_MISSED = 2;
	private final static int CALL_TYPE_MAX_PARAM = 2;
	
	private String number;
	private String contact;
	private int microLatitude;
	private int microLongitude;
	private long begin;
	private long end;
	private int callType;
	
	public CallInfo(String callNumber, String contactName)
	{
		contact = contactName;
		number = callNumber;
		microLatitude = 0;
		microLongitude = 0;
		callType = -1;
	}

	public Intent getIntent()
	{
		Intent retVal = new Intent();
		retVal.putExtra(FIELD_NUMBER, number);
		retVal.putExtra(FIELD_CONTACT, contact);
		retVal.putExtra(FIELD_CALL_TYPE, getCallType());
		retVal.putExtra(FIELD_LOCATION_LAT, getLatitude());
		retVal.putExtra(FIELD_LOCATION_LON, getLongitude());
		retVal.putExtra(FIELD_BEGIN, begin);
		retVal.putExtra(FIELD_END, end);
		return retVal;
	}
	
	public Map<String, Object> getMap()
	{
		Map<String, Object> retVal = new HashMap<String, Object>();
		retVal.put(FIELD_NUMBER, number);
		retVal.put(FIELD_CONTACT, contact);
		retVal.put(FIELD_CALL_TYPE, getCallType());
		retVal.put(FIELD_LOCATION_LAT, getLatitude());
		retVal.put(FIELD_LOCATION_LON, getLongitude());
		retVal.put(FIELD_BEGIN, begin);
		retVal.put(FIELD_END, end);
		return retVal;
	}
	
	public String getNumber()
	{
		return number;
	}
	
	public String getContact()
	{
		return contact;
	}

	public String getContactOrNumber()
	{
		return contact == null ? PhoneNumberUtils.formatNumber(number) : contact;
	}
	
	public int getLatitude()
	{
		return microLatitude;
	}

	public void setLatitude(double latitude)
	{
		this.microLatitude = (int) (latitude * 1E6);
	}

	public void setLatitude(int latitude)
	{
		this.microLatitude = latitude;
	}
	
	public int getLongitude()
	{
		return microLongitude;
	}

	public void setLongitude(double longtitude)
	{
		this.microLongitude = (int) (longtitude * 1E6);
	}
	
	public void setLongitude(int longtitude)
	{
		this.microLongitude = longtitude;
	}
	
	public long getBegin()
	{
		return begin;
	}
	
	public void setBegin(long begin)
	{
		this.begin = begin;
	}

	public long getEnd()
	{
		return end;
	}
	
	public void setEnd(long end)
	{
		this.end = end;
	}
	
	public int getCallType()
	{
		if (callType<0) 
			throw new NumberFormatException("Call type is not been setted");
		return callType;
	}

	public void setCallType(int type)
	{
		if (type<0 || type>CALL_TYPE_MAX_PARAM) 
			throw new NumberFormatException("Method parameter value isn't in range");
		this.callType = type;
	}

}

