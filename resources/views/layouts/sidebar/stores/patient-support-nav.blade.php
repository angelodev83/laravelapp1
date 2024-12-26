<ul>
    <li> 
        <a class="has-arrow" href="javascript:;"><i class="bx bx-menu"></i>Patient Support</a>
        @canany(['menu_store.patient_support.transfer_rx.index'])
            <ul>
                <li> <a href="/store/patient-support/{{$menu->id}}/transfer_rx/1/tribe_members"><i class="bx bx-right-arrow-alt"></i>Tribe Members</a></li>
            </ul>
        @endcanany
        @canany(['menu_store.patient_support.transfer_rx.index'])
            <ul>
                <li> <a href="/store/patient-support/{{$menu->id}}/transfer_rx/2/tribe_members"><i class="bx bx-right-arrow-alt"></i>Tribe Members Outside</a></li>
            </ul>
        @endcanany
        @canany(['menu_store.patient_support.transfer_rx.index'])
            <ul>
                <li> <a href="/store/patient-support/{{$menu->id}}/transfer_rx/3/tribe_members"><i class="bx bx-right-arrow-alt"></i>General</a></li>
            </ul>
        @endcanany
    </li>
</ul>
