package com.traceper.android.utils;

public final class GeoUtils
{
	public static int getHashFromPoint(int lat, int lon)
	{
		int hash = 391 + ((Object)lat).hashCode();
		hash = hash * 23 + ((Object)lon).hashCode();
		return hash;
	}
}
