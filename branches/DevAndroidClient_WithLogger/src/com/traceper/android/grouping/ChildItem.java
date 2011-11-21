package com.traceper.android.grouping;

import com.traceper.android.dao.model.CallInfo;
import com.traceper.android.grouping.Item;

public class ChildItem extends Item
{
	private CallInfo call; 
	
	public ChildItem(String text, CallInfo data)
	{
		call = data;
		this.setCaption(text);
	}
	
	public CallInfo getCall()
	{
		return call;
	}
}