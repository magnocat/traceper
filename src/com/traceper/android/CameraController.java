package com.traceper.android;


import java.io.File;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.IOException;

import android.app.Activity;
import android.app.Dialog;
import android.app.NotificationManager;
import android.app.Service;
import android.content.ComponentName;
import android.content.Context;
import android.content.Intent;
import android.content.ServiceConnection;
import android.graphics.PixelFormat;
import android.hardware.Camera;
import android.media.MediaRecorder;
import android.os.Bundle;
import android.os.IBinder;
import android.os.PowerManager;
import android.os.PowerManager.WakeLock;
import android.util.Log;
import android.view.KeyEvent;
import android.view.Menu;
import android.view.MenuItem;
import android.view.SurfaceHolder;
import android.view.SurfaceView;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.Window;
import android.view.WindowManager;
import android.widget.Button;
import android.widget.CheckBox;
import android.widget.EditText;
import android.widget.LinearLayout;
import android.widget.Toast;

import com.traceper.R;
import com.traceper.android.interfaces.IAppService;
import com.traceper.android.services.AppService;

public class CameraController extends Activity implements SurfaceHolder.Callback{

	private Camera mCamera;
	private SurfaceView surfaceView;
	private SurfaceHolder surfaceHolder;
	private boolean isPreviewRunning;
	private static final int UPLOAD_PHOTO = Menu.FIRST;
	private static final int TAKE_ANOTHER_PHOTO = Menu.FIRST + 1;
	private static final int BACK = Menu.FIRST + 2;
	private byte[] picture;
	private IAppService appService = null;
	private boolean pictureTaken = false;
	private Button takePictureButton = null;
	private NotificationManager mManager;
	private static int NOTIFICATION_ID = 0;
	private static final int DIALOG_ASK_TO_MAKE_IMAGE_PUBLIC = 0;
	private static final int DIALOG_ASK_TO_MAKE_VIDEO_PUBLIC = 1;
	private Button recordLiveVideoButton;
	private MediaRecorder mMediaRecorder;
	private String videoDirPath = "/sdcard/traceper";

	private Camera.PictureCallback mPictureCallbackRaw = new Camera.PictureCallback() {  
		public void onPictureTaken(byte[] data, Camera c) {  
			Log.e(getClass().getSimpleName(), "PICTURE CALLBACK RAW: " + data);  
		}  
	}; 
	private Camera.PictureCallback mPictureCallbackJpeg= new Camera.PictureCallback() { 

		public void onPictureTaken(byte[] data, Camera c) {
			picture = data;
			Log.e(getClass().getSimpleName(), "PICTURE CALLBACK JPEG: string.length = " + data.length);  
		}  
	};  

	private Camera.ShutterCallback mShutterCallback = new Camera.ShutterCallback() {  
		public void onShutter() {  
			Log.e(getClass().getSimpleName(), "SHUTTER CALLBACK");  
		}  
	};

	private ServiceConnection mConnection = new ServiceConnection() {
		public void onServiceConnected(ComponentName className, IBinder service) {          
			appService = ((AppService.IMBinder)service).getService();    
		}
		public void onServiceDisconnected(ComponentName className) {          
			appService = null;
		}
	};
	private PowerManager pw;
	private Button recordVideoButton;


