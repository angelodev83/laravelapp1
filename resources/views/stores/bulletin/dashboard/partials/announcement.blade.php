<div class="row">
    <!-- announcements starts -->
    <div class="col-lg-12 d-flex">
       <div class="card radius-10 w-100">
            <div class="card-body mb-0 pb-0">
                <div class="d-flex align-items-center mb-0 pb-0">
                    <div>
                        <h6 class="mb-1 text-primary">
                            <i class="fa-solid fa-bullhorn fa-sm me-2"></i>Announcements
                        </h6>
                    </div>
                    
                    <div class="ms-auto">
                        <a href="/store/bulletin/{{request()->id}}/announcements">
                            <button class="btn btn-sm btn-outline-primary">View More</button>
                        </a>
                    </div>
                </div>
                <hr class="p-0 m-1 mt-3">
            </div>

            <div class="row">
                <div>
                    <div id="bulletin-announcements-content" class="p-3 mb-3 announcement-list">
                        <div id="selected-announcement-content"></div>
                    </div>
                </div>
                <div>
                    <div id="bulletin-announcements-recent" class="p-3 pt-0 mb-2 announcement-list-items">
                        @foreach ($bulletinAnnouncementRecent as $item)
                            <a href="javascript:;" 
                                class="announcement-link" 
                                data-subject="{{ htmlspecialchars($item->subject) }}"
                                data-content="{!! htmlspecialchars($item->content) !!}" 
                                data-id="{{ $item->id }}" 
                                data-created-at="{{ date('M d, Y h:iA', strtotime($item->created_at)) }}"
                            >
                                <div class="p-2 cursor-pointer announcement-list-item d-flex align-items-center border-top border-bottom mb-3" style="min-height:70px;">
                                    <div class="ms-2">
                                        <h6 class="mb-1 font-14 bulletin-task-text-truncate">
                                            <i class="fa-solid fa-bullhorn me-2 text-primary"></i>{{ $item->subject }}
                                        </h6>
                                        <p class="mb-0 font-13 text-secondary bulletin-announcement-text-truncate">
                                            @if (isset($item->user->employee))
                                                <small>Created by:</small> {{ $item->user->employee->firstname }} {{ $item->user->employee->lastname }}
                                            @endif
                                        </p>
                                    </div>
                                    <div class="list-inline d-flex customers-contacts ms-auto">
                                        <small class="text-primary float-end text-end w-100 bulletin-announcement-time-ago">
                                            {{ $item->created_at->diffForHumans() }}
                                        </small>
                                    </div>
                                </div>
                            </a>
                            {{-- <hr class="hr-announcement-list"> --}}
                        @endforeach
                    </div>
                </div>
            </div>
            
            
           
       </div>
    </div>
    <!-- end announcement -->
</div>