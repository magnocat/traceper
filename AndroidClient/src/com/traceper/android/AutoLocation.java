package com.traceper.android;


import android.os.Bundle;
import com.traceper.R;

public class AutoLocation extends MenuControl 
{


protected void onCreate(Bundle savedInstanceState) 
{
    super.onCreate(savedInstanceState);
    setContentView (R.layout.auto_location_x);
    setTitleFromActivityLabel (R.id.title_text);
}
    
} // end class
