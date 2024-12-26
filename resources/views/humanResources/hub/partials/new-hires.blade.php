<div class="card">
    <div class="card-body m-3">

        <h6 class="pb-2" style="color: #f12383;"><b>New Hires</b></h6>

        <div class="row g-3">

            @if (count($newEmployees) > 0)
                @foreach ($newEmployees as $ne)
                    @php
                        $avatarPath = '/upload/userprofile/202401310302OIP.jpg';
                        if(!empty($ne->image)) {
                            $avatarPath = '/upload/userprofile/'.$ne->image;
                        }
                        $fullname = $ne->firstname.' '.$ne->lastname;
                    @endphp
                    <div class="col col-lg-4">
                        <img src="{{$avatarPath}}" class="rounded-circle p-1 border" width="90" height="90" alt="{{$fullname}}" title="{{$fullname}}">
                    </div>
                @endforeach
            @else
                <div class="col">
                    No new hires.
                </div>
            @endif
            
        </div>
    </div>
</div>