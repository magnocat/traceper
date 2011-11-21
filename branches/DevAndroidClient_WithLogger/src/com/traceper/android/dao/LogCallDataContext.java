package com.traceper.android.dao;

import android.text.format.DateFormat;
import android.util.Log;

import com.traceper.android.dao.ICallDataContext;
import com.traceper.android.dao.model.CallInfo;

public class LogCallDataContext implements ICallDataContext
{

	public void saveCall(CallInfo call)
	{
		Log.i("LogCallDataContext", "Hello, World!");
		String callType;
		if (call.getNumber().equals(""))
		{
			callType = "Outcoming call";
		}
		else
		{
			callType = String.format("Incoming call from %s", call.getNumber());
		}
		String beginCall = DateFormat.format("dd/MM/yyyy hh:mm:ss", call.getBegin()).toString();
		String endCall = DateFormat.format("dd/MM/yyyy hh:mm:ss", call.getEnd()).toString();
		Log.i("LogCallDataContext", String.format("%s begin at %s, end %s" , callType, beginCall, endCall));
	}

	public void closeContext()
	{
		// TODO Auto-generated method stub
		
	}

	public void openContext()
	{
		// TODO Auto-generated method stub
		
	}

}

