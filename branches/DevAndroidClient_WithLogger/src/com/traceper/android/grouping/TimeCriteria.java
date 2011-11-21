package com.traceper.android.grouping;

import java.util.Calendar;

import com.traceper.android.grouping.BaseGroupingCriteria;
import com.traceper.android.grouping.ChildItem;
import com.traceper.android.grouping.GroupItem;

public final class TimeCriteria extends BaseGroupingCriteria
{
	private static final int GROUPING_LONG_AGO = 4;

	private static final int GROUPING_THIS_MONTH = 3;

	private static final int GROUPING_THIS_WEEK = 2;

	private static final int GROUPING_YESTERDAY = 1;

	private static final int GROUPING_TODAY = 0;

	private final String[] groupCategories = 
	{
		"Today", "Yesterday", "This week", "This month", "Long ago"    
	};

	public TimeCriteria()
	{
		super();
		for (int i = 0; i < groupCategories.length; i++)
		{
			groups.add(new GroupItem(groupCategories[i]));
		}
	}
	
	public GroupItem getTargetGroup(ChildItem child)
	{
		Calendar measure = Calendar.getInstance();
        int year = measure.get(Calendar.YEAR);
        int month = measure.get(Calendar.MONTH);
        int day = measure.get(Calendar.DAY_OF_MONTH);
        
        measure = Calendar.getInstance();
    	measure.setTimeInMillis(child.getCall().getBegin());
    	
    	int callYear = measure.get(Calendar.YEAR);
    	int callMonth = measure.get(Calendar.MONTH);
    	int callDay = measure.get(Calendar.DAY_OF_MONTH);
    	int mondayDay = measure.get(Calendar.DAY_OF_MONTH);
    	
    	if (year > callYear || month > callMonth)
    		return groups.get(GROUPING_LONG_AGO);
    	else
    	if ((month == callMonth) && (day - mondayDay > 7))
    		return groups.get(GROUPING_THIS_MONTH);
    	else
    	if (callDay == (day-1))
    		return groups.get(GROUPING_YESTERDAY);
    	else
    	if (callDay == day)
    		return groups.get(GROUPING_TODAY);
    	else
    		return groups.get(GROUPING_THIS_WEEK);
	}
}