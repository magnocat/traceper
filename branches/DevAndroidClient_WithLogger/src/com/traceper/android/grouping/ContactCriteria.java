package com.traceper.android.grouping;

import java.util.ArrayList;

import android.content.ContentResolver;
import android.database.Cursor;

import com.traceper.android.dao.CallLoggContentProvider;
import com.traceper.android.dao.db.CallInfoTable;
import com.traceper.android.grouping.BaseGroupingCriteria;
import com.traceper.android.grouping.ChildItem;
import com.traceper.android.grouping.GroupItem;

public final class ContactCriteria extends BaseGroupingCriteria
{
	private ContentResolver contentResolver;
	private final String GROUPING_UNKNOWN = "Unknown";
	public static final int CALLS_NEED_GROUPING = 5;
	
	public ContactCriteria(ContentResolver cr)
	{
		super();
		contentResolver = cr;
		
		Cursor c = contentResolver.query(CallLoggContentProvider.CONTACT_GROUPING_URI, null, null, null, null);
		
		String groupName;
		
		if (c != null && c.moveToFirst()){
			do
			{
				groupName = c.getString(c.getColumnIndex(CallInfoTable.KEY_CONTACT_NAME));
				if (groupName == null)
				{
					groupName = c.getString(c.getColumnIndex(CallInfoTable.KEY_NUMBER));
				}
				this.groups.add(new GroupItem(groupName));
			}
			while (c.moveToNext());
		}
		c.close();
		
		c = contentResolver.query(CallLoggContentProvider.UNKNOWN_GROUPING_URI, null, null, null, null);
		GroupItem unknownGroup = new GroupItem(GROUPING_UNKNOWN);
		c.close();
		this.groups.add(unknownGroup);
	}
	
	private GroupItem getGroupByName(String name)
	{
		for (GroupItem group : groups)
		{
			if (group.getCaption().equals(name))
				return group;
		}
		return null;
	}
	
	public GroupItem getTargetGroup(ChildItem child)
	{
		GroupItem retGroup = getGroupByName(child.getCaption());
		if (retGroup == null)
		{
			retGroup = getGroupByName(GROUPING_UNKNOWN); 
			ArrayList<ChildItem> eqChilds = new ArrayList<ChildItem>();
			for (ChildItem item : retGroup.get()){
				if (item.getCall().getNumber().equals(child.getCall().getNumber()))
				{
					eqChilds.add(item);
				}
			}
			if (eqChilds.size() >= CALLS_NEED_GROUPING-1)
			{
				if (retGroup.get().removeAll(eqChilds))
				{
					String grName = eqChilds.get(0).getCaption();
					retGroup = new GroupItem(grName);
					for (ChildItem item : eqChilds)
					{
						retGroup.add(item);
					}
				}
			}
		}
		return retGroup;
	}
}