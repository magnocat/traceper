package com.traceper.android;

import java.util.ArrayList;
import java.util.HashMap;

import org.json.JSONArray;
import org.json.JSONObject;

import android.app.Activity;
import android.app.ProgressDialog;
import android.content.ComponentName;
import android.content.Context;
import android.content.Intent;
import android.content.IntentFilter;
import android.content.ServiceConnection;
import android.os.Bundle;
import android.os.Handler;
import android.os.IBinder;
import android.util.Log;
import android.view.ContextMenu;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.widget.AdapterView;
import android.widget.ListView;
import android.widget.Toast;

import com.traceper.R;
import com.traceper.android.interfaces.IAppService;
import com.traceper.android.services.AppService;



public class FriendList extends Activity {

	private IAppService appService = null;
	private JSONObject json;
	private ProgressDialog progressDialog;
	private Handler handler = new Handler();
	public static final int ALL_USER_MAPVIEW = Menu.FIRST;
	public static final int EXIT_APP_ID = Menu.FIRST + 1;
	public static final int LIST_REFRESH = Menu.FIRST + 2;

	final int CONTEXT_MENU_on_map =1;
	final int CONTEXT_MENU_past_points =2;

	static final String KEY_USERNAME = "f_username"; // parent node
	static final String KEY_ID = "id";
	static final String KEY_DURATIONTIME = "Time";
	static final String KEY_LOCATION = "Latitude";
	static final String KEY_THUMB_URL = "gp_image";
	static final String KEY_USERLISTNO = "";
	ArrayList<HashMap<String, String>> frlist = new ArrayList<HashMap<String, String>>();

	ListView list;
	FriendsListAdapter adapter;


	private ServiceConnection mConnection = new ServiceConnection() 
	{
		public void onServiceConnected(ComponentName className, IBinder service) {          
			appService = ((AppService.IMBinder)service).getService();  
			progressDialog = ProgressDialog.show(FriendList.this, "", getString(R.string.loading), true, false);	
			listele();

		}
		public void onServiceDisconnected(ComponentName className) {          
			appService = null;
			Toast.makeText(FriendList.this, R.string.local_service_stopped,
					Toast.LENGTH_SHORT).show();
		}
	};

	@Override
	protected void onPause() 
	{

		unbindService(mConnection);
		super.onPause();
	}

	@Override
	protected void onResume() 
	{			
		super.onResume();
		bindService(new Intent(FriendList.this, AppService.class), mConnection , Context.BIND_AUTO_CREATE);
		IntentFilter i = new IntentFilter();
		i.addAction(IAppService.LAST_LOCATION_DATA_SENT_TIME);

	}

	@Override
	public void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.listph);


	}

	public void listele(){
		frlist.clear();

		Thread friendListThread = new Thread(){
			@Override
			public void run() {
				final JSONArray  userlist =  appService.getUserList();

				
				handler.post(new Runnable() {
					
					@Override
					public void run() {
						try {
							for(int i=0;i<userlist.length();i++){						
								HashMap<String, String> fri = new HashMap<String, String>();	
								JSONObject e = userlist.getJSONObject(i);
								String getimage = "";

								if (e.getInt("account_type")==1){
									String faceid = e.getString("fb_id");
									getimage = "https://graph.facebook.com/" + faceid + "/picture";
								}
								else if (e.getInt("account_type")==2){
									getimage = e.getString("gp_image");
								}

								fri.put(KEY_USERLISTNO, (String.valueOf(i+1) + "/" + String.valueOf(userlist.length()-1)));
								fri.put(KEY_ID,  e.getString("user"));
								fri.put(KEY_USERNAME, e.getString("realname"));
								fri.put(KEY_LOCATION, "Lati= " + e.getString("latitude") + " Longi= " + e.getString("longitude"));
								fri.put(KEY_DURATIONTIME, e.getString("time"));
								fri.put(KEY_THUMB_URL, getimage);
								frlist.add(fri);			
							}
							
							list=(ListView)findViewById(R.id.list);

							// Getting adapter by passing xml data ArrayList
							adapter=new FriendsListAdapter(FriendList.this, frlist);  

							list.setAdapter(null);
							list.setAdapter(adapter);

							registerForContextMenu(list);


							progressDialog.dismiss();
							
						}
						catch (Exception e) {
							System.out.println("Exception in friends json");
						}
						
					}
				});
				
			}
		};
		
		friendListThread.start();

		
	}

	@Override
	public void onCreateContextMenu(ContextMenu menu, View v,ContextMenu.ContextMenuInfo menuInfo) {
		menu.setHeaderTitle("Choose one"); 
		menu.setHeaderIcon(android.R.drawable.ic_menu_more);
		menu.add(0, CONTEXT_MENU_on_map, Menu.NONE, "On the Map");
		menu.add(1, CONTEXT_MENU_past_points, Menu.NONE, "Past Points");
	}
	@Override
	public boolean onContextItemSelected(MenuItem item) {

		AdapterView.AdapterContextMenuInfo info= (AdapterView.AdapterContextMenuInfo) item.getMenuInfo();
		//  int id =int.valueOf(adapter.getItemId(info.position));/*what item was selected is ListView*/
		HashMap<String, String> o = (HashMap<String, String>) frlist.get(info.position);  
		switch (item.getItemId()) {
		case CONTEXT_MENU_on_map:

			//double lati =Double.valueOf(o.get("latitude"));
			//double longi =Double.valueOf(o.get("longitude"));
			int userid =Integer.valueOf(o.get("id"));

			Intent i = new Intent(FriendList.this, MapViewController.class);  
			i.setAction(IAppService.SHOW_USER_LOCATION);
			i.putExtra("userid",userid);
			//  i.putExtra("latitude",lati);
			//  i.putExtra("longitude",longi);
			startActivity(i);

			return(true);
		case CONTEXT_MENU_past_points:

			try{
				int user =Integer.valueOf(o.get("id"));
				Intent i1 = new Intent(FriendList.this, PastPoints.class);  
				i1.setAction(IAppService.SHOW_USER_PAST_POINT);
				i1.putExtra("user",user);
				startActivity(i1);

			}catch(Exception e)        {
				Log.e("log_tag", "Error parsing data "+e.toString());
			}
			return(true);    
		}
		return(super.onOptionsItemSelected(item));
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {		
		boolean result = super.onCreateOptionsMenu(menu);

		menu.add(0, LIST_REFRESH, 0, R.string.list_refresh).setIcon(R.drawable.rfrsh);

		menu.add(0, ALL_USER_MAPVIEW, 0, R.string.show_all_user_location_on_map).setIcon(R.drawable.users);

		menu.add(0, EXIT_APP_ID, 0, R.string.exit_application).setIcon(R.drawable.power);

		return result;
	}
	public boolean onOptionsItemSelected(MenuItem item) {

		switch(item.getItemId()) 
		{
		case LIST_REFRESH:
			listele();

			return true;
		case ALL_USER_MAPVIEW:
			Intent i = new Intent(FriendList.this, MapViewController.class);  
			i.setAction(IAppService.SHOW_ALL_USER_LOCATIONS);
			startActivity(i);

			return true;
		case EXIT_APP_ID:
			finish();

			return true;
		}

		return super.onOptionsItemSelected(item);
	} 

}