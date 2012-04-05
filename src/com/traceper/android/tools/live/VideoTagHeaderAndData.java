package com.traceper.android.tools.live;

public class VideoTagHeaderAndData extends GenericFlvTag{

	
	public static final int VIDEO_TAG_HEADER_LENGTH = 5;
	/*
	 * Type of video frame. The following values are defined:
			1 = key frame (for AVC, a seekable frame)
			2 = inter frame (for AVC, a non-seekable frame)
			3 = disposable inter frame (H.263 only)
			4 = generated key frame (reserved for server use only)
			5 = video info/command frame

			CodecId is 7 AVC
	 */
	byte frameTypeAndCodec = 0x17;
	/*
		0 = AVC sequence header
		1 = AVC NALU
		2 = AVC end of sequence (lower level NALU sequence ender is
		not required or supported)
	 */
	byte AVCPacketType = 0; 
	byte[] compositionTime = new byte[3];


	public VideoTagHeaderAndData(byte[] data, boolean initialFrame, boolean sequenceHeader) {
		this.tagType = VIDEO_TAG;
		
		if (initialFrame == true) {
			frameTypeAndCodec = 0x17;
		}
		else {
			frameTypeAndCodec = 0x27;
		}
		AVCPacketType = 1;
		if (sequenceHeader == true) {
			AVCPacketType = 0;
		}
	
		setData(data);		
		this.compositionTime[0] = 0;
		this.compositionTime[1] = 0;
		this.compositionTime[2] = 0;	
		
		prepareAndSetTagHeader();
	}
	
	public void prepareAndSetTagHeader(){
		byte[] tagHeader = new byte[5];
		
		tagHeader[0] = frameTypeAndCodec;
		tagHeader[1] = AVCPacketType;
		tagHeader[2] = compositionTime[0];
		tagHeader[3] = compositionTime[1];
		tagHeader[4] = compositionTime[2];
		
		setTagHeader(tagHeader);		
	}

	public void setFrameType(byte type){

		if (type == 1) {
			frameTypeAndCodec = 0x17;
		}
		else if (type == 2) {
			frameTypeAndCodec = 0x27;
		} 
		else if (type == 3) {
			frameTypeAndCodec = 0x37;
		}
		else if (type == 4) {
			frameTypeAndCodec = 0x47;				
		}
		else if (type == 5) {
			frameTypeAndCodec = 0x57;				
		}	
		
		prepareAndSetTagHeader();
	}

	public void setAVCPacketType(byte aVCPacketType) {
		AVCPacketType = aVCPacketType;
		prepareAndSetTagHeader();
	}

	public void setCompositionTime(int compositionTime) {
		if (AVCPacketType == 1) {
			this.compositionTime[0] = (byte)((compositionTime & 0x00FF0000) >> 16);
			this.compositionTime[1] = (byte)((compositionTime & 0x0000FF00) >> 8);
			this.compositionTime[2] = (byte)(compositionTime & 0x000000FF);	
			prepareAndSetTagHeader();
		}
	}

}