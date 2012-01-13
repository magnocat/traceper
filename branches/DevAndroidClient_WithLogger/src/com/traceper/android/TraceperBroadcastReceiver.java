package com.traceper.android;

import com.traceper.android.services.AppService;

import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.sax.StartElementListener;

public class TraceperBroadcastReceiver extends BroadcastReceiver {

	@Override
	public void onReceive(Context context, Intent intent) {
		Intent i = new Intent(context, AppService.class);
		i.setAction(intent.getAction());
		context.startService(i);

	}

}
