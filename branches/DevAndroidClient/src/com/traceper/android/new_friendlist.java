package com.traceper.android;


import java.util.ArrayList;
import java.util.HashMap;

import org.json.JSONArray;
import org.json.JSONObject;

import android.app.ProgressDialog;
import android.content.ComponentName;
import android.content.Context;
import android.content.Intent;
import android.content.IntentFilter;
import android.content.ServiceConnection;
import android.os.AsyncTask;
import android.os.Bundle;
import android.os.Handler;
import android.os.IBinder;
import android.support.v4.app.FragmentTransaction;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.ListView;
import android.widget.Toast;

import com.actionbarsherlock.app.ActionBar;
import com.actionbarsherlock.app.ActionBar.Tab;
import com.actionbarsherlock.app.SherlockListFragment;
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
	
	new_FriendsListAdapter adapter;
	ListView list;
	View view;
	


    
    private ServiceConnection mConnection = new ServiceConnection() 
	{
		public void onServiceConnected(ComponentName className, IBinder service) {          
			appService = ((AppService.IMBinder)service).getService();  
			//progressDialog = ProgressDialog.show(new_friendlist.this, "", getString(R.string.loading), true, false);	
			listele();
			
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
		
		
		
	}
	@Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState){
    	
		view = inflater.inflate(R.layout.f_list, container, false);
       
		adapter = new new_FriendsListAdapter(getActivity(), frlist);
		
		return view;

       //return super.onCreateView(inflater, container, savedInstanceState);
    }    
    @Override   
    public void onActivityCreated(Bundle savedInstanceState) {
    	super.onActivityCreated(savedInstanceState);
		Intent intent = new Intent(getActivity(), AppService.class);
        getActivity().bindService(intent, mConnection, Context.BIND_AUTO_CREATE);
    	
           }
    
   
    
    
    
    @Override
    public void onStart() {    	
    	super.onStart();
    	
        /** Setting the multiselect choice mode for the listview */
        getListView().setChoiceMode(ListView.CHOICE_MODE_MULTIPLE);

    }

	@Override
	public void onTabSelected(Tab tab, FragmentTransaction ft) {
		ft.add(android.R.id.content, this,"apple");
		
		ft.attach(this);
	}

	@Override
	public void onTabUnselected(Tab tab, FragmentTransaction ft) {
		ft.detach(this);
	}

	@Override
	public void onTabReselected(Tab tab, FragmentTransaction ft) {
	}
	
	
	
	@Override
	public void onPause() {
		// TODO Auto-generated method stub
		super.onPause();
	}

	@Override
	public void onResume() {
		// TODO Auto-generated method stub
		super.onResume();
		
		setListAdapter(adapter);
        

		
	}

	public void listele(){
		frlist.clear();

		Thread friendListThread = new Thread(){
			@Override
			public void run() {
				final JSONArray  userlist =  appService.getUserList();
					
						if (userlist == null){
							progressDialog.dismiss();
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

								fri.put(KEY_USERLISTNO, (String.valueOf(i+1) + "/" + String.valueOf(userlist.length()-1)));
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
							
				        	
				        	
				          //  adapter.notifyDataSetChanged();	
							// Getting adapter by passing xml data ArrayList
							//adapter=new FriendsListAdapter(FriendList.this, frlist);  
				        	
				          
						
							
							  adapter = new new_FriendsListAdapter(getActivity(), frlist);
						      list.setAdapter(adapter);
		

							
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
	
}