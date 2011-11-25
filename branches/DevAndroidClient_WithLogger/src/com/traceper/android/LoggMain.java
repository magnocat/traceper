package com.traceper.android;

import java.io.IOException;
import java.util.Calendar;
import java.util.List;
import java.util.concurrent.Executor;
import java.util.concurrent.Executors;

import org.apache.http.HttpResponse;
import org.apache.http.HttpStatus;
import org.apache.http.client.ClientProtocolException;
import org.apache.http.client.HttpClient;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.entity.StringEntity;
import org.apache.http.impl.client.DefaultHttpClient;
import org.apache.http.params.BasicHttpParams;
import org.apache.http.params.HttpConnectionParams;
import org.apache.http.params.HttpParams;
import org.json.JSONArray;
import org.json.JSONObject;

import android.app.ActivityManager;
import android.app.AlertDialog;
import android.app.ExpandableListActivity;
import android.app.ProgressDialog;
import android.app.ActivityManager.RunningServiceInfo;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.SharedPreferences;
import android.content.DialogInterface.OnClickListener;
import android.database.Cursor;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.preference.PreferenceManager;
import android.text.format.DateFormat;
import android.util.Log;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.widget.ExpandableListView;
import android.widget.Toast;

import com.traceper.android.CallDetails; 
import com.traceper.android.CallExpandableListAdapter ; 
import com.traceper.android.CallLoggerPreferencesActivity;

import com.traceper.R;
import com.traceper.android.dao.CallLoggContentProvider;
import com.traceper.android.dao.ClearCallsContentObserver;
import com.traceper.android.dao.NewCallsContentObserver;

import com.traceper.android.dao.model.CallInfo;
import com.traceper.android.dao.model.GlobalCallHolder;

import com.traceper.android.grouping.BaseGroupingCriteria;
import com.traceper.android.grouping.ChildItem;
import com.traceper.android.grouping.GroupItem;
import com.traceper.android.interfaces.IAppService;

import com.traceper.android.services.CallLoggerService;
import com.traceper.android.utils.CursorUtils;


public class LoggMain extends ExpandableListActivity {
	
	private static final int POST_SUCCES = 5;
	private static final int POST_FAILED = 4;
    private static final int START_SEND_MAIL = 3;
	private static final int UPDATE_EXPANDABLE_LIST = 2;
	private static final int WAIT_SCREEN_OFF = 1;
	private static final int WAIT_SCREEN_ON = 0;

	private CallExpandableListAdapter callListAdapter;
  
    private BaseGroupingCriteria activeGrouping;
    
    private SharedPreferences sharedPrefs;
    
    private NewCallsContentObserver callContentObserver;
    private ClearCallsContentObserver clearContentObserver;
    
    private Executor executor = Executors.newSingleThreadExecutor();
	
    private final int MENU_SERVICE = 0;
    private final int MENU_SHARE = 1;
    private final int MENU_GROUPING = 2;
    private final int MENU_PREFERENCES = 3;

  
    
	private Handler dlgManagerHandler = new Handler()
	{
		private ProgressDialog progressDialog;

		public void handleMessage(android.os.Message msg)
		{
			if (msg.what == WAIT_SCREEN_ON && progressDialog == null)
			{
				progressDialog = ProgressDialog.show(LoggMain.this, "Please wait", "Loading data...", true);
			}
			if (msg.what == WAIT_SCREEN_OFF && progressDialog != null)
			{
				progressDialog.dismiss();
				progressDialog = null;
			}
			if (msg.what == UPDATE_EXPANDABLE_LIST)
			{
				setListAdapter(callListAdapter);
			}
			if (msg.what == START_SEND_MAIL)
			{
				startActivity(Intent.createChooser((Intent) msg.obj, "Send mail..."));
			}
			if (msg.what == POST_FAILED)
			{
				Toast.makeText(LoggMain.this, "Posting failed!", Toast.LENGTH_LONG);
			}
			if (msg.what == POST_SUCCES)
			{
				Toast.makeText(LoggMain.this, "Posting successed!", Toast.LENGTH_LONG);
			}
		};
	};
    
    private Handler newCallHandler = new Handler()
    {
    	public void handleMessage(android.os.Message msg)
    	{
    		if (msg.what == NewCallsContentObserver.CALL_LOG_DB_CHANGED)
    		{    			
    			handleAddNewCall();
    		}
    	};
    };
    
    private Handler cearCallsHandler = new Handler()
    {
    	public void handleMessage(android.os.Message msg)
    	{
    		if (msg.what == ClearCallsContentObserver.CALL_LOG_DB_CHANGED)
    		{    			
    			handleClearCalls();
    		}
    	};
    };
    
