package com.traceper.android;




import android.os.Bundle;
import com.traceper.R;


public class TakePhoto extends MenuControl
{


protected void onCreate(Bundle savedInstanceState) 
{
    super.onCreate(savedInstanceState);
    setContentView (R.layout.take_photo_x);
    setTitleFromActivityLabel (R.id.title_text);
}
    
} // end class
