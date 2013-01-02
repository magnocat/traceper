package com.traceper.android;



import java.util.ArrayList;
import java.util.List;



import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import android.content.BroadcastReceiver;
import android.content.ComponentName;
import android.content.Context;
import android.content.Intent;
import android.content.IntentFilter;
import android.content.ServiceConnection;
import android.content.res.Resources.NotFoundException;
import android.graphics.drawable.Drawable;
import android.location.Location;
import android.os.AsyncTask;
import android.os.Bundle;
import android.os.Handler;
import android.os.IBinder;
import android.support.v4.app.FragmentTransaction;
import android.util.Log;
import android.view.View;
import android.view.View.OnClickListener;

import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.Toast;

import com.actionbarsherlock.app.ActionBar;
import com.actionbarsherlock.app.ActionBar.Tab;

import com.actionbarsherlock.app.SherlockMapActivity;
import com.actionbarsherlock.view.Menu;
import com.actionbarsherlock.view.MenuInflater;
import com.actionbarsherlock.view.MenuItem;
import com.actionbarsherlock.view.Window;
import com.google.android.maps.GeoPoint;
import com.google.android.maps.MapView;
import com.google.android.maps.Overlay;

import com.google.android.maps.MapController;

import com.readystatesoftware.maps.TapControlledMapView;
import com.traceper.R;
import com.traceper.android.interfaces.IAppService;
import com.traceper.android.map.CustomItemizedOverlay;
import com.traceper.android.map.CustomOverlayItem;
import com.traceper.android.services.AppService;






public class MapViewController extends SherlockMapActivity implements ActionBar.TabListener  {
	ActionBar actionBar;
	private TapControlledMapView  mapView;
	private MapController mapController;
	private Location location;
	private IAppService appService = null;
	private Location userLocation = null;
	private double lati=0;
	private double longi=0;
	private List<GeoPoint> geoPoints = new ArrayList<GeoPoint>();
	public static Context context;
	public static final int REFRESH_MAP = Menu.FIRST;
	public static final int MAP = Menu.FIRST+ 1;
	private Location lastLocation = null;
	ArrayList<Integer> tabs = new ArrayList<Integer>();
	private JSONObject e=null;
	private JSONArray  userlist;
	protected MapItemizedOverlay mapItemizedOverlay;
	LinearLayout markerLayout;
	double latitude = 0;
	double longitude = 0;

	
	final int OPERATION_MY_LOCATION=1;
	final int OPERATION_USER_LOCATION = 2;
	final int OPERATION_ALL_USER_LOCTION= 3;
	final int OPERATION_SHOW_USER_ALL_PAST_POINT_ON_MAP= 4;

