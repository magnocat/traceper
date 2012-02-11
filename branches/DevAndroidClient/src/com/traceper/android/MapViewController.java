package com.traceper.android;



import java.io.IOException;
import java.util.List;
import java.util.Locale;

import org.json.JSONException;
import org.json.JSONObject;

import android.content.BroadcastReceiver;
import android.content.ComponentName;
import android.content.Context;
import android.content.Intent;
import android.content.IntentFilter;
import android.content.ServiceConnection;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.graphics.Canvas;
import android.graphics.Point;
import android.location.Address;
import android.location.Geocoder;
import android.location.Location;
import android.os.Bundle;
import android.os.IBinder;
import android.util.Log;
import android.widget.Toast;

import com.google.android.maps.GeoPoint;
import com.google.android.maps.MapActivity;
import com.google.android.maps.MapController;
import com.google.android.maps.MapView;
import com.google.android.maps.Overlay;
import com.traceper.R;
import com.traceper.android.Main.MessageReceiver;
import com.traceper.android.interfaces.IAppService;
import com.traceper.android.services.AppService;





public class MapViewController extends MapActivity {

	private MapView mapView;
	private MapController mapController;
	private Location location;
	private IAppService appService = null;
	private Location userLocation = null;
	private double lati=0;
	private double longi=0;
	
	@Override
	protected void onStop() {
		// TODO Auto-generated method stub

		super.onStop();
	}

	
	public class MessageReceiver extends  BroadcastReceiver  {
		public void onReceive(Context context, Intent intent) {

			Log.i("Broadcast receiver ", "received a message");
			
			
			Bundle extra = intent.getExtras();
			if (extra != null)
			{
				String action = intent.getAction();
				if (action.equals(IAppService.SHOW_USER_LOCATION))
				{				
					//userLocation = (Location)extra.getParcelable(IAppService.SHOW_USER_LOCATION_LOC);
				//	String[] glocation;
					
					//glocation = extra.getStringArray(IAppService.SHOW_USER_LOCATION_LOC);
					 //mTestArray =  = getResources().getStringArray(R.array.testArray);  
					lati = Double.parseDouble(extra.getString("latitude"));
				    longi =Double.parseDouble(extra.getString("longitude"));	
							GeoPoint point = new GeoPoint(
			                         (int) (lati * 1E6), 
			                         (int) (longi * 1E6));
			        		 
			        		 
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
			}

		}

	};
	public MessageReceiver messageReceiver = new MessageReceiver();
	private ServiceConnection mConnection = new ServiceConnection() 
	{
		public void onServiceConnected(ComponentName className, IBinder service) {          
			appService = ((AppService.IMBinder)service).getService();    
		
		}
		public void onServiceDisconnected(ComponentName className) {          
			appService = null;
			Toast.makeText(MapViewController.this, R.string.local_service_stopped,
					Toast.LENGTH_SHORT).show();
		}
	};
	
	@Override
	public void onCreate(Bundle savedInstanceState) {
		 super.onCreate(savedInstanceState);

    	 
		 
  		double latitude = 0;
  		double longitude = 0;
  		
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

     	
			String action =  getIntent().getAction();
			if (action != null) {
				if (action.equals(IAppService.SHOW_MY_LOCATION)) {
					
			         Bundle extra = getIntent().getExtras();
			         if (extra != null) {
			 
			        	 location = (Location) extra.getParcelable(IAppService.LAST_LOCATION);
			        	 
			        		latitude = location.getLatitude();
			        	    longitude = location.getLongitude();
			        	    
			         }
					
					
				}else if (action.equals(IAppService.SHOW_USER_LOCATION))	{
					
			         Bundle extra = getIntent().getExtras();
			         if (extra != null) {
			 
			        		latitude = extra.getDouble("latitude");
			        	    longitude = extra.getDouble("longitude");
			       
			   	 /* listedeki tekil kullanýcý için o anki konum bilgisini almak
			   	        		int userid = 0;
			   	        			userid = extra.getInt("userid");
			   	        		if (userid != 0){ 
			   	    			appService.getUserLocation(userid);
			   	      */
			        	    
			         }
					
				}else if (action.equals(IAppService.SHOW_ALL_USER_LOCATIONS))	{
										
					
				}
			}
     
         

        	  
        		 
        		 GeoPoint point = new GeoPoint(
                         (int) (latitude * 1E6), 
                         (int) (longitude * 1E6));
        		 
        		 
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
	protected void onPause() 
	{
		unregisterReceiver(messageReceiver);		
		unbindService(mConnection);
		super.onPause();
	}

	@Override
	protected void onResume() 
	{			
		super.onResume();
		bindService(new Intent(MapViewController.this, AppService.class), mConnection , Context.BIND_AUTO_CREATE);
		IntentFilter i = new IntentFilter();
		i.addAction(IAppService.SHOW_USER_LOCATION);
		//i.addAction(IMService.FRIEND_LIST_UPDATED);
		registerReceiver(messageReceiver, i);	
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
			Bitmap bmp = BitmapFactory.decodeResource(getResources(), R.drawable.gps_point);
			canvas.drawBitmap(bmp, screenPts.x - 6 , screenPts.y - 30 , null); // 24 is the height of image        
			return true;
		}
	} 
}