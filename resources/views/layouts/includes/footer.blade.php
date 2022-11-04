<div class="footer-wrapper">

    <div class="footer-section f-section-1">
        <p class="">Copyright © {{ date('Y') }} <a target="_blank" href="#">ETERNITY</a>, All rights reserved.</p>
    </div>
    <div class="footer-section f-section-2">
        <p class="">Coded with <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="feather feather-heart">
                <path
                    d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z">
                </path>
            </svg></p>
    </div>
</div>
</div>
<!--  END CONTENT AREA  -->
<style>
            audio, canvas, embed, iframe, img, object, svg, video {
                display: unset !important;
                vertical-align: middle;
            }
            .sm\:p-6 {
                padding: 1.5rem;
                z-index: 999999;
            }
        </style>     
        <!-- <link href="https://unpkg.com/tailwindcss@^1.0/dist/tailwind.min.css" rel="stylesheet" defer> -->
        <!-- <div id="message" x-data="{ showMessage: false, message: '' }" class="fixed inset-0 flex items-end justify-center px-4 py-6 pointer-events-none sm:p-6 sm:items-start sm:justify-end">
            <div  x-show="showMessage"
                x-transition:enter="transform ease-out duration-300 transition"
                x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
                x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
                x-transition:leave="transition ease-in duration-100"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="max-w-sm w-full bg-white shadow-lg rounded-lg pointer-events-auto">
                <div class="rounded-lg shadow-xs overflow-hidden">
                    <div class="p-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                
                                <svg class="h-6 w-6 text-green-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-3 w-0 flex-1 pt-0.5">
                                <p class="text-sm leading-5 font-medium text-gray-900" x-text="message">
                                </p>
                            </div>
                            <div class="ml-4 flex-shrink-0 flex">
                                <button class="inline-flex text-gray-400 focus:outline-none focus:text-gray-500 transition ease-in-out duration-150" onclick="closeMessage();">
                                    
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> -->

</div>
<!-- END MAIN CONTAINER -->

 <!-- <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js"></script> -->
    <!-- <script src="{{ asset('js/app.js') }}"></script> -->
    <script>

    //     Echo.channel('events')
    //    .listen('RealTimeMessage', (e) => console.log('RealTimeMessage: ' + e.message));

        /*function closeMessage() {
            message.__x.$data.showMessage = false;
        }
        
        Echo.channel('events')
            .listen('RealTimeMessage', (e) => {
                let message = document.getElementById('message');
                //console.log(e.message);
                message.__x.$data.showMessage = true;
                message.__x.$data.message = e.message;
        

                setTimeout(function () {
                    closeMessage()
                }, 115000);
            });*/

    </script>

<!-- BEGIN GLOBAL MANDATORY SCRIPTS -->
<script src="{{asset('newasset/assets/js/libs/jquery-3.1.1.min.js')}}"></script>
<script src="{{asset('newasset/bootstrap/js/popper.min.js')}}"></script>
<script src="{{asset('newasset/bootstrap/js/bootstrap.min.js')}}"></script>

<script src="{{asset('assets/js/customjquery.validate.min.js')}}"></script>
<script src="https://jqueryvalidation.org/files/dist/additional-methods.min.js"></script>


<script src="{{asset('newasset/plugins/perfect-scrollbar/perfect-scrollbar.min.js')}}"></script>
<script src="{{asset('newasset/assets/js/app.js')}}"></script>
<script src="{{asset('assets/js/custom-validation.js')}}"></script>

<!-- sweet alert -->
<script src="{{asset('plugins/sweetalerts/sweetalert2.min.js')}}"></script>
<script src="{{asset('plugins/sweetalerts/custom-sweetalert.js')}}"></script>
<!-- multi select -->
<script src="{{asset('plugins/select2/select2.min.js')}}"></script>
<script src="{{asset('plugins/select2/custom-select2.js')}}"></script>

<script src="{{asset('assets/js/jquery.sumoselect.js')}}"></script>

<script>
    var APP_URL = {!! json_encode(url('/'))!!};

    $(document).ready(function () {
        App.init();
    });
</script>

<!-- END GLOBAL MANDATORY SCRIPTS -->

<!-- BEGIN PAGE LEVEL PLUGINS/CUSTOM SCRIPTS -->
<script src="{{asset('newasset/plugins/apex/apexcharts.min.js')}}"></script>
<script src="{{asset('newasset/assets/js/dashboard/dash_2.js')}}"></script>
<script src="{{asset('newasset/assets/js/widgets/modules-widgets.js')}}"></script>

