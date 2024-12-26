@extends('layouts.master')
@section('content')
<!--start page wrapper -->
		<div class="page-wrapper">
			<div class="page-content">
				<!-- PAGE-HEADER -->
				@include('layouts/pageContentHeader/store')
				<!-- PAGE-HEADER END -->
				<div class="card">
					<div class="card-body">
						
                        <img src="/source-images/store-gallery/ctclusi-org-chart.png" class="img-fluid" alt="">
                        
					</div>
				</div>
			</div>
		</div>
		<!--end page wrapper -->
@stop
@section('pages_specific_scripts') 
<script>
    
</script>  
@stop
