<!--wrapper-->
	<div class="wrapper">
			{{-- @switch($user->role->name)
				@case('Admin')
					@include('layouts/common-nav')
				@break

				@case('Compliance')
					@include('layouts/navs/compliance-nav')
				@break

				@case('Telehealth')
					@include('layouts/navs/telehealth-nav')
				@break

				@case('Data Pharmacy')
					@include('layouts/navs/data-pharmacy-nav')
				@break

				@case('Human Resource')
					@include('layouts/navs/hr-nav')
				@break

				@default
					@include('layouts/common-nav')
			@endswitch --}}

            @include('layouts/common-sidebar')

		<!--start header -->
		<header>
			<div class="topbar d-flex align-items-center">
				<nav class="navbar navbar-expand gap-3">
					<div class="mobile-toggle-menu"><i class='bx bx-menu'></i>
					</div>
					<div class="top-menu-left d-none d-lg-block">
				 	 <ul class="nav">
						
					</ul>
				   </div>
					<div class="search-bar flex-grow-1">
						<div class="position-relative search-bar-box">
							<form>
							  <input type="text" class="form-control search-control" autofocus placeholder="Type to search..."> <span class="position-absolute top-50 search-show translate-middle-y"><i class='bx bx-search'></i></span>
							   <span class="position-absolute top-50 search-close translate-middle-y"><i class='bx bx-x'></i></span>
						    </form>
						</div>
					</div>
                    {{-- Notifications for announcements start --}}
                    @include('layouts/notification')
                    {{-- Notifications for announcements ends --}}
                    <div class="top-menu">
                        <ul class="navbar-nav align-items-center gap-1">
                            
                            
                            
                            <li class="nav-item dropdown dropdown-large d-none">
                                <a class="nav-link dropdown-toggle dropdown-toggle-nocaret position-relative" href="javascript:;" role="button" data-bs-toggle="dropdown" aria-expanded="false"> <span class="alert-count">7</span>
                                    <i class='bx bx-bell'></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a href="javascript:;">
                                        <div class="msg-header">
                                            <p class="msg-header-title">Notifications</p>
                                            <p class="msg-header-clear ms-auto">Marks all as read</p>
                                        </div>
                                    </a>
                                    <div class="header-notifications-list">
                                        
                                    </div>
                                    <a href="javascript:;">
                                        <div class="text-center msg-footer">View All Notifications</div>
                                    </a>
                                </div>
                            </li>
                            <li class="nav-item dropdown dropdown-large d-none">
                                <a class="nav-link dropdown-toggle dropdown-toggle-nocaret position-relative" href="javascript:;" role="button" data-bs-toggle="dropdown" aria-expanded="false"> <span class="alert-count">8</span>
                                    <i class='bx bx-comment'></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a href="javascript:;">
                                        <div class="msg-header">
                                            <p class="msg-header-title">Messages</p>
                                            <p class="msg-header-clear ms-auto">Marks all as read</p>
                                        </div>
                                    </a>
                                    <div class="header-message-list">
                                        
                                        
                                    </div>
                                    <a href="javascript:;">
                                        <div class="text-center msg-footer">View All Messages</div>
                                    </a>
                                </div>
                            </li>
                        </ul>
                    </div>
                    
					<div class="user-box dropdown px-3">
						<a class="d-flex align-items-center nav-link dropdown-toggle dropdown-toggle-nocaret" href="javascript:;" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            
                            @if(isset($authEmployee->id))
                                @if(!empty($authEmployee->image))
                                    <img src="/upload/userprofile/{{$authEmployee->image}}" class="user-img" alt="user avatar">
                                @else
                                    <div class="col-auto">
                                        <div class="avatar-{{$authEmployee->initials_random_color}}-initials" style="width: 42px !important; height: 42px !important; font-size: 15px !important;">
                                            {{ strtoupper(substr($authEmployee->firstname, 0, 1)) }}{{ strtoupper(substr($authEmployee->lastname, 0, 1)) }}
                                        </div>
                                    </div>
                                @endif
                            @else
                                <div class="col-auto">
                                    <div class="user-avatar-initials">
                                        {{ strtoupper(substr($authEmployee->firstname, 0, 1)) }}{{ strtoupper(substr($authEmployee->lastname, 0, 1)) }}
                                    </div>
                                </div>
                            @endif
							<div class="user-info ps-3">
                                @if(isset($authEmployee->id))
                                    <p class="user-name mb-0">{{ $authEmployee->firstname }} {{ $authEmployee->lastname }}</p>
                                @else
								    <p class="user-name mb-0">{{ $user->name }}</p>
								@endif
                                <span class="text-secondary">{{ $user->role->display_name }}</span>
							</div>
						</a>
						<ul class="dropdown-menu dropdown-menu-end">
							<li><a class="dropdown-item" href="/admin/profile/profile_view"><i class="bx bx-user"></i><span>My Profile</span></a>
							</li>
							<!-- <li><a class="dropdown-item" href="javascript:;"><i class="bx bx-cog"></i><span>Settings</span></a>
							</li> -->
							
							</li>
							<li>
								<div class="dropdown-divider mb-0"></div>
							</li>
							
							<li><a class="dropdown-item" href="/admin/logout"><i class='bx bx-log-out-circle'></i><span>Logout</span></a>
							</li>
						</ul>
					</div>
				</nav>
			</div>
		</header>
		<!--end header -->
    @include('executiveDashboard/trp/patientSupport/tribeMembers/modal/default-automation-form')