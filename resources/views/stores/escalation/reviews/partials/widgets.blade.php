<div class="row my-0 py-0">
@foreach ($summaryOverallStars['stars'] as $star => $count)
    @if($star <= 3)
        <div class="col">
            <div class="card shadow-none py-2 mb-4">
                <div class="card-body my-2 py-2">
                    <div class="row my-0 py-0 mx-3 justify-content-center">
                        <div class="col">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $star)
                                    <i class="fas fa-star fa-2x text-warning"></i>
                                @else
                                    <i class="fas fa-star fa-2x" style="color: #d1d1d1;"></i>
                                @endif
                            @endfor
                        </div>
                        <div class="col ms-auto text-end">
                            <h5>{{ $count }}</h5>
                        </div>
                    </div>
                </div>
            </div>  
        </div>
    @endif
@endforeach
</div>