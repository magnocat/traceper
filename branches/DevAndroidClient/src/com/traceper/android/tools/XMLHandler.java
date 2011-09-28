package com.traceper.android.tools;

import org.xml.sax.Attributes;
import org.xml.sax.SAXException;
import org.xml.sax.helpers.DefaultHandler;

import com.traceper.android.Configuration;
import com.traceper.android.interfaces.IAppService;

public class XMLHandler extends DefaultHandler {
	
	private int actionResult = IAppService.HTTP_RESPONSE_ERROR_UNKNOWN_RESPONSE;
	private int gpsMinDataSentInterval = Configuration.MIN_GPS_DATA_SEND_INTERVAL;
	private int gpsMinDistanceInterval = Configuration.MIN_GPS_DISTANCE_INTERVAL;
	
	
	@Override
	public void startElement(String uri, String localName, String name,
			Attributes attributes) throws SAXException {
		
		if (localName == "actionResult") {
			actionResult = Integer.parseInt(attributes.getValue("value"));
		}
		else if (localName == "minDataSentInterval") {
			try {
				gpsMinDataSentInterval = Integer.parseInt(attributes.getValue("value"));
			}catch (NumberFormatException e) {
			}
		}
		else if (localName == "minDistanceInterval"){
			try {
				gpsMinDistanceInterval = Integer.parseInt(attributes.getValue("value"));
			}
			catch (NumberFormatException e) {
			}
		}
		
		super.startElement(uri, localName, name, attributes);
	}


	public int getActionResult() {
		return actionResult;
	}


	public int getGpsMinDataSentInterval() {
		if (gpsMinDataSentInterval < Configuration.MIN_GPS_DATA_SEND_INTERVAL){
			gpsMinDataSentInterval = Configuration.MIN_GPS_DATA_SEND_INTERVAL;
		}
		return gpsMinDataSentInterval;
	}


	public int getGpsMinDistanceInterval() {
		if (gpsMinDistanceInterval < Configuration.MIN_GPS_DISTANCE_INTERVAL) {
			gpsMinDistanceInterval = Configuration.MIN_GPS_DISTANCE_INTERVAL;
		}		
		return gpsMinDistanceInterval;
	}
}
