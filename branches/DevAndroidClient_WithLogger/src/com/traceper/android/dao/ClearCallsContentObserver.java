package com.traceper.android.dao;

import android.database.ContentObserver;
import android.os.Handler;

public class ClearCallsContentObserver extends ContentObserver
{

	public static final int CALL_LOG_DB_CHANGED = 0;

	private Handler handler;

	public ClearCallsContentObserver(Handler handler)
	{
		super(handler);
		this.handler = handler;
	}

	public void onChange(boolean selfChange)
	{
		super.onChange(selfChange);
		this.handler.sendEmptyMessage(CALL_LOG_DB_CHANGED);

	}

}
