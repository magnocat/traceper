package com.traceper.android;

import java.io.BufferedReader;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.util.ArrayList;
import java.util.HashMap;

import org.apache.http.HttpEntity;
import org.apache.http.HttpResponse;
import org.apache.http.client.HttpClient;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.impl.client.DefaultHttpClient;
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
import android.view.View;
import android.widget.AdapterView;
import android.widget.ListAdapter;
import android.widget.ListView;
import android.widget.SimpleAdapter;
import android.widget.Toast;
import android.widget.AdapterView.OnItemClickListener;
import com.traceper.R;
import com.traceper.android.interfaces.IAppService;
import com.traceper.android.services.AppService;



public class friends extends ListActivity {
    /** Called when the activity is first created. */
	private IAppService appService = null;
	private JSONObject json;
	private ProgressDialog progressDialog;
	
	
	
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
         
       
        
       // JSONObject json = getJSONfromURL("http://192.168.43.250");
          
         json= appService.getUserList();
 		
 		
         try{
         	
         	JSONArray  userlist = json.getJSONArray("userlist");
    	
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
         final ListView lv = getListView();
         lv.setTextFilterEnabled(true);	
         lv.setOnItemClickListener(new OnItemClickListener() {
         	public void onItemClick(AdapterView<?> parent, View view, int position, long id) {        		
         		@SuppressWarnings("unchecked")
         		
 				HashMap<String, String> o = (HashMap<String, String>) lv.getItemAtPosition(position);        		
         		Toast.makeText(friends.this, "ID '" + o.get("id") + "' was clicked.", Toast.LENGTH_SHORT).show(); 

         		
         		double lati =Double.valueOf(o.get("latitude"));
         		double longi =Double.valueOf(o.get("longitude"));
         		int userid =Integer.valueOf(o.get("id"));
         		
         		  Intent i = new Intent(friends.this, MapViewController.class);  
         		  i.setAction(IAppService.SHOW_USER_LOCATION);
                  i.putExtra("userid",userid);
                  i.putExtra("latitude",lati);
                  i.putExtra("longitude",longi);
                  startActivity(i);
         		
         		
 			}
 		});
    }


    
}