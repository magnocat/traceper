package com.traceper.android;


import java.util.ArrayList;
import java.util.HashMap;

import org.json.JSONArray;
import org.json.JSONObject;

import android.app.ProgressDialog;
import android.content.ComponentName;
import android.content.Context;
import android.content.Intent;

import android.content.ServiceConnection;


import android.os.AsyncTask;
import android.os.Bundle;
import android.os.Handler;
import android.os.IBinder;
import android.support.v4.app.FragmentTransaction;

import android.util.Log;
import android.view.ContextMenu;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;

import android.widget.AdapterView;
import android.widget.ArrayAdapter;
import android.widget.ListView;



import com.actionbarsherlock.app.ActionBar;
import com.actionbarsherlock.app.ActionBar.Tab;
import com.actionbarsherlock.app.SherlockListFragment;
import com.actionbarsherlock.view.Menu;
import com.actionbarsherlock.view.MenuInflater;
import com.actionbarsherlock.view.MenuItem;
import com.traceper.R;
import com.traceper.android.interfaces.IAppService;
import com.traceper.android.services.AppService;

public class new_friendlist extends SherlockListFragment  implements ActionBar.TabListener{
	
	private IAppService appService = null;
	private JSONObject json;
	private ProgressDialog progressDialog;
	private Handler handler = new Handler();
	static final String KEY_USERNAME = "f_username"; // parent node
	static final String KEY_ID = "id";
	static final String KEY_DURATIONTIME = "Time";
	static final String KEY_LOCATION = "Latitude";
	static final String KEY_THUMB_URL = "gp_image";
	static final String KEY_USERLISTNO = "";
	ArrayList<HashMap<String, String>> frlist = new ArrayList<HashMap<String, String>>();
	ArrayAdapter<String> mAdapter ;
	
	new_FriendsListAdapter adapter;
	ListView list;
	View view;
	
	JSONArray  userlist; 
	final int CONTEXT_MENU_on_map =1;
	final int CONTEXT_MENU_past_points =2;
	
	//private static final int MENU_PREFERENCES = Menu.FIRST;
	//private static final int MENU_LOGOUT = 2;
	
	public static final int ALL_USER_MAPVIEW = Menu.FIRST;
	public static final int EXIT_APP_ID = Menu.FIRST + 1;
	public static final int LIST_REFRESH = Menu.FIRST + 2;
	
	
	 //private Adapter_CustomList adapter = null;



    
    private ServiceConnection mConnection = new ServiceConnection() 
	{
		public void onServiceConnected(ComponentName className, IBinder service) {          
			appService = ((AppService.IMBinder)service).getService();  
			//progressDialog = ProgressDialog.show(new_friendlist.this, "", getString(R.string.loading), true, false);	
			//getActivity().setContentView(R.layout.listph);
			
			//listele();
		try{	
			new LongOperation().execute("");
		}catch(Exception e)        {
			Log.e("log_tag", "Error parsing data "+e.toString());
		}
            //ListView list = getListView();
          //  list.setAdapter(adapter);
           // adapter.notifyDataSetChanged();

		}
		public void onServiceDisconnected(ComponentName className) {          
			appService = null;
			//Toast.makeText(new_friendlist.this, R.string.local_service_stopped,
				//	Toast.LENGTH_SHORT).show();
		}
	};
    
    @Override
	public void onCreate(Bundle savedInstanceState) {
		// TODO Auto-generated method stub
		super.onCreate(savedInstanceState);
		  setHasOptionsMenu(true);
		  if (savedInstanceState == null) {
			  getActivity().setContentView(R.layout.listph);
		    } else {
		      
		    }
		 
		
	}
    /*
	@Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState){
    	
		view = inflater.inflate(R.layout.f_list, container, false);
       
		adapter = new new_FriendsListAdapter(getActivity(), frlist);
		
		return view;

       //return super.onCreateView(inflater, container, savedInstanceState);
    }    
    */


