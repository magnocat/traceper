package com.traceper.android.dao;

import com.traceper.android.dao.model.CallInfo;

public interface ICallDataContext
{
	void openContext();
	void closeContext();
	void saveCall(CallInfo call);
}
