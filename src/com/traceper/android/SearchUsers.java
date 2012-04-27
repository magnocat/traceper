package com.traceper.android;

import java.util.ArrayList;
import java.util.HashMap;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import android.app.Activity;
import android.app.AlertDialog;
import android.app.ProgressDialog;
import android.content.BroadcastReceiver;
import android.content.ComponentName;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.IntentFilter;
import android.content.ServiceConnection;
import android.graphics.drawable.Drawable;
import android.os.Bundle;
import android.os.IBinder;
import android.text.Editable;
import android.util.Log;
import android.view.ContextMenu;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.widget.AdapterView;
import android.widget.EditText;
import android.widget.ListView;
import android.widget.Toast;

import com.google.android.maps.GeoPoint;
import com.google.android.maps.MapView;
import com.traceper.R;
import com.traceper.android.MapViewController.MessageReceiver;
import com.traceper.android.interfaces.IAppService;
import com.traceper.android.services.AppService;

public class SearchUsers extends Activity {

	private IAppService appService = null;
	private JSONObject json;
	private ProgressDialog progressDialog;
	public static final int SEARCH_USERS = Menu.FIRST;
	public static final int TURN_BACK = Menu.FIRST + 1;
	public static final int INVITATION_LIST = Menu.FIRST + 2;
	
	final int CONTEXT_MENU_add =1;
	final int CONTEXT_MENU_delete =2;
	
	static final String KEY_USERNAME = "f_username"; // parent node
	static final String KEY_ID = "id";
	static final String KEY_DURATIONTIME = "Time";
	static final String KEY_LOCATION = "Latitude";
	static final String KEY_THUMB_URL = "gp_image";
	static final String KEY_STATUS ="status";
	static final String KEY_USERLISTNO = "";
	static final String KEY_INVITATION_NO = "friendShipId";
	public static  String search = "";
	public static JSONArray userlist = null;
	
	ArrayList<HashMap<String, String>> frlist = new ArrayList<HashMap<String, String>>();
	
	ListView list;
	SearchListAdapter adapter;

	