    @Override   
    public void onActivityCreated(Bundle savedInstanceState) {
    	super.onActivityCreated(savedInstanceState);
		Intent intent = new Intent(getActivity(), AppService.class);
        getActivity().bindService(intent, mConnection, Context.BIND_AUTO_CREATE);
     
        
        
	    String[] items = { "One", "Two"};

	       mAdapter = new ArrayAdapter<String>(getActivity(),
	    		   R.layout.listph,
	    		   R.id.list, items);
	      setListAdapter(mAdapter);
    	
	      setListShown(false);
	      
	      registerForContextMenu(getListView());
	      
	      //getListView().setCacheColorHint(Color.WHITE);
           }
    
 
    
  /*
    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState){
        // Creating array adapter to set data in listview 
    	 mAdapter = new ArrayAdapter<String>(getActivity().getBaseContext(), R.layout.listph);

  

        return super.onCreateView(inflater, container, savedInstanceState);

    }   
    
 */

    @Override
    public void onStart() {    	
    	super.onStart();
    	
        /** Setting the multiselect choice mode for the listview */
       getListView().setChoiceMode(ListView.CHOICE_MODE_MULTIPLE);
       

    }


	@Override
	public void onTabSelected(Tab tab, FragmentTransaction ft) {
		//ft.add(android.R.id.content, this,"apple");
		
		ft.attach(this);
	}

	@Override
	public void onTabUnselected(Tab tab, FragmentTransaction ft) {
		ft.detach(this);
	//	  ft.remove(this);
	}

	@Override
	public void onTabReselected(Tab tab, FragmentTransaction ft) {
	}
	
	
	
	@Override
	public void onPause() {
		// TODO Auto-generated method stub
		
		getActivity().unbindService(mConnection);
		super.onPause();
		
	}

	@Override
	public void onResume() {
		// TODO Auto-generated method stub
		
		super.onResume();
		
	}
	
	@Override
	public void onCreateContextMenu(ContextMenu menu, View v,ContextMenu.ContextMenuInfo menuInfo) {
		menu.setHeaderTitle("Choose one"); 
		menu.setHeaderIcon(android.R.drawable.ic_menu_more);
		menu.add(0, CONTEXT_MENU_on_map, Menu.NONE, "On the Map");
		menu.add(1, CONTEXT_MENU_past_points, Menu.NONE, "Past Points");
	}
	@Override
	 public boolean onContextItemSelected(android.view.MenuItem item) {

		AdapterView.AdapterContextMenuInfo info= (AdapterView.AdapterContextMenuInfo) item.getMenuInfo();
		//  int id =int.valueOf(adapter.getItemId(info.position));/*what item was selected is ListView*/
		HashMap<String, String> o = (HashMap<String, String>) frlist.get(info.position);  
		switch (item.getItemId()) {
		case CONTEXT_MENU_on_map:

			//double lati =Double.valueOf(o.get("latitude"));
			//double longi =Double.valueOf(o.get("longitude"));
			int userid =Integer.valueOf(o.get("id"));

			Intent i = new Intent(getActivity(), MapViewController.class);  
			i.setAction(IAppService.SHOW_USER_LOCATION);
			i.putExtra("userid",userid);
			//  i.putExtra("latitude",lati);
			//  i.putExtra("longitude",longi);
			startActivity(i);

			
			
			
			return(true);
		case CONTEXT_MENU_past_points:

			try{
				int user =Integer.valueOf(o.get("id"));
				Intent i1 = new Intent(getActivity(), PastPoints.class);  
				i1.setAction(IAppService.SHOW_USER_PAST_POINT);
				i1.putExtra("user",user);
				startActivity(i1);

			}catch(Exception e)        {
				Log.e("log_tag", "Error parsing data "+e.toString());
			}
			return(true);    
		}
		 return super.onContextItemSelected(item);
	}

	
    @Override
    public void onCreateOptionsMenu(Menu menu, MenuInflater inflater) {
    	// inflater.inflate(R.menu.friend_list_menu, menu);
    	super.onCreateOptionsMenu(menu, inflater);
        //menu.add("Menu 1a").setShowAsAction(MenuItem.SHOW_AS_ACTION_IF_ROOM) ;
       // menu.add("Menu 1b").setShowAsAction(MenuItem.SHOW_AS_ACTION_IF_ROOM);
        menu.add(2, LIST_REFRESH, 0, getString(R.string.list_refresh)).setIcon(
                android.R.drawable.ic_menu_preferences);
    	menu.add(0, ALL_USER_MAPVIEW, 0, getString(R.string.show_all_user_location_on_map)).setIcon(
                android.R.drawable.ic_menu_upload);
        menu.add(1, EXIT_APP_ID, 0, getString(R.string.exit_application)).setIcon(
                android.R.drawable.ic_menu_preferences);
      

    	 
    	   
    	    
    	
    }
    @Override
    public boolean onOptionsItemSelected(MenuItem item)
    {
       
		switch(item.getItemId()) 
		{
		case LIST_REFRESH:
			listele();

			return true;
		case ALL_USER_MAPVIEW:
			Intent i = new Intent(getActivity(), MapViewController.class);  
			i.setAction(IAppService.SHOW_ALL_USER_LOCATIONS);
			startActivity(i);
			
			return true;
		case EXIT_APP_ID:
			//finish();

			return true;
		}

		return super.onOptionsItemSelected(item);

    }
    
    
	private class LongOperation extends AsyncTask<String, Void, String> {

