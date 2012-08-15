package com.traceper.android;


import java.io.IOException;
import java.util.ArrayList;
import java.util.List;
import java.util.Locale;

import android.graphics.Canvas;
import android.graphics.drawable.Drawable;
import android.location.Address;
import android.location.Geocoder;
import android.widget.Toast;

import com.google.android.maps.GeoPoint;
import com.google.android.maps.ItemizedOverlay;
import com.google.android.maps.MapView;
import com.google.android.maps.OverlayItem;


public class MapItemizedOverlay extends ItemizedOverlay<OverlayItem>{

	private ArrayList<OverlayItem> overlayItemList = new ArrayList<OverlayItem>();

	public MapItemizedOverlay(Drawable marker) {
		super(boundCenterBottom(marker));
		// TODO Auto-generated constructor stub

		populate();
	}

	public void addItem(GeoPoint p, String title, String snippet){
		OverlayItem newItem = new OverlayItem(p, title, snippet);
		overlayItemList.add(newItem);
		populate();
	}

	@Override
	protected OverlayItem createItem(int i) {
		// TODO Auto-generated method stub
		return overlayItemList.get(i);
	}

	@Override
	public int size() {
		// TODO Auto-generated method stub
		return overlayItemList.size();
	}

	@Override
	public void draw(Canvas canvas, MapView mapView, boolean shadow) {
		// TODO Auto-generated method stub
		super.draw(canvas, mapView, shadow);
		//boundCenterBottom(marker);
	}
	@Override
	protected boolean onTap(int i){

		GeoPoint  gpoint = overlayItemList.get(i).getPoint();
		double lat = gpoint.getLatitudeE6()/1e6;
		double lon = gpoint.getLongitudeE6()/1e6;
		String toast = "User Name: "+overlayItemList.get(i).getTitle();
		toast += "\nUpdate Time: "+overlayItemList.get(i).getSnippet();
		toast += 	"\nSymbol coordinates: Lat = "+lat+" Lon = "+lon+" (microdegrees)";
		Toast.makeText(MapViewController.context, toast, Toast.LENGTH_LONG).show();
		/* convert address
	String address = ConvertPointToLocation(gpoint);
    Toast.makeText(MapViewController.context, address, Toast.LENGTH_SHORT).show();
		 */
		return(true);
	} 
	/* convert address function
public String ConvertPointToLocation(GeoPoint point) {   
	String address = "";
	Geocoder geoCoder = new Geocoder(
			MapViewController.context, Locale.getDefault());
	try {
		List<Address> addresses = geoCoder.getFromLocation(
				point.getLatitudeE6()  / 1E6, 
				point.getLongitudeE6() / 1E6, 1);

		if (addresses.size() > 0) {
			for (int index = 0; index < addresses.get(0).getMaxAddressLineIndex(); index++)
				address += addresses.get(0).getAddressLine(index) + " ";
		}
	}
	catch (IOException e) {                
		e.printStackTrace();
	}   

	return address;
} 
	 */
}