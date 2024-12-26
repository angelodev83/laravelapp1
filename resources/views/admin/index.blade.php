@extends('layouts.master')
@section('content')
<!--start page wrapper -->
		<div class="page-wrapper">
			<div class="page-content">

                <div class="row m-1">
                    @foreach ($announcements as $k => $a)
                        @if (isset($notifications[$k]))
                            <div class="alert alert-info border-0 alert-dismissible fade show py-2 dashboard-alert-info shadow-sm"> 
                                <div class="d-flex align-items-center">
                                    {{-- <div class="font-35 text-dark"><i class='bx bx-mail-send'></i>
                                    </div> --}}
                                    <div class="font-35 notify text-primary"><i class="bx bx-mail-send"></i>
                                    </div>
                                    <div class="ms-3">
                                        <h6 class="mb-0 text-dark">{{ $a['subject'] }}</h6>
                                        <div class="text-dark">
                                            <a href="/admin/human_resources/announcements/{{$k}}">Read the contents by <u>clicking here</u>
                                            </a>   
                                        </div>
                                    </div>
                                    <div class="ms-auto">
                                        <small>Created by: <b>{{isset($a['user']) ? ($a['user']['employee']['lastname'].', '.$a['user']['employee']['firstname']) : ''}}</b></small><br>
                                        <small>{{date('M d, Y H:iA',strtotime($a['created_at']))}}</small>
                                    </div>
                                </div>
                                {{-- <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button> --}}
                            </div>
                        @endif
                    @endforeach
                </div>
			
			  <div class="row row-cols-1 row-cols-lg-2 row-cols-xl-4">
			    <div class="col">
						<div class="card radius-10 overflow-hidden">
							<div class="card-body">
								<div class="d-flex align-items-center">
									<div>
										<p class="mb-0">Total Orders</p>
										<h6 class="mb-0">867</h6>
									</div>
									<div class="ms-auto">	<i class='bx bx-cart font-30'></i>
									</div>
								</div>
							</div>
							<div class="" id="chart1"></div>
						</div>
					</div>
					<div class="col">
						<div class="card radius-10 overflow-hidden">
							<div class="card-body">
								<div class="d-flex align-items-center">
									<div>
										<p class="mb-0">Total Income</p>
										<h6 class="mb-0">$52,945</h6>
									</div>
									<div class="ms-auto">	<i class='bx bx-wallet font-30'></i>
									</div>
								</div>
							</div>
							<div class="" id="chart2"></div>
						</div>
					</div>
					<div class="col">
						<div class="card radius-10 overflow-hidden">
							<div class="card-body">
								<div class="d-flex align-items-center">
									<div>
										<p class="mb-0">Total Users</p>
										<h6 class="mb-0">24.5K</h6>
									</div>
									<div class="ms-auto">	<i class='bx bx-group font-30'></i>
									</div>
								</div>
							</div>
							<div class="" id="chart3"></div>
						</div>
					</div>
					<div class="col">
						<div class="card radius-10 overflow-hidden">
							<div class="card-body">
								<div class="d-flex align-items-center">
									<div>
										<p class="mb-0">Comments</p>
										<h6 class="mb-0">869</h6>
									</div>
									<div class="ms-auto">	<i class='bx bx-chat font-30'></i>
									</div>
								</div>
							</div>
							<div class="" id="chart4"></div>
						</div>
					</div>
			  </div><!--end row-->
			 
				 
			
			</div>
		</div>
		<!--end page wrapper -->

@stop
@section('pages_specific_scripts')   
@stop