	public void onCreate(Bundle savedInstanceState){
		super.onCreate(savedInstanceState);

		this.getWindow().requestFeature(Window.FEATURE_NO_TITLE);
		this.getWindow().setFlags(WindowManager.LayoutParams.FLAG_FULLSCREEN, WindowManager.LayoutParams.FLAG_FULLSCREEN );

		setContentView(R.layout.camera_view);
		getWindow().setFormat(PixelFormat.TRANSLUCENT);

		pw = (PowerManager)getSystemService(Service.POWER_SERVICE);
		surfaceView = (SurfaceView) findViewById(R.id.surface);
		surfaceHolder = surfaceView.getHolder();
		surfaceHolder.addCallback(this);
		surfaceHolder.setType(SurfaceHolder.SURFACE_TYPE_PUSH_BUFFERS);

		LinearLayout linearlayout = (LinearLayout) findViewById(R.id.buttonsLayout);
		linearlayout.bringToFront();

		mCamera = Camera.open();
		takePictureButton = (Button) findViewById(R.id.takePictureButton);
		takePictureButton.bringToFront();
		takePictureButton.setOnClickListener(new View.OnClickListener() {			

			public void onClick(View arg0) {
				pictureTaken = true;
				mCamera.takePicture(mShutterCallback, mPictureCallbackRaw, mPictureCallbackJpeg);  
				openOptionsMenu();

			}
		});
		takePictureButton.setCompoundDrawablesWithIntrinsicBounds(android.R.drawable.ic_menu_camera,0,0,0);

		recordLiveVideoButton = (Button) findViewById(R.id.recordVideoButton);
		recordLiveVideoButton.setCompoundDrawablesWithIntrinsicBounds(R.drawable.btn_ic_video_record, 0, 0, 0);
		recordLiveVideoButton.bringToFront();

		recordLiveVideoButton.setOnClickListener(new View.OnClickListener() {			

			private boolean recording = false;
			private WakeLock wl;

			public void onClick(View arg0) {
				if(recording ){
					mMediaRecorder.stop();  // stop the recording
					releaseMediaRecorder(); // release the MediaRecorder object
					mCamera.lock();
					try {
						mCamera.reconnect();
					} catch (IOException e) {
						// TODO Auto-generated catch block
						e.printStackTrace();
					}
					recording = false;
					recordLiveVideoButton.setCompoundDrawablesWithIntrinsicBounds(R.drawable.btn_ic_video_record, 0, 0, 0);
					appService.stopLiveVideoUploading();
					wl.release();
				}
				else{
					if (prepareVideoRecorder()) {
						// Camera is available and unlocked, MediaRecorder is prepared,
						// now you can start recording
						mMediaRecorder.start();
						// inform the user that recording has started
						recording = true;
						//      new FileListener(videoPath);
						//logFileLength();
						appService.startLiveVideoUploading(getVideoPath());
						wl = pw.newWakeLock(PowerManager.SCREEN_DIM_WAKE_LOCK, "My Tag");
						wl.acquire();
						recordLiveVideoButton.setCompoundDrawablesWithIntrinsicBounds(R.drawable.btn_ic_video_record_stop, 0, 0, 0);
					} else {
						// prepare didn't work, release the camera
						releaseMediaRecorder();
						// inform user
					}

				}
			}
		});

		recordVideoButton = (Button) findViewById(R.id.sendAfterRecord);
		recordVideoButton.setOnClickListener(new OnClickListener() {

			private boolean recording = false;
			private WakeLock wl;

			@Override
			public void onClick(View arg0) {
				if (recording == true) {
					mMediaRecorder.stop();  // stop the recording
					releaseMediaRecorder(); // release the MediaRecorder object
					mCamera.lock();
					try {
						mCamera.reconnect();
					} catch (IOException e) {
						// TODO Auto-generated catch block
						e.printStackTrace();
					}
					recording = false;
					wl.release();
					showDialog(DIALOG_ASK_TO_MAKE_VIDEO_PUBLIC);
				}
				else {

					if (prepareVideoRecorder()) {
						// Camera is available and unlocked, MediaRecorder is prepared,
						// now you can start recording
						mMediaRecorder.start();
						// inform the user that recording has started
						recording = true;
						wl = pw.newWakeLock(PowerManager.SCREEN_DIM_WAKE_LOCK, "My Tag");
						wl.acquire();
					} else {
						// prepare didn't work, release the camera
						releaseMediaRecorder();
						// inform user
					}

				}

			}
		});

	}


	@Override
	public void surfaceChanged(SurfaceHolder holder, int format, int w, int h) {
		if (isPreviewRunning) {  
			mCamera.stopPreview();  
		}  

		try {
			mCamera.setPreviewDisplay(holder);
		} catch (IOException e) {
			e.printStackTrace();
		}  

		mCamera.startPreview(); 

		isPreviewRunning = true; 
	}

	@Override
	public void surfaceCreated(SurfaceHolder holder) {
		try {		
			mCamera.setPreviewDisplay(holder);
			mCamera.startPreview();
		} catch (IOException e) {

		}
	}

	@Override
	public void surfaceDestroyed(SurfaceHolder arg0) {
		isPreviewRunning = false;
	}



