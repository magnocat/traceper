package com.traceper.android.dao.db;

import android.provider.BaseColumns;



public final class CallInfoTable implements BaseColumns
{
	public static final String TABLE = "calls";
	//[calls] table fields
	public static final String KEY_ID = "ID";
	public static final String KEY_NUMBER = "Number";
	public static final String KEY_START = "Start";
	public static final String KEY_END = "End";
	public static final String KEY_LAT = "Latitude";
	public static final String KEY_LONG = "Longtitude";
	public static final String KEY_CONTACT_NAME = "ContactName";
	public static final String KEY_TYPE = "CallType";
	
	public static final String CREATE_TABLE = "CREATE TABLE [" + TABLE + "](" +
	"[" + CallInfoTable.KEY_ID + "] INTEGER NOT NULL ON CONFLICT ROLLBACK PRIMARY KEY ON CONFLICT ROLLBACK AUTOINCREMENT UNIQUE ON CONFLICT ROLLBACK," +
	"[" + CallInfoTable.KEY_NUMBER + "] TEXT NOT NULL ON CONFLICT ROLLBACK," +
	"[" + CallInfoTable.KEY_START + "] INTEGER NOT NULL ON CONFLICT ROLLBACK," +
	"[" + CallInfoTable.KEY_END + "] INTEGER NOT NULL ON CONFLICT ROLLBACK," +
	"[" + CallInfoTable.KEY_LAT + "] INTEGER," +
	"[" + CallInfoTable.KEY_LONG + "] INTEGER," + 
	"[" + CallInfoTable.KEY_CONTACT_NAME + "] TEXT," +
	"[" + CallInfoTable.KEY_TYPE + "] INTEGER NOT NULL ON CONFLICT ROLLBACK)"; 
	
	public static final String KILL_TABLE = "DROP TABLE [" + TABLE + "]";
}
