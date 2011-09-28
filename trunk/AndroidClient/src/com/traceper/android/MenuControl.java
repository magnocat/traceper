package com.traceper.android;


import android.app.Activity;
import android.content.Context;
import android.content.Intent;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.TextView;
import android.widget.Toast;
import com.traceper.R;


public abstract class MenuControl extends Activity 
{



protected void onCreate(Bundle savedInstanceState) 
{
    super.onCreate(savedInstanceState);
    //setContentView(R.layout.activity_default);
}
    

protected void onDestroy ()
{
   super.onDestroy ();
}



protected void onPause ()
{
   super.onPause ();
}



protected void onRestart ()
{
   super.onRestart ();
}



protected void onResume ()
{
   super.onResume ();
}



protected void onStart ()
{
   super.onStart ();
}



protected void onStop ()
{
   super.onStop ();
}

public void onClickHome (View v)
{
    goHome (this);
}


public void onClickSearch (View v)
{
    startActivity (new Intent(getApplicationContext(), Search.class));
}


public void onClickAbout (View v)
{
    startActivity (new Intent(getApplicationContext(), About.class));
}


public void onClickFeature (View v)
{
    int id = v.getId ();
    switch (id) {
      case R.id.send_location :
           startActivity (new Intent(getApplicationContext(), Main.class));
           break;
      case R.id.auto_location :
           startActivity (new Intent(getApplicationContext(), AutoLocation.class));
           break;
      case R.id.take_photo :
           startActivity (new Intent(getApplicationContext(), TakePhoto.class));
           break;
      case R.id.setting_b :
           startActivity (new Intent(getApplicationContext(), Setting.class));
           break;
      case R.id.history_b :
           startActivity (new Intent(getApplicationContext(), History.class));
           break;
      case R.id.exit_b :
           
           break;
      default: 
    	   break;
    }
}



public void goHome(Context context) 
{
    final Intent intent = new Intent(context, Home.class);
    intent.setFlags (Intent.FLAG_ACTIVITY_CLEAR_TOP);
    context.startActivity (intent);
}


public void setTitleFromActivityLabel (int textViewId)
{
    TextView tv = (TextView) findViewById (textViewId);
    if (tv != null) tv.setText (getTitle ());
} // end setTitleText

public void toast (String msg)
{
    Toast.makeText (getApplicationContext(), msg, Toast.LENGTH_SHORT).show ();
} // end toast


public void trace (String msg) 
{
    Log.d("Traceper", msg);
    toast (msg);
}

} // end class
