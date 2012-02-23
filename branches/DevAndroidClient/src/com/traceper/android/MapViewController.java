package com.traceper.android;



import java.io.IOException;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Locale;

import org.json.JSONArray;
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
import android.graphics.drawable.Drawable;
import android.location.Address;
import android.location.Geocoder;
import android.location.Location;
import android.os.Bundle;
import android.os.IBinder;
import android.util.Log;
import android.view.Menu;
import android.view.MenuItem;
import android.widget.Toast;



import com.google.android.maps.GeoPoint;
import com.google.android.maps.MapActivity;
import com.google.android.maps.MapController;
import com.google.android.maps.MapView;
import com.google.android.maps.Overlay;
import com.google.android.maps.OverlayItem;
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
	private List<GeoPoint> geoPoints = new ArrayList<GeoPoint>();
	static Context context;
	public static final int REFRESH_MAP = Menu.FIRST;

	
	
	@Override
	protected void onStop() {
		// TODO Auto-generated method stub

		super.onStop();
	}


	
	public class MessageReceiver extends  BroadcastReceiver  {
		public void onReceive(Context context, Intent intent) {

			Log.i("Broadcast receiver ", "received a message");
			
					 Drawable marker=getResources().getDrawable(android.R.drawable.star_big_on);
				     int markerWidth = marker.getIntrinsicWidth();
				     int markerHeight = marker.getIntrinsicHeight();
				     marker.setBounds(0, markerHeight, markerWidth, 0);
				 
				   
				     MapItemizedOverlay mapItemizedOverlay = new MapItemizedOverlay(marker);
				     mapView.getOverlays().add(mapItemizedOverlay); 
				     
			Bundle extra = intent.getExtras();
			if (extra != null)
			{
				String action = intent.getAction();
				if (action.equals(IAppService.SHOW_USER_LOCATION))
				{				

				     
					lati = Double.parseDouble(extra.getString("latitude"));
				    longi =Double.parseDouble(extra.getString("longitude"));	
					
			        		 
			        		 
							  GeoPoint myPoint1 =new GeoPoint(
				                         (int) (lati * 1E6), 
				                         (int) (longi * 1E6));
							     mapItemizedOverlay.addItem(myPoint1, "myPoint1", "myPoint1");
							  
								
				        		 mapController.animateTo(myPoint1);

			                 				
							
				}
			}
			 mapView.invalidate();	
		}

	};
	public MessageReceiver messageReceiver = new MessageReceiver();
	private ServiceConnection mConnection = new ServiceConnection() 
	{
		public void onServiceConnected(ComponentName className, IBinder service) {          
			appService = ((AppService.IMBinder)service).getService();    
			updateLocation();
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
  		
         setContentView(R.layout.map_view);
         
         context = getApplicationContext();

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

         mapView.invalidate();




	}

	public void updateLocation(){
		
		
		

   	 
		 
  		double latitude = 0;
  		double longitude = 0;
  		
	     Drawable marker=getResources().getDrawable(android.R.drawable.star_big_on);
	     int markerWidth = marker.getIntrinsicWidth();
	     int markerHeight = marker.getIntrinsicHeight();
	     marker.setBounds(0, markerHeight, markerWidth, 0);
	 
	   
	     MapItemizedOverlay mapItemizedOverlay = new MapItemizedOverlay(marker);
	     mapView.getOverlays().add(mapItemizedOverlay);
         
         
     	
			String action =  getIntent().getAction();
			if (action != null) {
				if (action.equals(IAppService.SHOW_MY_LOCATION)) {
					
			         Bundle extra = getIntent().getExtras();
			         if (extra != null) {
			 
			        	 location = (Location) extra.getParcelable(IAppService.LAST_LOCATION);
			        	 
			        		latitude = location.getLatitude();
			        	    longitude = location.getLongitude();
			        	    String gettime = String.valueOf(location.getTime());

					         GeoPoint myPoint =new GeoPoint(
			                         (int) (latitude * 1E6), 
			                         (int) (longitude * 1E6));
						     mapItemizedOverlay.addItem(myPoint, "My Point", gettime);
						  
							
			        		 mapController.animateTo(myPoint);
			        	    
			         }
					
					
				}else if (action.equals(IAppService.SHOW_USER_LOCATION))	{
					
			         Bundle extra = getIntent().getExtras();
			         if (extra != null) {
			 

			        	    int userid = extra.getInt("userid");

			        	    try{

			         				JSONObject e = appService.getUserInfo(userid);
			         				
			         		latitude = e.getDouble("latitude");
			        	    longitude = e.getDouble("longitude");	
			        	    
			        	      GeoPoint myPoint1 =new GeoPoint(
			                         (int) (latitude * 1E6), 
			                         (int) (longitude * 1E6));
						     mapItemizedOverlay.addItem(myPoint1 , e.getString("realname")
					         	         , e.getString("time"));
						     mapController.animateTo(myPoint1);
			         				
			                 }catch(JSONException e)        {
			                 	 Log.e("log_tag", "Error parsing data "+e.toString());
			                 }
			        	    
					      
			        		 
			         }
					
				}else if (action.equals(IAppService.SHOW_ALL_USER_LOCATIONS))	{
	   

				   						
				     
			         try{
			          	
			          	JSONArray  userlist =  appService.getUserList();
			     	
			  	        for(int i=0;i<userlist.length();i++){						
			  				
			  				JSONObject e = userlist.getJSONObject(i);
			  					  				
							lati = Double.parseDouble(e.getString("latitude"));
						    longi =Double.parseDouble(e.getString("longitude"));
			  				
			  	      geoPoints.add( new GeoPoint(
		                         (int) (lati * 1E6), 
		                         (int) (longi * 1E6)));
			  	      //mapItemizedOverlay.addItem(geoPoints.get(i), e.getString("realname"), e.getString("user"));
			  	      
			  	    mapItemizedOverlay.addItem(geoPoints.get(i), e.getString("realname"), e.getString("time"));
			  	      mapController.animateTo(geoPoints.get(i));
			  			}	
			  	        
			          }catch(JSONException e)        {
			          	 Log.e("log_tag", "Error parsing data "+e.toString());
			          }

	        		  
	                  mapController.setZoom(4);
				}
			}
     
         

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
	@Override
	public boolean onCreateOptionsMenu(Menu menu) {		
		boolean result = super.onCreateOptionsMenu(menu);
		/* 
		 * show sign up menu item if registration is made enabled.
		 */
		 menu.add(0, REFRESH_MAP, 0, R.string.map_refresh).setIcon(R.drawable.rfrsh);
		

		 
		return result;
	}
	@Override
	public boolean onMenuItemSelected(int featureId, MenuItem item) {
	    
		switch(item.getItemId()) 
	    {
	    	case REFRESH_MAP:
	    		updateLocation();
   		
    		return true;

	    }
	       
	    return super.onMenuItemSelected(featureId, item);
	}
	
}