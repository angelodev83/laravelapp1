<!-- patient feedback -->
<div class="row">
    <div class="col-sm-8 mb-3 mb-sm-0">
        <div class="card">
            <div class="card-body">
                <div class="d-flex gap-2 align-items-center mb-2">
                    <i class="fa-solid fa-comments text-warning fs-5"></i>
                    <h6 class="card-title text-secondary fw-bold mb-0"> Patient Feedback </h6>
                </div>
                <div id="patient-feedbacks" class="patient-container">
                    <div class="patient-thumbnail">
                        @foreach($patientFeedbacks['collections'] as $pfImage)
                            <div class="border border-2 border-secondary-subtle rounded-4 mb-3">
                                @if($pfImage['is_image'] === true)
                                    <img class="w-100 rounded-4" src="{{$pfImage['url']}}" alt="patient feedback">
                                @else
                                    <div class="m-3">
                                        <div class="d-flex">
                                            <img src="/source-images/store-gallery/patient_icon.png" width="45" height="45" class="rounded-circle image-has-border" alt="">
                                            <div class="flex-grow-1 ms-3 mt-2">
                                                <p class="mb-0 font-weight-bold font-12">
                                                    <b>{{ $pfImage['data']->firstname }} {{ $pfImage['data']->lastname }}</b>
                                                </p>
                                            </div>
                                        </div>

                                        <div class="row mt-3">
                                            <div class="col">
                                                PCC/Pharmacist Name:
                                            </div>
                                            <div class="col">
                                                Service Rating
                                            </div>
                                            <div class="col">
                                                Overall Pharmacy Rating
                                            </div>
                                            <div class="col">
                                                Expectations Rating
                                            </div>
                                        </div>

                                        <div class="row mt-1">
                                            <div class="col">
                                                {{ $pfImage['data']->pharmacist_name }}
                                            </div>
                                            <div class="col">
                                                @for($i = 1; $i <= 5; $i++)
                                                    @if($pfImage['data']->how_was_service_today_score > 0)
                                                        @if($i <= $pfImage['data']->how_was_service_today_score)
                                                            <i class="fas fa-star text-warning"></i>
                                                        @else
                                                            <i class="fas fa-star" style="color: #d1d1d1;"></i>
                                                        @endif
                                                    @else
                                                        <i class="fas fa-star" style="color: #d1d1d1;"></i>
                                                    @endif
                                                @endfor
                                            </div>
                                            <div class="col">
                                                @for($i = 1; $i <= 5; $i++)
                                                    @if($pfImage['data']->how_satisfied_with_our_pharmacy_overall_score > 0)
                                                        @if($i <= $pfImage['data']->how_satisfied_with_our_pharmacy_overall_score)
                                                            <i class="fas fa-star text-warning"></i>
                                                        @else
                                                            <i class="fas fa-star" style="color: #d1d1d1;"></i>
                                                        @endif
                                                    @else
                                                        <i class="fas fa-star" style="color: #d1d1d1;"></i>
                                                    @endif
                                                @endfor
                                            </div>
                                            <div class="col">
                                                @for($i = 1; $i <= 5; $i++)
                                                    @if($pfImage['data']->pharmacist_or_patient_care_team_live_up_to_expectation_score > 0)
                                                        @if($i <= $pfImage['data']->pharmacist_or_patient_care_team_live_up_to_expectation_score)
                                                            <i class="fas fa-star text-warning"></i>
                                                        @else
                                                            <i class="fas fa-star" style="color: #d1d1d1;"></i>
                                                        @endif
                                                    @else
                                                        <i class="fas fa-star" style="color: #d1d1d1;"></i>
                                                    @endif
                                                @endfor
                                            </div>
                                        </div>

                                        <div class="row mt-3">
                                            <div class="col">
                                                {{ $pfImage['data']->experience_feedback }}
                                            </div>
                                        </div>

                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-center pt-5">
                    <h4 class="mb-0 font-weight-bold">Review Ratings</h4>
                </div>
                <div id="reviews"></div>
            </div>
        </div>
    </div>
</div>