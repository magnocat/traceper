package com.traceper.android.tools.live;


import java.io.File;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.IOException;
import java.nio.ByteBuffer;
import java.util.ArrayList;

import org.apache.http.util.ByteArrayBuffer;


public class FLVCreator {

	private byte[] flvHeader = new byte[9];
	private byte[] sps;
	private byte[] pps;
	private int startOfDataFrame;

	private int readByCount = 0;
	private int frameCount = 0;

	private String filePath = null;

	public byte[] getHeader(){
		byte[] flvHeader = new byte[9];
		flvHeader[0]=70; //F
		flvHeader[1]=76; //L
		flvHeader[2]=86; //V
		flvHeader[3]=1;  //version
		flvHeader[4]=5;  //flags mean has audio and video content
		flvHeader[5]=0;  //length 5-8
		flvHeader[6]=0;  //length 5-8
		flvHeader[7]=0;  //length 5-8
		flvHeader[8]=9;  //length 5-8	

		return flvHeader;
	}
	
	public void setFile(String filePath){
		this.filePath = filePath;
		
	}


	public ArrayList<Frame>[] getFrameList(byte[] buffer, int startPos, int samplingRate){

		int i = startPos;
		int aacFrameDuration = (1024 * 1000) / samplingRate;
		ArrayList<Frame> frameList = new ArrayList<Frame>();
		ArrayList<Frame> audioList = new ArrayList<Frame>();

		if (startPos != 0) {
			//sps and pps should be ready
			frameList.add(getVideoHeaderFrame(sps, pps));
			audioList.add(getAudioHeaderFrame());
		}
		int timeStamp = 0;
		int audioFrameStartIndex = 0;
		boolean audioStarted = false;
		while (i < buffer.length){

			if (buffer[i] == 0 
					&& buffer[i+1] == 0 
					&& (buffer[i+2] != 0 || buffer[i+3] != 0) 
					&& ( (buffer[i+4] == (byte)0x65 && frameCount%30 == 0)
							|| (buffer[i+4] == (byte)0x41 && frameCount%30 != 0)
							)
					)
			{		
				//		System.out.println("frameCount->" + frameCount + " location ->" + i);
				if (audioStarted == true) {
					int audioLength = i - audioFrameStartIndex;
					FLVCreator.addNewAudioFrame(buffer, audioLength, audioFrameStartIndex, audioList, aacFrameDuration);
					audioStarted = false;
				}

				int videoFrameLength = ((buffer[i] & 0x000000FF) << 24) | 
						((buffer[i+1] & 0x000000FF) << 16) | 
						((buffer[i+2] & 0x000000FF) << 8) | 
						(buffer[i+3] & 0x000000FF);

				byte[] videoData = new byte[videoFrameLength+4];

				if (buffer.length > (i + videoFrameLength+4)) {
					System.arraycopy(buffer, i, videoData, 0, videoFrameLength+4);
				}
				else  {
					break;
				}

				boolean isInitialFrame = false;
				if ((buffer[i+5] & 0xF8) == 0xB8) {

					isInitialFrame = true;
				}
				else if ( ((buffer[i+5] & 0xFF) == 0x88) 
						&& ((buffer[i+5] & 0x80) == 0x80) ) {
					isInitialFrame = true;
				}

				timeStamp = frameCount * 1000 / 30;

				frameList.add(new Frame(videoData, isInitialFrame, false, timeStamp));
				i += videoFrameLength+4;
				frameCount++;
			}
			else if (buffer[i] == 'm' 
					&& buffer[i+1] == 'o' 
					&&	buffer[i+2] == 'o' 
					&& buffer[i+3] == 'v' ){

				if (audioStarted == true ) {
					int audioLength = i - audioFrameStartIndex;
					if (audioLength >= 179) {
						FLVCreator.addNewAudioFrame(buffer, audioLength, audioFrameStartIndex, audioList, aacFrameDuration);
						audioStarted = false;
					}
				}
				break;				
			}
			else {
				if (buffer[i] == (byte)0x00
						&& buffer[i+1] == (byte)0x00
						&& buffer[i+2] == (byte)0x14
						&& buffer[i+3] == (byte)0x03
						&& buffer[i+4] == (byte)0xe9
						&& buffer[i+5] == (byte)0x1c)
				{
					int audioLength = 6;
					FLVCreator.addNewAudioFrame(buffer, audioLength, i, audioList, aacFrameDuration);
					audioStarted = false;	
					i += 6;
					continue;
				}
				else if (buffer[i] == 0x01  /*&& 
						((buffer[i+1] & 0xF0) == 0x20 /*||
						 (buffer[i+1] & 0xF0) == 0x10)
						 */) {


					int audioLength = i - audioFrameStartIndex;
					if (audioStarted == true && audioLength >= 179 && audioLength < 300) 
					{
						FLVCreator.addNewAudioFrame(buffer, audioLength, audioFrameStartIndex, audioList, aacFrameDuration);
						audioStarted = false;
					}
				}

				if (audioStarted == false) {
					audioFrameStartIndex = i;
					audioStarted = true;
				}				
				i++;
			}

		}
		this.readByCount += i - 1;
		ArrayList<Frame>[] frames = new ArrayList[2];
		frames[0] = frameList;
		frames[1] = audioList;
		return frames;
	}

