
    <li class="sidebar-store-clinical-nav">
        <a class="sidebar-store-clinical-nav-a has-arrow" href="javascript:;">
            @if($numberOfStorePermissions == 1)
                <div class="parent-icon"><i class='fa-solid fa-stethoscope fa-xs' ></i></div>
                <div class="menu-title">Clinical</div>
            @else
                <i class="fa-solid fa-stethoscope me-2"></i>Clinical
            @endif
        </a>
        <ul>
            @canany(['menu_store.clinical.mtm_outcomes_report.index', 'menu_store.clinical.adherence_report.index'])
                <li>
                    <a href="/store/clinical/{{$menu->id}}/dashboard"><i class="fa-solid fa-chart-line ms-2 me-3"></i>Dashboard</a>
                </li>
            @endcanany
            {{-- @canany(['menu_store.clinical.kpi.index']) --}}
                <!-- <li>
                    <a href="/store/clinical/{{$menu->id}}/kpi"><i class="fa-solid fa-stethoscope ms-2 me-3"></i>KPI</a>
                </li> -->
            {{-- @endcanany --}}
            @canany(['menu_store.clinical.outreach.index'])
                <li>
                    <a href="/store/clinical/{{$menu->id}}/outreach"><i class="fa-solid fa-hand-holding-hand ms-2 me-3"></i>Outreach</a>
                </li>
            @endcanany
            @canany(['menu_store.clinical.prio_authorization.index'])
                <li>
                    <a href="/store/clinical/{{$menu->id}}/prio-authorization"><i class="fa-solid fa-hands-holding-child ms-2 me-3"></i>Prio Authorization</a>
                </li>
            @endcanany
            @canany(['menu_store.clinical.tebra_patients.index', 'menu_store.clinical.tebra_patients.create', 'menu_store.clinical.tebra_patients.update', 'menu_store.clinical.tebra_patients.delete'])
                <!-- <li>
                    <a href="/store/clinical/{{$menu->id}}/tebra-patients"><i class="fa-solid fa-person-half-dress ms-2 me-3"></i>Tebra Patients</a>
                </li> -->
            @endcanany
            @canany(['menu_store.clinical.pioneer_patients.index', 'menu_store.clinical.pioneer_patients.create', 'menu_store.clinical.pioneer_patients.update', 'menu_store.clinical.pioneer_patients.delete'])
                <li>
                    <a href="/store/clinical/{{$menu->id}}/pioneer-patients"><i class="fa-solid fa-hospital-user ms-2 me-3"></i>Pioneer Patients</a>
                </li>
            @endcanany
            @canany(['menu_store.clinical.renewals.index', 'menu_store.clinical.renewals.create', 'menu_store.clinical.renewals.update', 'menu_store.clinical.renewals.delete', 'menu_store.clinical.renewals.archive', 'menu_store.clinical.renewals.export'])
                <li>
                    <a href="/store/clinical/{{$menu->id}}/renewals"><i class="fa-solid fa-group-arrows-rotate ms-2 me-3"></i>Renewals</a>
                </li>
            @endcanany
            @canany(['menu_store.clinical.meetings.index', 'menu_store.clinical.meetings.create', 'menu_store.clinical.meetings.update', 'menu_store.clinical.meetings.delete'])
            <li>
                <a href="/store/clinical/{{$menu->id}}/meetings/{{date('Y')}}/{{date('n')}}"><i class="fa-solid fa-notes-medical ms-2 me-3"></i>Meetings</a>
            </li>
            @endcanany
            @canany(['menu_store.clinical.mtm_outcomes_report.index'])
                <li>
                    <a href="/store/clinical/{{$menu->id}}/mtm-outcomes-reports"><i class="fa-solid fa-file-waveform ms-2 me-3"></i>MTM, Outcomes Reports</a>
                </li>
            @endcanany
            @canany(['menu_store.clinical.adherence_report.index'])
                <li>
                    <a href="/store/clinical/{{$menu->id}}/adherence-reports"><i class="fa-solid fa-file-lines ms-2 me-3"></i>Adherence Reports</a>
                </li>
            @endcanany

            {{-- automations --}}
            @canany(['menu_store.clinical.pending_refill_requests.index'])
                <li>
                    <a href="/store/clinical/{{$menu->id}}/pending-refill-requests"><i class="fa-solid fa-fill ms-2 me-3"></i>Pending refill requests</a>
                </li>
            @endcanany
            @canany(['menu_store.clinical.brand_switchings.index'])
                <li>
                    <a href="/store/clinical/{{$menu->id}}/brand-switchings"><i class="fa-solid fa-shuffle ms-2 me-3"></i>Brand switching IOU</a>
                </li>
            @endcanany
            @canany(['menu_store.clinical.therapy_change_and_reco.index'])
                <li>
                    <a href="/store/clinical/{{$menu->id}}/therapy-change-and-reco"><i class="fa-solid fa-tablets ms-2 me-3"></i>Therapy change+reco</a>
                </li>
            @endcanany
            @canany(['menu_store.clinical.rx_daily_census.index'])
                <li>
                    <a href="/store/clinical/{{$menu->id}}/rx-daily-census"><i class="fa-solid fa-person-arrow-up-from-line ms-2 me-3"></i>THRC px+Rx Daily Census</a>
                </li>
            @endcanany
            @canany(['menu_store.clinical.rx_daily_transfers.index'])
                <li>
                    <a href="/store/clinical/{{$menu->id}}/rx-daily-transfers/in_progress"><i class="fa-solid fa-users-rectangle ms-2 me-3"></i>Daily Rx Transfers</a>
                </li>
            @endcanany
            @canany(['menu_store.clinical.rx_daily_transfers.index'])
                <li>
                    <a href="/store/clinical/{{$menu->id}}/rx-daily-transfers/pending"><i class="fa-solid fa-users-rays ms-2 me-3"></i>Daily Pending Rx Transfers</a>
                </li>
            @endcanany
            @canany(['menu_store.clinical.bridged_patients.index'])
                <li>
                    <a href="/store/clinical/{{$menu->id}}/bridged-patients"><i class="fa-solid fa-user-shield ms-2 me-3"></i>Bridged Patients</a>
                </li>
            @endcanany
        </ul>
    </li>
