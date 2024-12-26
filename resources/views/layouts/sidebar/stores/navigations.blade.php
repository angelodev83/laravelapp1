<!-- bulletin -->
@canany(array_keys($menuStoreGroupPermissions, 'bulletin'))
    @if($numberOfStorePermissions == 1)
        @include('layouts/sidebar/stores/bulletin-nav')
    @else
        <ul>
            @include('layouts/sidebar/stores/bulletin-nav')
        </ul>
    @endif
@endcanany
<!-- operations -->
@canany(array_keys($menuStoreGroupPermissions,'operations'))
    @if($numberOfStorePermissions == 1)
        @include('layouts/sidebar/stores/operations-nav')
    @else
        <ul>
            @include('layouts/sidebar/stores/operations-nav')
        </ul>
    @endif
@endcanany
<!-- financial reports -->
@canany(array_keys($menuStoreGroupPermissions,'financial_reports'))
    @if($numberOfStorePermissions == 1)
        @include('layouts/sidebar/stores/financial-reports-nav')
    @else
        <ul>
            @include('layouts/sidebar/stores/financial-reports-nav')
        </ul>
    @endif
@endcanany
<!-- clinical -->
@canany(array_keys($menuStoreGroupPermissions,'clinical'))
    @if($numberOfStorePermissions == 1)
        @include('layouts/sidebar/stores/clinical-nav')
    @else
        <ul>
            @include('layouts/sidebar/stores/clinical-nav')
        </ul>
    @endif
@endcanany
<!-- procurement -->
@canany(array_keys($menuStoreGroupPermissions,'procurement'))
    @if($numberOfStorePermissions == 1)
        @include('layouts/sidebar/stores/procurement-nav')
    @else
        <ul>
            @include('layouts/sidebar/stores/procurement-nav')
        </ul>
    @endif
@endcanany
<!-- data insights -->
@canany(array_keys($menuStoreGroupPermissions,'data_insights'))
    @if($numberOfStorePermissions == 1)
        @include('layouts/sidebar/stores/data-insights-nav')
    @else
        <ul>
            @include('layouts/sidebar/stores/data-insights-nav')
        </ul>
    @endif
@endcanany
<!-- compliance and regulation -->
@canany(array_keys($menuStoreGroupPermissions,'cnr'))
    @if($numberOfStorePermissions == 1)
        @include('layouts/sidebar/stores/compliance-and-regulation-nav')
    @else
        <ul>
            @include('layouts/sidebar/stores/compliance-and-regulation-nav')
        </ul>
    @endif
@endcanany
<!-- patient support -->
{{-- @canany(array_keys($menuStoreGroupPermissions,'patient_support'))
    @if($numberOfStorePermissions == 1)
        @include('layouts/sidebar/stores/patient-support-nav')
    @else
        <ul>
            @include('layouts/sidebar/stores/patient-support-nav')
        </ul>
    @endif
@endcanany --}}
<!-- escalation -->
@canany(array_keys($menuStoreGroupPermissions,'escalation'))
    @if($numberOfStorePermissions == 1)
        @include('layouts/sidebar/stores/escalation-nav')
    @else
        <ul>
            @include('layouts/sidebar/stores/escalation-nav')
        </ul>
    @endif
@endcanany
<!-- knowledge base -->
@canany(array_keys($menuStoreGroupPermissions,'knowledge_base'))
    @if($numberOfStorePermissions == 1)
        @include('layouts/sidebar/stores/knowledge-base-nav')
    @else
        <ul>
            @include('layouts/sidebar/stores/knowledge-base-nav')
        </ul>
    @endif
@endcanany
<!-- eod register report -->
@canany(array_keys($menuStoreGroupPermissions,'eod_register_report'))
    @if($numberOfStorePermissions == 1)
       @include('layouts/sidebar/stores/eod-register-report-nav')
    @else
        <ul>
           @include('layouts/sidebar/stores/eod-register-report-nav')
        </ul>
    @endif
@endcanany
<!-- human resource -->
@canany(array_keys($menuStoreGroupPermissions,'hr'))
    @if($numberOfStorePermissions == 1)
       @include('layouts/sidebar/stores/human-resource-nav')
    @else
        <ul>
           @include('layouts/sidebar/stores/human-resource-nav')
        </ul>
    @endif
@endcanany
<!-- marketing -->
@canany(array_keys($menuStoreGroupPermissions,'marketing'))
    @if($numberOfStorePermissions == 1)
       @include('layouts/sidebar/stores/marketing')
    @else
        <ul>
           @include('layouts/sidebar/stores/marketing')
        </ul>
    @endif
@endcanany
<!-- jot_form -->
@canany(array_keys($menuStoreGroupPermissions,'jot_form'))
    @if($numberOfStorePermissions == 1)
    @include('layouts/sidebar/stores/forms-nav')
    @else
        <ul>
            @include('layouts/sidebar/stores/forms-nav')
        </ul>
    @endif
@endcanany