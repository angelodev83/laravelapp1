<div class="col-lg-4 d-flex">
    <div class="card radius-10 w-100 ">
        <div class="card-body">
            <div class="d-flex align-items-center">
                <div>
                    <h6 class="mb-1" style="color: #438f9d;">
                        Upcoming Events
                    </h6>
                </div>
                
            </div>
            <div id="bulletin-upcoming-events" class="row mt-3 ms-1">
                
                @forelse($upcomingEvents as $event)
                <div class="d-flex p-2" onclick="">
                    @php
                        $date = \Carbon\Carbon::parse($event->date);
                        $formattedMonth = $date->format('M');
                        $formattedDay = $date->format('j');
                    @endphp
                    <div class="col-auto">
                        <div class="upcoming-events-date text-center alert border-0">
                            <h1>{{$formattedDay}}</h1>
                            <span>{{$formattedMonth}}</span>
                        </div>
                    </div>

                    <div class="ms-3 mt-2 text-left">
                        <h6 class="mb-1 font-15 upcoming-events-main-text">
                            {{$event->name}}
                        </h6>
                        <p class="mb-0 font-15 text-secondary upcoming-events-sub-text">
                            <small>{{$event->content}}</small>
                        </p>
                    </div>

                 </div>
                @empty
                    <p>No upcoming events.</p>
                @endforelse
                
            </div>  
        </div>

    </div>
</div>