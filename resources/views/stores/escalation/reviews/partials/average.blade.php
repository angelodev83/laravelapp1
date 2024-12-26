<div class="card shadow-none mt-2 py-2 mb-4">
    <div class="card-body my-0 py-0">
        <div class="row my-0 py-0 justify-content-center mx-3">
            <div class="col">
                <h4 class="py-0 mt-2">Average Review Rating</h4>
            </div>
            <div class="col ms-auto text-end">
                @for($i = 1; $i <= 5; $i++)
                    @if($i <= $summaryOverallStars['average'])
                        <i class="fas fa-star fa-3x text-warning"></i>
                    @else
                        <i class="fas fa-star fa-3x" style="color: #d1d1d1;"></i>
                    @endif
                @endfor
            </div>
        </div>
    </div>
</div>