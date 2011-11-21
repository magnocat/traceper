package com.traceper.android.grouping;

import java.util.ArrayList;
import java.util.List;


public class GroupItem extends Item
{
	private List<ChildItem> childItems;

	public GroupItem(String text)
	{
		super();
		this.setCaption(text);
		childItems = new ArrayList<ChildItem>();
	}
	
	public ChildItem get(int i)
	{
		return childItems.get(i);
	}
	
	public List<ChildItem> get()
	{
		return childItems;
	}
	
	public void add(ChildItem item)
	{
		if (!childItems.contains(item))
		{
			childItems.add(0, item);
		}
	}
	
	public int size()
	{
		return childItems.size();
	}
	
	public void clear()
	{
		childItems.clear();
	}
}
