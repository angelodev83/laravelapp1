<div class="col-lg-4 d-flex">
    <div class="card radius-10 w-100 ">
        <div class="card-body">
            <div class="d-flex align-items-center">
                <div>
                    <h6 class="mb-1" style="color: #5d63cf;">
                        <i class="fa-solid fa-ticket fa-sm me-2"></i>Ticket Reminders
                    </h6>
                    <small class="px-4">{{count($bulletinTicketRecent)}} Most Recent(s)</small>
                </div>
                <div class="ms-auto">
                    <a href="/store/escalation/{{request()->id}}/tickets">
                        <button class="btn btn-sm btn-outline-default" id="ticket_reminders_dashboard_view_more_btn">View More</button>
                    </a>
                </div>
            </div>
        </div>

        <div id="bulletin-tickets-recent-dashboard" class="customers-list pt-0 p-3 mb-3">
            @if (count($bulletinTicketRecent) > 0)    
                @foreach ($bulletinTicketRecent as $item)
                    <div class="customers-list-item d-flex align-items-center border-top border-bottom p-2 cursor-pointer" onclick="showTicketEditModal({{$item['id']}})">
                        @if (!empty($item['assignedToImage']))
                            <div class="">
                                <img src="/upload/userprofile/{{$item['assignedToImage']}}" class="rounded-circle" width="46" height="46" alt="" />
                            </div>
                        @else
                            <div class="col-auto">
                                <div class="avatar-{{!empty($item['assignedToInitialsRandomColor']) ? $item['assignedToInitialsRandomColor'] : 1}}-initials">
                                    {{ $item['assignedToInitials'] }}
                                </div>
                            </div>
                        @endif
                        <div class="ms-2">
                            <h6 class="mb-1 font-14 bulletin-task-text-truncate">
                            {{ $item['subject'] }}<i title="{{$item['priorityStatus']['name']}}" class="fa fa-flag ms-3 text-{{$item['priorityStatus']['class']}}"></i>
                            </h6>
                            <p class="mb-0 font-13 text-secondary bulletin-announcement-text-truncate">
                            <span style="border-radius: 25px;" class="badge badge-task bg-{{$item['status']['class']}} px-3 me-3">{{$item['status']['name']}}</span>
                            @if (isset($item['assigned_to']))
                                <small>Assigned to:</small> {{ $item['assigned_to'] }}
                            @endif
                            </p>
                        </div>
                        <div class="list-inline d-flex customers-contacts ms-auto">
                            <small class="text-primary float-end text-end w-100 bulletin-announcement-time-ago">
                                @if($item['hours_difference'] >= 24)
                                    @if (($item['hours_difference']/24) <= 7)
                                        {{ round($item['hours_difference']/24) }}d ago
                                    @else
                                        {{ round($item['hours_difference']/24/7) }}w ago
                                    @endif
                                @else
                                {{ $item['hours_difference'] }}h ago
                                @endif
                            </small>
                        </div>
                    </div>
                    {!! $item['actions'] !!}
                @endforeach     
            @else
                <div class="row text-center mt-5">
                    <span class="text-center text-secondary"><i class="fa fa-info-circle me-2"></i> No tickets assigned. You're all caught up!</span>
                </div>   
            @endif
        </div>
    </div>
</div>