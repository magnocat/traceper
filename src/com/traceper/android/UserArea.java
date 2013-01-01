package com.traceper.android;



import java.text.DateFormat;
import java.text.SimpleDateFormat;
import java.util.Date;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import com.actionbarsherlock.app.ActionBar;

import com.actionbarsherlock.app.ActionBar.Tab;
import com.actionbarsherlock.app.SherlockFragment;
import com.actionbarsherlock.app.SherlockFragmentActivity;
import com.actionbarsherlock.view.Menu;
import com.actionbarsherlock.view.MenuInflater;
import com.actionbarsherlock.view.MenuItem;


import com.traceper.R;

import com.traceper.android.interfaces.IAppService;
import com.traceper.android.list.ImageLoader;
import com.traceper.android.services.AppService;


import android.content.ComponentName;
import android.content.Context;
import android.content.Intent;
import android.content.ServiceConnection;
import android.content.SharedPreferences;
import android.net.ParseException;
import android.os.AsyncTask;
import android.os.Bundle;
import android.os.IBinder;
import android.support.v4.app.FragmentTransaction;

import android.util.Log;
import android.view.View;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.Toast;


import com.androidquery.AQuery;



public class UserArea extends SherlockFragment implements ActionBar.TabListener {

	
	private IAppService appService = null;


	private final String TAG = getClass().getName();
	private AQuery aq;
	protected SherlockFragmentActivity activity;
	
	final int OPERATION_USERINFO=1;
	
	JSONArray userinfo;
	public ImageLoader imageLoader; 
	boolean status = false;
	ImageView image;
	private EditText filterText; 
	
