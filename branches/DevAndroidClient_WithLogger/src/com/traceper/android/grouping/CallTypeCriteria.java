package com.traceper.android.grouping;

import java.security.InvalidParameterException;

import com.traceper.android.dao.model.CallInfo;
import com.traceper.android.grouping.BaseGroupingCriteria;
import com.traceper.android.grouping.ChildItem;
import com.traceper.android.grouping.GroupItem;

public final class CallTypeCriteria extends BaseGroupingCriteria
{
	private static final int GROUPING_INCOMING = 0;

	private static final int GROUPING_OUTGOING = 1;

	private static final int GROUPING_MISSED = 2;

	private final String[] groupCategories = 
	{
		"Incoming", "Outgoing", "Missed"    
	};

	public CallTypeCriteria()
	{
		super();
		for (int i = 0; i < groupCategories.length; i++)
		{
			groups.add(new GroupItem(groupCategories[i]));
		}
	}
	
	public GroupItem getTargetGroup(ChildItem child)
	{
		int index;
		
		switch (child.getCall().getCallType())
		{
			case CallInfo.CALL_TYPE_INCOMING:
					index = GROUPING_INCOMING;
				break;
			case CallInfo.CALL_TYPE_OUTGOING:
					index = GROUPING_OUTGOING;
				break;
			case CallInfo.CALL_TYPE_MISSED:
					index = GROUPING_MISSED;
				break;
			default:
				throw new InvalidParameterException("Target group detected incorrect call type");
		}
		return groups.get(index);
	}
}