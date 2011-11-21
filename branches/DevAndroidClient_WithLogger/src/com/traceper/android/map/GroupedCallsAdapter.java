package com.traceper.android.map;

import java.util.List;

import android.content.Context;
import android.text.format.DateFormat;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.ImageView;
import android.widget.TextView;

import com.traceper.R;
import com.traceper.android.dao.model.CallInfo;

public class GroupedCallsAdapter extends ArrayAdapter<CallInfo>
{
	public GroupedCallsAdapter(Context context, int textViewResourceId, List<CallInfo> objects)
	{
		super(context, textViewResourceId, objects);
	}
	
	@Override
	public View getView(int position, View contentView, ViewGroup viewGroup)
	{
		if (contentView == null)
			contentView = View.inflate(getContext(), R.layout.child_row, null);

		((TextView) contentView.findViewById(R.id.ContactOrNumber)).
		setText(getItem(position).getContactOrNumber());
		
		long begin = getItem(position).getBegin();
		((TextView) contentView.findViewById(R.id.CallTime)).setText(DateFormat.format("dd/MM/yyyy hh:mm:ss", begin));
		ImageView typeImg = (ImageView) contentView.findViewById(R.id.CallTypeImg);
		switch (getItem(position).getCallType())
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
			case CallInfo.CALL_TYPE_OUTGOING:
			{
				typeImg.setImageResource(android.R.drawable.sym_call_outgoing);
			}
			break;
		}
		return contentView;
	}
}
