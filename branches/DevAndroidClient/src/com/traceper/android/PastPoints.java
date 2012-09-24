package com.traceper.android;

import java.util.ArrayList;
import java.util.HashMap;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import android.app.ListActivity;
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
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemClickListener;
import android.widget.ListAdapter;
import android.widget.ListView;
import android.widget.SimpleAdapter;
import android.widget.Toast;

import com.traceper.R;
import com.traceper.android.interfaces.IAppService;
import com.traceper.android.services.AppService;



public class PastPoints extends ListActivity {

	private IAppService appService = null;
	protected Handler handler = new Handler();
	private ProgressDialog progressDialog;
	public static final int LIST_REFRESH = Menu.FIRST ;
	public static final int ALL_PAST_POINTS = Menu.FIRST+1;

	ListView lv;
	int userid; 


	private ServiceConnection mConnection = new ServiceConnection() 
	{
		public void onServiceConnected(ComponentName className, IBinder service) {          
			appService = ((AppService.IMBinder)service).getService();  

			String action =  getIntent().getAction();
			if (action != null) {
				if (action.equals(IAppService.SHOW_USER_PAST_POINT)) {

					Bundle extra = getIntent().getExtras();
					if (extra != null) {	

						int userid = extra.getInt("user");
						user_places(userid);
					}
				}
			}


		}
		public void onServiceDisconnected(ComponentName className) {          
			appService = null;
			Toast.makeText(PastPoints.this, R.string.local_service_stopped,
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
		bindService(new Intent(PastPoints.this, AppService.class), mConnection , Context.BIND_AUTO_CREATE);
		IntentFilter i = new IntentFilter();


	}

	@Override
	public void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		progressDialog = ProgressDialog.show(PastPoints.this, "", getString(R.string.loading), true, false);	
	}


	public void user_places(final int userid){

		setContentView(R.layout.past_point);

		final ArrayList<HashMap<String, String>> u_places = new ArrayList<HashMap<String, String>>();

		Thread thread = new Thread(){


			@Override
			public void run() {
				try{
					JSONArray  userwashere =  appService.getUserPastPoints(userid);

					for(int i=0;i<userwashere.length();i++){	

						HashMap<String, String> map = new HashMap<String, String>();
						JSONObject e = userwashere.getJSONObject(i);

						map.put("point_number", (String.valueOf(i+1) + "/" + String.valueOf(userwashere.length()-1)));
						map.put("calculatedTime", e.getString("calculatedTime"));
						map.put("latitude", e.getString("latitude"));
						map.put("longitude", e.getString("longitude"));
						u_places .add(map);
						
					}
					
					handler.post(new Runnable() {

						@Override
						public void run() {
							ListAdapter adapter = new SimpleAdapter(PastPoints.this, u_places , R.layout.user_places, 
									new String[] { "calculatedTime", "latitude" ,"longitude","point_number"}, 
									new int[] { R.id.point_time, R.id.title_1, R.id.title_2,R.id.number_point});

							setListAdapter(adapter);
							progressDialog.dismiss();
							lv = getListView();
							lv.setTextFilterEnabled(true);

							lv.setOnItemClickListener(new OnItemClickListener() {
								public void onItemClick(AdapterView<?> parent, View view, int position, long id) {        		
									@SuppressWarnings("unchecked")

									HashMap<String, String> o = (HashMap<String, String>) lv.getItemAtPosition(position);        		
									double lati =Double.valueOf(o.get("latitude"));
									double longi =Double.valueOf(o.get("longitude"));

									Intent i = new Intent(PastPoints.this, MapViewController.class);  
									i.setAction(IAppService.SHOW_USER_PAST_POINT_ON_MAP);
									i.putExtra("latitude",lati);
									i.putExtra("longitude",longi);
									startActivity(i);
								}
							});

						}
					});

				}catch(JSONException e)        {
					Log.e("log_tag", "Error parsing data "+e.toString());
				}
			}
		};
		thread.start();

	}



	@Override
	public boolean onCreateOptionsMenu(Menu menu) {		
		boolean result = super.onCreateOptionsMenu(menu);

		menu.add(0, LIST_REFRESH, 0, R.string.past_points_refresh).setIcon(R.drawable.rfrsh);

		menu.add(0, ALL_PAST_POINTS, 0, R.string.all_past_points_on_map).setIcon(android.R.drawable.ic_menu_mapmode);



		return result;
	}
	public boolean onOptionsItemSelected(MenuItem item) {

		String action =  getIntent().getAction();
		if (action != null) {
			if (action.equals(IAppService.SHOW_USER_PAST_POINT)) {

				Bundle extra = getIntent().getExtras();
				if (extra != null) {	
					userid = extra.getInt("user");
				}
			}
		}
		switch(item.getItemId()) 
		{
		case LIST_REFRESH:

			user_places(userid);


			return true;
		case ALL_PAST_POINTS:


			Intent i = new Intent(PastPoints.this, MapViewController.class);  
			i.setAction(IAppService.SHOW_USER_ALL_PAST_POINT_ON_MAP);
			i.putExtra("userid",userid);
			startActivity(i);

			return true;

		}

		return super.onOptionsItemSelected(item);
	} 

}