	private ServiceConnection mConnection = new ServiceConnection() 
	{
		public void onServiceConnected(ComponentName className, IBinder service) {          
			appService = ((AppService.IMBinder)service).getService();  
			String action =  getIntent().getAction();
			if (action != null) {
				if (action.equals(IAppService.SHOW_USER_SEARCH_LIST)) {
					progressDialog = ProgressDialog.show(SearchUsers.this, "", getString(R.string.loading), true, false);		
					inputfriends();
				}
				if (action.equals(IAppService.SHOW_USER_INVITATION_LIST)) {
					progressDialog = ProgressDialog.show(SearchUsers.this, "", getString(R.string.loading), true, false);		
					invitationfriends();
				}
			}

		}
		public void onServiceDisconnected(ComponentName className) {          
			appService = null;
			Toast.makeText(SearchUsers.this, R.string.local_service_stopped,
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
		bindService(new Intent(SearchUsers.this, AppService.class), mConnection , Context.BIND_AUTO_CREATE);
		IntentFilter i = new IntentFilter();

			
	}
	

    
    public void inputfriends(){
    	AlertDialog.Builder alert = new AlertDialog.Builder(this);

    	alert.setTitle("Search Users");
    	//alert.setMessage("Search");
    	

    	// Set an EditText view to get user input 
    	final EditText input = new EditText(this);
    	alert.setView(input);

    	alert.setPositiveButton("Search", new DialogInterface.OnClickListener() {
    	public void onClick(DialogInterface dialog, int whichButton) {
    	  Editable value = input.getText();
    	  // Do something with value!
    	 
    	 search=value.toString();
    	 userlist =  appService.SearchJSON(value.toString());
    	  
    	  if (userlist.length() != 0 ){
    	  listele();
    	  }else{
    		  progressDialog.dismiss();
    		  Toast.makeText(getBaseContext(),search + " " + getString(R.string.search_failed), Toast.LENGTH_LONG).show(); 
    		  inputfriends();
    	  }
    	  
    	  }
    	});

    	alert.setNegativeButton("Cancel", new DialogInterface.OnClickListener() {
    	  public void onClick(DialogInterface dialog, int whichButton) {
    			Intent i = new Intent(SearchUsers.this, Main.class);
      			startActivity(i);
      			SearchUsers.this.finish();
    	  }
    	});

    	alert.show();
    	

    }
    
    public void invitationfriends(){
    	
    	 
    	
    	 userlist =  appService.GetFriendRequestListJson();
    	  
    	  if (userlist.length() != 0){
    	  listele();
    	  }else{
    		  progressDialog.dismiss();
    		  Toast.makeText(getBaseContext(),search + " " + getString(R.string.couldnt_find), Toast.LENGTH_LONG).show(); 
    			Intent i = new Intent(SearchUsers.this, Main.class);
      			startActivity(i);
      			SearchUsers.this.finish();
    	  }
    
    	
    }
    
    public void memorysearch(){
   	 userlist =  appService.SearchJSON(search);
	  
	  if (userlist != null){
	  listele();
	  }
    }
    public void listele(){
    	 setContentView(R.layout.listph);
         
    	 frlist.clear();
    	 		
         try{
    	
 	        for(int i=0;i<userlist.length();i++){						
 				HashMap<String, String> fri = new HashMap<String, String>();	
 				JSONObject e = userlist.getJSONObject(i);
 				String getimage = "";
 				String isfriend ="";
 				
 				if (e.getInt("account_type")==1){
 					String faceid = e.getString("fb_id");
 					getimage = "https://graph.facebook.com/" + faceid + "/picture";
 				}
 				else if (e.getInt("account_type")==2){
 					getimage = e.getString("gp_image");
 				}
 				// get friend 
 				if (e.getInt("status")==0){
 					isfriend = "0";
 				}
 				else if (e.getInt("status")==1){
 					isfriend = "1";
 				}
 				else if(e.getInt("status")==-1){
 					isfriend = "-1";
 				}
 				
 				fri.put(KEY_USERLISTNO, (String.valueOf(i+1) + "/" + String.valueOf(userlist.length()-1)));
 				fri.put(KEY_ID,  e.getString("id"));
 	        	fri.put(KEY_USERNAME, e.getString("Name"));
 	        	fri.put(KEY_LOCATION, "");
 	        	fri.put(KEY_DURATIONTIME, "");
 	        	fri.put(KEY_THUMB_URL, getimage);
 	        	fri.put(KEY_STATUS, isfriend);
 	        	fri.put(KEY_INVITATION_NO, e.getString("friendShipId"));
 	        	
 	        	frlist.add(fri);			
 			}	
 	        
         }catch(JSONException e)        {
         	 Log.e("log_tag", "Error parsing data "+e.toString());
         }
         
         list=(ListView)findViewById(R.id.list);
 	
 		// Getting adapter by passing xml data ArrayList
         adapter=new SearchListAdapter(this, frlist);  


         
         list.setAdapter(null);
         list.setAdapter(adapter);
      
         registerForContextMenu(list);
      
        
         progressDialog.dismiss();
    }

    @Override
    public void onCreateContextMenu(ContextMenu menu, View v,ContextMenu.ContextMenuInfo menuInfo) {
     menu.setHeaderTitle("Choose one"); 
     menu.setHeaderIcon(android.R.drawable.ic_menu_more);
     menu.add(0, CONTEXT_MENU_add, Menu.NONE, "Add");
     menu.add(1, CONTEXT_MENU_delete, Menu.NONE, "Delete");
    }
    @Override
    public boolean onContextItemSelected(MenuItem item) {
    	 
         AdapterView.AdapterContextMenuInfo info= (AdapterView.AdapterContextMenuInfo) item.getMenuInfo();
       //  int id =int.valueOf(adapter.getItemId(info.position));/*what item was selected is ListView*/
         HashMap<String, String> o = (HashMap<String, String>) frlist.get(info.position);  
         switch (item.getItemId()) {
                 case CONTEXT_MENU_add:
                	
             		String action =  getIntent().getAction();
        			if (action != null) {
        				if (action.equals(IAppService.SHOW_USER_SEARCH_LIST)) {
                	 
                	 Integer result = null; 
           
                //	progressDialog = ProgressDialog.show(SearchUsers.this, "", getString(R.string.loading), true, false); 
                	result = Integer.parseInt(appService.AddAsFriend(o.get("id")).toString()); 
                	 
                	if ( result == 1){
                		memorysearch();
                	}else{
                		
                	}
        		}
        				
        		 if (action.equals(IAppService.SHOW_USER_INVITATION_LIST)) {
        				
        			 Integer result = null; 
        	           
                     //	progressDialog = ProgressDialog.show(SearchUsers.this, "", getString(R.string.loading), true, false); 
                     	result = Integer.parseInt(appService.approveFriendShip(o.get("friendShipId")).toString()); 
                     	 
                     	if ( result == 1){
                     		memorysearch();
                     	}else{
                     		
                     	} 
        			 
        		}
        				
        				
        				
        	}		
                      return(true);
                case CONTEXT_MENU_delete:
                	
                	try{
             		  int user =Integer.valueOf(o.get("id"));
             		  Intent i1 = new Intent(SearchUsers.this, PastPoints.class);  
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
	
		menu.add(0, TURN_BACK, 0, R.string.turn_back).setIcon(R.drawable.rotate);
		menu.add(0, SEARCH_USERS, 0, R.string.search_users).setIcon(R.drawable.search);
		menu.add(0, INVITATION_LIST, 0, R.string.friendship_requests).setIcon(R.drawable.users);
		 
		return result;
	}
	public boolean onOptionsItemSelected(MenuItem item) {
	    
		switch(item.getItemId()) 
	    {
	    	case SEARCH_USERS:
	    		inputfriends();
   		
    		return true;
	    	case TURN_BACK:
	    		Intent i = new Intent(SearchUsers.this, Main.class);  
                startActivity(i);
       		
	    		return true;
	    	case INVITATION_LIST:
	    		invitationfriends();
				
	    		return true;
	    }
	       
	    return super.onOptionsItemSelected(item);
	} 
    
}
