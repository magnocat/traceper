package com.traceper.android.tools.live;


public class AudioTagHeaderAndData extends GenericFlvTag{
		
		byte info = (byte)0xAF; // it can be 0xad or 0xaf  //format 4 bit => 10 = aac, rate 2 bit => 3 = 44khz, size 1 => 0 or 1 > 8-16 sample , type 1=1
		byte AACPacketType = 0; // it can be 0(aac sequence header) or 1(aac raw)

		public static final int AUDIO_TAG_HEADER_LENGTH = 2;
		
		public AudioTagHeaderAndData(byte[] data, boolean isSequenceHeader) {
			this.tagType = AUDIO_TAG;
			setData(data);
			
			AACPacketType = 1;
			if (isSequenceHeader == true) {
				AACPacketType = 0;				
			}
			byte[] header = new byte[2];
			header[0] = info;
			header[1] = AACPacketType;
			
			setTagHeader(header);			
		}
	
		
	}