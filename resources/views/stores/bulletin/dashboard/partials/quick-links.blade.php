<div class="col-lg-4 d-flex">
    <div class="card radius-10 w-100 ">
        <div class="card-body">
            <div class="d-flex align-items-center">
                <div>
                    <h6 class="mb-1" style="color: black;">
                        <i class="fa-solid fa-clock-rotate-left fa-sm me-2"></i>Quick Links
                    </h6>
                </div>
                
            </div>
            <div id="bulletin-quick-links" class="customers-list ms-auto mt-2">
                @foreach ($bulletinQuickLinks as $quickLink)
                @php
                    $link = $quickLink->link;
                    $pattern = '/\{\{\s*(.+?)\s*\}\}/';
                    $matches = [];
                    preg_match_all($pattern, $link, $matches);

                    foreach ($matches[0] as $index => $match) {
                        $expression = $matches[1][$index];
                        $value = eval('return ' . $expression . ';');
                        $link = str_replace($match, $value, $link);
                    }
                @endphp
                <a href="{{ $link }}">
                    <!-- <button class="btn btn-sm btn-outline-default" id="">My Tasks</button> -->
                    <div class="alert alert-success border-0 bg-success alert-dismissible fade show py-2" style="background-color: {{$quickLink->bg_color}}; border-radius: 20px;">
                        <div class="d-flex align-items-center">
                            <div class="font-15"><i  style=" color: {{$quickLink->txt_color}};" class="{{$quickLink->icon}}"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="mb-0"  style=" color: {{$quickLink->txt_color}};">{{$quickLink->name}}</h6>
                            </div>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>

    </div>
</div>