	      @Override
	      protected String doInBackground(String... params) {
	            
	                try {
	                   // Thread.sleep(1000);
	            		frlist.clear();
	            		/*
	            	    String[] items = { "One", "Two"};

	            	       mAdapter = new ArrayAdapter<String>(getActivity(),
	            	    		   R.layout.listph,
	            	    		   R.id.list, items);
	            	      setListAdapter(mAdapter);
	             	
	            	      setListShown(false);
	            	      
	            		*/
	            		Thread friendListThread = new Thread(){
	            			@Override
	            			public void run() {
	            			userlist =  appService.getUserList();
	            					
	            						if (userlist == null){
	            							
	            							userlist =  appService.getUserList();
	            							//progressDialog.dismiss();
	            							//finish();
	            						}
	            				
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

	            								fri.put(KEY_USERLISTNO, (String.valueOf(i+1) + "/" + String.valueOf(userlist.length())));
	            								fri.put(KEY_ID,  e.getString("user"));
	            								fri.put(KEY_USERNAME, e.getString("realname"));
	            								fri.put(KEY_LOCATION, "Lati= " + e.getString("latitude") + " Longi= " + e.getString("longitude"));
	            								fri.put(KEY_DURATIONTIME, e.getString("time"));
	            								fri.put(KEY_THUMB_URL, getimage);
	            								frlist.add(fri);			
	            							}
	            							

	            						//	String[] array = frlist.toArray(new String[frlist.size()]);
	            							   // adapter.clear();
	            						        //adapter.addAll(array);
	            						       // adapter.notifyDataSetChanged();
	            				        	
	            							//
	            							
	            						   adapter = new new_FriendsListAdapter(getActivity(), frlist);
	            						   setListAdapter(adapter);
	            						   setListShown(true);
	            				           adapter.notifyDataSetChanged();	
	            				           
	            							// Getting adapter by passing xml data ArrayList
	            							//adapter=new FriendsListAdapter(FriendList.this, frlist);  
	            				        	
	            				          
	            						
	            							
	            							  //adapter = new new_FriendsListAdapter(getActivity(), frlist);
	            						      //list.setAdapter(adapter);
	            				           
	            				         
	            							
	            						}
	            						catch (Exception e) {
	            							System.out.println("Exception in friends json");
	            							
	            						}
	            						
	            					}
	            				});
	            				
	            			}
	            		};
	            		
	            		friendListThread.start();
	                	
	                	
	                
	                } catch (Exception e) {
	                    // TODO Auto-generated catch block
	                    e.printStackTrace();
	                }
	           
	           
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
    
    
    
	public void listele(){

	
	
		
	}
	
	
	
	
}