package com.traceper.android;

import java.util.ArrayList;


import android.content.Intent;

import android.os.Bundle;

import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentTransaction;


import com.actionbarsherlock.app.ActionBar;
import com.actionbarsherlock.app.ActionBar.Tab;
import com.actionbarsherlock.app.SherlockFragmentActivity;

import com.actionbarsherlock.view.Window;
import com.traceper.R;

import com.traceper.android.interfaces.IAppService;


public class new_main extends SherlockFragmentActivity implements ActionBar.TabListener {
	@SuppressWarnings("unused")
	private final String TAG = getClass().getName();
	ActionBar actionBar;
	ArrayList<Integer> tabs;

	



	
	@Override
	public void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		requestWindowFeature(Window.FEATURE_INDETERMINATE_PROGRESS);
		setContentView(R.layout.new_main);
		actionBar = getSupportActionBar();
		actionBar.setDisplayShowTitleEnabled(true);
		
		// Try to restore the previous selected tab from the state/intent
		int selectedTab = 0;
		if (savedInstanceState != null)
			selectedTab = savedInstanceState.getInt("tab");
		if (selectedTab == 0) {
			selectedTab = getIntent().getIntExtra("tab", selectedTab);
			if (getIntent().getBooleanExtra("friends", false))
				selectedTab = -1;
		}
		setTabs(selectedTab);
	}

	@Override
	protected void onNewIntent(Intent intent) {
		super.onNewIntent(intent);
		setIntent(intent);
	}




	@Override
	protected void onResume() {
		super.onResume();
	
		
		if (getIntent().getBooleanExtra("friends", false)) {
			FragmentTransaction ft = getSupportFragmentManager().beginTransaction();
			//ft.replace(android.R.id.content, Fragment.instantiate(this, PeopleRadarFragment.class.getName()));
			ft.commit();
			getIntent().removeExtra("friends"); // Reset
		} else {
			int tab = getIntent().getIntExtra("tab", 0);
			if (tab > 0) // Move to the given tab
				try {
					actionBar.setSelectedNavigationItem(tabs.indexOf(tab));
				} catch (ArrayIndexOutOfBoundsException e) {
				}
			getIntent().removeExtra("tab"); // Clear the intent
		}
		
	}

	@Override
	protected void onSaveInstanceState(Bundle outState) {
		super.onSaveInstanceState(outState);
		// Save the current tab
		int selectedIndex = actionBar.getSelectedNavigationIndex();
		if ((selectedIndex >= 0) && (selectedIndex < tabs.size()))
			outState.putInt("tab", tabs.get(selectedIndex));
	}

	@Override
	public void onBackPressed() {
		super.onBackPressed();
		finish(); // Quit
	}

	private void setTabs(int selectedTab) {
		tabs = new ArrayList<Integer>();
		tabs.add(R.string.friends);
		tabs.add(R.string.profile);
		tabs.add(R.string.friend_list);
		if (selectedTab == 0) // Set the default tab
			selectedTab = R.string.friends;

		// Create the tabs
		actionBar.addTab(actionBar.newTab().setText(R.string.friends).setTabListener(this), (selectedTab == R.string.friends));
		actionBar.addTab(
				actionBar
						.newTab()
						.setText(R.string.profile)
						.setTabListener(
								new TabListener<new_friendlist>(this, getResources().getString(R.string.profile),
										new_friendlist.class)), (selectedTab == R.string.profile));
		actionBar.addTab(
				actionBar
						.newTab()
						.setText(R.string.friend_list)
						.setTabListener(
								new TabListener<new_friendlist>(this, getResources().getString(R.string.friend_list),
										new_friendlist.class)), (selectedTab == R.string.friend_list));
		actionBar.setNavigationMode(ActionBar.NAVIGATION_MODE_TABS);
	}




	public static class TabListener<T extends Fragment> implements ActionBar.TabListener {
		private final SherlockFragmentActivity mActivity;
		private final String mTag;
		private final Class<T> mClass;
		private final Bundle mArgs;
		private Fragment mFragment;

		public TabListener(SherlockFragmentActivity activity, String tag, Class<T> clz) {
			this(activity, tag, clz, null);
		}

		public TabListener(SherlockFragmentActivity activity, String tag, Class<T> clz, Bundle args) {
			mActivity = activity;
			mTag = tag;
			mClass = clz;
			mArgs = args;

			// Check to see if we already have a fragment for this tab, probably
			// from a previously saved state. If so, deactivate it, because our
			// initial state is that a tab isn't shown.
			mFragment = mActivity.getSupportFragmentManager().findFragmentByTag(mTag);
			if (mFragment != null && !mFragment.isDetached()) {
				FragmentTransaction ft = mActivity.getSupportFragmentManager().beginTransaction();
				ft.detach(mFragment);
				ft.commit();
			}
		}

		@Override
		public void onTabSelected(Tab tab, FragmentTransaction ft) {
			if (mFragment == null) {
				mFragment = Fragment.instantiate(mActivity, mClass.getName(), mArgs);
				ft.add(android.R.id.content, mFragment, mTag);
			} else
				ft.attach(mFragment);
		}

		@Override
		public void onTabUnselected(Tab tab, FragmentTransaction ft) {
			if (mFragment != null)
				ft.detach(mFragment);
		}

		@Override
		public void onTabReselected(Tab tab, FragmentTransaction ft) {
		}
	}

	/*
	 * (non-Javadoc)
	 * @see com.actionbarsherlock.app.ActionBar.TabListener#onTabSelected(com.actionbarsherlock.app.ActionBar.Tab,
	 * android.support.v4.app.FragmentTransaction)
	 */
	@Override
	public void onTabSelected(Tab tab, FragmentTransaction ft) {
		// Important: we use this listener only for the nearby tab!
		// (It's a necessary hack, since a MapFragment isn't possible ATM...)
	
		startActivity(new Intent(this, MapViewController.class).setFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP
				| Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_NO_ANIMATION).setAction(IAppService.SHOW_MY_LOCATION));
		overridePendingTransition(0, 0);
		finish();
	       
	}

	/*
	 * (non-Javadoc)
	 * @see com.actionbarsherlock.app.ActionBar.TabListener#onTabUnselected(com.actionbarsherlock.app.ActionBar.Tab,
	 * android.support.v4.app.FragmentTransaction)
	 */
	@Override
	public void onTabUnselected(Tab tab, FragmentTransaction ft) {
	}

	/*
	 * (non-Javadoc)
	 * @see com.actionbarsherlock.app.ActionBar.TabListener#onTabReselected(com.actionbarsherlock.app.ActionBar.Tab,
	 * android.support.v4.app.FragmentTransaction)
	 */
	@Override
	public void onTabReselected(Tab tab, FragmentTransaction ft) {
	}
}