	public boolean onCreateOptionsMenu(Menu menu){
		boolean result  = super.onCreateOptionsMenu(menu);
		if (pictureTaken == true) {
			menu.add(0, UPLOAD_PHOTO, 0, "Upload photo").setIcon(android.R.drawable.ic_menu_upload);
			menu.add(0, TAKE_ANOTHER_PHOTO, 1, "Take another photo").setIcon(android.R.drawable.ic_menu_camera);
		}
		menu.add(0, BACK, 2, "Back").setIcon(android.R.drawable.ic_menu_revert);

		return result;		
	}

	public boolean onPrepareOptionsMenu(Menu menu)
	{
		boolean result = super.onPrepareOptionsMenu(menu);
		MenuItem item = menu.findItem(UPLOAD_PHOTO);
		if (item == null && pictureTaken == true) {
			menu.add(0, UPLOAD_PHOTO, 0, "Upload photo").setIcon(android.R.drawable.ic_menu_upload);
			menu.add(0, TAKE_ANOTHER_PHOTO, 1, "Take another photo").setIcon(android.R.drawable.ic_menu_camera);
		}
		else if (item != null && pictureTaken == false){
			menu.removeItem(UPLOAD_PHOTO);
			menu.removeItem(TAKE_ANOTHER_PHOTO);
		}


		return result;		
	}


	@Override
	public boolean onOptionsItemSelected(MenuItem item) {
		switch (item.getItemId()) 
		{
		case UPLOAD_PHOTO:	
			showDialog(DIALOG_ASK_TO_MAKE_IMAGE_PUBLIC);
			break;
		case TAKE_ANOTHER_PHOTO:
			pictureTaken = false;
			mCamera.startPreview();  
			break;
		case BACK:
			CameraController.this.finish();
			break;
		}
		return super.onOptionsItemSelected(item);
	}

	public boolean onKeyDown(int keyCode, KeyEvent event)  
	{  
		if (keyCode == KeyEvent.KEYCODE_BACK) {  
			return super.onKeyDown(keyCode, event);  
		}  
		else if (keyCode == KeyEvent.KEYCODE_DPAD_CENTER) {  
			pictureTaken = true;
			mCamera.takePicture(mShutterCallback, mPictureCallbackRaw, mPictureCallbackJpeg);  
			this.openOptionsMenu();
			return true;  
		}  

		return false;  
	} 	

	protected void onResume(){
		super.onResume();
		bindService(new Intent(CameraController.this, AppService.class), mConnection , Context.BIND_AUTO_CREATE);
	}

	@Override
	protected void onPause() {
		unbindService(mConnection);
		releaseMediaRecorder();       // if you are using MediaRecorder, release it first
		releaseCamera(); 
		super.onPause();
	}

	@Override
	protected Dialog onCreateDialog(int id) {
		switch (id) {
		case DIALOG_ASK_TO_MAKE_IMAGE_PUBLIC:
		{
			final Dialog dialog = new Dialog(this);

			dialog.setContentView(R.layout.photo_description);
			dialog.setTitle(R.string.upload_photo);

			dialog.findViewById(R.id.sendPhotoButton).setOnClickListener(new OnClickListener() {
				@Override
				public void onClick(View v) {
					CheckBox isPublicCheckbox = (CheckBox)dialog.findViewById(R.id.isPublic);
					EditText desriptionEditText = (EditText)dialog.findViewById(R.id.photo_description);
					String description = desriptionEditText.getText().toString();
					if (description.length() > 0) {
						uploadImage(isPublicCheckbox.isChecked(), description);
						dialog.dismiss();
						CameraController.this.finish();
					}
					else {
						//	showDialog(DIALOG_ASK_TO_MAKE_IMAGE_PUBLIC);
						Toast.makeText(CameraController.this, R.string.please_enter_description, Toast.LENGTH_SHORT).show();
					}

				}
			});
			dialog.findViewById(R.id.cancelButton).setOnClickListener(new OnClickListener() {
				@Override
				public void onClick(View arg0) {
					dialog.dismiss();
					finish();

				}
			});

			return dialog;
		}
		case DIALOG_ASK_TO_MAKE_VIDEO_PUBLIC:
		{
			final Dialog dialog = new Dialog(this);

			dialog.setContentView(R.layout.photo_description);
			dialog.setTitle(R.string.upload_photo);

			dialog.findViewById(R.id.sendPhotoButton).setOnClickListener(new OnClickListener() {
				@Override
				public void onClick(View v) {
					CheckBox isPublicCheckbox = (CheckBox)dialog.findViewById(R.id.isPublic);
					EditText desriptionEditText = (EditText)dialog.findViewById(R.id.photo_description);
					String description = desriptionEditText.getText().toString();
					if (description.length() > 0) {
						uploadVideo(isPublicCheckbox.isChecked(), description);
						dialog.dismiss();
						CameraController.this.finish();
					}
					else {
						//	showDialog(DIALOG_ASK_TO_MAKE_IMAGE_PUBLIC);
						Toast.makeText(CameraController.this, R.string.please_enter_description, Toast.LENGTH_SHORT).show();
					}

				}
			});
			dialog.findViewById(R.id.cancelButton).setOnClickListener(new OnClickListener() {
				@Override
				public void onClick(View arg0) {
					dialog.dismiss();
					finish();

				}
			});

			return dialog;
		}
		}

		return super.onCreateDialog(id);
	}

