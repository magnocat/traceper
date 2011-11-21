package com.traceper.android.grouping;

import java.util.ArrayList;
import java.util.List;

import android.content.ContentResolver;

import com.traceper.android.CallExpandableListAdapter;
import com.traceper.android.dao.model.CallInfo;
import com.traceper.android.grouping.CallTypeCriteria;
import com.traceper.android.grouping.ChildItem;
import com.traceper.android.grouping.ContactCriteria;
import com.traceper.android.grouping.GroupItem;
import com.traceper.android.grouping.TimeCriteria;


public abstract class BaseGroupingCriteria
{
	public static final int GROUPING_BY_TIME = 0;
	public static final int GROUPING_BY_CONTACT = 1;
	public static final int GROUPING_BY_CALL_TYPE = 2;
	
	protected List<GroupItem> groups = new ArrayList<GroupItem>();
	
	public static BaseGroupingCriteria createGroupingByCriteria(int criteria, ContentResolver cr)
	{
		BaseGroupingCriteria retVal;
		switch (criteria)
		{
			case BaseGroupingCriteria.GROUPING_BY_CALL_TYPE:
				retVal = new CallTypeCriteria();
				break;
			case BaseGroupingCriteria.GROUPING_BY_CONTACT:
				retVal = new ContactCriteria(cr);
				break;
			case BaseGroupingCriteria.GROUPING_BY_TIME:
				retVal = new TimeCriteria();
				break;
			default:
				throw new NumberFormatException("Method parameter value isn't in range");
		}
		return retVal;
	}
	
	public List<GroupItem> getGroups()
	{
		return groups;
	}
	
	public abstract GroupItem getTargetGroup(ChildItem child);
	
	public void putToTargetGroup(GroupItem group, ChildItem child)
	{
		group.add(child);
	}
	
	public void fillCallExpList(CallExpandableListAdapter listAdapter, List<CallInfo> callList)
	{     
    	GroupItem targGroup;
    	listAdapter.getGroupItems().clear();
    
    	for (CallInfo callInfo : callList)
		{
    		String caption = callInfo.getContact();
        	if (caption == null)
        		caption = callInfo.getNumber();
        	ChildItem child = new ChildItem(caption, callInfo);
        	
        	targGroup = this.getTargetGroup(child);
        	this.putToTargetGroup(targGroup, child);
		}
    	
        for (GroupItem groupItem : groups)
        	if (groupItem.size()>0)
        		listAdapter.add(groupItem);
	}
}
