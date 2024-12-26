<div>
    @canany($knowledgeBasePermissions['create'])
    @if(!empty($page_id))
    <div class="mb-3 d-grid w-100">
        <button class="btn btn-default btn-custom-sop d-flex align-items-center justify-content-center" onclick="clickUploadBtn()">
            <i class="p-2 fa fa-cloud-arrow-up me-2"></i>Upload Documents
        </button>
    </div>
    @endif
    @endcanany
    @include('stores/knowledgeBase/partials/recent_upload_files')
    <!-- <div class="card">
        <div class="card-body">
            <h6 class="mb-0 text-custom-sop font-weight-bold">{{ $usedFiles['total']['size_text'] }} <span class="float-end text-secondary">{{$usedFiles['storage']['total_size']['GB']}} GB</span></h6>
            <p class="mt-2 mb-0">
                <span class="text-secondary">Used</span>
                <span class="float-end text-custom-sop">{{ number_format($usedFiles['total']['count'], 0, '.', '.') }} files</span>
            </p>

            <div class="mt-3 progress" style="height:7px;">

                <div class="progress-bar bg-primary" role="progressbar" style="width: {{$usedFiles['images']['percentage']}}%" aria-valuenow="{{$usedFiles['images']['percentage']}}" aria-valuemin="0" aria-valuemax="100"></div>

                <div class="progress-bar bg-success" role="progressbar" style="width: {{$usedFiles['documents']['percentage']}}%" aria-valuenow="{{$usedFiles['documents']['percentage']}}" aria-valuemin="0" aria-valuemax="100"></div>

                <div class="progress-bar bg-danger" role="progressbar" style="width: {{$usedFiles['media']['percentage']}}%" aria-valuenow="{{$usedFiles['media']['percentage']}}" aria-valuemin="0" aria-valuemax="100"></div>

                <div class="progress-bar bg-warning" role="progressbar" style="width: {{$usedFiles['other']['percentage']}}%" aria-valuenow="{{$usedFiles['other']['percentage']}}" aria-valuemin="0" aria-valuemax="100"></div>

                <div class="progress-bar bg-info" role="progressbar" style="width: {{$usedFiles['unknown']['percentage']}}%" aria-valuenow="{{$usedFiles['unknown']['percentage']}}" aria-valuemin="0" aria-valuemax="100"></div>

            </div>
            <div class="mt-3"></div>
            <div class="d-flex align-items-center">
                <div class="fm-file-box bg-light-primary text-primary"><i class='bx bx-image'></i>
                </div>
                <div class="flex-grow-1 ms-2">
                    <h6 class="mb-0">Images</h6>
                    <p class="mb-0 text-secondary">{{ number_format($usedFiles['images']['count'], 0, '.', '.') }} files</p>
                </div>
                <h6 class="mb-0 text-custom-sop">{{ $usedFiles['images']['size_text'] }}</h6>
            </div>
            <div class="mt-3 d-flex align-items-center">
                <div class="fm-file-box bg-light-success text-success"><i class='bx bxs-file-doc'></i>
                </div>
                <div class="flex-grow-1 ms-2">
                    <h6 class="mb-0">Documents</h6>
                    <p class="mb-0 text-secondary">{{ number_format($usedFiles['documents']['count'], 0, '.', '.') }} files</p>
                </div>
                <h6 class="mb-0 text-custom-sop">{{ $usedFiles['documents']['size_text'] }}</h6>
            </div>
            <div class="mt-3 d-flex align-items-center">
                <div class="fm-file-box bg-light-danger text-danger"><i class='bx bx-video'></i>
                </div>
                <div class="flex-grow-1 ms-2">
                    <h6 class="mb-0">Media Files</h6>
                    <p class="mb-0 text-secondary">{{ number_format($usedFiles['media']['count'], 0, '.', '.') }} files</p>
                </div>
                <h6 class="mb-0 text-custom-sop">{{ $usedFiles['media']['size_text'] }}</h6>
            </div>
            <div class="mt-3 d-flex align-items-center">
                <div class="fm-file-box bg-light-warning text-warning"><i class='bx bx-image'></i>
                </div>
                <div class="flex-grow-1 ms-2">
                    <h6 class="mb-0">Other Files</h6>
                    <p class="mb-0 text-secondary">{{ number_format($usedFiles['other']['count'], 0, '.', '.') }} files</p>
                </div>
                <h6 class="mb-0 text-custom-sop">{{ $usedFiles['other']['size_text'] }}</h6>
            </div>
            <div class="mt-3 d-flex align-items-center">
                <div class="fm-file-box bg-light-info text-info"><i class='bx bx-image'></i>
                </div>
                <div class="flex-grow-1 ms-2">
                    <h6 class="mb-0">Unknown Files</h6>
                    <p class="mb-0 text-secondary">{{ number_format($usedFiles['unknown']['count'], 0, '.', '.') }} files</p>
                </div>
                <h6 class="mb-0 text-custom-sop">{{ $usedFiles['unknown']['size_text'] }}</h6>
            </div>
        </div>
    </div> -->