	private void uploadImage(final boolean publicImage, final String description){

		Thread uploadThread = new Thread(){
			@Override
			public void run() {
				appService.uploadImage(picture, publicImage, description);
			}
		};
		uploadThread.start();
	}

	private boolean prepareVideoRecorder(){

		mMediaRecorder = new MediaRecorder();

		// Step 1: Unlock and set camera to MediaRecorder
		mCamera.unlock();
		mMediaRecorder.setCamera(mCamera);

		// Step 2: Set sources
		mMediaRecorder.setAudioSource(MediaRecorder.AudioSource.CAMCORDER);
		mMediaRecorder.setVideoSource(MediaRecorder.VideoSource.CAMERA);

		// Step 3: Set a CamcorderProfile (requires API Level 8 or higher)
		//mMediaRecorder.setProfile(CamcorderProfile.get(CamcorderProfile.QUALITY_HIGH));
		mMediaRecorder.setOutputFormat(MediaRecorder.OutputFormat.MPEG_4);
		mMediaRecorder.setAudioEncoder(MediaRecorder.AudioEncoder.DEFAULT);
		mMediaRecorder.setVideoEncoder(MediaRecorder.VideoEncoder.DEFAULT);


		// Step 4: Set output file

		mMediaRecorder.setOutputFile(getVideoPath());

		// Step 5: Set the preview output
		mMediaRecorder.setPreviewDisplay(surfaceHolder.getSurface());

		// Step 6: Prepare configured MediaRecorder
		try {
			mMediaRecorder.prepare();
		} catch (IllegalStateException e) {
			releaseMediaRecorder();
			return false;
		} catch (IOException e) {
			releaseMediaRecorder();
			return false;
		}
		return true;
	}


	private void releaseMediaRecorder(){
		if (mMediaRecorder != null) {
			mMediaRecorder.reset();   // clear recorder configuration
			mMediaRecorder.release(); // release the recorder object
			mMediaRecorder = null;
			mCamera.lock();           // lock camera for later use
		}
	}

	private void releaseCamera(){
		if (mCamera != null){
			mCamera.release();        // release the camera for other applications
			mCamera = null;
		}
	}

	private String getVideoPath() 
	{
		File videoDir = new File(videoDirPath);
		if (videoDir.exists() == false) {
			videoDir.mkdir();
		}
		return videoDir + "/videoPath.mp4";
	}

	private void uploadVideo(final boolean isPublic, final String description) 
	{
		File file = new File(getVideoPath());
		FileInputStream fin = null;
		try {
			fin = new FileInputStream(file);
			final byte[] buffer = new byte[(int) file.length()];
			fin.read(buffer);
			fin.close();

			Thread uploadThread = new Thread(){
				@Override
				public void run() {
					appService.uploadVideo(buffer, isPublic, description);
				}
			};
			uploadThread.start();
		} 
		catch (FileNotFoundException e) {
			e.printStackTrace();
		} catch (IOException e) {
			e.printStackTrace();
		}

	}

}
