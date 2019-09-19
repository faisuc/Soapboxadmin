@extends('layouts.master')
@section('title', 'Create New Social Cell')

@section('dashboardContent')
<div class="row">
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
        <div class="section-block" id="basicform">
            <h3 class="section-title">{{ $social_cell->cell_name }}</h3>
        </div>
        <?php
        foreach($social_accounts as $ac_key => $account) {
            echo $account->name.'<br>';
            echo $account->url.'<br>';
        }
        ?>
    </div>
</div>
<script type="text/javascript">    
$(document).ready(function(){
});
</script>
@endsection