@extends('layouts.master')
@section('title', 'Dashboard')

@section('dashboardContent')
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <!-- <h3 class="text-center">Content goes here!</h3> -->

            <div class="row">
            	<div class="col-sm-12">
            		<?php
            		/*if(isset($twitter_follower)) {
            			$twitter_follower = $twitter_follower[0];
            		?>
            		<h3>Twitter</h3>
            		<p><strong>Name: </strong> <?php echo $twitter_follower['name']; ?></p>
            		<p><strong>Screen Name: </strong> <?php echo $twitter_follower['screen_name']; ?></p>
            		<p><strong>Followers: </strong> <?php echo $twitter_follower['formatted_followers_count']; ?></p>
            		<?php
            		}*/ /*
            		if(isset($twitter_follower)) {
            		?>
            		<h3>Twitter</h3>
            		<p><strong>Name: </strong> <?php echo $name; ?></p>
            		<p><strong>Screen Name: </strong> <?php echo $screen_name; ?></p>
            		<p><strong>Followers: </strong> <?php echo $followers; ?></p>
            		<p><strong>Friends: </strong> <?php echo $friends; ?></p>
            		<p><strong>Likes: </strong> <?php echo $likes; ?></p>
            		<p><strong>Statuses: </strong> <?php echo $statuses; ?></p>
            		<?php
            		}*/
            		?>
            	</div>
            </div>

            <div class="social_cards_wrapper container">
	            <div class="row">
			            		
			            		<?php
			            		echo "<pre>";
			            		print_r($past_info);
			            		echo "</pre>";
			            		?>
	            	
	            	<?php /*if(isset($facebook_follower)) { ?>
		            	<div class="col-md-4">
		            		<div class="social_card">
			            		<div class="card_header">
				            		<h3>
				            			<!-- <img src="{{ asset('assets/images/logo_hat.png') }}" alt="facebook" height="20px"> -->
				            			<i class="fab fa-facebook"></i>
					            		Facebook
					            	</h3>
					            	<!--p>5.11 Tactical &nbsp; - &nbsp; 5.11 Tactical</p-->
			            		</div>
			            		<?php

				            		$fb_talking_about_count = $fb_fan_count = $fb_rating_count = $fb_published_posts_count = '';
			            			$fb_talking_about_count = $talking_about_count;
			            			$fb_fan_count = $fan_count;
			            			$fb_rating_count = $rating_count;
			            			// $fb_new_like_count = $new_like_count;
			            			$fb_published_posts_count = $published_posts_count;
			            		?>
			            		<div class="card_section">
				            		<?php
				            			if($fb_talking_about_count > $past_info[0]->fb_talking_about_count) {
				            				$circle = 'fa fa-arrow-circle-up';
				            				$color = 'text-success';
				            			}else if($fb_talking_about_count < $past_info[0]->fb_talking_about_count) {
											$circle = 'fa fa-arrow-circle-down';
											$color = 'text-danger';
				            			}else{
											$circle = 'fa fa-minus-circle';
											$color = 'text-gray';
				            			}
				            		?>		            			
	            					<p class="main-title">Total Talking About Count: <span class="up-down-price"><?php echo $fb_talking_about_count; ?> <span class="{{ $color }}"><i class="{{ $circle }}"></i></span>  <span class="prise-down-up"><b>{{ $past_info[0]->fb_talking_about_count }}</b><br> Prev 7 Days</span> </span> </p>
			            		</div>
			            		<div class="card_section">
			            			<?php
				            			if($fb_fan_count > $past_info[0]->fb_fan_count) {
				            				$circle = 'fa fa-arrow-circle-up';
				            				$color = 'text-success';
				            			}else if($fb_fan_count < $past_info[0]->fb_fan_count) {
											$circle = 'fa fa-arrow-circle-down';
											$color = 'text-danger';
				            			}else{
											$circle = 'fa fa-minus-circle';
											$color = 'text-gray';
				            			}
				            		?>
			            			<p class="main-title">Total Friends: <span class="up-down-price"><?php echo $fb_fan_count; ?> <span class="{{ $color }}"><i class="{{ $circle }}"></i></span>  <span class="prise-down-up"><b>{{ $past_info[0]->fb_fan_count }}</b><br> Prev 7 Days</span> </span> </p>
			            		</div>
			            		<div class="card_section">
			            			<?php
				            			if($fb_rating_count > $past_info[0]->fb_rating_count) {
				            				$circle = 'fa fa-arrow-circle-up';
				            				$color = 'text-success';
				            			}else if($fb_rating_count < $past_info[0]->fb_rating_count) {
											$circle = 'fa fa-arrow-circle-down';
											$color = 'text-danger';
				            			}else{
											$circle = 'fa fa-minus-circle';
											$color = 'text-gray';
				            			}
				            		?>
		            				<p class="main-title">Total Rating Count: <span class="up-down-price"><?php echo $fb_rating_count; ?> <span class="{{ $color }}"><i class="{{ $circle }}"></i></span>  <span class="prise-down-up"><b>{{ $past_info[0]->fb_rating_count }}</b><br> Prev 7 Days</span> </span> </p>
			            		</div>
			            		<div class="card_section">
			            			<?php
				            			if($fb_published_posts_count > $past_info[0]->fb_published_posts_count) {
				            				$circle = 'fa fa-arrow-circle-up';
				            				$color = 'text-success';
				            			}else if($fb_published_posts_count < $past_info[0]->fb_published_posts_count) {
											$circle = 'fa fa-arrow-circle-down';
											$color = 'text-danger';
				            			}else{
											$circle = 'fa fa-minus-circle';
											$color = 'text-gray';
				            			}
				            		?>
		            				<p class="main-title">Total Posts Summary: <span class="up-down-price"><?php echo $fb_published_posts_count; ?> <span class="{{ $color }}"><i class="{{ $circle }}"></i></span>  <span class="prise-down-up"><b>{{ $past_info[0]->fb_published_posts_count }}</b><br> Prev 7 Days</span> </span> </p>
			            		</div>		            		
		            		</div>

		            	</div>
		            <?php } */ ?>

					<?php if(isset($twitter_follower)) {?>
		            	<div class="col-md-4">
		            		<div class="social_card">
			            		<div class="card_header">
				            		<h3>
				            			<!-- <img src="{{ asset('assets/images/logo_hat.png') }}" alt="facebook" height="20px"> -->
				            			<i class="fab fa-twitter"></i>
					            		Twitter
					            	</h3>
					            	<!-- p>5.11 Tactical &nbsp; - &nbsp; 5.11 Tactical</p-->
			            		</div>
			            		<?php
			            			$twt_total_fans= $twt_total_following = $twt_total_likes= $twt_total_posts= '';
			            		
			            			$twt_total_fans = $followers;
			            			$twt_total_following = $friends;
			            			$twt_total_likes = $likes;
			            			$twt_total_posts = $statuses;
			            		
			            		?>
			            		<div class="card_section">
									<?php
				            			if($twt_total_fans > $past_info[0]->twt_followers_count) {
				            				$circle = 'fa fa-arrow-circle-up';
				            				$color = 'text-success';
				            			}else if($twt_total_fans < $past_info[0]->twt_followers_count) {
											$circle = 'fa fa-arrow-circle-down';
											$color = 'text-danger';
				            			}else{
											$circle = 'fa fa-minus-circle';
											$color = 'text-gray';
				            			}
				            		?>
			            			<p class="main-title">Total Followers: <span class="up-down-price"><?php echo $twt_total_fans; ?> <span class="{{ $color }}"><i class="{{ $circle }}"></i></span>  <span class="prise-down-up"><b>{{ $past_info[0]->twt_followers_count }}</b><br> Prev 7 Days</span> </span> </p>
			            		</div>
			            		<div class="card_section">
			            			<?php
				            			if($twt_total_following > $past_info[0]->twt_following_count) {
				            				$circle = 'fa fa-arrow-circle-up';
				            				$color = 'text-success';
				            			}else if($twt_total_following < $past_info[0]->twt_following_count) {
											$circle = 'fa fa-arrow-circle-down';
											$color = 'text-danger';
				            			}else{
											$circle = 'fa fa-minus-circle';
											$color = 'text-gray';
				            			}
				            		?>
			            			<p class="main-title">Total Following: <span class="up-down-price"><?php echo $twt_total_following; ?> <span class="{{ $color }}"><i class="{{ $circle }}"></i></span>  <span class="prise-down-up"><b>{{ $past_info[0]->twt_following_count }}</b><br> Prev 7 Days</span> </span> </p>
			            		</div>
			            		<div class="card_section">
			            			<?php
				            			if($twt_total_likes > $past_info[0]->twt_likes_count) {
				            				$circle = 'fa fa-arrow-circle-up';
				            				$color = 'text-success';
				            			}else if($twt_total_likes < $past_info[0]->twt_likes_count) {
											$circle = 'fa fa-arrow-circle-down';
											$color = 'text-danger';
				            			}else{
											$circle = 'fa fa-minus-circle';
											$color = 'text-gray';
				            			}
				            		?>
			            			<p class="main-title">Total Likes: <span class="up-down-price"><?php echo $twt_total_likes; ?> <span class="{{ $color }}"><i class="{{ $circle }}"></i></span>  <span class="prise-down-up"><b>{{ $past_info[0]->twt_likes_count }}</b><br> Prev 7 Days</span> </span> </p>
			            		</div>
			            		<div class="card_section">
			            			<?php
				            			if($twt_total_posts > $past_info[0]->twt_posts_count) {
				            				$circle = 'fa fa-arrow-circle-up';
				            				$color = 'text-success';
				            			}else if($twt_total_posts < $past_info[0]->twt_posts_count) {
											$circle = 'fa fa-arrow-circle-down';
											$color = 'text-danger';
				            			}else{
											$circle = 'fa fa-minus-circle';
											$color = 'text-gray';
				            			}
				            		?>
			            			<p class="main-title">Total Tweets: <span class="up-down-price"><?php echo $twt_total_posts; ?> <span class="{{ $color }}"><i class="{{ $circle }}"></i></span>  <span class="prise-down-up"><b>{{ $past_info[0]->twt_posts_count }}</b><br> Prev 7 Days</span> </span> </p>
			            		</div>
			            		
		            		</div>

		            	</div>
		            <?php }?>

					<?php if(isset($instagram_follower)) {?>
		            	<div class="col-md-4">
		            		<div class="social_card">	
			            		<div class="card_header">
				            		<h3>
				            			<!-- <img src="{{ asset('assets/images/logo_hat.png') }}" alt="facebook" height="20px"> -->
				            			<i class="fab fa-instagram"></i>
					            		Instagram
					            	</h3>
					            	<!--p>5.11 Tactical &nbsp; - &nbsp; 5.11 Tactical</p-->
			            		</div>
			            		<?php
			            			$insta_total_fans = $insta_total_following = $insta_total_likes = $insta_total_posts = '';
			            		
			            			$insta_total_fans = $total_fans;
			            			$insta_total_following = $total_following;
			            			$insta_total_likes = $total_likes;
			            			$insta_total_posts = $total_posts;
			            		?>
			            		<div class="card_section">
			            			<?php
				            			if($insta_total_fans > $past_info[0]->insta_followers_count) {
				            				$circle = 'fa fa-arrow-circle-up';
				            				$color = 'text-success';
				            			}else if($insta_total_fans < $past_info[0]->insta_followers_count) {
											$circle = 'fa fa-arrow-circle-down';
											$color = 'text-danger';
				            			}else{
											$circle = 'fa fa-minus-circle';
											$color = 'text-gray';
				            			}
				            		?>
		            				<p class="main-title">Total Followers: <span class="up-down-price"><?php echo $insta_total_fans; ?> <span class="{{ $color }}"><i class="{{ $circle }}"></i></span>  <span class="prise-down-up"><b>{{ $past_info[0]->insta_followers_count }}</b><br> Prev 7 Days</span> </span> </p>
			            		</div>
			            		<div class="card_section">
			            			<?php
				            			if($insta_total_following > $past_info[0]->insta_following_count) {
				            				$circle = 'fa fa-arrow-circle-up';
				            				$color = 'text-success';
				            			}else if($insta_total_following < $past_info[0]->insta_following_count) {
											$circle = 'fa fa-arrow-circle-down';
											$color = 'text-danger';
				            			}else{
											$circle = 'fa fa-minus-circle';
											$color = 'text-gray';
				            			}
				            		?>
			            			<p class="main-title">Total Following: <span class="up-down-price"><?php echo $insta_total_following; ?> <span class="{{ $color }}"><i class="{{ $circle }}"></i></span>  <span class="prise-down-up"><b>{{ $past_info[0]->insta_following_count }}</b><br> Prev 7 Days</span> </span> </p>
			            		</div>
			            		<div class="card_section">
			            			<?php
				            			if($insta_total_likes > $past_info[0]->insta_likes_count) {
				            				$circle = 'fa fa-arrow-circle-up';
				            				$color = 'text-success';
				            			}else if($insta_total_likes < $past_info[0]->insta_likes_count) {
											$circle = 'fa fa-arrow-circle-down';
											$color = 'text-danger';
				            			}else{
											$circle = 'fa fa-minus-circle';
											$color = 'text-gray';
				            			}
				            		?>
			            			<p class="main-title">Total Likes: <span class="up-down-price"><?php echo $insta_total_likes; ?> <span class="{{ $color }}"><i class="{{ $circle }}"></i></span>  <span class="prise-down-up"><b>{{ $past_info[0]->insta_likes_count }}</b><br> Prev 7 Days</span> </span> </p>
			            		</div>
			            		<div class="card_section">
			            			<?php
				            			if($insta_total_posts > $past_info[0]->insta_posts_count) {
				            				$circle = 'fa fa-arrow-circle-up';
				            				$color = 'text-success';
				            			}else if($insta_total_posts < $past_info[0]->insta_posts_count) {
											$circle = 'fa fa-arrow-circle-down';
											$color = 'text-danger';
				            			}else{
											$circle = 'fa fa-minus-circle';
											$color = 'text-gray';
				            			}
				            		?>
			            			<p class="main-title">Total Posts: <span class="up-down-price"><?php echo $insta_total_posts; ?> <span class="{{ $color }}"><i class="{{ $circle }}"></i></span>  <span class="prise-down-up"><b>{{ $past_info[0]->insta_posts_count }}</b><br> Prev 7 Days</span> </span> </p>
			            		</div>
			            		
		            		</div>

		            	</div>
		            <?php }?>

	            </div>

	            <?php /* fb, twitter, insta row backup
	            <div class="row">
	            	
	            	<div class="col-md-4">
	            		<div class="social_card">
		            		<div class="card_header">
			            		<h3>
			            			<!-- <img src="{{ asset('assets/images/logo_hat.png') }}" alt="facebook" height="20px"> -->
			            			<i class="fab fa-facebook"></i>
				            		Facebook
				            	</h3>
				            	<p>5.11 Tactical &nbsp; - &nbsp; 5.11 Tactical</p>
		            		</div>
		            		<div class="card_section">		            			
            					<p class="main-title">Total Fans: <span class="up-down-price">1,175,199 <span class="text-success"><i class="fa fa-arrow-circle-up"></i></span>  <span class="prise-down-up"><b>1,175,199</b> Prev 7 Days</span> </span> </p>
		            		</div>
		            		<div class="card_section">
		            			<p class="main-title">New Fans: <span class="up-down-price">1,175,199 <span class="text-success"><i class="fa fa-arrow-circle-up"></i></span>  <span class="prise-down-up"><b>1,175,199</b> Prev 7 Days</span> </span> </p>
		            		</div>
		            		<div class="card_section">
	            				<p class="main-title">New Posts: <span class="up-down-price">1,175,199 <span class="text-success"><i class="fa fa-arrow-circle-up"></i></span>  <span class="prise-down-up"><b>1,175,199</b> Prev 7 Days</span> </span> </p>
		            		</div>
		            		<div class="card_section">
	            				<p class="main-title">Engagements: <span class="up-down-price">1,175,199 <span class="text-danger"><i class="fa fa-arrow-circle-down"></i></span>  <span class="prise-down-up"><b>1,175,199</b> Prev 7 Days</span> </span> </p>
		            		</div>
		            		<div class="card_post">
	            				<div class="row">
		            				<div class="col-md-8">
		            					Top Post:
		            				</div>
		            				<div class="col-md-4">
		            					July 30, 2018
		            				</div>
		            			</div>
		            			<div class="row">
		            				<div class="col-md-8">
		            					Lorem Ipsum. Lorem Ipsum. Lorem Ipsum. Lorem Ipsum. Lorem Ipsum. Lorem Ipsum. Lorem Ipsum. Lorem Ipsum. Lorem Ipsum. Lorem Ipsum. Lorem Ipsum. Lorem Ipsum. 
		            				</div>
		            				<div class="col-md-4">
		            					
		            				</div>
		            			</div>
		            		</div>
	            		</div>

	            	</div>

	            	<div class="col-md-4">
	            		<div class="social_card">
		            		<div class="card_header">
			            		<h3>
			            			<!-- <img src="{{ asset('assets/images/logo_hat.png') }}" alt="facebook" height="20px"> -->
			            			<i class="fab fa-twitter"></i>
				            		Twitter
				            	</h3>
				            	<p>5.11 Tactical &nbsp; - &nbsp; 5.11 Tactical</p>
		            		</div>
		            		<div class="card_section">
		            			<p class="main-title">Total Fans: <span class="up-down-price">1,175,199 <span class="text-success"><i class="fa fa-arrow-circle-up"></i></span>  <span class="prise-down-up"><b>1,175,199</b> Prev 7 Days</span> </span> </p>
		            		</div>
		            		<div class="card_section">
		            			<p class="main-title">New Fans: <span class="up-down-price">1,175,199 <span class="text-success"><i class="fa fa-arrow-circle-up"></i></span>  <span class="prise-down-up"><b>1,175,199</b> Prev 7 Days</span> </span> </p>
		            		</div>
		            		<div class="card_section">
		            			<p class="main-title">New Posts: <span class="up-down-price">1,175,199 <span class="text-success"><i class="fa fa-arrow-circle-up"></i></span>  <span class="prise-down-up"><b>1,175,199</b> Prev 7 Days</span> </span> </p>
		            		</div>
		            		<div class="card_section">
		            			<p class="main-title">Engagements: <span class="up-down-price">1,175,199 <span class="text-danger"><i class="fa fa-arrow-circle-down"></i></span>  <span class="prise-down-up"><b>1,175,199</b> Prev 7 Days</span> </span> </p>
		            		</div>
		            		<div class="card_post">
		            			<div class="row">
		            				<div class="col-md-8">
		            					Top Post:
		            				</div>
		            				<div class="col-md-4">
		            					July 30, 2018
		            				</div>
		            			</div>
		            			<div class="row">
		            				<div class="col-md-8">
		            					Lorem Ipsum. Lorem Ipsum. Lorem Ipsum. Lorem Ipsum. Lorem Ipsum. Lorem Ipsum. Lorem Ipsum. Lorem Ipsum. Lorem Ipsum. Lorem Ipsum. Lorem Ipsum. Lorem Ipsum. 
		            				</div>
		            				<div class="col-md-4">
		            					
		            				</div>
		            			</div>
		            		</div>
	            		</div>

	            	</div>

	            	<div class="col-md-4">
	            		<div class="social_card">	
		            		<div class="card_header">
			            		<h3>
			            			<!-- <img src="{{ asset('assets/images/logo_hat.png') }}" alt="facebook" height="20px"> -->
			            			<i class="fab fa-instagram"></i>
				            		Instagram
				            	</h3>
				            	<p>5.11 Tactical &nbsp; - &nbsp; 5.11 Tactical</p>
		            		</div>
		            		<?php
		            		$insta_total_fans = '';
		            		if(isset($instagram_follower)) {
		            			$insta_total_fans = $total_fans;
		            		}
		            		?>
		            		<div class="card_section">
	            				<p class="main-title">Total Fans: <span class="up-down-price"><?php echo $insta_total_fans; ?> <span class="text-success"><i class="fa 	fa-arrow-circle-up"></i></span>  <span class="prise-down-up"><b>1,175,199</b> Prev 7 Days</span> </span> </p>
		            		</div>
		            		<div class="card_section">
		            			<p class="main-title">New Fans: <span class="up-down-price">1,175,199 <span class="text-success"><i class="fa 	fa-arrow-circle-up"></i></span>  <span class="prise-down-up"><b>1,175,199</b> Prev 7 Days</span> </span> </p>
		            		</div>
		            		<div class="card_section">
		            			<p class="main-title">New Posts: <span class="up-down-price">1,175,199 <span class="text-success"><i class="fa 	fa-arrow-circle-up"></i></span>  <span class="prise-down-up"><b>1,175,199</b> Prev 7 Days</span> </span> </p>
		            		</div>
		            		<div class="card_section">
		            			<p class="main-title">Engagements: <span class="up-down-price">1,175,199 <span class="text-danger"><i class="fa 	fa-arrow-circle-down"></i></span>  <span class="prise-down-up"><b>1,175,199</b> Prev 7 Days</span> </span> </p>
		            		</div>
		            		<div class="card_post">
		            			<div class="row">
		            				<div class="col-md-8">
		            					Top Post:
		            				</div>
		            				<div class="col-md-4">
		            					July 30, 2018
		            				</div>
		            			</div>
		            			<div class="row">
		            				<div class="col-md-8">
		            					Lorem Ipsum. Lorem Ipsum. Lorem Ipsum. Lorem Ipsum. Lorem Ipsum. Lorem Ipsum. Lorem Ipsum. Lorem Ipsum. Lorem Ipsum. Lorem Ipsum. Lorem Ipsum. Lorem Ipsum. 
		            				</div>
		            				<div class="col-md-4">
		            					
		            				</div>
		            			</div>
		            		</div>
	            		</div>

	            	</div>

	            </div>
				*/ ?>

            </div>


        </div>
    </div>
@endsection