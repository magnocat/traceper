package com.traceper.android.map;

import java.util.Arrays;
import java.util.List;

import com.traceper.android.dao.model.CallInfo;
import com.google.android.maps.GeoPoint;
import com.google.android.maps.OverlayItem;

public class CallOverlayItem extends OverlayItem
{
	private List<CallInfo> calls;
	
	public CallOverlayItem(GeoPoint point, List<CallInfo> callList)
	{
		super(point, "", "");
		calls = callList;
	}
	
	public CallOverlayItem(GeoPoint point, CallInfo call)
	{
		super(point, getTitle(call), "");
		calls = Arrays.asList(call);
	}
	
	public int getCallsCount(){
		return calls.size();
	}
	
	public List<CallInfo> getCalls(){
		return calls;
	}
	
	public CallInfo getCall(){
		return calls.size() == 0 ? null : calls.get(0);
	}
	
	private static String getTitle(CallInfo info){
		return info.getContact() == null ? info.getNumber() : info.getContact();
	}
}
