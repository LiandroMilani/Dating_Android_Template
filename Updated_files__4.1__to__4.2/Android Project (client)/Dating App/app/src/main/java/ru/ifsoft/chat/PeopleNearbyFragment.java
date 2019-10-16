package ru.ifsoft.chat;

import android.Manifest;
import android.app.Activity;
import android.content.Intent;
import android.content.pm.PackageManager;
import android.location.Location;
import android.location.LocationManager;
import android.net.Uri;
import android.os.Bundle;
import android.provider.Settings;
import android.support.annotation.NonNull;
import android.support.design.widget.Snackbar;
import android.support.v4.app.ActivityCompat;
import android.support.v4.app.Fragment;
import android.support.v4.content.ContextCompat;
import android.support.v4.widget.SwipeRefreshLayout;
import android.support.v7.widget.DefaultItemAnimator;
import android.support.v7.widget.GridLayoutManager;
import android.support.v7.widget.LinearLayoutManager;
import android.support.v7.widget.RecyclerView;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.Menu;
import android.view.MenuInflater;
import android.view.MenuItem;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.TextView;
import android.widget.Toast;

import com.android.volley.DefaultRetryPolicy;
import com.android.volley.Request;
import com.android.volley.Response;
import com.android.volley.RetryPolicy;
import com.android.volley.VolleyError;
import com.google.android.gms.location.FusedLocationProviderClient;
import com.google.android.gms.location.LocationServices;
import com.google.android.gms.tasks.OnCompleteListener;
import com.google.android.gms.tasks.Task;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.Map;
import java.util.concurrent.TimeUnit;

import ru.ifsoft.chat.adapter.AdvancedPeopleListAdapter;
import ru.ifsoft.chat.app.App;
import ru.ifsoft.chat.constants.Constants;
import ru.ifsoft.chat.dialogs.PeopleNearbySettingsDialog;
import ru.ifsoft.chat.dialogs.PeopleNearbySexDialog;
import ru.ifsoft.chat.dialogs.PeopleNearbySexOrientationDialog;
import ru.ifsoft.chat.model.Profile;
import ru.ifsoft.chat.util.CustomRequest;
import ru.ifsoft.chat.util.Helper;

public class PeopleNearbyFragment extends Fragment implements Constants, SwipeRefreshLayout.OnRefreshListener {

    private static final String STATE_LIST = "State Adapter Data";

    private FusedLocationProviderClient mFusedLocationClient;
    private Location mLastLocation;

    Menu MainMenu;

    RecyclerView mRecyclerView;
    TextView mMessage, mDetails;
    ImageView mSplash;

    SwipeRefreshLayout mItemsContainer;

    LinearLayout mSpotLight, mPermissionSpotlight;

    Button mGrantPermission;

    private ArrayList<Profile> itemsList;
    private AdvancedPeopleListAdapter itemsAdapter;

    private int sex = 2, sexOrientation = 0;

    private int itemId = 0;
    private int arrayLength = 0;
    private Boolean loadingMore = false;
    private Boolean viewMore = false;
    private Boolean restore = false;
    private Boolean spotlight = true;

    private int distance = 50;      // im miles

    int pastVisiblesItems = 0, visibleItemCount = 0, totalItemCount = 0;

    public PeopleNearbyFragment() {
        // Required empty public constructor
    }

