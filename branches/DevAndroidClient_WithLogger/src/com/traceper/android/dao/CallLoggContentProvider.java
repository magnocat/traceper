package com.traceper.android.dao;

import java.util.HashMap;

import android.content.ContentProvider;
import android.content.ContentUris;
import android.content.ContentValues;
import android.content.Context;
import android.content.UriMatcher;
import android.database.Cursor;
import android.database.sqlite.SQLiteDatabase;
import android.database.sqlite.SQLiteOpenHelper;
import android.database.sqlite.SQLiteQueryBuilder;
import android.net.Uri;



import com.traceper.android.dao.db.CallInfoTable;
import com.traceper.android.grouping.ContactCriteria;

public class CallLoggContentProvider extends ContentProvider
{
	public static final String CONTENT_TYPE = "vnd.android.cursor.dir/vnd.call.log";
	public static final String SINGLE_ITEM = "vnd.android.cursor.item/vnd.call.log";
	
	public static String AUTHORITY = "com.traceper.android.LogContentProvider";
	
	private static final String allCalls = "calls";
	private static final String lastCall = "last";
	private static final String contactGroups = "contgroup";
	private static final String unknownGroups = "unkngroup";
	private static final String mapDetails = "map";
	private static final String clear = "clear";
	
	public static final Uri CALLS_URI = Uri.parse("content://" + AUTHORITY + "/" + allCalls); 
	public static final Uri LAST_ADDED_CALL_URI = Uri.parse("content://" + AUTHORITY + "/" + lastCall);
	public static final Uri CONTACT_GROUPING_URI = Uri.parse("content://" + AUTHORITY + "/" + contactGroups);
	public static final Uri UNKNOWN_GROUPING_URI = Uri.parse("content://" + AUTHORITY + "/" + unknownGroups);
	public static final Uri MAP_CALL_DETAILS_URI = Uri.parse("content://" + AUTHORITY + "/" + mapDetails);
	public static final Uri CLEAR_CALLS_URI = Uri.parse("content://" + AUTHORITY + "/" + clear);
	
	public static final String DB_NAME = "loggerDB.db3";
	public static final int DB_VERSION = 1;

	private static final UriMatcher logUriMatcher;
	private static HashMap<String, String> logProjectionMap;
	
	private static final int CALLS = 1;
	private static final int LAST_CALL = 2;
	private static final int CONTACT_GROUPING = 3;
	private static final int UNKNOWN_GROUPING = 4;
	private static final int MAP_CALL_DETAILS = 5;
	private static final int CLEAR_CALLS = 6;
	
	private static DatabaseHelper dbHelper;
	private static SQLiteDatabase db;
	
	private static class DatabaseHelper extends SQLiteOpenHelper 
	{
        DatabaseHelper(Context context) 
        {
        	super(context, DB_NAME, null, DB_VERSION);
        }

        @Override
    	public void onCreate(SQLiteDatabase database)
    	{
    		database.execSQL(CallInfoTable.CREATE_TABLE);
    	}
    	
    	@Override
    	public void onUpgrade(SQLiteDatabase database, int oldVersion, int newVersion)
    	{
    		database.execSQL(CallInfoTable.KILL_TABLE);
    		onCreate(database);
    	}
    }
	
	@Override
	public String getType(Uri uri)
	{
		switch (logUriMatcher.match(uri))
		{
		case CALLS:
			return CONTENT_TYPE;
		default:
			throw new IllegalArgumentException("Unknown URI " + uri);
		}
	}

	@Override
	public boolean onCreate()
	{
		dbHelper = new DatabaseHelper(getContext());
		db = dbHelper.getReadableDatabase();
		return (db == null)? false : true;
	}

