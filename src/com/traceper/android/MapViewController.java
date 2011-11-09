package com.traceper.android;



import com.google.android.maps.MapActivity;
import com.google.android.maps.MapView;
import com.google.android.maps.MapController;
import com.google.android.maps.GeoPoint;



import android.location.Geocoder;
import android.location.Address;

import com.google.android.maps.Overlay;
import com.traceper.R;

import android.graphics.Canvas;
import android.graphics.Point;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;

import java.util.List;
import java.util.Locale;
import java.io.IOException;

import android.os.Bundle;

import android.widget.Toast;





public class MapViewController extends MapActivity {
	
	private MapView mapView;
	private MapController mapController;
	
	private static double lat;
	private static double lon;
	
	
	@Override
	protected void onStop() {
		// TODO Auto-generated method stub
		
		super.onStop();
	}


	public static void putLatLong(double latitude, double longitude){
		lat = latitude;
		lon =longitude;
	}
	
    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        
 
        setContentView(R.layout.map_view);
  
        

        
        mapView = (MapView) findViewById(R.id.mapView);
        
        // enable Street view by default
        mapView.setStreetView(false);
        
        // enable to show Satellite view
        // mapView.setSatellite(true);
        
        // enable to show Traffic on map
        // mapView.setTraffic(true);
        mapView.setBuiltInZoomControls(true);
        
        mapController = mapView.getController();
        mapController.setZoom(16); 
        
        
        GeoPoint point = new GeoPoint(
                (int) (lat * 1E6), 
                (int) (lon * 1E6));
        
        /* Toast.makeText(getBaseContext(), 
                "Latitude: " + location.getLatitude() + 
                " Longitude: " + location.getLongitude(), 
                Toast.LENGTH_SHORT).show();*/
                        
        mapController.animateTo(point);
        mapController.setZoom(15);
        
        // add marker
        MapOverlay mapOverlay = new MapOverlay();
		mapOverlay.setPointToDraw(point);
		List<Overlay> listOfOverlays = mapView.getOverlays();
		listOfOverlays.clear();
		listOfOverlays.add(mapOverlay);
		
        String address = ConvertPointToLocation(point);
        Toast.makeText(getBaseContext(), address, Toast.LENGTH_SHORT).show();

        mapView.invalidate();
 
        
   
    }
    
    @Override
    protected boolean isRouteDisplayed() {
        return false;
    }

   
	public String ConvertPointToLocation(GeoPoint point) {   
    	String address = "";
        Geocoder geoCoder = new Geocoder(
                getBaseContext(), Locale.getDefault());
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


    
    class MapOverlay extends Overlay
    {
    	private GeoPoint pointToDraw;

    	public void setPointToDraw(GeoPoint point) {
    		pointToDraw = point;
    	}

    	public GeoPoint getPointToDraw() {
    		return pointToDraw;
    	}
    	
        @Override
        public boolean draw(Canvas canvas, MapView mapView, boolean shadow, long when) {
            super.draw(canvas, mapView, shadow);                   
 
            // convert point to pixels
            Point screenPts = new Point();
            mapView.getProjection().toPixels(pointToDraw, screenPts);
 
            // add marker
            Bitmap bmp = BitmapFactory.decodeResource(getResources(), R.drawable.redstar);
            canvas.drawBitmap(bmp, screenPts.x, screenPts.y - 24, null); // 24 is the height of image        
            return true;
        }
    } 
}