    @Override
    public void onCreate(Bundle savedInstanceState) {

        super.onCreate(savedInstanceState);

//        setRetainInstance(true);

        setHasOptionsMenu(true);

        if (savedInstanceState != null) {

            itemsList = savedInstanceState.getParcelableArrayList(STATE_LIST);
            itemsAdapter = new AdvancedPeopleListAdapter(getActivity(), itemsList);

            viewMore = savedInstanceState.getBoolean("viewMore");
            restore = savedInstanceState.getBoolean("restore");
            spotlight = savedInstanceState.getBoolean("spotlight");
            itemId = savedInstanceState.getInt("itemId");
            distance = savedInstanceState.getInt("distance");

            sex = savedInstanceState.getInt("sex");
            sexOrientation = savedInstanceState.getInt("sexOrientation");

        } else {

            itemsList = new ArrayList<Profile>();
            itemsAdapter = new AdvancedPeopleListAdapter(getActivity(), itemsList);

            restore = false;
            spotlight = true;
            itemId = 0;
            sexOrientation = 0;
            distance = 50;

            sex = SEX_UNKNOWN;
        }

        // Get Location

        LocationManager lm = (LocationManager) getActivity().getSystemService(getActivity().LOCATION_SERVICE);

        if (lm.isProviderEnabled(LocationManager.GPS_PROVIDER) && ContextCompat.checkSelfPermission(getActivity(), Manifest.permission.ACCESS_FINE_LOCATION) == PackageManager.PERMISSION_GRANTED) {

            mFusedLocationClient = LocationServices.getFusedLocationProviderClient(getActivity());

            mFusedLocationClient.getLastLocation().addOnCompleteListener(getActivity(), new OnCompleteListener<Location>() {
                @Override
                public void onComplete(@NonNull Task<Location> task) {

                    if (task.isSuccessful() && task.getResult() != null) {

                        mLastLocation = task.getResult();

                        Log.d("GPS", "PeopleNearby onCreate" + Double.toString(mLastLocation.getLatitude()));
                        Log.d("GPS", "PeopleNearby onCreate" + Double.toString(mLastLocation.getLongitude()));

                        App.getInstance().setLat(mLastLocation.getLatitude());
                        App.getInstance().setLng(mLastLocation.getLongitude());

                    } else {

                        Log.d("GPS", "getLastLocation:exception", task.getException());
                    }
                }
            });
        }
    }

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState) {

        View rootView = inflater.inflate(R.layout.fragment_people_nearby, container, false);

        mItemsContainer = (SwipeRefreshLayout) rootView.findViewById(R.id.container_items);
        mItemsContainer.setOnRefreshListener(this);

        mMessage = (TextView) rootView.findViewById(R.id.message);
        mSplash = (ImageView) rootView.findViewById(R.id.splash);

        mSpotLight = (LinearLayout) rootView.findViewById(R.id.spotlight);
        mDetails = (TextView) rootView.findViewById(R.id.openLocationSettings);

        mPermissionSpotlight = (LinearLayout) rootView.findViewById(R.id.permission_spotlight);
        mGrantPermission = (Button) rootView.findViewById(R.id.grantPermissionBtn);

        mRecyclerView = (RecyclerView) rootView.findViewById(R.id.recycler_view);

        final LinearLayoutManager mLayoutManager = new GridLayoutManager(getActivity(), Helper.getGridSpanCount(getActivity()));
        mRecyclerView.setLayoutManager(mLayoutManager);
        mRecyclerView.setHasFixedSize(true);
        mRecyclerView.setItemAnimator(new DefaultItemAnimator());

        mRecyclerView.setAdapter(itemsAdapter);

        mRecyclerView.addOnScrollListener(new RecyclerView.OnScrollListener()
        {
            @Override
            public void onScrolled(RecyclerView recyclerView, int dx, int dy)
            {

                //check for scroll down

                if (dy > 0) {

                    visibleItemCount = mLayoutManager.getChildCount();
                    totalItemCount = mLayoutManager.getItemCount();
                    pastVisiblesItems = mLayoutManager.findFirstVisibleItemPosition();

                    if (!loadingMore) {

                        if ((visibleItemCount + pastVisiblesItems) >= totalItemCount && (viewMore) && !(mItemsContainer.isRefreshing())) {

                            loadingMore = true;
                            Log.e("...", "Last Item Wow !");

                            getItems();
                        }
                    }
                }
            }
        });


        itemsAdapter.setOnItemClickListener(new AdvancedPeopleListAdapter.OnItemClickListener() {

            @Override
            public void onItemClick(View view, Profile item, int position) {

                Intent intent = new Intent(getActivity(), ProfileActivity.class);
                intent.putExtra("profileId", item.getId());
                startActivity(intent);
            }
        });

        if (itemsAdapter.getItemCount() == 0) {

            showMessage(getText(R.string.label_empty_list).toString());

        } else {

            hideMessage();
        }

        mDetails.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {

                Intent i = new Intent(getActivity(), LocationActivity.class);
                startActivityForResult(i, 101);
            }
        });


        mGrantPermission.setOnClickListener(new View.OnClickListener() {

            @Override
            public void onClick(View v) {

                if (ContextCompat.checkSelfPermission(getActivity(), android.Manifest.permission.ACCESS_FINE_LOCATION) != PackageManager.PERMISSION_GRANTED && ContextCompat.checkSelfPermission(getActivity(), android.Manifest.permission.ACCESS_COARSE_LOCATION) != PackageManager.PERMISSION_GRANTED) {

                    if (ActivityCompat.shouldShowRequestPermissionRationale(getActivity(), android.Manifest.permission.ACCESS_COARSE_LOCATION) || ActivityCompat.shouldShowRequestPermissionRationale(getActivity(), android.Manifest.permission.ACCESS_FINE_LOCATION)){

                        ActivityCompat.requestPermissions(getActivity(), new String[]{android.Manifest.permission.ACCESS_COARSE_LOCATION, android.Manifest.permission.ACCESS_FINE_LOCATION}, MY_PERMISSIONS_REQUEST_ACCESS_LOCATION);

                    } else {

                        ActivityCompat.requestPermissions(getActivity(), new String[]{android.Manifest.permission.ACCESS_COARSE_LOCATION, android.Manifest.permission.ACCESS_FINE_LOCATION}, MY_PERMISSIONS_REQUEST_ACCESS_LOCATION);
                    }
                }
            }
        });

        updateSpotLight();

        if (!restore && App.getInstance().getLat() != 0.000000 && App.getInstance().getLng() != 0.000000) {

            if (ContextCompat.checkSelfPermission(getActivity(), android.Manifest.permission.ACCESS_FINE_LOCATION) != PackageManager.PERMISSION_GRANTED && ContextCompat.checkSelfPermission(getActivity(), android.Manifest.permission.ACCESS_COARSE_LOCATION) != PackageManager.PERMISSION_GRANTED) {

                updateSpotLight();

            } else {

                showMessage(getText(R.string.msg_loading_2).toString());

                getItems();
            }
        }


        // Inflate the layout for this fragment
        return rootView;
    }

    public void updateSpotLight() {

        if (ContextCompat.checkSelfPermission(getActivity(), android.Manifest.permission.ACCESS_FINE_LOCATION) != PackageManager.PERMISSION_GRANTED && ContextCompat.checkSelfPermission(getActivity(), android.Manifest.permission.ACCESS_COARSE_LOCATION) != PackageManager.PERMISSION_GRANTED) {

            if (ActivityCompat.shouldShowRequestPermissionRationale(getActivity(), android.Manifest.permission.ACCESS_COARSE_LOCATION) || ActivityCompat.shouldShowRequestPermissionRationale(getActivity(), android.Manifest.permission.ACCESS_FINE_LOCATION)){

                showPermissionSpotlight();
                hideNoLocationSpotlight();
                hideItemsContainer();
                hideMessage();

            } else {

                showPermissionSpotlight();
                hideNoLocationSpotlight();
                hideItemsContainer();
                hideMessage();
            }

        } else {

            hidePermissionSpotlight();

            if (App.getInstance().getLat() != 0.000000 && App.getInstance().getLng() != 0.000000) {

                hidePermissionSpotlight();
                hideNoLocationSpotlight();
                showItemsContainer();

            } else {

                showNoLocationSpotlight();
                hideItemsContainer();
                hideMessage();
            }
        }

        getActivity().invalidateOptionsMenu();
    }

    public void showItemsContainer() {

        mItemsContainer.setVisibility(View.VISIBLE);
    }

    public void hideItemsContainer() {

        mItemsContainer.setVisibility(View.GONE);
    }

    public void showPermissionSpotlight() {

        mPermissionSpotlight.setVisibility(View.VISIBLE);
    }

    public void showNoLocationSpotlight() {

        mSpotLight.setVisibility(View.VISIBLE);
    }

    public void hidePermissionSpotlight() {

        mPermissionSpotlight.setVisibility(View.GONE);
    }

    public void hideNoLocationSpotlight() {

        mSpotLight.setVisibility(View.GONE);
    }

    public void updateItems() {

        if (App.getInstance().getLat() != 0.000000 && App.getInstance().getLng() != 0.000000) {

            showMessage(getText(R.string.msg_loading_2).toString());

            itemId = 0;

            getItems();
        }
    }

    @Override
    public void onStart() {

        super.onStart();

        updateSpotLight();
    }

    @Override
    public void onRefresh() {

        if (App.getInstance().isConnected()) {

            itemId = 0;

            getItems();

        } else {

            mItemsContainer.setRefreshing(false);
        }
    }

    @Override
    public void onActivityResult(int requestCode, int resultCode, Intent data) {

        super.onActivityResult(requestCode, resultCode, data);

        if (requestCode == 101 && resultCode == getActivity().RESULT_OK) {

            updateSpotLight();

            updateItems();

        } else if (requestCode == 10001 && resultCode == getActivity().RESULT_OK) {

            updateSpotLight();

            updateItems();
        }
    }

    @Override
    public void onRequestPermissionsResult(int requestCode, String permissions[], int[] grantResults) {

        super.onRequestPermissionsResult(requestCode, permissions, grantResults);

        switch (requestCode) {

            case MY_PERMISSIONS_REQUEST_ACCESS_LOCATION: {

                // If request is cancelled, the result arrays are empty.

                if (grantResults.length > 0 && grantResults[0] == PackageManager.PERMISSION_GRANTED) {

                    LocationManager lm = (LocationManager) getActivity().getSystemService(getActivity().LOCATION_SERVICE);

                    if (lm.isProviderEnabled(LocationManager.GPS_PROVIDER) && ContextCompat.checkSelfPermission(getActivity(), Manifest.permission.ACCESS_FINE_LOCATION) == PackageManager.PERMISSION_GRANTED) {

                        mFusedLocationClient = LocationServices.getFusedLocationProviderClient(getActivity());

                        mFusedLocationClient.getLastLocation().addOnCompleteListener(getActivity(), new OnCompleteListener<Location>() {
                            @Override
                            public void onComplete(@NonNull Task<Location> task) {

                                if (task.isSuccessful() && task.getResult() != null) {

                                    mLastLocation = task.getResult();

                                    Log.d("GPS", "PeopleNearby onComplete" + Double.toString(mLastLocation.getLatitude()));
                                    Log.d("GPS", "PeopleNearby onComplete" + Double.toString(mLastLocation.getLongitude()));

                                    App.getInstance().setLat(mLastLocation.getLatitude());
                                    App.getInstance().setLng(mLastLocation.getLongitude());

                                } else {

                                    Log.d("GPS", "getLastLocation:exception", task.getException());
                                }

                                updateSpotLight();

                                updateItems();
                            }
                        });
                    }

                    updateSpotLight();

                } else if (grantResults.length > 0 && grantResults[0] == PackageManager.PERMISSION_DENIED) {

                    if (!ActivityCompat.shouldShowRequestPermissionRationale(getActivity(), android.Manifest.permission.ACCESS_COARSE_LOCATION) || !ActivityCompat.shouldShowRequestPermissionRationale(getActivity(), android.Manifest.permission.ACCESS_FINE_LOCATION)) {

                        showNoLocationPermissionSnackbar();
                    }
                }

                return;
            }
        }
    }

    public void showNoLocationPermissionSnackbar() {

        Snackbar.make(getView(), getString(R.string.label_no_location_permission) , Snackbar.LENGTH_LONG).setAction(getString(R.string.action_settings), new View.OnClickListener() {

            @Override
            public void onClick(View v) {

                openApplicationSettings();

                Toast.makeText(getActivity(), getString(R.string.label_grant_location_permission), Toast.LENGTH_SHORT).show();

            }

        }).show();
    }

    public void openApplicationSettings() {

        Intent appSettingsIntent = new Intent(Settings.ACTION_APPLICATION_DETAILS_SETTINGS, Uri.parse("package:" + getActivity().getPackageName()));
        startActivityForResult(appSettingsIntent, 10001);
    }

    @Override
    public void onSaveInstanceState(Bundle outState) {

        super.onSaveInstanceState(outState);

        outState.putBoolean("viewMore", viewMore);
        outState.putBoolean("restore", true);
        outState.putBoolean("spotlight", spotlight);
        outState.putInt("itemId", itemId);
        outState.putInt("sex", sex);
        outState.putInt("sexOrientation", sexOrientation);
        outState.putInt("distance", distance);
        outState.putParcelableArrayList(STATE_LIST, itemsList);
    }

    public void onChangeSexOrientation(int position) {

        sexOrientation = position;

        itemId = 0;

        getItems();
    }

    public void onChangeSex(int position) {

        switch (position) {

            case 0: {

                sex = SEX_UNKNOWN;

                itemId = 0;

                getItems();

                break;
            }

            case 1: {

                sex = SEX_FEMALE;

                itemId = 0;

                getItems();

                break;
            }

            default: {

                sex = SEX_MALE;

                itemId = 0;

                getItems();

                break;
            }
        }
    }

    public void onChangeDistance(int position) {

        switch (position) {

            case 0: {

                distance = 50;

                itemId = 0;

                getItems();

                break;
            }

            case 1: {

                distance = 100;

                itemId = 0;

                getItems();

                break;
            }

            case 2: {

                distance = 250;

                itemId = 0;

                getItems();

                break;
            }

            case 3: {

                distance = 500;

                itemId = 0;

                getItems();

                break;
            }

            case 4: {

                distance = 1000;

                itemId = 0;

                getItems();

                break;
            }

            default: {

                distance = 50;

                itemId = 0;

                getItems();

                break;
            }
        }
    }

    public void getItems() {

        if (App.getInstance().getLat() != 0.000000 && App.getInstance().getLng() != 0.000000) {

            mItemsContainer.setRefreshing(true);

            CustomRequest jsonReq = new CustomRequest(Request.Method.POST, METHOD_PROFILE_PEOPLE_NEARBY_GET, null,
                    new Response.Listener<JSONObject>() {
                        @Override
                        public void onResponse(JSONObject response) {

                            if (!isAdded() || getActivity() == null) {

                                Log.e("ERROR", "PeopleNearbyFragment Not Added to Activity");

                                return;
                            }

                            if (!loadingMore) {

                                itemsList.clear();
                            }

                            try {

                                arrayLength = 0;

                                if (!response.getBoolean("error")) {

                                    itemId = response.getInt("itemId");

                                    if (response.has("items")) {

                                        JSONArray usersArray = response.getJSONArray("items");

                                        arrayLength = usersArray.length();

                                        if (arrayLength > 0) {

                                            for (int i = 0; i < usersArray.length(); i++) {

                                                JSONObject userObj = (JSONObject) usersArray.get(i);

                                                Profile profile = new Profile(userObj);

                                                itemsList.add(profile);
                                            }
                                        }
                                    }

                                }

                            } catch (JSONException e) {

                                e.printStackTrace();

                            } finally {

                                loadingComplete();

                                Log.d("Success", response.toString());
                            }
                        }
                    }, new Response.ErrorListener() {
                @Override
                public void onErrorResponse(VolleyError error) {

                    if (!isAdded() || getActivity() == null) {

                        Log.e("ERROR", "PeopleNearbyFragment Not Added to Activity");

                        return;
                    }

                    loadingComplete();
                }
            }) {

                @Override
                protected Map<String, String> getParams() {
                    Map<String, String> params = new HashMap<String, String>();
                    params.put("accountId", Long.toString(App.getInstance().getId()));
                    params.put("accessToken", App.getInstance().getAccessToken());
                    params.put("distance", Integer.toString(distance));
                    params.put("lat", Double.toString(App.getInstance().getLat()));
                    params.put("lng", Double.toString(App.getInstance().getLng()));
                    params.put("itemId", Long.toString(itemId));
                    params.put("sex", Integer.toString(sex));
                    params.put("sex_orientation", Integer.toString(sexOrientation));

                    return params;
                }
            };

            RetryPolicy policy = new DefaultRetryPolicy((int) TimeUnit.SECONDS.toMillis(15), DefaultRetryPolicy.DEFAULT_MAX_RETRIES, DefaultRetryPolicy.DEFAULT_BACKOFF_MULT);

            jsonReq.setRetryPolicy(policy);

            App.getInstance().addToRequestQueue(jsonReq);
        }
    }

    public void loadingComplete() {

        if (arrayLength == LIST_ITEMS) {

            viewMore = true;

        } else {

            viewMore = false;
        }

        itemsAdapter.notifyDataSetChanged();

        if (itemsAdapter.getItemCount() == 0) {

            showMessage(getText(R.string.label_empty_list).toString());

        } else {

            hideMessage();
        }

        loadingMore = false;
        mItemsContainer.setRefreshing(false);
    }

    public void showMessage(String message) {

        mMessage.setText(message);
        mMessage.setVisibility(View.VISIBLE);

        mSplash.setVisibility(View.VISIBLE);
    }

    public void hideMessage() {

        mMessage.setVisibility(View.GONE);

        mSplash.setVisibility(View.GONE);
    }

    @Override
    public void onPrepareOptionsMenu(Menu menu) {

        super.onPrepareOptionsMenu(menu);
    }

    @Override
    public void onCreateOptionsMenu(Menu menu, MenuInflater inflater) {

        super.onCreateOptionsMenu(menu, inflater);

        menu.clear();

        inflater.inflate(R.menu.menu_nearby, menu);

        MainMenu = menu;
    }

    @Override
    public boolean onOptionsItemSelected(MenuItem item) {
        // Handle action bar item clicks here. The action bar will
        // automatically handle clicks on the Home/Up button, so long
        // as you specify a parent activity in AndroidManifest.xml.
        switch (item.getItemId()) {

            case R.id.action_nearby_settings: {

                /** Getting the fragment manager */
                android.app.FragmentManager fm = getActivity().getFragmentManager();

                /** Instantiating the DialogFragment class */
                PeopleNearbySettingsDialog alert = new PeopleNearbySettingsDialog();

                /** Creating a bundle object to store the selected item's index */
                Bundle b  = new Bundle();

                /** Storing the selected item's index in the bundle object */
                b.putInt("distance", distance);

                /** Setting the bundle object to the dialog fragment object */
                alert.setArguments(b);

                /** Creating the dialog fragment object, which will in turn open the alert dialog window */

                alert.show(fm, "alert_dialog_nearby_settings");

                return true;
            }

            case R.id.action_nearby_sex: {

                /** Getting the fragment manager */
                android.app.FragmentManager fm = getActivity().getFragmentManager();

                /** Instantiating the DialogFragment class */
                PeopleNearbySexDialog alert = new PeopleNearbySexDialog();

                /** Creating a bundle object to store the selected item's index */
                Bundle b  = new Bundle();

                /** Storing the selected item's index in the bundle object */
                b.putInt("sex", sex);

                /** Setting the bundle object to the dialog fragment object */
                alert.setArguments(b);

                /** Creating the dialog fragment object, which will in turn open the alert dialog window */

                alert.show(fm, "alert_dialog_nearby_sex_settings");

                return true;
            }

            case R.id.action_nearby_sex_orientation_settings: {

                /** Getting the fragment manager */
                android.app.FragmentManager fm = getActivity().getFragmentManager();

                /** Instantiating the DialogFragment class */
                PeopleNearbySexOrientationDialog alert = new PeopleNearbySexOrientationDialog();

                /** Creating a bundle object to store the selected item's index */
                Bundle b  = new Bundle();

                /** Storing the selected item's index in the bundle object */
                b.putInt("sexOrientation", sexOrientation);

                /** Setting the bundle object to the dialog fragment object */
                alert.setArguments(b);

                /** Creating the dialog fragment object, which will in turn open the alert dialog window */

                alert.show(fm, "alert_dialog_nearby_sex_orientation_settings");

                return true;
            }

            default: {

                return super.onOptionsItemSelected(item);
            }
        }
    }

    @Override
    public void onAttach(Activity activity) {
        super.onAttach(activity);
    }

    @Override
    public void onDetach() {
        super.onDetach();
    }
}