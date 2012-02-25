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
import android.os.IBinder;
import android.util.Log;
import android.view.ContextMenu;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.widget.AdapterView;
import android.widget.ListAdapter;
import android.widget.ListView;
import android.widget.SimpleAdapter;
import android.widget.Toast;

import com.traceper.R;
import com.traceper.android.interfaces.IAppService;
import com.traceper.android.services.AppService;



public class friends extends ListActivity {

	private IAppService appService = null;
	private JSONObject json;
	private ProgressDialog progressDialog;
	public static final int ALL_USER_MAPVIEW = Menu.FIRST;
	public static final int EXIT_APP_ID = Menu.FIRST + 1;
	public static final int LIST_REFRESH = Menu.FIRST + 2;
	
	final int CONTEXT_MENU_on_map =1;
	final int CONTEXT_MENU_past_points =2;
	
	ListView lv;
	
	
	
	private ServiceConnection mConnection = new ServiceConnection() 
	{
		public void onServiceConnected(ComponentName className, IBinder service) {          
			appService = ((AppService.IMBinder)service).getService();  
			
			listele();

		}
		public void onServiceDisconnected(ComponentName className) {          
			appService = null;
			Toast.makeText(friends.this, R.string.local_service_stopped,
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
		bindService(new Intent(friends.this, AppService.class), mConnection , Context.BIND_AUTO_CREATE);
		IntentFilter i = new IntentFilter();
		i.addAction(IAppService.LAST_LOCATION_DATA_SENT_TIME);
			
	}
	
    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        progressDialog = ProgressDialog.show(friends.this, "", getString(R.string.loading), true, false);	
    }
    
    public void listele(){
    	 setContentView(R.layout.listph);
         
         ArrayList<HashMap<String, String>> mylist = new ArrayList<HashMap<String, String>>();
         
 		
         try{
         	
         	JSONArray  userlist =  appService.getUserList();
    	
 	        for(int i=0;i<userlist.length();i++){						
 				HashMap<String, String> fri = new HashMap<String, String>();	
 				JSONObject e = userlist.getJSONObject(i);
 				
 				fri.put("id",  e.getString("user"));
 	        	fri.put("realname", "User Name:" + e.getString("realname"));
 	        	fri.put("latitude",  e.getString("latitude"));
 	        	fri.put("longitude", e.getString("longitude"));
 	        	mylist.add(fri);			
 			}	
 	        
         }catch(JSONException e)        {
         	 Log.e("log_tag", "Error parsing data "+e.toString());
         }
         
         ListAdapter adapter = new SimpleAdapter(this, mylist , R.layout.f_list, 
                         new String[] { "realname", "latitude" ,"longitude"}, 
                         new int[] { R.id.item_title, R.id.item_subtitle , R.id.item_subtitle_2 });
         
         setListAdapter(adapter);
         progressDialog.dismiss();
         lv = getListView();
         lv.setTextFilterEnabled(true);	
         registerForContextMenu(lv);

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
         Long id = getListAdapter().getItemId(info.position);/*what item was selected is ListView*/
         HashMap<String, String> o = (HashMap<String, String>) lv.getItemAtPosition(info.position);  
         switch (item.getItemId()) {
                 case CONTEXT_MENU_on_map:
       		
                 		double lati =Double.valueOf(o.get("latitude"));
                  		double longi =Double.valueOf(o.get("longitude"));
                  		int userid =Integer.valueOf(o.get("id"));
                  		
                  		  Intent i = new Intent(friends.this, MapViewController.class);  
                  		  i.setAction(IAppService.SHOW_USER_LOCATION);
                           i.putExtra("userid",userid);
                           i.putExtra("latitude",lati);
                           i.putExtra("longitude",longi);
                           startActivity(i);
                     
                      return(true);
                case CONTEXT_MENU_past_points:
                	
               		int user =Integer.valueOf(o.get("id"));

             		  Intent i1 = new Intent(friends.this, PastPoints.class);  
              		  i1.setAction(IAppService.SHOW_USER_PAST_POINT);
                      i1.putExtra("user",user);
                      startActivity(i1);

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
	    		Intent i = new Intent(friends.this, MapViewController.class);  
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