package com.traceper.android.dao.model;

import java.util.ArrayList;
import java.util.Collection;
import java.util.HashMap;
import java.util.Iterator;
import java.util.List;
import java.util.ListIterator;
import java.util.Map;

import android.content.ContentResolver;
import android.database.Cursor;

import com.traceper.android.dao.CallLoggContentProvider;
import com.traceper.android.dao.db.CallInfoTable;
import com.traceper.android.dao.model.CallInfo;

import com.traceper.android.grouping.ChildItem;
import com.traceper.android.utils.CursorUtils;
import com.traceper.android.utils.GeoUtils;

public class GlobalCallHolder implements List<CallInfo>
{

	private static List<CallInfo> storage = null;
	private static ContentResolver contentResolver = null;
	
	
	public static List<CallInfo> getEntireCallList()
	{
		if (storage == null)
		{
			if (contentResolver == null) 
			{
				throw new NullPointerException("Content resolver is not been initialized!");
			}
			storage = CursorUtils.getAllCalls(contentResolver.query(CallLoggContentProvider.CALLS_URI, null, null, null,
					CallInfoTable.KEY_START + " DESC"));
		}
		return storage;
	}
	
	public static List<CallInfo> getEntireCallList(ContentResolver cr)
	{
		if (storage == null)
		{
			if (contentResolver == null) 
			{
				contentResolver = cr;
			}
			storage  = CursorUtils.getAllCalls(contentResolver.query(CallLoggContentProvider.CALLS_URI, null, null, null,
					CallInfoTable.KEY_START + " DESC")) ;
		}
		return storage;
	}
	
	public static ChildItem loadDBScopeIdentity()
	{
		if (contentResolver == null) 
		{
			throw new NullPointerException("Content resolver is not been initialized!");
		}
		Cursor c = contentResolver.query(CallLoggContentProvider.LAST_ADDED_CALL_URI, 
				null, null, null, null);
		List<CallInfo> newCall = CursorUtils.getLimitCalls(c, 1);
		c.close();

		CallInfo callInfo = newCall.get(0);
		GlobalCallHolder.getEntireCallList().add(0, callInfo);
		
		String caption = callInfo.getContactOrNumber();

		return new ChildItem(caption, callInfo);
	}
	
	public static Collection<List<CallInfo>> getCallsGroupedByLocation()
	{
		Map<Integer, List<CallInfo>> table = new HashMap<Integer, List<CallInfo>>();
		List<CallInfo> group;
		for (CallInfo callInfo : getEntireCallList())
		{
			int key = GeoUtils.getHashFromPoint(callInfo.getLatitude(), callInfo.getLongitude());
			group = table.get(key);
			if (group == null)
			{
				group = new ArrayList<CallInfo>();
			}
			group.add(callInfo);
			table.put(key, group);
		}
		return table.values();
	}
	
	public boolean add(CallInfo call)
	{
		storage.add(call);
		return false;
	}

	public void add(int index, CallInfo call)
	{
		storage.add(index, call);
	}

	public boolean addAll(Collection<? extends CallInfo> callsCollection)
	{
		return storage.addAll(callsCollection);
	}

	public boolean addAll(int index, Collection<? extends CallInfo> callsCollection)
	{
		return storage.addAll(index, callsCollection);
	}

	public void clear()
	{
		storage.clear();
	}

	public boolean contains(Object obj)
	{
		return storage.contains(obj);
	}

	public boolean containsAll(Collection<?> callsCollection)
	{
		return storage.containsAll(callsCollection);
	}

	public CallInfo get(int index)
	{
		return storage.get(index);
	}

	public int indexOf(Object obj)
	{
		return storage.indexOf(obj);
	}

	public boolean isEmpty()
	{
		return storage.isEmpty();
	}

	public Iterator<CallInfo> iterator()
	{
		return storage.iterator();
	}

	public int lastIndexOf(Object obj)
	{
		return storage.lastIndexOf(obj);
	}

	public ListIterator<CallInfo> listIterator()
	{
		return storage.listIterator();
	}

	public ListIterator<CallInfo> listIterator(int index)
	{
		return storage.listIterator(index);
	}

	public CallInfo remove(int index)
	{
		return storage.remove(index);
	}

	public boolean remove(Object obj)
	{
		return storage.remove(obj);
	}

	public boolean removeAll(Collection<?> callsCollection)
	{
		return storage.remove(callsCollection);
	}

	public boolean retainAll(Collection<?> callsCollection)
	{
		return storage.retainAll(callsCollection);
	}

	public CallInfo set(int index, CallInfo call)
	{
		return storage.set(index, call);
	}

	public int size()
	{
		return storage.size();
	}

	public List<CallInfo> subList(int first, int last)
	{
		return storage.subList(first, last);
	}

	public Object[] toArray()
	{
		return storage.toArray();
	}

	public <T> T[] toArray(T[] arr)
	{
		return storage.toArray(arr);
	}

}
