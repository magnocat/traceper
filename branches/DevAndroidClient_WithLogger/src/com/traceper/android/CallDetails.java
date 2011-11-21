package com.traceper.android;

import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;
import android.telephony.PhoneNumberUtils;
import android.text.format.DateFormat;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.Button;
import android.widget.ImageView;
import android.widget.TextView;

import com.traceper.android.CallOnMapActivity;
import com.traceper.R;
import com.traceper.android.dao.model.CallInfo;

public class CallDetails extends Activity
{
	public static final String EXTRA_COORD_LAT = "lat";
	public static final String EXTRA_COORD_LON = "lon";
	
	private int lat;
	private int lon;
	
	public void onCreate(Bundle savedInstanceState)
	{
		super.onCreate(savedInstanceState);
		setContentView(R.layout.call_info);
		
		ImageView typeImg = (ImageView)findViewById(R.id.typeImg);
		TextView typeTxt = (TextView)findViewById(R.id.typeTxt);
		
		int type = getIntent().getExtras().getInt(CallInfo.FIELD_CALL_TYPE);
		switch (type)
		{
			case CallInfo.CALL_TYPE_INCOMING:
			{
				typeImg.setImageResource(android.R.drawable.sym_call_incoming);
				typeTxt.setText("Incoming call");
			}
			break;
			case CallInfo.CALL_TYPE_MISSED:
			{
				typeImg.setImageResource(android.R.drawable.sym_call_missed);
				typeTxt.setText("Missed call");
			}
			break;
			default:
			{	
				typeTxt.setText("Outgoing call");
				typeImg.setImageResource(android.R.drawable.sym_call_outgoing);
			}
			break;
		}
		
		String contact = getIntent().getExtras().getString(CallInfo.FIELD_CONTACT);
		if (contact == null)
		{
			((TextView)findViewById(R.id.contact)).setVisibility(View.GONE);
			((TextView)findViewById(R.id.contactLbl)).setVisibility(View.GONE);
			((TextView)findViewById(R.id.contactLblDash)).setVisibility(View.GONE);
		}
		else
			((TextView)findViewById(R.id.contact)).setText(contact);

		String number = getIntent().getExtras().getString(CallInfo.FIELD_NUMBER);
		((TextView)findViewById(R.id.number)).setText(PhoneNumberUtils.formatNumber(number));

		long begin = getIntent().getExtras().getLong(CallInfo.FIELD_BEGIN);
		((TextView)findViewById(R.id.start)).setText(DateFormat.format("dd/MM/yyyy hh:mm:ss", begin));

		long end = getIntent().getExtras().getLong(CallInfo.FIELD_END);
		((TextView)findViewById(R.id.finish)).setText(DateFormat.format("dd/MM/yyyy hh:mm:ss", end));

		
		
		((TextView)findViewById(R.id.duration)).setText(getTimeDif(begin, end));
				
		lat = getIntent().getExtras().getInt(CallInfo.FIELD_LOCATION_LAT);
		lon = getIntent().getExtras().getInt(CallInfo.FIELD_LOCATION_LON);
		
		if (lat == 0 && lon == 0)
		{
			findViewById(R.id.posCaption).setVisibility(View.GONE);
			findViewById(R.id.posDelim).setVisibility(View.GONE);
			
			findViewById(R.id.latCaption).setVisibility(View.GONE);
			findViewById(R.id.delimLat).setVisibility(View.GONE);
			findViewById(R.id.latitude).setVisibility(View.GONE);

			findViewById(R.id.longCaption).setVisibility(View.GONE);
			findViewById(R.id.delimLong).setVisibility(View.GONE);
			findViewById(R.id.longtude).setVisibility(View.GONE);
			
			findViewById(R.id.goToMap).setVisibility(View.GONE);
		}
		else
		{
			((TextView)findViewById(R.id.latitude)).setText(String.valueOf(lat / 1E6));
			((TextView)findViewById(R.id.longtude)).setText(String.valueOf(lon / 1E6));
			((Button)findViewById(R.id.goToMap)).setOnClickListener(new OnClickListener()
			{
				public void onClick(View view)
				{
					Intent goToMap = new Intent();
					goToMap.putExtra(EXTRA_COORD_LAT, lat);
					goToMap.putExtra(EXTRA_COORD_LON, lon);
					goToMap.setClass(getApplicationContext(), CallOnMapActivity.class);
					startActivity(goToMap);
				}
			});
		}
	}
	
	private static String getTimeDif(long begin, long end){
		long raw_ss = (end-begin)/1000;
		long hh = raw_ss/3600;
		long mm = (raw_ss % 3600)/60;
		long ss = raw_ss - hh * 3600 - mm * 60;
		
		String tss = ss < 10 ? "0" + String.valueOf(ss) : String.valueOf(ss);
		String tmm = mm < 10 ? "0" + String.valueOf(mm) : String.valueOf(mm);
		String thh = hh < 10 ? "0" + String.valueOf(hh) : String.valueOf(hh);
		return thh + ":" + tmm + ":" + tss;
	}
}