    @Override
    public void onCreate(Bundle savedInstanceState) 
    {
        super.onCreate(savedInstanceState);
        
        sharedPrefs = PreferenceManager.getDefaultSharedPreferences(this);
        
        registerObservers(true);
        
        callListAdapter = new CallExpandableListAdapter(LoggMain.this);

        activeGrouping = BaseGroupingCriteria.createGroupingByCriteria(
        		sharedPrefs.getInt(	CallLoggerPreferencesActivity.defaultGrouping, 
        							CallLoggerPreferencesActivity.defaultGroupingVal),
        							getContentResolver());
        setContentView(R.layout.logg_main);
        activeGrouping.fillCallExpList(callListAdapter, GlobalCallHolder.getEntireCallList(getContentResolver()));
        setListAdapter(callListAdapter);
    }
    
    @Override
    public boolean onCreateOptionsMenu(Menu menu) 
    {
    	MenuItem srvControl = menu.add(0, MENU_SERVICE, 1, R.string.start_service); 
    	if (isCallServiceRun())
    	{
    		srvControl.setTitle(getString(R.string.stop_service));
    	}
    	else
    	{
    		srvControl.setTitle(getString(R.string.start_service));
    	}
    	menu.add(0, MENU_GROUPING, 1, R.string.grouping);
        menu.add(0, MENU_SHARE, 1, R.string.sharing);
        menu.add(0, MENU_PREFERENCES, 1, R.string.preferences);
        return true;
    }
    
    public boolean onOptionsItemSelected(MenuItem item) {
        switch (item.getItemId()) {
        case MENU_SERVICE:
        	if (!isCallServiceRun())
        	{
        		if (startService(new Intent(this, CallLoggerService.class)) != null)
        			item.setTitle(getString(R.string.stop_service));
        	}
        	else
        	{
        		if (!stopService(new Intent(this, CallLoggerService.class)))
        		{
        			Log.e("MENU", "Can't stop service");
        		}
        		else
        		{
        			item.setTitle(getString(R.string.start_service));
        		}
        	}
            break;
        case MENU_SHARE:
        	final CharSequence[] shareTo = { "E-Mail", "Server"  };
        	AlertDialog.Builder builder = new AlertDialog.Builder(this);
			builder.setTitle("Send calls to ...")
			.setItems(shareTo, new OnClickListener()
			{
				public void onClick(DialogInterface dlg, int which)
				{
					dlgManagerHandler.sendEmptyMessage(WAIT_SCREEN_ON);
					switch (which)
					{
						case 0:
							sendLogByEMail();
							break;
						case 1:
							sendLogToServer();
							break;
					
					}
				}
			});
			AlertDialog alert = builder.create();
			alert.show();
        	break;
        case MENU_GROUPING:
        	showGroupingDlg();
            break;
        case MENU_PREFERENCES:
        	Intent intent = new Intent(this, CallLoggerPreferencesActivity.class);
        	startActivity(intent);
            break;
        }
        return false;
    }

    @Override
	public boolean onChildClick(ExpandableListView parent, View v, int groupPosition, int childPosition, long id)
	{
		ChildItem item = (ChildItem)getExpandableListAdapter().getChild(groupPosition, childPosition);
		Intent callDetails = item.getCall().getIntent();
		callDetails.setClass(this, CallDetails.class);
		startActivity(callDetails);
		return true;
	}
    
    @Override
    public void onDestroy()
    {
    	super.onDestroy();
    	unregisterObservers();
    }
    
	private boolean isCallServiceRun()
	{
		ActivityManager activMan = (ActivityManager) getSystemService(ACTIVITY_SERVICE);
		List<ActivityManager.RunningServiceInfo> servList = activMan.getRunningServices(Integer.MAX_VALUE);
		
		if (servList != null && servList.size()>0)
		{
			for (RunningServiceInfo runningServiceInfo : servList)
			{
				if (runningServiceInfo.service.getClassName().equalsIgnoreCase(CallLoggerService.class.getName()))
				{
					return true;
				}
			}
		}
		return false;
	}
    
	private void showGroupingDlg()
	{
		new AlertDialog.Builder(LoggMain.this).setTitle("Grouping").
		setSingleChoiceItems(R.array.grouping_choice, getDefaulGrouping(), 
			new OnClickListener(){
				public void onClick(DialogInterface dlgInterface, final int which){
					dlgInterface.dismiss();
					dlgManagerHandler.sendEmptyMessage(WAIT_SCREEN_ON);
					executor.execute(new Runnable(){	
						public void run(){
							regroupList(which);
						}
					});
				}
			}).setCancelable(true).show();
	}
	
	private void regroupList(int which)
	{
		int prevGrouping = sharedPrefs.getInt(CallLoggerPreferencesActivity.defaultGrouping, BaseGroupingCriteria.GROUPING_BY_TIME);
		if (which == prevGrouping) return;
		activeGrouping = BaseGroupingCriteria.createGroupingByCriteria(which, getContentResolver());
		sharedPrefs.edit().
			putInt(CallLoggerPreferencesActivity.defaultGrouping, which).
			commit();
		activeGrouping.fillCallExpList(callListAdapter, GlobalCallHolder.getEntireCallList());
		dlgManagerHandler.sendEmptyMessage(UPDATE_EXPANDABLE_LIST);
		dlgManagerHandler.sendEmptyMessage(WAIT_SCREEN_OFF);
	}
        
