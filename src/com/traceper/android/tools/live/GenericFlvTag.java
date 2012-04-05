package com.traceper.android.tools.live;

public abstract class GenericFlvTag {
	
	public static final byte AUDIO_TAG = 8;
	public static final byte VIDEO_TAG = 9;
	
	
	byte tagType;
	/*
	 * Length of the message. Number of bytes after StreamID to
	 * end of tag (Equal to length of the tag � 11)
	 */
	byte[] dataSize = new byte[3];
	/*
	 * Time in milliseconds at which the data in this tag applies.
	 * This value is relative to the first tag in the FLV file, which
	 * always has a timestamp of 0.
	 */
	byte[] timeStamp = new byte[3];
	/*
	 * Extension of the Timestamp field to form a SI32 value. This
	 * field represents the upper 8 bits, while the previous
	 * Timestamp field represents the lower 24 bits of the time in
	 * milliseconds.
	 */
	byte timeStampExtend = 0;
	/*
	 * Always 0.
	 */
	byte[] streamId = new byte[3];
	/*
	 * audio or video tag header
	 */
	byte[] tagHeader;	
	/*
	 * audio or video data
	 */
	protected byte[] data;
	
	public GenericFlvTag() {
		this.timeStamp[0] = 0;
		this.timeStamp[1] = 0;
		this.timeStamp[2] = 0;
		this.streamId[0] = 0;
		this.streamId[1] = 0;
		this.streamId[2] = 0;
	}
	
	public void setTagType(byte tagType) {
		this.tagType = tagType;
	}
	
	public void setDataSize(int dataSize) {
		this.dataSize[0] = (byte)((dataSize & 0x00FF0000) >> 16);
		this.dataSize[1] = (byte)((dataSize & 0x0000FF00) >> 8);
		this.dataSize[2] = (byte)(dataSize & 0x000000FF);
	}
	
	public void setTimeStamp(int timeStamp) {
		this.timeStamp[0] = (byte)((timeStamp & 0x00FF0000) >> 16);
		this.timeStamp[1] = (byte)((timeStamp & 0x0000FF00) >> 8);
		this.timeStamp[2] = (byte)(timeStamp & 0x000000FF);
		
		this.timeStampExtend = (byte)((timeStamp & 0xFF000000) >> 24);
	}
	
	
	public byte[] getHeader(){
		byte[] header = new byte[11];
		
		header[0] = tagType;
		header[1] = dataSize[0];
		header[2] = dataSize[1];
		header[3] = dataSize[2];
		
		header[4] = timeStamp[0];
		header[5] = timeStamp[1];
		header[6] = timeStamp[2];
		
		header[7] = timeStampExtend;
		
		header[8] = streamId[0];
		header[9] = streamId[1];
		header[10] = streamId[2];
		
		return header;	
	}
	
	public void setTagHeader(byte[] tagHeader) {
		this.tagHeader = tagHeader;
	}
	
	public byte[] getTagHeader() {
		return tagHeader;
	}
	
	public void setData(byte[] data) {
		this.data = data;
	}
	
	public byte[] getData() {
		return data;
	}
	
	public int getDataSize() {
		
		int dataSizeField = ((dataSize[0] & 0x000000FF) << 16) | 
							((dataSize[1] & 0x000000FF) << 8) | 
							(dataSize[2] & 0x000000FF);
		
		return dataSizeField;
	}
}
