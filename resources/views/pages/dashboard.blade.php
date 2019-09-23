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
	            	if(isset($facebook_follower)) {
	            	?>
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
		            			$fb_talking_about_count = $fb_data['talking_about_count'];
		            			$fb_fan_count = $fb_data['fan_count'];
		            			$fb_rating_count = $fb_data['rating_count'];
		            			$fb_published_posts_count = $fb_data['published_posts_count'];
		            		?>
		            		<div class="card_section">
			            		<?php
			            		$circle = 'fa fa-minus-circle';
								$color = 'text-gray';
		            			if($fb_talking_about_count > $fbpastinfo->fb_talking_about_count) {
		            				$circle = 'fa fa-arrow-circle-up';
		            				$color = 'text-success';
		            			}else if($fb_talking_about_count < $fbpastinfo->fb_talking_about_count) {
									$circle = 'fa fa-arrow-circle-down';
									$color = 'text-danger';
		            			}else{
									$circle = 'fa fa-minus-circle';
									$color = 'text-gray';
		            			}
			            		?>		            			
            					<p class="main-title">Total Talking About Count: <span class="up-down-price"><?php echo $fb_talking_about_count; ?> <span class="{{ $color }}"><i class="{{ $circle }}"></i></span>  <span class="prise-down-up"><b>{{ $fbpastinfo->fb_talking_about_count }}</b><br> Prev 7 Days</span> </span> </p>
		            		</div>
		            		<div class="card_section">
		            			<?php
		            			if($fb_fan_count > $fbpastinfo->fb_fan_count) {
		            				$circle = 'fa fa-arrow-circle-up';
		            				$color = 'text-success';
		            			}else if($fb_fan_count < $fbpastinfo->fb_fan_count) {
									$circle = 'fa fa-arrow-circle-down';
									$color = 'text-danger';
		            			}else{
									$circle = 'fa fa-minus-circle';
									$color = 'text-gray';
		            			}
			            		?>
		            			<p class="main-title">Total Friends: <span class="up-down-price"><?php echo $fb_fan_count; ?> <span class="{{ $color }}"><i class="{{ $circle }}"></i></span>  <span class="prise-down-up"><b>{{ $fbpastinfo->fb_fan_count }}</b><br> Prev 7 Days</span> </span> </p>
		            		</div>
		            		<div class="card_section">
		            			<?php
		            			if($fb_rating_count > $fbpastinfo->fb_rating_count) {
		            				$circle = 'fa fa-arrow-circle-up';
		            				$color = 'text-success';
		            			}else if($fb_rating_count < $fbpastinfo->fb_rating_count) {
									$circle = 'fa fa-arrow-circle-down';
									$color = 'text-danger';
		            			}else{
									$circle = 'fa fa-minus-circle';
									$color = 'text-gray';
		            			}
			            		?>
	            				<p class="main-title">Total Rating Count: <span class="up-down-price"><?php echo $fb_rating_count; ?> <span class="{{ $color }}"><i class="{{ $circle }}"></i></span>  <span class="prise-down-up"><b>{{ $fbpastinfo->fb_rating_count }}</b><br> Prev 7 Days</span> </span> </p>
		            		</div>
		            		<div class="card_section">
		            			<?php
		            			if($fb_published_posts_count > $fbpastinfo->fb_published_posts_count) {
		            				$circle = 'fa fa-arrow-circle-up';
		            				$color = 'text-success';
		            			}else if($fb_published_posts_count < $fbpastinfo->fb_published_posts_count) {
									$circle = 'fa fa-arrow-circle-down';
									$color = 'text-danger';
		            			}else{
									$circle = 'fa fa-minus-circle';
									$color = 'text-gray';
		            			}
			            		?>
	            				<p class="main-title">Total Posts Summary: <span class="up-down-price"><?php echo $fb_published_posts_count; ?> <span class="{{ $color }}"><i class="{{ $circle }}"></i></span>  <span class="prise-down-up"><b>{{ $fbpastinfo->fb_published_posts_count }}</b><br> Prev 7 Days</span> </span> </p>
		            		</div>		            		
	            		</div>

	            	</div>
	            	<?php
	            	}
	            	?>

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
			            			$twt_total_fans = $twt_data['followers'];
			            			$twt_total_following = $twt_data['friends'];
			            			$twt_total_likes = $twt_data['likes'];
			            			$twt_total_posts = $twt_data['statuses'];
			            		
			            		?>
			            		<div class="card_section">
									<?php
				            			if($twt_total_fans > $twtpastinfo->twt_followers_count) {
				            				$circle = 'fa fa-arrow-circle-up';
				            				$color = 'text-success';
				            			}else if($twt_total_fans < $twtpastinfo->twt_followers_count) {
											$circle = 'fa fa-arrow-circle-down';
											$color = 'text-danger';
				            			}else{
											$circle = 'fa fa-minus-circle';
											$color = 'text-gray';
				            			}
				            		?>
			            			<p class="main-title">Total Followers: <span class="up-down-price"><?php echo $twt_total_fans; ?> <span class="{{ $color }}"><i class="{{ $circle }}"></i></span>  <span class="prise-down-up"><b>{{ $twtpastinfo->twt_followers_count }}</b><br> Prev 7 Days</span> </span> </p>
			            		</div>
			            		<div class="card_section">
			            			<?php
				            			if($twt_total_following > $twtpastinfo->twt_following_count) {
				            				$circle = 'fa fa-arrow-circle-up';
				            				$color = 'text-success';
				            			}else if($twt_total_following < $twtpastinfo->twt_following_count) {
											$circle = 'fa fa-arrow-circle-down';
											$color = 'text-danger';
				            			}else{
											$circle = 'fa fa-minus-circle';
											$color = 'text-gray';
				            			}
				            		?>
			            			<p class="main-title">Total Following: <span class="up-down-price"><?php echo $twt_total_following; ?> <span class="{{ $color }}"><i class="{{ $circle }}"></i></span>  <span class="prise-down-up"><b>{{ $twtpastinfo->twt_following_count }}</b><br> Prev 7 Days</span> </span> </p>
			            		</div>
			            		<div class="card_section">
			            			<?php
				            			if($twt_total_likes > $twtpastinfo->twt_likes_count) {
				            				$circle = 'fa fa-arrow-circle-up';
				            				$color = 'text-success';
				            			}else if($twt_total_likes < $twtpastinfo->twt_likes_count) {
											$circle = 'fa fa-arrow-circle-down';
											$color = 'text-danger';
				            			}else{
											$circle = 'fa fa-minus-circle';
											$color = 'text-gray';
				            			}
				            		?>
			            			<p class="main-title">Total Likes: <span class="up-down-price"><?php echo $twt_total_likes; ?> <span class="{{ $color }}"><i class="{{ $circle }}"></i></span>  <span class="prise-down-up"><b>{{ $twtpastinfo->twt_likes_count }}</b><br> Prev 7 Days</span> </span> </p>
			            		</div>
			            		<div class="card_section">
			            			<?php
				            			if($twt_total_posts > $twtpastinfo->twt_posts_count) {
				            				$circle = 'fa fa-arrow-circle-up';
				            				$color = 'text-success';
				            			}else if($twt_total_posts < $twtpastinfo->twt_posts_count) {
											$circle = 'fa fa-arrow-circle-down';
											$color = 'text-danger';
				            			}else{
											$circle = 'fa fa-minus-circle';
											$color = 'text-gray';
				            			}
				            		?>
			            			<p class="main-title">Total Tweets: <span class="up-down-price"><?php echo $twt_total_posts; ?> <span class="{{ $color }}"><i class="{{ $circle }}"></i></span>  <span class="prise-down-up"><b>{{ $twtpastinfo->twt_posts_count }}</b><br> Prev 7 Days</span> </span> </p>
			            		</div>
			            		
		            		</div>

		            	</div>
		            <?php } ?>

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
			            		
			            			$insta_total_fans = $insta_data['total_fans'];
			            			$insta_total_following = $insta_data['total_following'];
			            			$insta_total_likes = $insta_data['total_likes'];
			            			$insta_total_posts = $insta_data['total_posts'];
			            		?>
			            		<div class="card_section">
			            			<?php
				            			if($insta_total_fans > $instapastinfo->insta_followers_count) {
				            				$circle = 'fa fa-arrow-circle-up';
				            				$color = 'text-success';
				            			}else if($insta_total_fans < $instapastinfo->insta_followers_count) {
											$circle = 'fa fa-arrow-circle-down';
											$color = 'text-danger';
				            			}else{
											$circle = 'fa fa-minus-circle';
											$color = 'text-gray';
				            			}
				            		?>
		            				<p class="main-title">Total Followers: <span class="up-down-price"><?php echo $insta_total_fans; ?> <span class="{{ $color }}"><i class="{{ $circle }}"></i></span>  <span class="prise-down-up"><b>{{ $instapastinfo->insta_followers_count }}</b><br> Prev 7 Days</span> </span> </p>
			            		</div>
			            		<div class="card_section">
			            			<?php
				            			if($insta_total_following > $instapastinfo->insta_following_count) {
				            				$circle = 'fa fa-arrow-circle-up';
				            				$color = 'text-success';
				            			}else if($insta_total_following < $instapastinfo->insta_following_count) {
											$circle = 'fa fa-arrow-circle-down';
											$color = 'text-danger';
				            			}else{
											$circle = 'fa fa-minus-circle';
											$color = 'text-gray';
				            			}
				            		?>
			            			<p class="main-title">Total Following: <span class="up-down-price"><?php echo $insta_total_following; ?> <span class="{{ $color }}"><i class="{{ $circle }}"></i></span>  <span class="prise-down-up"><b>{{ $instapastinfo->insta_following_count }}</b><br> Prev 7 Days</span> </span> </p>
			            		</div>
			            		<div class="card_section">
			            			<?php
				            			if($insta_total_likes > $instapastinfo->insta_likes_count) {
				            				$circle = 'fa fa-arrow-circle-up';
				            				$color = 'text-success';
				            			}else if($insta_total_likes < $instapastinfo->insta_likes_count) {
											$circle = 'fa fa-arrow-circle-down';
											$color = 'text-danger';
				            			}else{
											$circle = 'fa fa-minus-circle';
											$color = 'text-gray';
				            			}
				            		?>
			            			<p class="main-title">Total Likes: <span class="up-down-price"><?php echo $insta_total_likes; ?> <span class="{{ $color }}"><i class="{{ $circle }}"></i></span>  <span class="prise-down-up"><b>{{ $instapastinfo->insta_likes_count }}</b><br> Prev 7 Days</span> </span> </p>
			            		</div>
			            		<div class="card_section">
			            			<?php
				            			if($insta_total_posts > $instapastinfo->insta_posts_count) {
				            				$circle = 'fa fa-arrow-circle-up';
				            				$color = 'text-success';
				            			}else if($insta_total_posts < $instapastinfo->insta_posts_count) {
											$circle = 'fa fa-arrow-circle-down';
											$color = 'text-danger';
				            			}else{
											$circle = 'fa fa-minus-circle';
											$color = 'text-gray';
				            			}
				            		?>
			            			<p class="main-title">Total Posts: <span class="up-down-price"><?php echo $insta_total_posts; ?> <span class="{{ $color }}"><i class="{{ $circle }}"></i></span>  <span class="prise-down-up"><b>{{ $instapastinfo->insta_posts_count }}</b><br> Prev 7 Days</span> </span> </p>
			            		</div>				            		
		            		</div>
		            	</div>
		            <?php } ?>
	            </div>

            </div>


        </div>
    </div>
@endsection