    private ServiceConnection mConnection = new ServiceConnection() 
	{
		public void onServiceConnected(ComponentName className, IBinder service) {          
			appService = ((AppService.IMBinder)service).getService();  
			//progressDialog = ProgressDialog.show(new_friendlist.this, "", getString(R.string.loading), true, false);
			
			Long dt = appService.getLastLocationSentTime();
			
			if (dt != null) {

				try {
					
					aq.find(R.id.txt_user_i).text((getFormattedDate(dt)));
				} catch (Exception e) {
					// TODO Auto-generated catch block
					e.printStackTrace();
				}
			}
			
			try{

				new LongOperation().execute(new String[] {String.valueOf(OPERATION_USERINFO)});

			}catch(Exception e)        {
				Log.e("log_tag", "Error parsing data "+e.toString());
			}
	
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
	
		
	
		
		
		
	}
	@Override
	public void onActivityCreated(Bundle savedInstanceState) {
		// TODO Auto-generated method stub
		super.onActivityCreated(savedInstanceState);
		activity = getSherlockActivity();
		aq = new AQuery(activity);
		activity.setContentView(R.layout.activity_user_area);
		ActionBar actionBar = getSherlockActivity().getSupportActionBar();
		actionBar.setDisplayShowTitleEnabled(true);
		actionBar.setDisplayHomeAsUpEnabled(true);
		initButtons();
		Intent intent = new Intent(getActivity(), AppService.class);
        getActivity().bindService(intent, mConnection, Context.BIND_AUTO_CREATE);
        setHasOptionsMenu(true);
        SharedPreferences preferences = activity.getSharedPreferences(Configuration.PREFERENCES_NAME, 0);
        status = preferences.getBoolean(Configuration.PREFRENCES_AUTO_SEND_CHECKBOX, false);
        image = (ImageView) getSherlockActivity().findViewById(R.id.image_auto);
        if (status == true){
        	image.setImageResource(android.R.drawable.btn_star_big_on);	
        }else{
        	image.setImageResource(android.R.drawable.btn_star_big_off);	
        }
        
        
        
        
	}
	
	public String getFormattedDate(Long time) 
	{
		//DateFormat df = new SimpleDateFormat("dd MMM yyyy HH:mm");
		SimpleDateFormat fmtOut = new SimpleDateFormat("dd/MM/yyyy hh:mm:ss");
		return fmtOut.format(time);
	}

	@Override
	public void onPause() {
		// TODO Auto-generated method stub
		super.onPause();
		getActivity().unbindService(mConnection);
		//getActivity().setContentView(null);
		
	}


	

	
    @Override
    public void onCreateOptionsMenu(Menu menu, MenuInflater inflater) {
        super.onCreateOptionsMenu(menu, inflater);

        inflater.inflate(R.menu.activity_user_area, menu);
    }

	
	@Override
	public boolean onOptionsItemSelected(MenuItem item) {
		// TODO Auto-generated method stub
		switch(item.getItemId()){
		case R.id.auto_checkin:
			
			//SharedPreferences.Editor editor = activity.getSharedPreferences(Configuration.PREFERENCES_NAME,0).edit();
			//editor.putBoolean(Configuration.PREFRENCES_AUTO_SEND_CHECKBOX,isChecked);
			//editor.commit();
			
			if (status == false) { 
				appService.setAutoCheckin(true);
				Toast.makeText(activity.getBaseContext(), "Auto sending is ready!", Toast.LENGTH_SHORT).show(); 
				status = true;
			} 
			else 
			{ 
				appService.setAutoCheckin(false);
				Toast.makeText(activity.getBaseContext(), "Auto sending is disabled!", Toast.LENGTH_SHORT).show(); 
				status = false;
			} 
			
	        if (status == true){
	        	image.setImageResource(android.R.drawable.btn_star_big_on);	
	        }else{
	        	image.setImageResource(android.R.drawable.btn_star_big_off);	
	        }
			
			break;

			
			
			
			}
			
		
		return super.onOptionsItemSelected(item);
	}
	

	
	protected void initButtons() {
		// Define the video button
		aq.find(R.id.btn_video).clicked(new View.OnClickListener() {
			@Override
			public void onClick(View v) {
				Intent intent = new Intent(activity, CameraController.class);
				//intent.putExtra("user", Utility.getInstance().userInfo);
				startActivity(intent);
			}
		});
		// Define the picture button
		aq.find(R.id.btn_picture).clicked(new View.OnClickListener() {
			@Override
			public void onClick(View v) {
				Intent intent = new Intent(activity, CameraController.class);
				//intent.putExtra("user", Utility.getInstance().userInfo);
				startActivity(intent);
			}
		});
		
		// Define the check-in button
		aq.find(R.id.btn_checkin).clicked(new View.OnClickListener() {
			@Override
			public void onClick(View v) {
				//startActivity(new Intent(activity, BaseFragmentActivity.class).putExtra("fragment",
				//	ActionHistoryFragment.class.getName()));
				appService.sendLocationNow();
					
			}
		});
	
		// Define the pastpoint button
		aq.find(R.id.btn_pastpoint).clicked(new View.OnClickListener() {
			@Override
			public void onClick(View v) {
			
				int user =Integer.valueOf(appService.getUserId());
				Intent i1 = new Intent(activity, PastPoints.class);  
				i1.setAction(IAppService.SHOW_USER_PAST_POINT);
				i1.putExtra("user",user);
				startActivity(i1);
				//onPause();
			}
		});
			
		// Define the setting button
		aq.find(R.id.btn_setting).clicked(new View.OnClickListener() {
			@Override
			public void onClick(View v) {
		
				Intent i = new Intent(activity, SearchUsers.class);
		 		i.setAction(IAppService.SHOW_USER_SEARCH_LIST);
				startActivity(i);	
				
			}
		});
		// Define the exit button
		aq.find(R.id.btn_exit).clicked(new View.OnClickListener() {
			@Override
			public void onClick(View v) {
			getActivity().finish();
			}
		});
		
		
	};

	
	@Override
	public void onResume() {
		// TODO Auto-generated method stub
		
		super.onResume();


		
	}
	
	protected void updateViews() {
		
		//User userInfo = Utility.getInstance().userInfo;
		try {
			aq.find(R.id.txt_username).text(userinfo.getJSONObject(0).getString("realname"));
		
			
			String faceid = userinfo.getJSONObject(0).getString("fb_id");
			String getimage = "https://graph.facebook.com/" + faceid + "/picture?width=200&height=200";
			imageLoader=new ImageLoader(activity.getApplicationContext());
			imageLoader.DisplayImage(getimage,  aq.id(R.id.imageView1).getImageView());
			
		} catch (JSONException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}

	}

	private class LongOperation extends AsyncTask<String, Void, String> {

	      @Override
	      protected String doInBackground(String... params) {
	            
	                try {
	                   // Thread.sleep(1000);
	                switch(Integer.parseInt(params[0].toString())){
	                case OPERATION_USERINFO:
	                
	                	try {
	                	userinfo = appService.getUserInfo(appService.getUserId());
	                	if (userinfo ==null){
	                		userinfo = appService.getUserInfo(appService.getUserId());
	                	}
	                	 } catch (Exception e) {
	 	                    // TODO Auto-generated catch block
	 	                    e.printStackTrace();
	 	                }
	                }
	                
	                } catch (Exception e) {
	                    // TODO Auto-generated catch block
	                    e.printStackTrace();
	                }
	           
	           
	                return null;
	     	      }      

	     	      @Override
	     	      protected void onPostExecute(String result) {      
	     	    	 updateViews(); 
	     	      }

	     	      @Override
	     	      protected void onPreExecute() {
	     	      }

	     	      @Override
	     	      protected void onProgressUpdate(Void... values) {
	     	      }
	}

	@Override
	public void onTabSelected(Tab tab, FragmentTransaction ft) {
		// TODO Auto-generated method stub
		ft.attach(this);
	}
	@Override
	public void onTabUnselected(Tab tab, FragmentTransaction ft) {
		// TODO Auto-generated method stub
		ft.detach(this);	
		
	}
	@Override
	public void onTabReselected(Tab tab, FragmentTransaction ft) {
		// TODO Auto-generated method stub
		
	}


}
