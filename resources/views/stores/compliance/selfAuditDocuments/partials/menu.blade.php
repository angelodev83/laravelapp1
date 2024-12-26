<div class="card">
    <div class="card-body" id="ir-monthly-control-counts-fm-menu" >
        @canany($permissions['create'])
        @if(!empty(request()->month_number))
        <div class="mb-3 d-grid w-100">
            <button class="btn btn-success2 d-flex align-items-center justify-content-center"  onclick="clickUploadBtn()">
                <i class="p-2 fa fa-cloud-arrow-up me-2"></i>Upload Documents
            </button>
        </div>
        @endif
        @endcanany
        <h6 class="my-3 mt-0">My Documents</h6>
        <div class="fm-menu">
            <div class="list-group list-group-flush"> 

                <a href="/store/compliance/{{request()->id}}/self-audit-documents/{{request()->year}}/0" class="list-group-item py-2 {{request()->month_number == 0 ? 'ir-monthly-control-counts-folder-card-selected fw-bold' : ''}}"><i class='fa-regular fa-folder me-2'></i>All Files {{request()->year}}</a>

                @for($i = 1; $i <= 12; $i++)
                    @if(isset(request()->month_number))
                        @if(request()->month_number == $i)
                            <a href="/store/compliance/{{request()->id}}/self-audit-documents/{{request()->year}}/{{$i}}" class="selected-month operations-submenu-month ir-monthly-control-counts-folder-card-selected list-group-item py-2" id="menu_folder_{{$i}}">
                                <i class='fa-regular fa-calendar me-2 text-success2'></i><b class="text-success2">{{ date('F', strtotime(request()->year.'-'.sprintf("%0d", $i).'-01')) }}</b>
                            </a>
                        @else
                            <a href="/store/compliance/{{request()->id}}/self-audit-documents/{{request()->year}}/{{$i}}" class="list-group-item operations-submenu-month py-2" id="menu_folder_{{$i}}">
                                <i class='fa-regular fa-calendar me-2'></i>{{ date('F', strtotime(request()->year.'-'.sprintf("%0d", $i).'-01')) }}
                            </a>
                        @endif
                    @endif
                @endfor

            </div>
        </div>
    </div>
</div>