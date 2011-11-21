package com.traceper.android;

import java.util.ArrayList;
import java.util.List;

import android.content.Context;
import android.text.format.DateFormat;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseExpandableListAdapter;
import android.widget.ImageView;
import android.widget.TextView;

import com.traceper.R;
import com.traceper.android.dao.model.CallInfo;
import com.traceper.android.grouping.ChildItem;
import com.traceper.android.grouping.GroupItem;



public class CallExpandableListAdapter extends BaseExpandableListAdapter
{
	private List<GroupItem> items = new ArrayList<GroupItem>();

	private Context context;
	
	public CallExpandableListAdapter(Context context)
	{
		this.context = context;
	}
	
	public void clear()
	{
		for (GroupItem item : items)
		{
			item.clear();
		}
		items.clear();
	}
	
	public boolean isEmpty()
	{
		return items.size()>0 ? false : true; 
	}
	
	public void add(GroupItem item)
	{
		int index = items.indexOf(item);
		if (index<0)
			items.add(item);
		else
			for (ChildItem child : item.get())
					items.get(index).add(child);
	}
	
	public Object getChild(int groupPosition, int childPosition)
	{
		return items.get(groupPosition).get(childPosition);
	}

	public long getChildId(int groupPosition, int childPosition)
	{
		return childPosition;
	}

	public int getChildrenCount(int groupPosition)
	{
		return items.get(groupPosition).size();
	}

	public Object getGroup(int groupPosition)
	{
		return items.get(groupPosition);
	}

	public List<GroupItem> getGroupItems()
	{
		return items;
	}
	
	public int getGroupCount()
	{
		return items.size();
	}

	public long getGroupId(int groupPosition)
	{
		return groupPosition;
	}

	public View getGroupView(int groupPosition, boolean isExpanded, View contentView, ViewGroup parent)
	{
		if (contentView == null)
			contentView = View.inflate(context, R.layout.group_row, null);
		((TextView) contentView.findViewById(R.id.period)).setText(items.get(groupPosition).getCaption());

		return contentView;
	}

	public View getChildView(int groupPosition, int childPosition, boolean isLastChild, View contentView,
			ViewGroup parent)
	{
		if (contentView == null)
			contentView = View.inflate(context, R.layout.child_row, null);

		((TextView) contentView.findViewById(R.id.ContactOrNumber)).
		setText(items.get(groupPosition).get(childPosition).getCall().getContactOrNumber());

		long begin = items.get(groupPosition).get(childPosition).getCall().getBegin();
		((TextView) contentView.findViewById(R.id.CallTime)).setText(DateFormat.format("dd/MM/yyyy hh:mm:ss", begin));

		int type = items.get(groupPosition).get(childPosition).getCall().getCallType();
		ImageView typeImg = (ImageView) contentView.findViewById(R.id.CallTypeImg);
		switch (type)
		{
			case CallInfo.CALL_TYPE_INCOMING:
			{
				typeImg.setImageResource(android.R.drawable.sym_call_incoming);
			}
			break;
			case CallInfo.CALL_TYPE_MISSED:
			{
				typeImg.setImageResource(android.R.drawable.sym_call_missed);
			}
			break;
			default:
			{
				typeImg.setImageResource(android.R.drawable.sym_call_outgoing);
			}
			break;
		}
		
		return contentView;
	}

	public boolean hasStableIds()
	{
		return true;
	}

	public void onGroupCollapsed(int groupPosition)
	{}
	
	public void onGroupExpanded(int groupPosition)
	{}

	public boolean isChildSelectable(int arg0, int arg1)
	{
		return true;
	}
}
