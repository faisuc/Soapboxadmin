@extends('layouts.master')
@section('title', 'Dashboard')

@section('dashboardContent')
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <!-- <h3 class="text-center">Content goes here!</h3> -->

            <div class="row">
            	<div class="col-sm-12">
            		<?php
            		if(isset($twitter_follower)) {
            			/*echo "<pre>";
            			print_r($twitter_follower);
            			echo "</pre>";*/
            			$twitter_follower = $twitter_follower[0];
            		?>
            		<h3>Twitter</h3>
            		<p><strong>Name: </strong> <?php echo $twitter_follower['name']; ?></p>
            		<p><strong>Screen Name: </strong> <?php echo $twitter_follower['screen_name']; ?></p>
            		<p><strong>Followers: </strong> <?php echo $twitter_follower['formatted_followers_count']; ?></p>
            		<?php
            		}
            		?>
            	</div>
            </div>

            <div class="social_cards_wrapper container">
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
		            		<div class="card_section">
	            				<p class="main-title">Total Fans: <span class="up-down-price">1,175,199 <span class="text-success"><i class="fa 	fa-arrow-circle-up"></i></span>  <span class="prise-down-up"><b>1,175,199</b> Prev 7 Days</span> </span> </p>
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

            </div>


        </div>
    </div>
@endsection