	List<Overlay> mapOverlays;
	Drawable drawable;
	CustomItemizedOverlay<CustomOverlayItem> itemizedOverlay;
	


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
				if (action.equals(IAppService.LAST_LOCATION_DATA_SENT_TIME))
				{				

					lastLocation = (Location)extra.getParcelable(IAppService.LAST_LOCATION);	
					drawable = getResources().getDrawable(android.R.drawable.star_big_on);
					itemizedOverlay = new CustomItemizedOverlay<CustomOverlayItem>(drawable, mapView);

				


					
					GeoPoint MyPoint1 = new GeoPoint((int)(lastLocation.getLatitude() * 1E6),(int)(lastLocation.getLongitude() *1E6));
					CustomOverlayItem overlayItem3 = new CustomOverlayItem(MyPoint1,getResources().getString(R.string.mylocation), 
							appService.getRealName(), null);
					itemizedOverlay.addOverlay(overlayItem3);

					mapOverlays.add(itemizedOverlay);

					final MapController mc = mapView.getController();
					mc.animateTo(MyPoint1);
					mc.setZoom(16);
					
					//mapController.animateTo(point3);



				}
			}
		//	mapView.invalidate();	
			
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
	protected Handler handler = new Handler();

	@Override
	public void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		requestWindowFeature(Window.FEATURE_OPTIONS_PANEL);
		setContentView(R.layout.map_view);
		
		
		//context = getApplicationContext();
	
		actionBar = getSupportActionBar();
		actionBar.setDisplayShowTitleEnabled(true);
		setTabs();
		

		mapView = (TapControlledMapView) findViewById(R.id.mapview);
		mapView.setBuiltInZoomControls(true);

		
		mapOverlays = mapView.getOverlays();

		drawable = getResources().getDrawable(android.R.drawable.star_big_off);
		itemizedOverlay = new CustomItemizedOverlay<CustomOverlayItem>(drawable, mapView);
		


		ImageView meButton = (ImageView) findViewById(R.id.meButton);
		meButton.setOnClickListener(new OnClickListener() {
			@Override
			public void onClick(View v) {
			//	zoomMyLocation();
				if (lastLocation != null){
					
					drawable = getResources().getDrawable(android.R.drawable.star_big_off);
					itemizedOverlay = new CustomItemizedOverlay<CustomOverlayItem>(drawable, mapView);

					
					GeoPoint MyPoint1 = new GeoPoint((int)(lastLocation.getLatitude() * 1E6),(int)(lastLocation.getLongitude() *1E6));
					CustomOverlayItem overlayItem3 = new CustomOverlayItem(MyPoint1,getResources().getString(R.string.mylocation), 
							"(interiors)", null);
					itemizedOverlay.addOverlay(overlayItem3);

					mapOverlays.add(itemizedOverlay);

					final MapController mc = mapView.getController();
					mc.animateTo(MyPoint1);
					mc.setZoom(16);
					
				}
			}
		});
	}

	public void updateLocation(){
		



		String action =  getIntent().getAction();
		if (action != null) {
			if (action.equals(IAppService.SHOW_MY_LOCATION)) {

			//	Bundle extra = this.getIntent().getExtras();
			//	if (extra != null) {
			
					//location = (Location) extra.getParcelable(IAppService.LAST_LOCATION);
					
					new LongOperation().execute(new String[] {String.valueOf(OPERATION_MY_LOCATION)});

			//	}


			}else if (action.equals(IAppService.SHOW_USER_LOCATION))	{

				Bundle extra = getIntent().getExtras();
				if (extra != null) {

					final int userid = extra.getInt("userid");

					
							try {
								
								new LongOperation().execute(new String[] {String.valueOf(OPERATION_USER_LOCATION),String.valueOf(userid)});
							}
							catch(Exception e){

							}
						
				
				}

			}else if (action.equals(IAppService.SHOW_ALL_USER_LOCATIONS))	{

				try{

					new LongOperation().execute(new String[] {String.valueOf(OPERATION_ALL_USER_LOCTION)});

				}catch(Exception e)        {
					Log.e("log_tag", "Error parsing data "+e.toString());
				}


			//	mapController.setZoom(4);
			}else if (action.equals(IAppService.SHOW_USER_PAST_POINT_ON_MAP))	{

				Bundle extra = getIntent().getExtras();
				if (extra != null) {


					drawable = getResources().getDrawable(android.R.drawable.star_big_off);
					itemizedOverlay = new CustomItemizedOverlay<CustomOverlayItem>(drawable, mapView);

					
					GeoPoint MyPoint1 = new GeoPoint((int)(extra.getDouble("latitude") * 1E6),(int)(extra.getDouble("longitude") *1E6));
					CustomOverlayItem overlayItem3 = new CustomOverlayItem(MyPoint1,getResources().getString(R.string.user_past_point), 
							extra.getString("calculatedTime"), null);
					itemizedOverlay.addOverlay(overlayItem3);

					mapOverlays.add(itemizedOverlay);

					final MapController mc = mapView.getController();
					mc.animateTo(MyPoint1);
					mc.setZoom(16);  

				}				
			}else if (action.equals(IAppService.SHOW_USER_ALL_PAST_POINT_ON_MAP))	{

				try{

					new LongOperation().execute(new String[] {String.valueOf(OPERATION_SHOW_USER_ALL_PAST_POINT_ON_MAP)});

				}catch(Exception e)        {
					Log.e("log_tag", "Error parsing data "+e.toString());
				}

			}

		}

	//	mapView.invalidate();



	}
	@Override
	protected void onPause() 
	{
		try{
		unregisterReceiver(messageReceiver);		
		unbindService(mConnection);
		}catch(Exception e){
			e.printStackTrace();
		}
		super.onPause();
	}

	@Override
	protected void onResume() 
	{			
		super.onResume();
		

		try{
		bindService(new Intent(MapViewController.this, AppService.class), mConnection , Context.BIND_AUTO_CREATE);
		IntentFilter i = new IntentFilter();
		i.addAction(IAppService.LAST_LOCATION_DATA_SENT_TIME);
		//i.addAction(IMService.FRIEND_LIST_UPDATED);
		registerReceiver(messageReceiver, i);	
		}catch(Exception e){
			
		}
	}
	@Override
	protected void onActivityResult(int requestCode, int resultCode, Intent data) {		
		super.onActivityResult(requestCode, resultCode, data);		
	}
	private void setTabs() {
		tabs = new ArrayList<Integer>();
		tabs.add(R.string.friends);
		tabs.add(R.string.profile);
		tabs.add(R.string.friend_list);

		// Create the tabs
		actionBar.addTab(actionBar.newTab().setText(R.string.friends).setTabListener(this));
		actionBar.addTab(actionBar.newTab().setText(R.string.profile).setTabListener(this));
		actionBar.addTab(actionBar.newTab().setText(R.string.friend_list).setTabListener(this));
		actionBar.setNavigationMode(ActionBar.NAVIGATION_MODE_TABS);
	}
	@Override
	public void onTabSelected(Tab tab, FragmentTransaction ft) {
		int selectedTabID = tabs.get(tab.getPosition());
		if (selectedTabID != R.string.friends) {
			startActivity(new Intent(this, new_main.class).putExtra("tab", selectedTabID).setFlags(
					Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TOP | Intent.FLAG_ACTIVITY_NO_ANIMATION));
			overridePendingTransition(0, 0);
			finish();
		}
	}
	@Override
	protected boolean isRouteDisplayed() {
		return false;
	}
	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		/* 
		 * show sign up menu item if registration is made enabled.
		 */
		//menu.add(0, REFRESH_MAP, 0, R.string.map_refresh).setIcon(R.drawable.rfrsh);
	
    	getSupportMenuInflater().inflate(R.menu.map_menu, menu);
		return true;

		//return result;
	}
	
	@Override
	public boolean onOptionsItemSelected(MenuItem item) {
		switch (item.getItemId()) {
		case R.id.m_refresh:
			onResume();
			appService.sendLocationNow();
			return true;
			
		case R.id.m_friends:
			
			try{
				
				new LongOperation().execute(new String[] {String.valueOf(OPERATION_ALL_USER_LOCTION)});

			}catch(Exception e)        {
				Log.e("log_tag", "Error parsing data "+e.toString());
			}
			
			return true;
		}
		return super.onOptionsItemSelected(item);
	}
	




	@Override
	public void onTabUnselected(Tab tab, FragmentTransaction ft) {
		// TODO Auto-generated method stub
		
	}

	@Override
	public void onTabReselected(Tab tab, FragmentTransaction ft) {
		// TODO Auto-generated method stub
		
	}

	private class LongOperation extends AsyncTask<String, Void, String> {

	      @Override
	      protected String doInBackground(String... params) {
	    	  
	                try {
	                    Thread.sleep(1000);
	                    
	                  switch(Integer.parseInt(params[0].toString())) 
	                  { 
	                  case(OPERATION_MY_LOCATION):
	                  //my location
	  					
	              		try {
	    					appService.sendLocationNow();
	              		
	              		} catch (Exception e) {
	    						// TODO Auto-generated catch block
	    						e.printStackTrace();
	    					}	

	                  
	                  
	                	  return null; 
	                  case(OPERATION_USER_LOCATION):
	                  //user location
		                		try{
			                	unregisterReceiver(messageReceiver);
			                	}catch(Exception e){
			                			
			                	}
	                
	                  			try{
	                  			userlist = appService.getUserInfo(Integer.parseInt(params[1].toString()));
	                  		
	                  			if (userlist == null){
	                  				userlist = appService.getUserInfo(Integer.parseInt(params[1].toString()));
	                  			}
	                  				}catch(Exception e){
	                  			e.printStackTrace();
	                  			}//try1
	
	  	                  	  Thread thrd1 = new Thread(){
	  	                          @Override
	  	                          public void run() {
	  		                  
	  								for(int i=0;i<userlist.length();i++){						


	  									try {
	  										e = userlist.getJSONObject(i);
	  									} catch (JSONException e1) {
	  										// TODO Auto-generated catch block
	  										e1.printStackTrace();
	  									}//try2

	  									try {
	  										if ((e.getString("latitude")!=null) & (e.getString("longitude")!=null)) {	  				
	  										//lati = Double.parseDouble(e.getString("latitude"));
	  										//longi =Double.parseDouble(e.getString("longitude"));
	  										
	  											double latitude = e.getDouble("latitude");
	  											double longitude = e.getDouble("longitude");
	  											String fb_id = e.getString("fb_id"); 
	  											String user_image = "https://graph.facebook.com/" + fb_id + "/picture";
	  											String user_name=e.getString("realname");
	  											String datetime = e.getString("calculatedTime");
	  										
	  									
	  										geoPoints.add( new GeoPoint(
	  												(int) (latitude * 1E6), 
	  												(int) (longitude * 1E6)));
	  										
	  	                        			drawable = getResources().getDrawable(android.R.drawable.star_big_on);
	  	                        			itemizedOverlay = new CustomItemizedOverlay<CustomOverlayItem>(drawable, mapView);
	  									

	  										try {
	  		                        			//GeoPoint UserPoint1 = new GeoPoint((int)(latitude * 1E6),(int)(longitude * 1E6));
	  		                        			CustomOverlayItem overlayItem = new CustomOverlayItem(geoPoints.get(i), user_name, datetime, user_image);		
	  		                        			itemizedOverlay.addOverlay(overlayItem);
	  							
	  		                        			mapOverlays.add(itemizedOverlay);
	  		                        			
	  	                        				try {
	  												
	  	                        					final MapController mc = mapView.getController();
	  	                        					mc.animateTo(geoPoints.get(i));
	  	                        					mc.setZoom(16);
	  						
	  	                        					} catch(Exception ee){
	  							
	  	                        					}
	  					
	  											/*
	  										mapItemizedOverlay.addItem(geoPoints.get(i), e.getString("realname"), e.getString("calculatedTime"));
	  										mapController.animateTo(geoPoints.get(i));
	  										mapController.setZoom(10); 
	  										*/
	  											} catch (Exception ei) {
	  												// TODO Auto-generated catch block
	  												ei.printStackTrace();
	  											}//try3_1

	  	                    				
	  										
	  											}
	  										} catch (NumberFormatException e1) {
	  										// TODO Auto-generated catch block
	  											e1.printStackTrace();
	  										} catch (JSONException e1) {
	  									// TODO Auto-generated catch block
	  									e1.printStackTrace();
	  									}//try3
	  							}//for	
	  		                	  
	  	                }
	  		          };
	  		                                    
	  		      thrd1.start();  
	            				
	            				
	            				
	                return null;
	                
	                  case(OPERATION_ALL_USER_LOCTION):     
	                	try{
	                	unregisterReceiver(messageReceiver);
	                	}catch(Exception e){
	                		
	                	}
	                  
	                	try {
	  					
	                		userlist =  appService.getUserList();
	                		if (userlist == null){
	                			userlist =  appService.getUserList();
	                		}
	                	  
	                	}catch(Exception e){
	                		  e.printStackTrace();
	                	}//try1
	                	
	                  	  Thread thrd2 = new Thread(){
                          @Override
                          public void run() {
	                  
							for(int i=0;i<userlist.length();i++){						


								try {
									e = userlist.getJSONObject(i);
								} catch (JSONException e1) {
									// TODO Auto-generated catch block
									e1.printStackTrace();
								}//try2

								try {
									if ((e.getString("latitude")!=null) & (e.getString("longitude")!=null)) {	  				
									//lati = Double.parseDouble(e.getString("latitude"));
									//longi =Double.parseDouble(e.getString("longitude"));
									
										double latitude = e.getDouble("latitude");
										double longitude = e.getDouble("longitude");
										String fb_id = e.getString("fb_id"); 
										String user_image = "https://graph.facebook.com/" + fb_id + "/picture";
										String user_name=e.getString("realname");
										String datetime = e.getString("calculatedTime");
									
									
									geoPoints.add( new GeoPoint(
											(int) (latitude * 1E6), 
											(int) (longitude * 1E6)));
									
                        			drawable = getResources().getDrawable(android.R.drawable.star_big_on);
                        			itemizedOverlay = new CustomItemizedOverlay<CustomOverlayItem>(drawable, mapView);



									try {
	                        			//GeoPoint UserPoint1 = new GeoPoint((int)(latitude * 1E6),(int)(longitude * 1E6));
	                        			CustomOverlayItem overlayItem = new CustomOverlayItem(geoPoints.get(i), user_name, datetime, user_image);		
	                        			itemizedOverlay.addOverlay(overlayItem);
						
	                        			mapOverlays.add(itemizedOverlay);
	                        			
                        				try {
											
                        					final MapController mc = mapView.getController();
                        					mc.animateTo(geoPoints.get(i));
                        					mc.setZoom(16);
					
                        					} catch(Exception ee){
						
                        					}
				
										/*
									mapItemizedOverlay.addItem(geoPoints.get(i), e.getString("realname"), e.getString("calculatedTime"));
									mapController.animateTo(geoPoints.get(i));
									mapController.setZoom(10); 
									*/
										} catch (Exception ei) {
											// TODO Auto-generated catch block
											ei.printStackTrace();
										}//try3_1

                    				
									
										}
									} catch (NumberFormatException e1) {
									// TODO Auto-generated catch block
										e1.printStackTrace();
									} catch (JSONException e1) {
								// TODO Auto-generated catch block
								e1.printStackTrace();
								}//try3
						}//for	
	                	  
                }
	          };
	                                    
	      thrd2.start();  				
							
	      return null;
	                  
	                  case(OPERATION_SHOW_USER_ALL_PAST_POINT_ON_MAP): 
	      
		                	try{
			                	unregisterReceiver(messageReceiver);
			                	}catch(Exception e){
			                		
			                	} 	  
	                	  
	      				Bundle extra = getIntent().getExtras();
	  				if (extra != null) {

	  					int userid = extra.getInt("userid");

	  						try {
	  							
	  						userlist =  appService.getUserPastPoints(userid);
	  						
	  						if (userlist==null){
	  						
	  							userlist =  appService.getUserPastPoints(userid);
	  						}
	  						
		  					}catch(Exception e)        {
		  						Log.e("log_tag", "Error parsing data "+e.toString());
		  					}
	  						
	  						 Thread thrd3 = new Thread(){
	  	                          @Override
	  	                          public void run() {
	  		           
	  	                        	  
	  								for(int i=0;i<userlist.length()-15;i++){						


	  									try {
	  										e = userlist.getJSONObject(i);
	  									} catch (JSONException e1) {
	  										// TODO Auto-generated catch block
	  										e1.printStackTrace();
	  									}//try2

	  									try {
	  										if ((e.getString("latitude")!=null) & (e.getString("longitude")!=null)) {	  				
	  										//lati = Double.parseDouble(e.getString("latitude"));
	  										//longi =Double.parseDouble(e.getString("longitude"));
	  										
	  											double latitude = e.getDouble("latitude");
	  											double longitude = e.getDouble("longitude");
	  											//String fb_id = e.getString("fb_id"); 
	  											//String user_image = "https://graph.facebook.com/" + fb_id + "/picture";
	  											//String user_name=e.getString("realname");
	  											String datetime = e.getString("calculatedTime");
	  										
	  										
	  										geoPoints.add( new GeoPoint(
	  												(int) (latitude * 1E6), 
	  												(int) (longitude * 1E6)));
	  										
	  	                        			drawable = getResources().getDrawable(android.R.drawable.star_big_off);
	  	                        			itemizedOverlay = new CustomItemizedOverlay<CustomOverlayItem>(drawable, mapView);



	  										try {
	  		                        			//GeoPoint UserPoint1 = new GeoPoint((int)(latitude * 1E6),(int)(longitude * 1E6));
	  		                        			CustomOverlayItem overlayItem = new CustomOverlayItem(geoPoints.get(i), getResources().getString(R.string.user_past_point), datetime, null);		
	  		                        			itemizedOverlay.addOverlay(overlayItem);
	  							
	  		                        			mapOverlays.add(itemizedOverlay);
	  		                        			
	  	                        				try {
	  												
	  	                        					final MapController mc = mapView.getController();
	  	                        					mc.animateTo(geoPoints.get(i));
	  	                        					mc.setZoom(12);
	  						
	  	                        					} catch(Exception ee){
	  							
	  	                        					}

	  											} catch (Exception ei) {
	  												// TODO Auto-generated catch block
	  												ei.printStackTrace();
	  											}//try3_1

	  	                    				
	  										
	  											}
	  										} catch (NumberFormatException e1) {
	  										// TODO Auto-generated catch block
	  											e1.printStackTrace();
	  										} catch (JSONException e1) {
	  									// TODO Auto-generated catch block
	  									e1.printStackTrace();
	  									}//try3
	  							}//for	
	  		                	  
	  	                }
	  		          };
	  		                                    
	  		      thrd3.start();  				
	  						
	  						
	  						
	  				}
	                	  
	  			return null;
	      
	                  }//////////////////////////////////switch
	                    
	   		    
      				
      				
	                } catch (InterruptedException e) {
	                    // TODO Auto-generated catch block
	                    e.printStackTrace();
	                }//try_00
	          
	          ;
	            return null;
	      }      

	      @Override
	      protected void onPostExecute(String result) {               
	      }

	      @Override
	      protected void onPreExecute() {
	      }

	      @Override
	      protected void onProgressUpdate(Void... values) {
	      }
	}

}