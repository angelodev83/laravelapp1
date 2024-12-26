<div class="card">
    <div class="card-body" style="min-height: 400px;">
        <ul class="list-group">
            <li class="list-group-item" id="task-relation-subject">DRUG ORDER DETAILS</li>
        </ul>
        <div class="row g-3 mt-2">
            <div class="col-md-12" id="drug-order-partials" style="display: none">
                @include('stores/bulletin/tasks/drugOrder/partials/edit-form')
            </div>
            <div class="col-md-12" id="supply-order-partials"  style="display: none">
                @include('stores/bulletin/tasks/supplyOrder/partials/edit-form')
            </div>
            <div class="col-md-12" id="inmar-return-partials"  style="display: none">
                @include('stores/bulletin/tasks/inmarReturn/partials/edit-form')
            </div>
            <div class="col-md-12" id="clinical-order-partials"  style="display: none">
                @include('stores/bulletin/tasks/clinicalOrder/partials/edit-form')
            </div>
        </div>
    </div>
</div>