package com.traceper.android;

import java.util.Collection;
import java.util.List;

import android.graphics.drawable.Drawable;
import android.os.Bundle;

import com.traceper.android.CallDetails;
import com.traceper.R;
import com.traceper.android.dao.model.CallInfo;
import com.traceper.android.dao.model.GlobalCallHolder;
import com.traceper.android.map.CallLocationOverlay;
import com.traceper.android.map.CallOverlayItem;


import com.google.android.maps.GeoPoint;
import com.google.android.maps.MapActivity;
import com.google.android.maps.MapController;
import com.google.android.maps.MapView;

public class CallOnMapActivity extends MapActivity
{
	private MapView mapView;
	
	private CallLocationOverlay missedOverlays;
	private CallLocationOverlay incomOverlays;
	private CallLocationOverlay outgoingOverlays;
	private CallLocationOverlay multiOverlays;
	
	public void onCreate(Bundle savedInstanceState)
	{
		super.onCreate(savedInstanceState);
		
		setContentView(R.layout.map_view);
		
		mapView = (MapView) findViewById(R.id.mapView);
		mapView.setBuiltInZoomControls(true);
		MapController mapController = mapView.getController();
		createOverlays();
		
		GeoPoint p = new GeoPoint(
	            getIntent().getExtras().getInt(CallDetails.EXTRA_COORD_LAT), 
	            getIntent().getExtras().getInt(CallDetails.EXTRA_COORD_LON));

		mapController.animateTo(p);
		fillOverlays();
	    mapView.invalidate();
	}
	
	private void createOverlays(){
		Drawable iconIncoming = getResources().getDrawable(R.drawable.ldpi_incoming);
		Drawable iconOutgoing = getResources().getDrawable(R.drawable.ldpi_outgoing);
		Drawable iconMissed = getResources().getDrawable(R.drawable.ldpi_missed);
		Drawable iconMulticall = getResources().getDrawable(R.drawable.ldpi_multicall);
		
		missedOverlays = new CallLocationOverlay(this, iconMissed);
		incomOverlays = new CallLocationOverlay(this, iconIncoming);
		outgoingOverlays = new CallLocationOverlay(this, iconOutgoing);
		multiOverlays = new CallLocationOverlay(this, iconMulticall);
				
		mapView.getOverlays().add(missedOverlays);
		mapView.getOverlays().add(outgoingOverlays);
		mapView.getOverlays().add(incomOverlays);
		mapView.getOverlays().add(multiOverlays);
	}
	
	private void fillOverlays()
	{
		Collection<List<CallInfo>> calls = GlobalCallHolder.getCallsGroupedByLocation();
		for(List<CallInfo> callList : calls)
		{
			if(callList.isEmpty()){
				continue;
			}
			CallInfo ci = callList.get(0);
			GeoPoint point = new GeoPoint(ci.getLatitude(), ci.getLongitude());
			if (callList.size() > 1)
			{
				multiOverlays.addOverlay(new CallOverlayItem(point, callList));
			}
			else
			{
				switch (ci.getCallType())
				{
					case CallInfo.CALL_TYPE_INCOMING:
						incomOverlays.addOverlay(new CallOverlayItem(point, ci));
						break;
					case CallInfo.CALL_TYPE_OUTGOING:
						outgoingOverlays.addOverlay(new CallOverlayItem(point, ci));
						break;
					case CallInfo.CALL_TYPE_MISSED:
						missedOverlays.addOverlay(new CallOverlayItem(point, ci));
						break;
				}
			}
		}
		missedOverlays.populateCalls();
		outgoingOverlays.populateCalls();
		incomOverlays.populateCalls();
		multiOverlays.populateCalls();
	}
	
	protected boolean isRouteDisplayed()
	{
		return false;
	}
}