	private static void addNewAudioFrame(byte[] data, int length, int offset, ArrayList<Frame> list, int duration){
		byte[] audioData = new byte[length];
		int timeStamp = duration * list.size();

		System.arraycopy(data, offset, audioData, 0, audioData.length);		
		list.add(new Frame(audioData, true, false, timeStamp));		

		System.out.println("Count ->" + list.size() + "Audio frame offset -> " + offset + " length -> " + length);

	}

	public byte[] getSps() {
		return sps;
	}

	public byte[] getPps() {
		return pps;
	}
	public int getStartOfDataFrame() {
		return startOfDataFrame;
	}

	public boolean prepareVideoParams(byte[] buffer){

		boolean avcC1Found = false;
		byte[] avcC1Box = null;
		boolean mdatFound = false;
		for (int i = 0; i < buffer.length; i++) {

			if (buffer[i] == 'm' && buffer[i+1] == 'd' && buffer[i+2] == 'a' && buffer[i+3] == 't') {
				startOfDataFrame = i + 4;
				mdatFound = true;
				if (avcC1Found == true) {
					break;
				}
			}
			else if (buffer[i] == 'a' &&
					buffer[i+1] == 'v' &&
					buffer[i+2] == 'c' && 
					buffer[i+3] == 'C' &&
					buffer[i+4] == 0x01)
			{
				int length = buffer[i-1] - 9; // minus 9 is for length 4 byte and avcC1 5 byte 
				avcC1Box = new byte[length];
				System.arraycopy(buffer, i+5, avcC1Box, 0, length);
				avcC1Found = true;
				if (mdatFound == true){
					break;
				}
			}
		}

		boolean result = false;

		if (avcC1Found == true) {
			int spsSize = ((avcC1Box[5] & 0x00FF) << 8) |
					(avcC1Box[6] & 0x00FF);
			sps = new byte[spsSize];
			System.arraycopy(avcC1Box, 7, sps, 0, sps.length);

			int ppsSize = ((avcC1Box[8+sps.length] & 0x00FF) << 8) |
					(avcC1Box[9+sps.length] & 0x00FF);
			pps = new byte[ppsSize];
			System.arraycopy(avcC1Box, 10+sps.length, pps, 0, pps.length);	
			result = true;
		}
		return result;
	}


	private Frame getAudioHeaderFrame(){

		byte[] data = new byte[2];
		data[0] = 0x15;
		data[1] = (byte)0x88;

		Frame frame = new Frame(data, true, true, 0);		
		return frame;		
	}


	private Frame getVideoHeaderFrame(byte[] sps, byte[] pps){

		byte[] data = new byte[11 + sps.length + pps.length];
		data[0] = 1;
		data[1] = sps[1];
		data[2] = sps[2];
		data[3] = sps[3];
		data[4] = (byte)0xff;
		data[5] = (byte)0xe1;
		short spsSize = (short)sps.length;
		data[6] = (byte)((spsSize & 0xFF00) >> 8); 
		data[7] = (byte)(spsSize & 0x00FF); 

		//put sps	
		System.arraycopy(sps, 0, data, 8, sps.length);

		short ppsSize = (short)pps.length;
		data[8 + sps.length] =  1; 		// number of pps
		data[9 + sps.length] = (byte)((ppsSize & 0xFF00) >> 8);
		data[10 + sps.length] = (byte)(ppsSize & 0x00FF); 

		//put pps
		System.arraycopy(pps, 0, data, 11+sps.length, pps.length);

		Frame frame = new Frame(data, true, true, 0);

		return frame;
	}

	/**
	 * sets sps, pps, mdat position
	 * @param filePath
	 * @return
	 */
	public void setVideoParams(byte[] sps, byte[] pps, int startOfDataFrame){
		this.sps = sps;
		this.pps = pps;
		this.startOfDataFrame = startOfDataFrame;
	}




