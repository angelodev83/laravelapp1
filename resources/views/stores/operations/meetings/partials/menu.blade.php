<div class="card">
    <div class="card-body" id="operations-meetings-fm-menu" >
        @canany($permissions['create'])
        @if(!empty($page_id) && !empty(request()->month_number))
        <div class="mb-3 d-grid w-100">
            <button class="btn btn-success d-flex align-items-center justify-content-center"  onclick="clickUploadBtn()">
                <i class="p-2 fa fa-cloud-arrow-up me-2"></i>Upload Documents
            </button>
        </div>
        @endif
        @endcanany
        <h6 class="my-3 mt-0">My Documents</h6>
        <div class="fm-menu">
            <div class="list-group list-group-flush"> 

                <a href="/store/operations/{{request()->id}}/meetings/{{request()->year}}/0" class="list-group-item py-2 {{request()->month_number == 0 ? 'operations-meetings-folder-card-selected fw-bold' : ''}}"><i class='fa-regular fa-folder me-2'></i>All Files</a>

                @for($i = 1; $i <= 12; $i++)
                    @if(isset(request()->month_number))
                        @if(request()->month_number == $i)
                            <a href="/store/operations/{{request()->id}}/meetings/{{request()->year}}/{{$i}}" class="selected-month operations-submenu-month operations-meetings-folder-card-selected list-group-item py-2" id="menu_folder_{{$i}}">
                                <i class='fa-regular fa-calendar me-2 text-success2'></i><b class="text-success2">{{ date('F', strtotime(request()->year.'-'.sprintf("%0d", $i).'-01')) }}</b>
                                <i class="fa fa-chevron-down ms-auto text-success2"></i>
                            </a>

                            @foreach($weeks as $week)
                                <span id="submenu_folder_{{$week['monthWeek']}}" class="list-group-item operations-submenu operations-meetings-folder-card selected-month {{ $week['monthWeek'] != count($weeks) ? 'selected-month-no-border-bottom' : '' }} text-success2 py-2" onclick="clickSubmenuMonthWeek(event, {{$week['monthWeek']}})">
                                    <i class='fa fa-arrow-right-long mx-2'></i> {{$week['title']}}
                                </span>
                            @endforeach
                        @else
                            <a href="/store/operations/{{request()->id}}/meetings/{{request()->year}}/{{$i}}" class="list-group-item operations-submenu-month py-2" id="menu_folder_{{$i}}">
                                <i class='fa-regular fa-calendar me-2'></i>{{ date('F', strtotime(request()->year.'-'.sprintf("%0d", $i).'-01')) }}
                            </a>
                        @endif
                    @endif
                @endfor

            </div>
        </div>
    </div>
</div>