	@Override
	public Cursor query(Uri uri, String[] projection, String selection, String[] selectionArgs, String sortOrder)
	{
		SQLiteQueryBuilder queryBuilder = new SQLiteQueryBuilder();
		queryBuilder.setTables(CallInfoTable.TABLE);
		
		switch (logUriMatcher.match(uri))
		{
		case CALLS:
			queryBuilder.setProjectionMap(logProjectionMap);
			break;
		case LAST_CALL:
		{
			String query =  "SELECT *, MAX([" + CallInfoTable.KEY_ID + "]) AS MID " +
							"FROM [Calls] LIMIT 1";
			return db.rawQuery(query, null);
		}
		case CONTACT_GROUPING:
		{
			
			String query = "SELECT " +
					CallInfoTable.KEY_NUMBER + ", " +
					CallInfoTable.KEY_CONTACT_NAME +
					" FROM " + CallInfoTable.TABLE +
					" GROUP BY " + CallInfoTable.KEY_NUMBER +
					" HAVING (count(" + CallInfoTable.KEY_NUMBER + ") >= " + ContactCriteria.CALLS_NEED_GROUPING + ") " +
					"OR (" + CallInfoTable.KEY_CONTACT_NAME + " NOT NULL) " +
					"ORDER BY " + CallInfoTable.KEY_CONTACT_NAME;
			return db.rawQuery(query, null);
		}
		case UNKNOWN_GROUPING:
		{
			String query = "SELECT " +
			CallInfoTable.KEY_NUMBER + ", " +
			CallInfoTable.KEY_CONTACT_NAME +
			" FROM " + CallInfoTable.TABLE +
			" GROUP BY " + CallInfoTable.KEY_NUMBER +
			" HAVING (count(" + CallInfoTable.KEY_NUMBER + ") < 5) " +
			"OR (" + CallInfoTable.KEY_CONTACT_NAME + " IS NULL) " +
			"ORDER BY " + CallInfoTable.KEY_CONTACT_NAME;
			return db.rawQuery(query, null);
		}
		case MAP_CALL_DETAILS:
		{
			String query = "SELECT " +
			CallInfoTable.KEY_NUMBER + ", " +
			CallInfoTable.KEY_CONTACT_NAME + ", " +
			CallInfoTable.KEY_LAT + ", " +
			CallInfoTable.KEY_LONG + 
			" FROM " + CallInfoTable.TABLE;
			return db.rawQuery(query, null);
		}
		default:
			throw new IllegalArgumentException("Unknown URI " + uri);
		}
		Cursor cursor = queryBuilder.query(db, projection, selection, selectionArgs, null, null, sortOrder);
		cursor.setNotificationUri(getContext().getContentResolver(), uri);
		return cursor;
	}

	@Override
	public int delete(Uri uri, String where, String[] whereArg)	
	{
		if (logUriMatcher.match(uri) == CLEAR_CALLS)
		{
			getContext().getContentResolver().notifyChange(CallLoggContentProvider.CLEAR_CALLS_URI, null);
			return db.delete(CallInfoTable.TABLE, null, null);
		}
		return 0;
	}
	
	@Override
	public Uri insert(Uri uri, ContentValues initValues)
	{
		long rowID = db.insert(CallInfoTable.TABLE, null, initValues);
		if (rowID > 0)
		{
			uri = ContentUris.withAppendedId(CALLS_URI, rowID);
			getContext().getContentResolver().notifyChange(CallLoggContentProvider.CALLS_URI, null);
			return uri;
		}
		return null;
	}
	
	@Override
	public int update(Uri uri, ContentValues values, String where, String[] whereArgs)
	{return 0;}
	
	static
	{
		logUriMatcher = new UriMatcher(UriMatcher.NO_MATCH);
		logUriMatcher.addURI(AUTHORITY, allCalls, CALLS);
		logUriMatcher.addURI(AUTHORITY, lastCall, LAST_CALL);
		logUriMatcher.addURI(AUTHORITY, contactGroups, CONTACT_GROUPING);
		logUriMatcher.addURI(AUTHORITY, unknownGroups, UNKNOWN_GROUPING);
		logUriMatcher.addURI(AUTHORITY, mapDetails, MAP_CALL_DETAILS);
		logUriMatcher.addURI(AUTHORITY, clear, CLEAR_CALLS);
		
		logProjectionMap = new HashMap<String, String>();
		logProjectionMap.put(CallInfoTable.KEY_ID, CallInfoTable.KEY_ID);
		logProjectionMap.put(CallInfoTable.KEY_NUMBER, CallInfoTable.KEY_NUMBER);
		logProjectionMap.put(CallInfoTable.KEY_CONTACT_NAME, CallInfoTable.KEY_CONTACT_NAME);
		logProjectionMap.put(CallInfoTable.KEY_TYPE, CallInfoTable.KEY_TYPE);
		logProjectionMap.put(CallInfoTable.KEY_START, CallInfoTable.KEY_START);
		logProjectionMap.put(CallInfoTable.KEY_END, CallInfoTable.KEY_END);
		logProjectionMap.put(CallInfoTable.KEY_LAT, CallInfoTable.KEY_LAT);
		logProjectionMap.put(CallInfoTable.KEY_LONG, CallInfoTable.KEY_LONG);
	}

}

