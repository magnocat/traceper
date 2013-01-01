package com.traceper.android;

import java.util.ArrayList;
import java.util.HashMap;

import android.app.Activity;
import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.ImageView;
import android.widget.TextView;

import com.traceper.R;
import com.traceper.android.list.ImageLoader;

public class new_FriendsListAdapter extends BaseAdapter {
    
    private Activity activity;
    private ArrayList<HashMap<String, String>> data;
    private static LayoutInflater inflater=null;
    public ImageLoader imageLoader; 
    
    public new_FriendsListAdapter(Activity a, ArrayList<HashMap<String, String>> d) {
        activity = a;
        data=d;
        //inflater = ((Activity)activity).getLayoutInflater();
        inflater = (LayoutInflater)activity.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
        imageLoader=new ImageLoader(activity.getApplicationContext());
    }

    public int getCount() {
        return data.size();
    }

    public Object getItem(int position) {
        return position;
    }

    public long getItemId(int position) {
        return position;
    }
    public void clear(){
    	data.clear();
    }
    
    public View getView(int position, View convertView, ViewGroup parent) {
        View vi=convertView;
        if(convertView==null)
            vi = inflater.inflate(R.layout.f_list, null);

        TextView username = (TextView)vi.findViewById(R.id.f_username);
        TextView time = (TextView)vi.findViewById(R.id.title_1); 
        TextView location = (TextView)vi.findViewById(R.id.title_2); 
        TextView userlistno = (TextView)vi.findViewById(R.id.number_user); 
        ImageView thumb_image=(ImageView)vi.findViewById(R.id.list_image); 
        
        HashMap<String, String> userlist = new HashMap<String, String>();
        userlist = data.get(position);
        
        // Setting all values in listview
        username.setText(userlist.get(new_friendlist.KEY_USERNAME));
        time.setText(userlist.get(new_friendlist.KEY_DURATIONTIME));
        location.setText(userlist.get(new_friendlist.KEY_LOCATION));
        userlistno.setText(userlist.get(new_friendlist.KEY_USERLISTNO));
        imageLoader.DisplayImage(userlist.get(new_friendlist.KEY_THUMB_URL), thumb_image);
        return vi;
    }
    }