<script src="{{asset('assets/js/form-validation.js')}}"></script>
<script src="{{asset('assets/js/custom.js')}}"></script>
<script src="{{asset('assets/js/jquery.toast.js')}}"></script>
<!-- BEGIN PAGE LEVEL PLUGINS/CUSTOM SCRIPTS -->

<script src="{{asset('plugins/table/datatable/datatables.js')}}"></script>
<!-- NOTE TO Use Copy CSV Excel PDF Print Options You Must Include These Files  -->
<script src="{{asset('plugins/table/datatable/button-ext/dataTables.buttons.min.js')}}"></script>
<script src="{{asset('plugins/table/datatable/button-ext/jszip.min.js')}}"></script>
<script src="{{asset('plugins/table/datatable/button-ext/buttons.html5.min.js')}}"></script>
<script src="{{asset('plugins/table/datatable/button-ext/buttons.print.min.js')}}"></script>
<script>

 /* Formatting function for row details - modify as you need */
    function format(d) {
        // `d` is the original data object for the row
        return '<div class="card">'+
				'<div class="card-body">'+
								'<ul class="nav nav-tabs nav-primary" role="tablist">'+
									'<li class="nav-item" role="presentation">'+
										'<a class="nav-link active" data-toggle="tab" href="#primaryhome" role="tab" aria-selected="true">'+
											'<div class="d-flex align-items-center">'+
												'<div class="tab-icon"><i class="bx bx-home font-18 me-1"></i>'+
												'</div>'+
												'<div class="tab-title">TXN Details</div>'+
											'</div>'+
										'</a>'+
									'</li>'+
                                    '<li class="nav-item" role="presentation">'+
										'<a class="nav-link" data-toggle="tab" href="#primaryprofile" role="tab" aria-selected="false">'+
											'<div class="d-flex align-items-center">'+
												'<div class="tab-icon"><i class="bx bx-user-pin font-18 me-1"></i>'+
												'</div>'+
												'<div class="tab-title">Timeline</div>'+
											'</div>'+
										'</a>'+
									'</li>'+
									'<li class="nav-item" role="presentation">'+
										'<a class="nav-link" data-toggle="tab" href="#primarycontact" role="tab" aria-selected="true">'+
											'<div class="d-flex align-items-center">'+
												'<div class="tab-icon"><i class="bx bx-microphone font-18 me-1"></i>'+
												'</div>'+
												'<div class="tab-title">Order Details</div>'+
											'</div>'+
										'</a>'+
									'</li>'+
								'</ul>'+
								'<div class="tab-content py-3">'+
									'<div class="tab-pane active" id="primaryhome" role="tabpanel">'+
                                    '<div class="row">'+
                                    '<div class="col-md-4">'+
                                    '<strong class="labels">'+d.txndetails+
                                    '</div>'+
                                    '<div class="col-md-8">'+d.trackinglink+
                                    '</div>'+
                                    '</div>'+
									'</div>'+
                                    '<div class="tab-pane fade" id="primaryprofile" role="tabpanel">'+d.trail+
									'</div>'+
									'<div class="tab-pane fade" id="primarycontact" role="tabpanel">'+d.orderdetails+
									'</div>'+
								'</div>'+
							'</div>'+
						'</div>'+
                        '<script type="text/javascript">\n' + 
                    "var map = new google.maps.Map(document.getElementById('map-"+d.id+"'), {zoom: 8, center: 'Delhi',});\n"+
                    "var directionsDisplay = new google.maps.DirectionsRenderer({'draggable': false});\n"+
                    "var directionsService = new google.maps.DirectionsService();\n"+
                    "var travel_mode = 'DRIVING';\n"+ 
                    "var origin = '"+d.con_city+"';\n"+
                    "var destination = '"+d.city+"';\n"+
                    "directionsService.route({\n"+
                                "origin: origin,\n"+
                                "destination: destination,\n"+
                                "travelMode: travel_mode,\n"+
                                "avoidTolls: true\n"+
                            "}, function (response, status) {\n"+
                                "if (status === 'OK') {\n"+
                                    "directionsDisplay.setMap(map);\n"+
                                    "directionsDisplay.setDirections(response);\n"+
                                "} else {\n"+
                                    "directionsDisplay.setMap(null);\n"+
                                    "directionsDisplay.setDirections(null);\n"+
                                    // "alert('Unknown route found with error code 0, contact your manager');\n"+
                                "}\n"+
                    "});\n"+
             '<\/script>';

    }

    $('.get-datatable').DataTable({

        "dom": "<'dt--top-section'<'row'<'col-sm-12 col-md-6 d-flex justify-content-md-start justify-content-center'B><'col-sm-12 col-md-6 d-flex justify-content-md-end justify-content-center mt-md-0 mt-3'f>>>" +
            "<'table-responsive'tr>" +
            "<'dt--bottom-section d-sm-flex justify-content-sm-between text-center'<'dt--pages-count  mb-sm-0 mb-3'i><'dt--pagination'p>>",
        buttons: {
            buttons: [
                // { extend: 'copy', className: 'btn btn-sm' },
                // { extend: 'csv', className: 'btn btn-sm' },
                { extend: 'excel', className: 'btn btn-sm' },
                // { extend: 'print', className: 'btn btn-sm' }
            ]
        },
        "oLanguage": {
            "oPaginate": { "sPrevious": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>', "sNext": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>' },
            "sInfo": "Showing page PAGE of _PAGES_",
            "sSearch": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
            "sSearchPlaceholder": "Search...",
            "sLengthMenu": "Results :  _MENU_",
        },

         "ordering": false,
        "paging": true,
        "pageLength": 100,

    });

    $(document).ready(function () {
        var jobs_count = 0;

        setInterval(function () {
            // the actual code:
            // $.get('notification.php', checkJobsCounter);
            // notification.php returns a single number, e.g.: 10

            // demo: return the number of minutes in each hour instead the job_id and jobs_count
            var mock = (new Date()).getMinutes();
            checkJobsCounter(mock);

        }, 15000);

        function checkJobsCounter(data) {
            if (jobs_count !== data) {
                if ($(".alert:not(.hidden)").length <= 0) { // don't show new alert if the user haven't dismissed the old one

                    // job_id got updated... need to show it
                    var $notification = $("#alert_template")
                        .clone()
                        .removeClass("hidden")
                        .attr("id", "") // removes id of the cloned DOM node
                        .appendTo("body");
                }

                $(".alert-dismissable span#jobs_count").text(data);
                jobs_count = data;
            }

            if (jobs_count === 0) {
                $(".alert-dismissable:not(.hidden) button").click();
            }
        }

    });

    ////////////////////////////////////////////////////////
    // $("#select_all").click(function () {
    //             if($(this).is(':checked')){

    //                 $('.ddd').prop('checked', true);
    //          }else{
    //            $('.ddd').prop('checked', false);
    //       }
    //         });
    //         $(function () {
    //             $('#launch_model').click(function () {
    //                 var selectedID = [];
    //         $(':checkbox[name="checked_consign[]"]:checked').each (function () {
    //             selectedID.push(this.value);
    //         });

    //                 //alert(selectedID );
    //                 $('#consignment_id').val(selectedID);
    //             }); 
    //         });
    //         ///////////////////////////
    $("#select_all").click(function () {
        if ($(this).is(':checked')) {

            $('.ddd').prop('checked', true);
        } else {
            $('.ddd').prop('checked', false);
        }
    });
    $(function () {
        $('#create_edd').click(function () {

            var consignmentID = [];
            $(':checkbox[name="checked_consign[]"]:checked').each(function () {
                consignmentID.push(this.value);
            });
    
            $.ajax({
                url: "create-drs",
                method: "POST",
                data: { consignmentID: consignmentID },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                beforeSend: function () {
                    $('.disableDrs').prop('disabled', true);

                },
                complete: function (response) {
                    $('.disableDrs').prop('disabled', true);
                },
                success: function (data) {
                    if (data.success == true) {

                        swal('success','Drs Created Successfully','success');
                        window.location.href = "transaction-sheet";
                    }
                    else {
                        swal('error','something wrong','error');
                    }

                }
            })


        });
    });
    ///////////////////////////
    $('.updat_edd').blur(function () {
        //alert('h');
        var consignment_id = $(this).attr('data-id');
        var drs_edd = $(this).val();
        var _token = $('input[name="_token"]').val();
        $.ajax({
            url: "update-edd",
            method: "POST",
            data: { drs_edd: drs_edd, consignment_id: consignment_id, _token: _token },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType: 'json',
            success: function (result) {

            }
        })

    });

    //   // Add event listener for opening and closing details
    //   $('#usertable tbody').on('click', 'td.dt-control', function () {
    //         var tr = $(this).closest('tr');
    //         var row = table.row(tr);

    //         if (row.child.isShown()) {
    //             // This row is already open - close it
    //             row.child.hide();
    //             tr.removeClass('shown');
    //         } else {
    //             // Open this row
    //             row.child(format(row.data())).show();
    //             tr.addClass('shown');
    //         }
    //     });

    $('#consignment_report').DataTable({
        "dom": "<'dt--top-section'<'row'<'col-sm-12 col-md-6 d-flex justify-content-md-start justify-content-center'B><'col-sm-12 col-md-6 d-flex justify-content-md-end justify-content-center mt-md-0 mt-3'f>>>" +
            "<'table-responsive'tr>" +
            "<'dt--bottom-section d-sm-flex justify-content-sm-between text-center'<'dt--pages-count  mb-sm-0 mb-3'i><'dt--pagination'p>>",
        buttons: {
            buttons: [
                // { extend: 'copy', className: 'btn btn-sm' },
                // { extend: 'csv', className: 'btn btn-sm' },
                { extend: 'excel', className: 'btn btn-sm', title: '', },
                // { extend: 'print', className: 'btn btn-sm' }
            ]
        },
        "oLanguage": {
            "oPaginate": { "sPrevious": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>', "sNext": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>' },
            "sInfo": "Showing page _PAGE_ of _PAGES_",
            "sSearch": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
            "sSearchPlaceholder": "Search...",
            "sLengthMenu": "Results :  _MENU_",
        },

        "ordering": true,
        "paging": true,
        "pageLength": 80,

    });
    /////////////////////////////////////////////////////////
    $('#consignment_reportall').DataTable({
        "dom": "<'dt--top-section'<'row'<'col-sm-12 col-md-6 d-flex justify-content-md-start justify-content-center'B><'col-sm-12 col-md-6 d-flex justify-content-md-end justify-content-center mt-md-0 mt-3'f>>>" +
            "<'table-responsive'tr>" +
            "<'dt--bottom-section d-sm-flex justify-content-sm-between text-center'<'dt--pages-count  mb-sm-0 mb-3'i><'dt--pagination'p>>",
        buttons: {
            buttons: [
                // { extend: 'copy', className: 'btn btn-sm' },
                // { extend: 'csv', className: 'btn btn-sm' },
                { extend: 'excel', className: 'btn btn-sm', title: '', },
                // { extend: 'print', className: 'btn btn-sm' }
            ]
        },
        "oLanguage": {
            "oPaginate": { "sPrevious": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>', "sNext": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>' },
            "sInfo": "Showing page _PAGE_ of _PAGES_",
            "sSearch": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
            "sSearchPlaceholder": "Search...",
            "sLengthMenu": "Results :  _MENU_",
        },

        "ordering": true,
        "paging": true,
        "pageLength": 80,

    });

    ///////////////////////////////////
    $('#').DataTable({
        "dom": "<'dt--top-section'<'row'<'col-sm-12 col-md-6 d-flex justify-content-md-start justify-content-center'B><'col-sm-12 col-md-6 d-flex justify-content-md-end justify-content-center mt-md-0 mt-3'f>>>" +
            "<'table-responsive'tr>" +
            "<'dt--bottom-section d-sm-flex justify-content-sm-between text-center'<'dt--pages-count  mb-sm-0 mb-3'i><'dt--pagination'p>>",
        buttons: {
            buttons: [
                // { extend: 'copy', className: 'btn btn-sm' },
                // { extend: 'csv', className: 'btn btn-sm' },
                { extend: 'excel', className: 'btn btn-sm', title: '', },
                // { extend: 'print', className: 'btn btn-sm' }
            ]
        },
        "oLanguage": {
            "oPaginate": { "sPrevious": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>', "sNext": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>' },
            "sInfo": "Showing page _PAGE_ of _PAGES_",
            "sSearch": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
            "sSearchPlaceholder": "Search...",
            "sLengthMenu": "Results :  _MENU_",
        },

        "ordering": true,
        "paging": true,
        "pageLength": 80,

    });
    /////////////////////////////////////////////////
    function lrCheck(that) {
        if (that.value == "Successful") {
            document.getElementById("lrid").style.display = "block";
        } else {
            document.getElementById("lrid").style.display = "none";
        }
    }

    $(".bulk_loder").on("submit", function () {
        $("#pageloader").fadeIn();
    });
</script>
<!-- END PAGE LEVEL SCRIPTS -->