	public byte[] parse() {

		byte[] flvData = null;
		File f = new File(filePath);
		try {
			FileInputStream fileInputStream = new FileInputStream(f);
			int length = (int)f.length();
			int arrayLength = length - this.readByCount;
			if (arrayLength <= 0) {
				return null;
			}
			
			byte[] buffer = new byte[arrayLength];
			
			

			fileInputStream.skip(this.readByCount);

			int byteCount = fileInputStream.read(buffer);
			fileInputStream.close();

			ByteArrayBuffer byteBuffer = new ByteArrayBuffer(0);
			int offset = 0;
			byte[] previousTagSize = new byte[4];
			previousTagSize[0] = 0;
			previousTagSize[1] = 0;
			previousTagSize[2] = 0;
			previousTagSize[3] = 0;
			
			if (this.readByCount == 0) {
				offset = this.startOfDataFrame;
				// put flv header
				byte[] header = this.getHeader();
				byteBuffer.append(header, 0, header.length);
				byteBuffer.append(previousTagSize, 0, previousTagSize.length);
			}

			
			
			ArrayList<Frame>[] framesList = this.getFrameList(buffer, offset, 8000);

			ArrayList<Frame> videoFrameList = framesList[0];
			ArrayList<Frame> audioFrameList = framesList[1];

//			File flv = new File("118.flv");
//			FileOutputStream fileOutputStream = new FileOutputStream(flv);
			
//			fileOutputStream.write(this.getHeader());

//			byte[] previousTagSize = new byte[4];
//			previousTagSize[0] = 0;
//			previousTagSize[1] = 0;
//			previousTagSize[2] = 0;
//			previousTagSize[3] = 0;
//			fileOutputStream.write(previousTagSize);

			int frameCount = audioFrameList.size(); 
			if (videoFrameList.size() > frameCount) {
				frameCount = videoFrameList.size();
			}

			VideoTagHeaderAndData videoTagHeaderAndData;

			AudioTagHeaderAndData audioTagHeaderAndData;
			for (int i = 0; i < frameCount; i++) {
				if (i < videoFrameList.size()) {
					Frame frame = videoFrameList.get(i);
					videoTagHeaderAndData = new VideoTagHeaderAndData(frame.getData(), frame.isInitialFrame(), frame.isSequenceHeader());
					videoTagHeaderAndData.setTimeStamp(frame.getTimeStamp());
					videoTagHeaderAndData.setDataSize(frame.getData().length + VideoTagHeaderAndData.VIDEO_TAG_HEADER_LENGTH);


					byte[] data = videoTagHeaderAndData.getHeader();
					byteBuffer.append(data, 0, data.length);
					//fileOutputStream.write(videoTagHeaderAndData.getHeader());
					data = videoTagHeaderAndData.getTagHeader();
					byteBuffer.append(data, 0, data.length);
					//fileOutputStream.write(videoTagHeaderAndData.getTagHeader());
					data = videoTagHeaderAndData.getData();
					byteBuffer.append(data, 0, data.length);
					//fileOutputStream.write(videoTagHeaderAndData.getData());

					int totalTagSize = videoTagHeaderAndData.getDataSize() + 11;

					previousTagSize[0] = (byte)((totalTagSize & 0xFF000000) >> 24);
					previousTagSize[1] = (byte)((totalTagSize & 0x00FF0000) >> 16);;
					previousTagSize[2] = (byte)((totalTagSize & 0x0000FF00) >> 8);;
					previousTagSize[3] = (byte)(totalTagSize &  0x000000FF);

					byteBuffer.append(previousTagSize, 0, previousTagSize.length);
					//fileOutputStream.write(previousTagSize);
				}

				if (i < audioFrameList.size()) {
					Frame frame = audioFrameList.get(i);
					/*	if (frame.getData().length == 6) {
						continue;
					}
					 */	
					 audioTagHeaderAndData = new AudioTagHeaderAndData(frame.getData(), frame.isSequenceHeader());
					 audioTagHeaderAndData.setTimeStamp(frame.getTimeStamp());
					 audioTagHeaderAndData.setDataSize(frame.getData().length + AudioTagHeaderAndData.AUDIO_TAG_HEADER_LENGTH);


					 byte[] data = audioTagHeaderAndData.getHeader();
					 byteBuffer.append(data, 0, data.length);
					 //fileOutputStream.write(audioTagHeaderAndData.getHeader());
					 data = audioTagHeaderAndData.getTagHeader();
					 byteBuffer.append(data, 0, data.length);
					 //fileOutputStream.write(audioTagHeaderAndData.getTagHeader());
					 data = audioTagHeaderAndData.getData();
					 byteBuffer.append(data, 0, data.length);
					 //fileOutputStream.write(audioTagHeaderAndData.getData());

					 int totalTagSize = audioTagHeaderAndData.getDataSize() + 11;

					 previousTagSize[0] = (byte)((totalTagSize & 0xFF000000) >> 24);
					 previousTagSize[1] = (byte)((totalTagSize & 0x00FF0000) >> 16);;
					 previousTagSize[2] = (byte)((totalTagSize & 0x0000FF00) >> 8);;
					 previousTagSize[3] = (byte)(totalTagSize &  0x000000FF);

					 byteBuffer.append(previousTagSize, 0, previousTagSize.length);
					 //fileOutputStream.write(previousTagSize);
				}
			}
//			fileOutputStream.close();
			
			if (byteBuffer.capacity() > 0)
			{
				flvData = byteBuffer.toByteArray();
			}

		} catch (IOException e) {
			e.printStackTrace();
		}
		
		return flvData;
	}

}
