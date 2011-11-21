package com.traceper.android.map;

import java.util.ArrayList;
import java.util.List;

import android.app.AlertDialog;
import android.content.DialogInterface;
import android.content.DialogInterface.OnClickListener;
import android.graphics.Canvas;
import android.graphics.Color;
import android.graphics.Paint;
import android.graphics.Point;
import android.graphics.Typeface;
import android.graphics.drawable.Drawable;

import com.traceper.R;
import com.traceper.android.dao.model.CallInfo;

import com.traceper.android.map.CallOverlayItem;
import com.traceper.android.map.GroupedCallsAdapter;
import com.google.android.maps.ItemizedOverlay;
import com.google.android.maps.MapActivity;
import com.google.android.maps.MapView;

public class CallLocationOverlay extends ItemizedOverlay<CallOverlayItem>
{
	private List<CallOverlayItem> markers = new ArrayList<CallOverlayItem>();
	private MapActivity mapActivity;
	
	public CallLocationOverlay(MapActivity mapActivity, Drawable icon)
	{
		super(boundCenterBottom(icon));
		this.mapActivity = mapActivity;
	}

	public CallLocationOverlay(MapActivity mapActivity, Drawable icon, List<CallInfo> calls)
	{
		super(boundCenterBottom(icon));
		this.mapActivity = mapActivity;
	}
	
	@Override
	protected CallOverlayItem createItem(int i)
	{
		return markers.get(i);
	}

	@Override
	protected boolean onTap(int i)
	{
		new AlertDialog.Builder(mapActivity).setTitle("Grouping").
		setAdapter(new GroupedCallsAdapter(mapActivity, R.layout.child_row , markers.get(i).getCalls()), new OnClickListener()
		{
			public void onClick(DialogInterface arg0, int arg1)
			{}
		})
		.show();
		
		return true;
	}
	
	@Override
	public int size()
	{
		return markers.size();
	}

	public void addOverlay(CallOverlayItem overlay) {
		markers.add(overlay);
	}
	
	public CallLocationOverlay populateCalls()
	{
		populate();
		return this;
	}
	
	@Override
	public void draw(Canvas canvas, MapView mapView, boolean shadow)
	{
		super.draw(canvas, mapView, shadow);
		for(CallOverlayItem item : markers){
			if(item.getCallsCount() > 1){
				Point p = new Point();
				mapView.getProjection().toPixels(item.getPoint(), p);
				Paint paint = new Paint();
				paint.setAntiAlias(true);
				paint.setTextAlign(Paint.Align.CENTER);
				
				paint.setColor(Color.BLACK);
				int zoom  = mapView.getZoomLevel();
				if (zoom > 17)
				{
					paint.setTypeface(Typeface.create("", Typeface.BOLD));
					paint.setTextSize(16);
					canvas.drawText(String.valueOf(item.getCallsCount()), p.x, p.y - 22, paint);
				}
				else
				if (zoom > 16)
				{
					paint.setTextSize(12);
					canvas.drawText(String.valueOf(item.getCallsCount()), p.x, p.y - 22, paint);
				}
				else
				if (zoom > 15)
				{
					paint.setTextSize(10);
					canvas.drawText("...", p.x, p.y - 22, paint);
				}
			}
		}
	}
}
