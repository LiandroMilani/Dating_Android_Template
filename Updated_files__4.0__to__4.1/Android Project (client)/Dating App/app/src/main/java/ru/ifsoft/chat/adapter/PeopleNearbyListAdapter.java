package ru.ifsoft.chat.adapter;

import android.content.Context;
import android.support.v7.widget.RecyclerView;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.ProgressBar;
import android.widget.TextView;

import com.balysv.materialripple.MaterialRippleLayout;
import com.bumptech.glide.Glide;
import com.bumptech.glide.load.resource.drawable.GlideDrawable;
import com.bumptech.glide.request.RequestListener;
import com.bumptech.glide.request.target.Target;

import java.util.List;

import ru.ifsoft.chat.R;
import ru.ifsoft.chat.model.Profile;


public class PeopleNearbyListAdapter extends RecyclerView.Adapter<PeopleNearbyListAdapter.MyViewHolder> {

	private Context mContext;
	private List<Profile> itemList;

	public class MyViewHolder extends RecyclerView.ViewHolder {

		public TextView mProfileFullname, mProfileUsername;
		public ImageView mProfilePhoto, mProfileOnlineIcon, mProfileIcon, mProfileProIcon;
		public MaterialRippleLayout mParent;
		public ProgressBar mProgressBar;

		public MyViewHolder(View view) {

			super(view);

			mParent = (MaterialRippleLayout) view.findViewById(R.id.parent);

			mProfilePhoto = (ImageView) view.findViewById(R.id.profileImg);
			mProfileFullname = (TextView) view.findViewById(R.id.profileFullname);
			mProfileUsername = (TextView) view.findViewById(R.id.profileUsername);
			mProfileOnlineIcon = (ImageView) view.findViewById(R.id.profileOnlineIcon);
			mProfileIcon = (ImageView) view.findViewById(R.id.profileIcon);
			mProfileProIcon = (ImageView) view.findViewById(R.id.profileProIcon);
			mProgressBar = (ProgressBar) view.findViewById(R.id.progressBar);
		}
	}


	public PeopleNearbyListAdapter(Context mContext, List<Profile> itemList) {

		this.mContext = mContext;
		this.itemList = itemList;
	}

	@Override
	public MyViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {

		View itemView = LayoutInflater.from(parent.getContext()).inflate(R.layout.profile_thumbnail, parent, false);


		return new MyViewHolder(itemView);
	}

	@Override
	public void onBindViewHolder(final MyViewHolder holder, int position) {

		Profile item = itemList.get(position);

		holder.mProgressBar.setVisibility(View.VISIBLE);
		holder.mProfilePhoto.setVisibility(View.VISIBLE);

		if (item.getNormalPhotoUrl() != null && item.getNormalPhotoUrl().length() > 0) {

			final ImageView img = holder.mProfilePhoto;
			final ProgressBar progressView = holder.mProgressBar;

			Glide.with(mContext)
					.load(item.getNormalPhotoUrl())
					.listener(new RequestListener<String, GlideDrawable>() {
						@Override
						public boolean onException(Exception e, String model, Target<GlideDrawable> target, boolean isFirstResource) {

							progressView.setVisibility(View.GONE);
							img.setImageResource(R.drawable.profile_default_photo);
							img.setVisibility(View.VISIBLE);
							return false;
						}

						@Override
						public boolean onResourceReady(GlideDrawable resource, String model, Target<GlideDrawable> target, boolean isFromMemoryCache, boolean isFirstResource) {

							progressView.setVisibility(View.GONE);
							img.setVisibility(View.VISIBLE);
							return false;
						}
					})
					.into(holder.mProfilePhoto);

		} else {

			holder.mProgressBar.setVisibility(View.GONE);
			holder.mProfilePhoto.setVisibility(View.VISIBLE);

			holder.mProfilePhoto.setImageResource(R.drawable.profile_default_photo);
		}

		holder.mProfileFullname.setText(item.getFullname());

		if (item.getDistance() > 0.0) {

			holder.mProfileUsername.setText(String.format("%.1f km @%s", item.getDistance(), item.getUsername()));

		} else {

			holder.mProfileUsername.setText(String.format("@%s", item.getUsername()));
		}

		if (item.isOnline()) {

			holder.mProfileOnlineIcon.setVisibility(View.VISIBLE);

		} else {

			holder.mProfileOnlineIcon.setVisibility(View.GONE);
		}

		if (item.isVerify()) {

			holder.mProfileIcon.setVisibility(View.VISIBLE);

		} else {

			holder.mProfileIcon.setVisibility(View.GONE);
		}

		if (item.isProMode()) {

			holder.mProfileProIcon.setVisibility(View.VISIBLE);

		} else {

			holder.mProfileProIcon.setVisibility(View.GONE);
		}
	}

	@Override
	public int getItemCount() {

		return itemList.size();
	}
}