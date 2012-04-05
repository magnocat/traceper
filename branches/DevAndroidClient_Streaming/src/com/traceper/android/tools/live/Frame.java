package com.traceper.android.tools.live;


public class Frame {
	private byte[] data;
	private boolean initialFrame;
	private boolean sequenceHeader;
	private int timeStamp;
	public Frame(byte[] data, boolean initialFrame, boolean sequenceHeader, int timeStamp) {
		super();
		this.data = data;
		this.initialFrame = initialFrame;
		this.sequenceHeader = sequenceHeader;
		this.timeStamp = timeStamp;
	}
	public byte[] getData() {
		return data;
	}
	public boolean isInitialFrame() {
		return initialFrame;
	}
	
	public boolean isSequenceHeader() {
		return sequenceHeader;
	}
	
	public int getTimeStamp() {
		return timeStamp;
	}
	
	

}
