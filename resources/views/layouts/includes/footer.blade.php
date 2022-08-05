            <div class="footer-wrapper">
                <div class="footer-section f-section-1">
                    <p class="">Copyright © {{ date('Y') }} <a target="_blank" href="#">ETERNITY</a>, All rights reserved.</p>
                </div>
                <div class="footer-section f-section-2">
                    <p class="">Coded with <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-heart"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg></p>
                </div>
            </div>
        </div>
        <!--  END CONTENT AREA  --> 

    </div>
    <!-- END MAIN CONTAINER -->
    
    <!-- BEGIN GLOBAL MANDATORY SCRIPTS --> 
    <script src="{{asset('newasset/assets/js/libs/jquery-3.1.1.min.js')}}"></script>
    <script src="{{asset('newasset/bootstrap/js/popper.min.js')}}"></script>
    <script src="{{asset('newasset/bootstrap/js/bootstrap.min.js')}}"></script>

    <script src="{{asset('assets/js/customjquery.validate.min.js')}}"></script>
    <script src="http://jqueryvalidation.org/files/dist/additional-methods.min.js"></script>
    
    
    <script src="{{asset('newasset/plugins/perfect-scrollbar/perfect-scrollbar.min.js')}}"></script>
    <script src="{{asset('newasset/assets/js/app.js')}}"></script>
    <script src="{{asset('assets/js/custom-validation.js')}}"></script>
    <!-- multi select -->
    <script src="{{asset('plugins/select2/select2.min.js')}}"></script>
    <script src="{{asset('plugins/select2/custom-select2.js')}}"></script>

    <!-- sweet alert -->
    <script src="{{asset('plugins/sweetalerts/sweetalert2.min.js')}}"></script>
    <script src="{{asset('plugins/sweetalerts/custom-sweetalert.js')}}"></script>

    <script>
        var APP_URL = {!! json_encode(url('/')) !!};
        
        $(document).ready(function() {
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
										'<a class="nav-link active" data-bs-toggle="tab" href="#primaryhome" role="tab" aria-selected="true">'+
											'<div class="d-flex align-items-center">'+
												'<div class="tab-icon"><i class="bx bx-home font-18 me-1"></i>'+
												'</div>'+
												'<div class="tab-title">TXN Details</div>'+
											'</div>'+
										'</a>'+
									'</li>'+
								'</ul>'+
								'<div class="tab-content py-3">'+
									'<div class="tab-pane active" id="primaryhome" role="tabpanel">'+
                                    '<div class="row">'+
                                    '<div class="col-md-3">'+
                                    '<strong class="labels">Job Id:</strong> '+d.job_id+'<br/>'+
                                    '<strong class="labels">Oder No:</strong> '+d.order_id+'<br/>'+
                                    '<strong class="labels">Lr No:</strong> '+d.id+'<br/>'+
                                    '<strong class="labels">Consigner:</strong> '+d.consigner_id+'<br/>'+
                                    '<strong class="labels">Consigner City:</strong> '+d.con_city+'<br/>'+
                                    '<strong class="labels">Consignee :</strong> '+d.consignee_id+'<br/>'+
                                    '<strong class="labels">Consignee Address:</strong> '+d.city+'<br/>'+
                                    '<strong class="labels">Invoice No:</strong> '+d.invoice_no+'<br/>'+
                                    '<strong class="labels">Invoice Date :</strong> '+d.invoice_date+'<br/>'+
                                    '<strong class="labels">Invoice Amount:</strong> '+d.invoice_amount+'<br/>'+
                                    '<strong class="labels">Vehicle No:</strong> '+d.consignee_id+'<br/>'+
                                    '<strong class="labels">Boxes:</strong> '+d.total_quantity+'<br/>'+
                                    '<strong class="labels">Net Weight:</strong> '+d.total_weight+'<br/><br/>'+
                                    ''+d.route+'<br/>'+
                                    '</div>'+
                                    '<div class="col-md-9">'+
                                    '<div id="map-'+d.id+'"><iframe id="iGmap" width="100%" height="400px" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="'+d.tracking_link+'" ></iframe></div>'+
                                    '</div>'+
                                    '</div>'+
									'</div>'+
								'</div>'+
							'</div>'+
						'</div>';
               

        }
        
        $(document).ready(function () {
            var table = $('#lrlist').DataTable({
                ajax: '/admin/clist',
                columns: [
                    {
                        className: 'dt-control',
                        orderable: false,
                        data: null,
                        defaultContent: '',
                    },
                    { data: 'lrdetails' },
                    { data: 'route' },
                    { data: 'impdates' },
                    { data: 'poptions' },
                    { data: 'status' },
                    { data: 'delivery_status' },
                ],
                order: [[1, 'asc']],
                "dom": "<'dt--top-section'<'row'<'col-sm-12 col-md-6 d-flex justify-content-md-start justify-content-center'B><'col-sm-12 col-md-6 d-flex justify-content-md-end justify-content-center mt-md-0 mt-3'f>>>" +
                "<'table-responsive'tr>" +
                "<'dt--bottom-section d-sm-flex justify-content-sm-between text-center'<'dt--pages-count  mb-sm-0 mb-3'i><'dt--pagination'p>>",
            buttons: {
                buttons: [
                    // { extend: 'copy', className: 'btn btn-sm' },
                    // { extend: 'csv', className: 'btn btn-sm' },
                    // { extend: 'excel', className: 'btn btn-sm' },
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
            
            "ordering": false,
            "paging": true,
            "pageLength": 100,
            });
        
            // Add event listener for opening and closing details
            $('#lrlist tbody').on('click', 'td.dt-control', function () {
                var tr = $(this).closest('tr');
                var row = table.row(tr);
        
                if (row.child.isShown()) {
                    // This row is already open - close it
                    row.child.hide();
                    tr.removeClass('shown');
                } else {
                    // Open this row
                    row.child(format(row.data())).show();
                    tr.addClass('shown');
                }
            });
        });

        $('.get-datatable').DataTable( {
            
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
            
            "ordering": true,
            "paging": true,
            "pageLength": 100,

        } );

        $(document).ready(function() {
    var jobs_count = 0;

    setInterval(function() {
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

        if(jobs_count === 0) {
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
                    if($(this).is(':checked')){
                     
                        $('.ddd').prop('checked', true);
                 }else{ 
                   $('.ddd').prop('checked', false);
              }
                });
                $(function () {
                    $('#create_edd').click(function () { 

                        var consignmentID = [];
                $(':checkbox[name="checked_consign[]"]:checked').each (function () {
                    consignmentID.push(this.value);
                });
                // alert(consignmentID); return false;

                $.ajax({
                    url: "create-drs",
                    method: "POST",
                    data: {consignmentID: consignmentID},
                    headers   : {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                    dataType  : 'json',
                    beforeSend  : function () {
                   $('.disableDrs').prop('disabled', true);
             
                   },
                    complete: function (response) {
                        $('.disableDrs').prop('disabled', true);
                    },
                    success: function (data) {
                        if(data.success == true){
                        
                        alert('Data Updated Successfully');
                        location.reload();
                    }
                    else{
                        alert('something wrong');
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
                    data: { drs_edd: drs_edd,consignment_id:consignment_id, _token: _token },
                    headers   : {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                    dataType  : 'json',
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

$('#consignment_report').DataTable( {
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
                "sInfo": "Showing page _PAGE_ of _PAGES_",
                "sSearch": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
                "sSearchPlaceholder": "Search...",
               "sLengthMenu": "Results :  _MENU_",
            },
            
            "ordering": true,
            "paging": true,
            "pageLength": 80,
            
        } );

        ///////////////////////////////////
        /////////////////////////////////////////////////
        function lrCheck(that) {
            if (that.value == "Successful") {
                document.getElementById("lrid").style.display = "block";
        
            } else{
                document.getElementById("lrid").style.display = "none";         
            }
        }

        $(".bulk_loder").on("submit", function(){
    // alert('hello');
    $("#pageloader").fadeIn();
  });//submit
            </script>
            <!-- END PAGE LEVEL SCRIPTS -->  