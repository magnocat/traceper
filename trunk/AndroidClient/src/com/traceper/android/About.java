package com.traceper.android;



import android.os.Bundle;
import com.traceper.R;


public class About extends MenuControl
{



protected void onCreate(Bundle savedInstanceState) 
{
    super.onCreate(savedInstanceState);

    setContentView (R.layout.about_x);
    setTitleFromActivityLabel (R.id.title_text);
}
    
} // end class
