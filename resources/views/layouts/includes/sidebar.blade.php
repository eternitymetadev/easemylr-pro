<ul class="list-unstyled menu-categories" id="accordionExample">
    <div class="logoBox">
        <img id="openSidebarLogo" class="toggleLogo" alt="logo"
            src="{{asset('assets/img/d2f-logo.png')}}">
        <img id="closeSidebarLogo" alt="logo" src="{{asset('assets/img/d2f-logo.png')}}">
    </div>


    <?php $authuser = Auth::user();
    $currentURL = url()->current();
    ?>

    <?php
    $url = URL::to('/');
    $string = request()->route()->getPrefix();
    $getprefix = str_replace('/', '', $string);
    $segment = Request::segment(2);
    $prefixurl = $url . '/' . $getprefix . '/';
    $authuser = Auth::user();
    $permissions = App\Models\UserPermission::where('user_id', $authuser->id)->pluck('permisssion_id')->ToArray();
    $submenusegment = Request::segment(3);
    // dd($permissions);
    ?>
    <div class="shadow-bottom"></div>
    <li class="menu">
        <a href="{{$prefixurl.'dashboard'}}" data-active="<?php if($segment == 'dashboard'){?>true<?php }?>"
            class="dropdown-toggle">
            <div class="@if(str_contains($currentURL, 'dashboard')) active @endif">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="feather feather-home">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                </svg>
                <span>Dashboard</span>
            </div>
        </a>
    </li>
    <?php
    if(!empty($permissions)){
    if(in_array('1', $permissions))
    {
    ?>
    <!-- <li class="menu">
        <a href="{{$prefixurl.'users'}}" data-active="<?php if($segment == 'users'){?>true<?php }?>"
            class="dropdown-toggle">
            <div class="@if(str_contains($currentURL, 'users')) active @endif">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="feather feather-users">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
                <span>Users</span>
            </div>
        </a>
    </li> -->
    <?php }
    }
    ?>
    <?php
    if(!empty($permissions)){
    if(in_array('2', $permissions))
    {
    ?>
    <!-- <li class="menu">
        <a href="{{$prefixurl.'locations'}}" data-active="<?php if($segment == 'locations'){?>true<?php }?>"
            class="dropdown-toggle">
            <div class="@if(str_contains($currentURL, 'locations')) active @endif">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="feather feather-map-pin">
                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                    <circle cx="12" cy="10" r="3"></circle>
                </svg>
                <span>Branch Locations</span>
            </div>
        </a>
    </li> -->
    <?php }
    }
    ?>
    <?php
    if(!empty($permissions)){
    if(in_array('3', $permissions))
    {
    ?>
    <p class="menuHead menuHeadHidden mb-0">Masters</p>
    <li class="menu">
        <a href="{{$prefixurl.'consigners'}}" data-active="<?php if($segment == 'consigners'){?>true<?php }?>"
            class="dropdown-toggle">
            <div class="@if(str_contains($currentURL, 'consigners')) active @endif">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="feather feather-user">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
                <span>Pilot HQ</span>
            </div>
        </a>
    </li>
    <?php }
    } ?>
    <?php
    if(!empty($permissions)){
    if(in_array('4', $permissions))
    {
    ?>
    <li class="menu">
        <a href="{{$prefixurl.'consignees'}}" data-active="<?php if($segment == 'consignees'){?>true<?php }?>"
            class="dropdown-toggle">
            <div class="@if(str_contains($currentURL, 'consignees')) active @endif">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="feather feather-user">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
                <span>Farmer</span>
            </div>
        </a>
    </li>
    <?php }
    }
    ?>
    <!-- <li class="menu">
                        <a href="{{$prefixurl.'brokers'}}" data-active="<?php if($segment == 'brokers'){?>true<?php }?>" class="dropdown-toggle">
                            <div class="">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-users"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                                <span>Brokers</span>
                            </div>
                        </a>
                    </li> -->
    <?php
    if(!empty($permissions)){
    if(in_array('5', $permissions))
    {
    ?>
    <li class="menu">
        <a href="{{$prefixurl.'drivers'}}" data-active="<?php if($segment == 'drivers'){?>true<?php }?>"
            class="dropdown-toggle">
            <div class="@if(str_contains($currentURL, 'drivers')) active @endif">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="feather feather-users">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
                <span>Pilot</span>
            </div>
        </a>
    </li>
    <?php }
    } ?>
    <?php
    if(!empty($permissions)){
    if(in_array('6', $permissions))
    {
    ?>
    <li class="menu">
        <a href="{{$prefixurl.'vehicles'}}" data-active="<?php if($segment == 'vehicles'){?>true<?php }?>"
            class="dropdown-toggle">
            <div class="@if(str_contains($currentURL, 'vehicles')) active @endif">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="feather feather-truck">
                    <rect x="1" y="3" width="15" height="13"></rect>
                    <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
                    <circle cx="5.5" cy="18.5" r="2.5"></circle>
                    <circle cx="18.5" cy="18.5" r="2.5"></circle>
                </svg>
                <span>Drone</span>
            </div>
        </a>
    </li>
    <?php }
    } ?>
    <?php
    if(!empty($permissions)){
    if(in_array('7', $permissions))
    {
    ?>
    <?php }
    } ?>
    <?php if(!empty($permissions)){
    if(in_array('5', $permissions))
    {
    ?>
    <li class="menu">
        <a href="{{$prefixurl.'postal-code'}}" data-active="<?php if($segment == 'postal-code'){?>true<?php }?>"
            class="dropdown-toggle">
            <div class="@if(str_contains($currentURL, 'postal-code')) active @endif">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="feather feather-users">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
                <span>Pin Code</span>
            </div>
        </a>
    </li>
    <?php }
    } ?>
    <!-- li class="menu">
        <a href="#consignment" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
            <div
                class="@if(str_contains($currentURL, 'consignments') || str_contains($currentURL, 'bulklr-view')) active @endif">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="feather feather-server">
                    <rect x="2" y="2" width="20" height="8" rx="2" ry="2"></rect>
                    <rect x="2" y="14" width="20" height="8" rx="2" ry="2"></rect>
                    <line x1="6" y1="6" x2="6.01" y2="6"></line>
                    <line x1="6" y1="18" x2="6.01" y2="18"></line>
                </svg>
                <span>Consignment</span>
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="feather feather-chevron-right submenuArrow">
                <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
        </a>
        <ul class="collapse submenu list-unstyled" id="consignment" data-parent="#accordionExample">
            <li>
                <div class="submenuListStyle"></div><a href="{{$prefixurl.'consignments/create'}}">Create Consignment
                </a>
            </li>
            <li>
                <div class="submenuListStyle"></div><a href="{{$prefixurl.'consignments'}}"> Consignment List </a>
            </li>
            <li>
                <div class="submenuListStyle"></div><a href="{{$prefixurl.'bulklr-view'}}"> Bulk Lr Download </a>
            </li>

        </ul>
    </li -->
    <p class="menuHead menuHeadHidden mb-0">Pickup & Delivery</p>

    <li class="menu">
        <a href="#ftl" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
            <div
                class="@if(str_contains($currentURL, 'order-book-ftl') || str_contains($currentURL, 'create-ftl')) active @endif">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="feather feather-layers">
                    <polygon points="12 2 2 7 12 12 22 7 12 2"></polygon>
                    <polyline points="2 17 12 22 22 17"></polyline>
                    <polyline points="2 12 12 17 22 12"></polyline>
                </svg>
                <span>FTL Consignment</span>
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="feather feather-chevron-right submenuArrow">
                <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
        </a>
        <ul class="collapse submenu list-unstyled" id="ftl" data-parent="#accordionExample">
            <li>
                <div class="submenuListStyle"></div><a href="{{$prefixurl.'order-book-ftl'}}"> Block LR No </a>
            </li>
            <li>
                <div class="submenuListStyle"></div><a href="{{$prefixurl.'reserve-lr'}}"> Complete Blocked LR </a>
            </li>
            <li>
                <div class="submenuListStyle"></div><a href="{{$prefixurl.'create-ftl'}}"> Create FTL LR</a>
            </li>
            <li>
                <div class="submenuListStyle"></div><a href="{{$prefixurl.'consignments'}}"> Consignment List </a>
            </li>
            <li>
                <div class="submenuListStyle"></div><a href="{{$prefixurl.'bulklr-view'}}"> Bulk Lr Download </a>
            </li>
            <li>
                <div class="submenuListStyle"></div><a href="{{$prefixurl.'pod-view'}}"> Pod View </a>
            </li>
          
        </ul>
    </li>

    <li class="menu">
        <a href="#Ptl" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
            <div
                class="@if(str_contains($currentURL, 'order-book-ptl') || str_contains($currentURL, 'orders') || str_contains($currentURL, 'create-ptl')) active @endif">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="feather feather-trello">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                    <rect x="7" y="7" width="3" height="9"></rect>
                    <rect x="14" y="7" width="3" height="5"></rect>
                </svg>
                <span>PTL Consignment</span>
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="feather feather-chevron-right submenuArrow">
                <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
        </a>
        <ul class="collapse submenu list-unstyled" id="Ptl" data-parent="#accordionExample">
            <li>
                <div class="submenuListStyle"></div><a href="{{$prefixurl.'orders'}}"> Order List</a>
            </li>
            <li>
                <div class="submenuListStyle"></div><a href="{{$prefixurl.'order-book-ptl'}}"> Create PTL lR</a>
            </li>
            <!-- <li>
                <div class="submenuListStyle"></div><a href="{{$prefixurl.'create-ptl'}}"> Create LR Ptl</a>
            </li> -->
            <li>
                <div class="submenuListStyle"></div><a href="{{$prefixurl.'consignments'}}"> Consignment List </a>
            </li>
            <li>
                <div class="submenuListStyle"></div><a href="{{$prefixurl.'bulklr-view'}}"> Bulk Lr Download </a>
            </li>
            <li>
                <div class="submenuListStyle"></div><a href="{{$prefixurl.'pod-view'}}"> Pod View </a>
            </li>

            <!-- <li class="submenuListStyle">
                <a href="#drs" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                    DRS 🔻
                </a>
                <ul class="collapse submenu sub-submenu list-unstyled" id="drs" data-parent="#Ptl">
                    <li>🔹 <a href="{{$prefixurl.'unverified-list'}}"> Create Drs </a></li>
                    <li>🔹 <a href="{{$prefixurl.'transaction-sheet'}}"> Drs List </a></li>
                </ul>
            </li>

            <li class="submenuListStyle">
                <a href="#hrs" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                    HRS 🔻
                </a>
                <ul class="collapse submenu sub-submenu list-unstyled" id="hrs" data-parent="#Ptl">
                    <li>🔹 <a href="{{$prefixurl.'hrs-list'}}"> Create Hrs </a></li>
                    <li>🔹 <a href="{{$prefixurl.'hrs-sheet'}}"> Hrs List </a></li>
                    <li>🔹 <a href="{{$prefixurl.'incoming-hrs'}}"> Incoming Hrs </a></li>
                    <li>🔹 <a href="{{$prefixurl.'outgoing-hrs'}}"> Outgoing Hrs </a></li>
                </ul>
            </li> -->
        </ul>
    </li>

    <li class="menu">
        <a href="#drs" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
            <div
                class="@if(str_contains($currentURL, 'unverified-list') || str_contains($currentURL, 'transaction-sheet')) active @endif">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="feather feather-trello">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                    <rect x="7" y="7" width="3" height="9"></rect>
                    <rect x="14" y="7" width="3" height="5"></rect>
                </svg>
                <span>DRS</span>
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="feather feather-chevron-right submenuArrow">
                <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
        </a>
        <ul class="collapse submenu list-unstyled" id="drs" data-parent="#accordionExample">
            <li>
                <div class="submenuListStyle"></div><a href="{{$prefixurl.'unverified-list'}}"> Create Drs</a>
            </li>
            <li>
                <div class="submenuListStyle"></div><a href="{{$prefixurl.'transaction-sheet'}}">Drs List</a>
            </li>
        </ul>
    </li>

    <!-- <li class="menu">
        <a href="#hrs" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
            <div
                class="@if(str_contains($currentURL, 'hrs-list') || str_contains($currentURL, 'hrs-sheet') || str_contains($currentURL, 'incoming-hrs')  || str_contains($currentURL, 'outgoing-hrs')) active @endif">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="feather feather-trello">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                    <rect x="7" y="7" width="3" height="9"></rect>
                    <rect x="14" y="7" width="3" height="5"></rect>
                </svg>
                <span>HRS</span>
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="feather feather-chevron-right submenuArrow">
                <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
        </a>
        <ul class="collapse submenu list-unstyled" id="hrs" data-parent="#accordionExample">
            <li>
                <div class="submenuListStyle"></div><a href="{{$prefixurl.'hrs-list'}}"> Create Hrs</a>
            </li>
            <li>
                <div class="submenuListStyle"></div><a href="{{$prefixurl.'hrs-sheet'}}">Hrs List</a>
            </li>
            <li>
                <div class="submenuListStyle"></div><a href="{{$prefixurl.'incoming-hrs'}}"> Incoming Hrs</a>
            </li>
            <li>
                <div class="submenuListStyle"></div><a href="{{$prefixurl.'outgoing-hrs'}}">Outgoing Hrs</a>
            </li>
        </ul>
    </li> -->

    <?php if($authuser->role_id == 1 || $authuser->role_id ==2 || $authuser->role_id ==3 || $authuser->role_id ==4){ ?>
    <!-- <li class="menu">
        <a href="#prs" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
            <div
                class="@if(str_contains($currentURL, 'prs') || str_contains($currentURL, 'driver-tasks') || str_contains($currentURL, 'vehicle-receivegate')) active @endif">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="feather feather-archive">
                    <polyline points="21 8 21 21 3 21 3 8"></polyline>
                    <rect x="1" y="3" width="22" height="5"></rect>
                    <line x1="10" y1="12" x2="14" y2="12"></line>
                </svg>
                <span>PRS</span>
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="feather feather-chevron-right submenuArrow">
                <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
        </a>
        <ul class="collapse submenu list-unstyled" id="prs" data-parent="#accordionExample">
            <li>
                <div class="submenuListStyle"></div><a href="{{$prefixurl.'prs'}}"> Create Prs </a>
            </li>
            <li>
                <div class="submenuListStyle"></div><a href="{{$prefixurl.'driver-tasks'}}"> Driver Task </a>
            </li>
            <li>
                <div class="submenuListStyle"></div><a href="{{$prefixurl.'vehicle-receivegate'}}"> Hub Receiving </a>
            </li>
            <li>
                <div class="submenuListStyle"></div><a href="{{$prefixurl.'pickup-loads'}}">Pickup Load List </a>
            </li>

        </ul>
    </li> -->
   <?php }
   if($authuser->role_id == 2 || $authuser->role_id ==3 || $authuser->role_id ==5){ ?>
    <!-- <p class="menuHead menuHeadHidden mb-0">Payments</p>
    <li class="menu">
        <a href="{{$prefixurl.'vendor-list'}}" data-active="<?php if($segment == 'vendor-list'){?>true<?php }?>"
            class="dropdown-toggle">
            <div class="@if(str_contains($currentURL, 'drivers')) active @endif">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="feather feather-users">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
                <span>Vendors</span>
            </div>
        </a>
    </li> -->

    <!-- <li class="menu">
        <a href="#drsPayments" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
            <div
                class="@if(str_contains($currentURL, 'drs-paymentlist') || str_contains($currentURL, 'request-list') || str_contains($currentURL, 'payment-report-view') || str_contains($currentURL, 'drswise-report')) active @endif">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="feather feather-dollar-sign">
                    <line x1="12" y1="1" x2="12" y2="23"></line>
                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                </svg>
                <span>DRS Payments</span>
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="feather feather-chevron-right submenuArrow">
                <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
        </a> 
        <ul class="collapse submenu list-unstyled" id="drsPayments" data-parent="#accordionExample"> -->
            <!-- <li>
                <div class="submenuListStyle"></div><a href="{{$prefixurl.'vendor-list'}}"> Vendor List </a>
            </li> -->
            <!-- <li>
                <div class="submenuListStyle"></div><a href="{{$prefixurl.'drs-paymentlist'}}"> Create Payments </a>
            </li>
            <li>
                <div class="submenuListStyle"></div><a href="{{$prefixurl.'request-list'}}"> Transaction Status </a>
            </li>
            <li>
                <div class="submenuListStyle"></div><a href="{{$prefixurl.'payment-report-view'}}">Report -Transaction Id </a>
            </li>
            <li>
                <div class="submenuListStyle"></div><a href="{{$prefixurl.'drswise-report'}}">Payment Report-DRS Id</a>
            </li>

        </ul>
    </li> -->

    <!-- <li class="menu">
        <a href="#hrsPayments" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
            <div
                class="@if(str_contains($currentURL, 'hrs-payment-list')) active @endif">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="feather feather-dollar-sign">
                    <line x1="12" y1="1" x2="12" y2="23"></line>
                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                </svg>
                <span>HRS Payments</span>
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="feather feather-chevron-right submenuArrow">
                <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
        </a>
        <ul class="collapse submenu list-unstyled" id="hrsPayments" data-parent="#accordionExample">
            <li>
                <div class="submenuListStyle"></div><a href="{{$prefixurl.'hrs-payment-list'}}"> Create Payment </a>
            </li>
            <li>
                <div class="submenuListStyle"></div><a href="{{$prefixurl.'hrs-request-list'}}"> Request List </a>
            </li>
        </ul>
    </li> -->
    <!-- <li class="menu">
        <a href="#prsPayments" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
            <div
                class="@if(str_contains($currentURL, 'prs-paymentlist')) active @endif">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="feather feather-dollar-sign">
                    <line x1="12" y1="1" x2="12" y2="23"></line>
                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                </svg>
                <span>PRS Payments</span>
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="feather feather-chevron-right submenuArrow">
                <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
        </a>
        <ul class="collapse submenu list-unstyled" id="prsPayments" data-parent="#accordionExample">
            <li>
                <div class="submenuListStyle"></div><a href="{{$prefixurl.'prs-paymentlist'}}"> Create Payment </a>
            </li>
            <li>
                <div class="submenuListStyle"></div><a href="{{$prefixurl.'prs-request-list'}}"> Request List </a>
            </li>

        </ul>
    </li> -->
    <?php } ?>

    <!-- <p class="menuHead menuHeadHidden mb-0">Reports</p>

    <li class="menu">
        <a href="#misReports" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
            <div
                class="@if(str_contains($currentURL, 'consignment-misreport') || str_contains($currentURL, 'consignment-report2')) active @endif">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="feather feather-clipboard">
                    <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
                    <rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect>
                </svg>
                <span>MIS Reports</span>
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="feather feather-chevron-right submenuArrow">
                <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
        </a>
        <ul class="collapse submenu list-unstyled" id="misReports" data-parent="#accordionExample">
            <li>
                <div class="submenuListStyle"></div><a href="{{$prefixurl.'consignment-misreport'}}"> Mis Report 1 </a>
            </li>
            <li>
                <div class="submenuListStyle"></div><a href="{{$prefixurl.'consignment-report2'}}"> Mis Report 2 </a>
            </li>
        </ul>
    </li> -->

    
    <!-- <li class="menu">
        <a href="#accountReports" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
            <div
                class="@if(str_contains($currentURL, 'pod-view')) active @endif">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="feather feather-clipboard">
                    <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
                    <rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect>
                </svg>
                <span>Account Reports</span>
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="feather feather-chevron-right submenuArrow">
                <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
        </a>
        <ul class="collapse submenu list-unstyled" id="accountReports" data-parent="#accordionExample">
            <li>
                <div class="submenuListStyle"></div><a href="{{$prefixurl.'pod-view'}}"> Pod View </a>
            </li>
        </ul>
    </li> -->

    <!-- <li class="menu">
        <a href="#vendors" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
            <div
                class="@if(str_contains($currentURL, 'vendor-list') || str_contains($currentURL, 'drs-paymentlist') || str_contains($currentURL, 'request-list') || str_contains($currentURL, 'payment-report-view') || str_contains($currentURL, 'drswise-report')) active @endif">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="feather feather-dollar-sign">
                    <line x1="12" y1="1" x2="12" y2="23"></line>
                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                </svg>
                <span>Payments</span>
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="feather feather-chevron-right submenuArrow">
                <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
        </a>
        <ul class="collapse submenu list-unstyled" id="vendors" data-parent="#accordionExample">
            <li>
                <div class="submenuListStyle"></div><a href="{{$prefixurl.'vendor-list'}}"> Vendors </a>
            </li>
            <li>
                <div class="submenuListStyle"></div><a href="{{$prefixurl.'drs-paymentlist'}}"> Create Payments </a>
            </li>
            <li>
                <div class="submenuListStyle"></div><a href="{{$prefixurl.'request-list'}}"> Transaction Status </a>
            </li>
            <li>
                <div class="submenuListStyle"></div><a href="{{$prefixurl.'payment-report-view'}}">Payment Report
                    -Transaction Id wise </a>
            </li>
            <li>
                <div class="submenuListStyle"></div><a href="{{$prefixurl.'drswise-report'}}">Payment Report - DRS id
                    wise </a>
            </li>

        </ul>
    </li> -->

    <!-- <li class="menu">
        <a href="#reports" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
            <div
                class="@if(str_contains($currentURL, 'consignment-misreport') || str_contains($currentURL, 'consignment-report2')) active @endif">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="feather feather-clipboard">
                    <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
                    <rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect>
                </svg>
                <span>Reports</span>
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="feather feather-chevron-right submenuArrow">
                <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
        </a>
        <ul class="collapse submenu list-unstyled" id="reports" data-parent="#accordionExample">
            <li>
                <div class="submenuListStyle"></div><a href="{{$prefixurl.'postal-code'}}"> Pin Code </a>
            </li>
        </ul>
    </li> -->
    <?php if($authuser->role_id == 1){ ?>
    <!-- <li class="menu">
        <a href="#users" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
            <div
                class="@if(str_contains($currentURL, 'users') || str_contains($currentURL, 'roles') || str_contains($currentURL, 'permissions')) active @endif">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="feather feather-users">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
                <span>System Settings</span>
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="feather feather-chevron-right submenuArrow">
                <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
        </a>
        <ul class="collapse submenu list-unstyled" id="users" data-parent="#accordionExample">
            <li>
                <div class="submenuListStyle"></div><a href="{{$prefixurl.'users'}}"> All Users </a>
            </li>
            <li>
                <div class="submenuListStyle"></div><a href="{{$prefixurl.'roles'}}"> All Roles </a>
            </li>
            <li>
                <div class="submenuListStyle"></div><a href="{{$prefixurl.'permissions'}}"> All Permissions </a>
            </li>
            <li>
                <div class="submenuListStyle"></div><a href="{{$prefixurl.'clients'}}"> Clients </a>
            </li>
        </ul>
    </li> -->
    <?php } if($authuser->role_id == 1 || $authuser->role_id == 3){ ?>
    <li class="menu">
        <a href="#forms" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
            <div
                class="@if(str_contains($currentURL, 'bulk-import') || str_contains($currentURL, 'branch-address')) active @endif">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="feather feather-clipboard">
                    <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
                    <rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect>
                </svg>
                <span>System Settings</span>
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="feather feather-chevron-right">
                <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
        </a>
        <ul class="collapse submenu " id="forms" data-parent="#accordionExample">
            <li>
                <div class="submenuListStyle"></div><a href="{{url($prefix.'/bulk-import')}}"> Import Data </a>
            </li>
            <?php if($authuser->role_id == 1){ ?>
            <li>
                <div class="submenuListStyle"></div><a href="{{url($prefix.'/settings/branch-address')}}">Company Setup </a>
            </li>
            <li>
                <div class="submenuListStyle"></div><a href="{{url($prefix.'/users')}}">All Users</a>
            </li>
            <li>
                <div class="submenuListStyle"></div><a href="{{url($prefix.'/locations')}}">Branches </a>
            </li>
            <li>
                <div class="submenuListStyle"></div><a href="{{$prefixurl.'clients'}}"> Base Clients </a>
            </li>
            <li>
                <div class="submenuListStyle"></div><a href="{{$prefixurl.'reginal-clients'}}"> Regional Client </a>
            </li>
            <?php } ?>

        </ul>
    </li>
    <?php } ?>

</ul>