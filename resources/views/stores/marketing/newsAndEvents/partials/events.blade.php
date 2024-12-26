
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

    <!-- Close button -->
    <button type="button" class="btn-close top-0 end-0 m-2" onclick="deleteEvent('{{$event->id}}')" aria-label="Close"></button>
</div>
@empty
    <p>No upcoming events.</p>
@endforelse
                                        