    private void registerObservers(boolean notifyForDescendents)
    {
    	if (callContentObserver == null)
    	{
    		getContentResolver().registerContentObserver
    			(CallLoggContentProvider.CALLS_URI, notifyForDescendents, 
    					callContentObserver = new NewCallsContentObserver(newCallHandler));
    		getContentResolver().registerContentObserver
			(CallLoggContentProvider.CLEAR_CALLS_URI, notifyForDescendents, 
					clearContentObserver = new ClearCallsContentObserver(cearCallsHandler));
    	}
    }
    
    private void unregisterObservers()
    {
    	if (callContentObserver != null)
    		getContentResolver().unregisterContentObserver(callContentObserver);
    	if (clearContentObserver != null)
    		getContentResolver().unregisterContentObserver(clearContentObserver);
    }
    
    private void sendLogByEMail()
    {	
    	executor.execute(new Runnable()
		{
			public void run()
			{
				final  Intent emailIntent = new Intent(android.content.Intent.ACTION_SEND);
				Cursor c = getContentResolver().query(CallLoggContentProvider.CALLS_URI, null, null, null, null);
				
				String body = CursorUtils.getCallsForSharing(c, CursorUtils.SHARE_AS_PLAIN_TEXT);
				
				emailIntent.setType("plain/text");
				emailIntent.putExtra(android.content.Intent.EXTRA_EMAIL, new String[]{getDefaultEMail()});
				emailIntent.putExtra(android.content.Intent.EXTRA_SUBJECT, "Call Logger: "
						+ DateFormat.format("dd/MM/yyyy hh:mm:ss", Calendar.getInstance().getTimeInMillis()).toString());
				emailIntent.putExtra(android.content.Intent.EXTRA_TEXT, body);
				emailIntent.addFlags(Intent.FLAG_ACTIVITY_NEW_TASK);
				dlgManagerHandler.sendEmptyMessage(WAIT_SCREEN_OFF);
				Message msg = new Message();
				msg.what = START_SEND_MAIL;
				msg.obj = emailIntent;
				dlgManagerHandler.sendMessage(msg);
			}
		});
    	
    }
    
    private void sendLogToServer()
	{
    	executor.execute(new Runnable()
		{
			public void run()
			{
				HttpParams httpParams = new BasicHttpParams();

				HttpConnectionParams.setConnectionTimeout(httpParams, 3000);
				HttpConnectionParams.setSoTimeout(httpParams, 5000);
				
				final HttpClient client = new DefaultHttpClient();
				
				JSONArray callsStat = new JSONArray();
				
				for (CallInfo call : GlobalCallHolder.getEntireCallList(getContentResolver()))
				{
					JSONObject callObj = new JSONObject(call.getMap());
					callsStat.put(callObj);
				}
				
				HttpPost httpPostRequest = new HttpPost("http://192.168.2.25/elman/trac");
				
				String s = callsStat.toString();
				HttpResponse response = null;
				try
				{
					httpPostRequest.setEntity(new StringEntity(s));
					
					response = client.execute(httpPostRequest);
					if (response.getStatusLine().getStatusCode() == HttpStatus.SC_OK)
						dlgManagerHandler.sendEmptyMessage(POST_SUCCES);						
					else
						dlgManagerHandler.sendEmptyMessage(POST_FAILED);
				}
				catch (ClientProtocolException e)
				{
					e.printStackTrace();
				}
				catch (IOException e)
				{
					e.printStackTrace();
				}
				dlgManagerHandler.sendEmptyMessage(WAIT_SCREEN_OFF);
			}
		});
	}
    
    private String getDefaultEMail()
    {
    	return sharedPrefs.getString(CallLoggerPreferencesActivity.defaultEMail, "");
    }
    
    private int getDefaulGrouping()
	{
    	return sharedPrefs.getInt(CallLoggerPreferencesActivity.defaultGrouping, BaseGroupingCriteria.GROUPING_BY_TIME);
	}

	private void handleAddNewCall()
	{
		ChildItem child = GlobalCallHolder.loadDBScopeIdentity();
		GroupItem grIt = activeGrouping.getTargetGroup(child);
		activeGrouping.putToTargetGroup(grIt, child);
		callListAdapter.add(grIt);
		setListAdapter(callListAdapter);
	}
	
	private void handleClearCalls()
	{
		GlobalCallHolder.getEntireCallList().clear();
		callListAdapter.clear();
		setListAdapter(callListAdapter);
	}
	
}