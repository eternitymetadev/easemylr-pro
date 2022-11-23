jQuery(document).ready(function () {
    /* check box checked create/update user permission page  */
    jQuery(document).on("click", "#ckbCheckAll", function () {
        if (this.checked) {
            jQuery("#dropdownMenuButton").prop("disabled", false);
            jQuery(".chkBoxClass").each(function () {
                this.checked = true;
            });
        } else {
            jQuery(".chkBoxClass").each(function () {
                this.checked = false;
            });
            jQuery("#dropdownMenuButton").prop("disabled", true);
        }
    });

    jQuery(document).on("click", ".chkBoxClass", function () {
        if ($(".chkBoxClass:checked").length == $(".chkBoxClass").length) {
            $("#ckbCheckAll").prop("checked", true);
            jQuery("#dropdownMenuButton").prop("disabled", false);
        } else {
            var checklength = $(".chkBoxClass:checked").length;
            if (checklength < 1) {
                jQuery("#dropdownMenuButton").prop("disabled", true);
            } else {
                jQuery("#dropdownMenuButton").prop("disabled", false);
            }
            $("#ckbCheckAll").prop("checked", false);
        }
    });
    /*===== End check box checked create/update user permission page =====*/

    /// search by assign user

    jQuery('#searchvehicle').SumoSelect({
        search: true,
        // selectAll: true,
        okCancelInMulti: true,
        triggerChangeCombined: false
    });

    jQuery("#searchvehicle ~ .optWrapper .MultiControls .btnOk").click( function () {
        var selectedvehicles = jQuery('#searchvehicle').val();
        //var search =  jQuery('#search').val();
        var url =  jQuery(this).val();
        jQuery.ajax({
            type      : 'get',
            url       : url,
            data      : {searchvehicle:selectedvehicles},
            headers   : {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType  : 'json',
            success:function(response){
              if(response.html){
                jQuery('.main-table').html(response.html);
                jQuery("#search-paymentvehicle").modal("hide");
                // jQuery('#searchvehicle').multiselect( 'reset');
                jQuery('.assignedtoarray').val(selectedvehicles);
              }
            }
        });
        return false;
    });

    /*===== For create/update vehicle page =====*/
    $(document).on("keyup", "#regn_no", function () {
        var regn_no = $(this).val().toUpperCase();
        var regn_no = regn_no.replace(/[^A-Z0-9]/g, "");
        $(this).val(regn_no);
    });

    /*===== Delete Branch =====*/
    jQuery(document).on("click", ".delete_branch", function () {
        jQuery("#deletebranch").modal("show");
        var branchid = jQuery(this).attr("data-id");
        var url = jQuery(this).attr("data-action");
        jQuery(document)
            .off("click", ".deletebranchconfirm")
            .on("click", ".deletebranchconfirm", function () {
                jQuery.ajax({
                    type: "post",
                    url: url,
                    data: { branchid: branchid },
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                    dataType: "JSON",
                    success: function (response) {
                        if (response.success) {
                            jQuery("#deletebranch").modal("hide");
                            location.reload();
                        }
                    },
                });
            });
    });
    /*===== End delete Branch =====*/

    /*===== delete Consigner =====*/
    jQuery(document).on("click", ".delete_consigner", function () {
        jQuery("#deleteconsigner").modal("show");
        var consignerid = jQuery(this).attr("data-id");
        var url = jQuery(this).attr("data-action");
        jQuery(document)
            .off("click", ".deleteconsignerconfirm")
            .on("click", ".deleteconsignerconfirm", function () {
                jQuery.ajax({
                    type: "post",
                    url: url,
                    data: { consignerid: consignerid },
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                    dataType: "JSON",
                    success: function (data) {
                        if (data) {
                            jQuery("#deleteconsigner").modal("hide");
                            location.reload();
                        }
                    },
                });
            });
    });
    /*===== End delete Consigner =====*/

    /*===== delete Consignee =====*/
    jQuery(document).on("click", ".delete_consignee", function () {
        jQuery("#deleteconsignee").modal("show");
        var consigneeid = jQuery(this).attr("data-id");
        var url = jQuery(this).attr("data-action");
        jQuery(document)
            .off("click", ".deleteconsigneeconfirm")
            .on("click", ".deleteconsigneeconfirm", function () {
                jQuery.ajax({
                    type: "post",
                    url: url,
                    data: { consigneeid: consigneeid },
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                    dataType: "JSON",
                    success: function (data) {
                        if (data) {
                            jQuery("#deleteconsignee").modal("hide");
                            location.reload();
                        }
                    },
                });
            });
    });
    /*===== End delete Consignee =====*/

    /*===== delete Broker =====*/
    jQuery(document).on("click", ".delete_broker", function () {
        jQuery("#deletebroker").modal("show");
        var brokerid = jQuery(this).attr("data-id");
        var url = jQuery(this).attr("data-action");
        jQuery(document)
            .off("click", ".deletebrokerconfirm")
            .on("click", ".deletebrokerconfirm", function () {
                jQuery.ajax({
                    type: "post",
                    url: url,
                    data: { brokerid: brokerid },
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                    dataType: "JSON",
                    success: function (data) {
                        if (data) {
                            jQuery("#brokertable").load(" #brokertable");
                            jQuery("#deletebroker").modal("hide");
                        }
                    },
                });
            });
    });
    /*===== End delete Broker =====*/

    /*===== delete Driver =====*/
    jQuery(document).on("click", ".delete_driver", function () {
        jQuery("#deletedriver").modal("show");
        var driverid = jQuery(this).attr("data-id");
        var url = jQuery(this).attr("data-action");
        jQuery(document)
            .off("click", ".deletedriverconfirm")
            .on("click", ".deletedriverconfirm", function () {
                jQuery.ajax({
                    type: "post",
                    url: url,
                    data: { driverid: driverid },
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                    dataType: "JSON",
                    success: function (data) {
                        if (data) {
                            jQuery("#deletedriver").modal("hide");
                            location.reload();
                        }
                    },
                });
            });
    });
    /*===== End delete Driver =====*/

    // branch image add more
    $(".add_more_images").click(function () {
        var c = $(".images").length;
        c = c + 1;
        var rows = "";
        if (x < max_fields) {
            //max input box allowed
            x++; //text box increment

            rows += '<div class="images mt-3 col-md-2"><div class="row">';
            rows += '<div class="col-md-2">';
            rows +=
                '<input type="file" data-id="' +
                c +
                '" name="files[]" class="first"/>';
            rows +=
                '<p style="display:none;color:red" class="gif-errormsg' +
                c +
                '">Invalid image format</p>';
            rows += "</div>";
            rows +=
                '<a href="javascript:void(0)" class="btn-danger remove_field" style="margin: 5px 0 0 160px">';
            rows += '<i class="ml-2 fa fa-trash"></a>';
            rows += "</div></div>";

            $(".branch-image").append(rows);
        } else {
            $("#error-msg").css("display", "block");
            // $(".add_more_images").css("display", "none");
            $(".add_more_images").attr("disabled", true);
        }
        var html = $("#branch-upload").html();
        $(".after-add-more").after(html);
        $(".change").append(
            "<label for=''>&nbsp;</label><br/><a class='btn btn-danger remove'>- Remove</a>"
        );
    });

    $('input[type="file"]').change(function (event) {
        var _size = this.files[0].size;
        var exactSize = Math.round(_size / (1024 * 1024));
        //console.log('FILE SIZE = ',exactSize);
        if (exactSize >= "5") {
            $("#size-error").show();
        } else {
            $("#size-error").hide();
        }
    });

    // Delete branch Image from updatebranch view //
    $(document).on("click", ".deletebranchimg", function () {
        let id = $(this).attr("data-id");
        $("#deletebranchimgpop").modal("show");
        jQuery(".deletebranchimgdata").attr("data-id", id);
    });

    // Delete branch Image Method //
    $("body").on("click", ".deletebranchimgdata", function () {
        let id = jQuery(this).attr("data-id");
        var url = jQuery(this).attr("data-action");

        jQuery.ajax({
            type: "post",
            data: { branchimgid: id },
            url: url,
            dataType: "JSON",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (data) {
                if (data) {
                    jQuery("#deletebranchimgpop").modal("hide");
                    location.reload();
                }
            },
        });
    });

    // Delete driver Image from updatedriver view //
    $(document).on("click", ".deletelicenseimg", function () {
        let id = $(this).attr("data-id");
        $("#deletedriverlicenseimgpop").modal("show");
        jQuery(".deletedriverlicenseimgdata").attr("data-id", id);
    });

    // Delete driver Image Method //
    $("body").on("click", ".deletedriverlicenseimgdata", function () {
        let id = jQuery(this).attr("data-id");
        var url = jQuery(this).attr("data-action");

        jQuery.ajax({
            type: "post",
            data: { licenseimgid: id },
            url: url,
            dataType: "JSON",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (data) {
                if (data) {
                    jQuery("#deletedriverlicenseimgpop").modal("hide");
                    location.reload();
                }
            },
        });
    });

    // Delete vehicle RC Image from update vehicle view //
    $(document).on("click", ".deletercimg", function () {
        let id = $(this).attr("data-id");
        $("#deletevehiclercimgpop").modal("show");
        jQuery(".deletevehiclercimgdata").attr("data-id", id);
    });

    // Delete vehicle RC Image Method //
    $("body").on("click", ".deletevehiclercimgdata", function () {
        let id = jQuery(this).attr("data-id");
        var url = jQuery(this).attr("data-action");

        jQuery.ajax({
            type: "post",
            data: { rcimgid: id },
            url: url,
            dataType: "JSON",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (data) {
                if (data) {
                    jQuery("#deletevehiclercimgpop").modal("hide");
                    location.reload();
                }
            },
        });
    });

    /*===== delete User =====*/
    jQuery(document).on("click", ".delete_user", function () {
        jQuery("#deleteuser").modal("show");
        var userid = jQuery(this).attr("data-id");
        var url = jQuery(this).attr("data-action");
        jQuery(document)
            .off("click", ".deleteuserconfirm")
            .on("click", ".deleteuserconfirm", function () {
                jQuery.ajax({
                    type: "post",
                    url: url,
                    data: { userid: userid },
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                    dataType: "JSON",
                    success: function (data) {
                        if (data) {
                            jQuery("#deleteuser").modal("hide");
                            location.reload();
                        }
                    },
                });
            });
    });
    /*===== End delete User =====*/

    /*===== delete Regional client =====*/
    jQuery(document).on("click", ".delete_client", function () {
        jQuery("#deleteclient").modal("show");
        var regclient_id = jQuery(this).attr("data-id");
        var url = jQuery(this).attr("data-action");
        jQuery(document)
            .off("click", ".deleteclientconfirm")
            .on("click", ".deleteclientconfirm", function () {
                jQuery.ajax({
                    type: "post",
                    url: url,
                    data: { regclient_id: regclient_id },
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                    dataType: "JSON",
                    success: function (data) {
                        if (data) {
                            jQuery("#deleteclient").modal("hide");
                            location.reload();
                        }
                    },
                });
            });
    });

    /*===== delete location =====*/
    jQuery(document).on("click", ".delete_location", function () {
        jQuery("#deletelocation").modal("show");
        var location_id = jQuery(this).attr("data-id");
        var url = jQuery(this).attr("data-action");
        jQuery(document)
            .off("click", ".deletelocationconfirm")
            .on("click", ".deletelocationconfirm", function () {
                jQuery.ajax({
                    type: "post",
                    url: url,
                    data: { location_id: location_id },
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                    dataType: "JSON",
                    success: function (data) {
                        if (data) {
                            jQuery("#deletelocation").modal("hide");
                            location.reload();
                        }
                    },
                });
            });
    });

    /*===== delete vehicle =====*/
    jQuery(document).on("click", ".delete_vehicle", function () {
        jQuery("#deletevehicle").modal("show");
        var vehicleid = jQuery(this).attr("data-id");
        var url = jQuery(this).attr("data-action");
        jQuery(document)
            .off("click", ".deletevehicleconfirm")
            .on("click", ".deletevehicleconfirm", function () {
                jQuery.ajax({
                    type: "post",
                    url: url,
                    data: { vehicleid: vehicleid },
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                    dataType: "JSON",
                    success: function (data) {
                        if (data) {
                            jQuery("#deletevehicle").modal("hide");
                            location.reload();
                        }
                    },
                });
            });
    });
    /*===== End delete vehicle =====*/

    /*===== get driver detail on create vehicle page =====*/
    // not use yet this function
    $("#vehicle_driver").change(function (e) {
        $("#driver_dl_no").val("");
        $("#driver_mobile_no").val("");
        let driver_id = $(this).val();
        getDrivers(driver_id);
    });

    function getDrivers(driver_id) {
        $.ajax({
            type: "get",
            url: APP_URL + "/get_drivers",
            data: { driver_id: driver_id },
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            dataType: "json",
            success: function (res) {
                if (res.data) {
                    $("#driver_dl_no").val(res.data.license_number);
                    $("#driver_mobile_no").val(res.data.phone);
                }
            },
        });
    }
    /*===== End get driver detail on create vehicle page =====*/

    /*======get consigner on regional client =====*/
    $("#select_regclient").change(function (e) {
        $("#items_table").find("tr:gt(1)").remove();
        var regclient_id = $(this).val();
        $("#select_consigner").empty();
        $("#select_consignee").empty();
        $("#select_ship_to").empty();
        $.ajax({
            url: "/get-consigner-regional",
            type: "get",
            cache: false,
            data: { regclient_id: regclient_id },
            dataType: "json",
            headers: {
                "X-CSRF-TOKEN": jQuery('meta[name="_token"]').attr("content"),
            },
            beforeSend: function () {
                $("#select_consigner").empty();
            },
            success: function (res) {
                $("#consigner_address").empty();
                $("#consignee_address").empty();
                $("#ship_to_address").empty();

                $("#select_consigner").append(
                    '<option value="">select consigner</option>'
                );
                $("#select_consignee").append(
                    '<option value="">Select Consignee</option>'
                );
                $("#select_ship_to").append(
                    '<option value="">Select Ship To</option>'
                );

                $.each(res.data, function (index, value) {
                    $("#select_consigner").append(
                        '<option value="' +
                        value.id +
                        '">' +
                        value.nick_name +
                        "</option>"
                    );
                });

                if (res.data_regclient == null) {
                    var multiple_invoice = "";
                } else {
                    if (
                        res.data_regclient.is_multiple_invoice == null ||
                        res.data_regclient.is_multiple_invoice == ""
                    ) {
                        var multiple_invoice = "";
                    } else {
                        var multiple_invoice =
                            res.data_regclient.is_multiple_invoice;
                    }
                }

                if (multiple_invoice == 1) {
                    $(".insert-more").attr("disabled", false);
                } else {
                    $(".insert-more").attr("disabled", true);
                }
            },
        });
    });

    /*===== get consigner address on create consignment page =====*/
    $("#select_consigner").change(function (e) {
        $("#select_consignee").empty();
        $("#select_ship_to").empty();
        let consigner_id = $(this).val();
        getConsigners(consigner_id);
    });

    function getConsigners(consigner_id) {
        $.ajax({
            type: "get",
            url: APP_URL + "/get_consigners",
            data: { consigner_id: consigner_id },
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            dataType: "json",
            success: function (res) {
                $("#consigner_address").empty();
                $("#consignee_address").empty();
                $("#ship_to_address").empty();

                $("#select_consignee").append(
                    '<option value="">Select Consignee</option>'
                );
                $("#select_ship_to").append(
                    '<option value="">Select Ship To</option>'
                );
                $.each(res.consignee, function (key, value) {
                    $("#select_consignee, #select_ship_to").append(
                        '<option value="' +
                        value.id +
                        '">' +
                        value.nick_name +
                        "</option>"
                    );
                });
                if (res.data) {
                    console.log(res.data);
                    $("#regclient_id").val(res.data.regionalclient_id);

                    if (res.data.address_line1 == null) {
                        var address_line1 = "";
                    } else {
                        var address_line1 = res.data.address_line1 + "<br>";
                    }
                    if (res.data.address_line2 == null) {
                        var address_line2 = "";
                    } else {
                        var address_line2 = res.data.address_line2 + "<br>";
                    }
                    if (res.data.address_line3 == null) {
                        var address_line3 = "";
                    } else {
                        var address_line3 = res.data.address_line3 + "<br>";
                    }
                    if (res.data.address_line4 == null) {
                        var address_line4 = "";
                    } else {
                        var address_line4 = res.data.address_line4 + "<br>";
                    }
                    if (res.data.gst_number == null) {
                        var gst_number = "";
                    } else {
                        var gst_number =
                            "GST No: " + res.data.gst_number + "<br>";
                    }
                    if (res.data.phone == null) {
                        var phone = "";
                    } else {
                        var phone = "Phone: " + res.data.phone;
                    }

                    $("#consigner_address").append(
                        address_line1 +
                        " " +
                        address_line2 +
                        "" +
                        address_line3 +
                        " " +
                        address_line4 +
                        " " +
                        gst_number +
                        " " +
                        phone +
                        ""
                    );

                    $("#dispatch").val(res.data.city);
                }
            },
        });
    }

    /*===== get consignee address on create consignment page =====*/
    $("#select_consignee").change(function (e) {
        $("#consignee_address").empty();
        let consignee_id = $(this).val();
        getConsignees(consignee_id);
    });

    function getConsignees(consignee_id) {
        $.ajax({
            type: "get",
            url: APP_URL + "/get_consignees",
            data: { consignee_id: consignee_id },
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            dataType: "json",
            success: function (res) {
                // $('#consignee_address').empty();
                if (res.data) {
                    if (res.data.address_line1 == null) {
                        var address_line1 = "";
                    } else {
                        var address_line1 = res.data.address_line1 + "<br>";
                    }
                    if (res.data.address_line2 == null) {
                        var address_line2 = "";
                    } else {
                        var address_line2 = res.data.address_line2 + "<br>";
                    }
                    if (res.data.address_line3 == null) {
                        var address_line3 = "";
                    } else {
                        var address_line3 = res.data.address_line3 + "<br>";
                    }
                    if (res.data.address_line4 == null) {
                        var address_line4 = "";
                    } else {
                        var address_line4 = res.data.address_line4 + "<br>";
                    }
                    if (res.data.gst_number == null) {
                        var gst_number = "";
                    } else {
                        var gst_number =
                            "GST No: " + res.data.gst_number + "<br>";
                    }
                    if (res.data.phone == null) {
                        var phone = "";
                    } else {
                        var phone = "Phone: " + res.data.phone;
                    }

                    $("#consignee_address").append(
                        address_line1 +
                        " " +
                        address_line2 +
                        "" +
                        address_line3 +
                        " " +
                        address_line4 +
                        " " +
                        gst_number +
                        " " +
                        phone +
                        ""
                    );
                }
            },
        });
    }

    $("#select_ship_to").change(function (e) {
        $("#ship_to_address").empty();
        let consignee_id = $(this).val();
        getShipto(consignee_id);
    });

    function getShipto(consignee_id) {
        $.ajax({
            type: "get",
            url: APP_URL + "/get_consignees",
            data: { consignee_id: consignee_id },
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            dataType: "json",
            success: function (res) {
                if (res.data) {
                    if (res.data.address_line1 == null) {
                        var address_line1 = "";
                    } else {
                        var address_line1 = res.data.address_line1 + "<br>";
                    }
                    if (res.data.address_line2 == null) {
                        var address_line2 = "";
                    } else {
                        var address_line2 = res.data.address_line2 + "<br>";
                    }
                    if (res.data.address_line3 == null) {
                        var address_line3 = "";
                    } else {
                        var address_line3 = res.data.address_line3 + "<br>";
                    }
                    if (res.data.address_line4 == null) {
                        var address_line4 = "";
                    } else {
                        var address_line4 = res.data.address_line4 + "<br>";
                    }
                    if (res.data.gst_number == null) {
                        var gst_number = "";
                    } else {
                        var gst_number =
                            "GST No: " + res.data.gst_number + "<br>";
                    }
                    if (res.data.phone == null) {
                        var phone = "";
                    } else {
                        var phone = "Phone: " + res.data.phone;
                    }

                    $("#ship_to_address").append(
                        address_line1 +
                        " " +
                        address_line2 +
                        "" +
                        address_line3 +
                        " " +
                        address_line4 +
                        " " +
                        gst_number +
                        " " +
                        phone +
                        ""
                    );
                }
            },
        });
    }

    //get location on create consigner page on client change
    $("#regionalclient_id").change(function () {
        let location_id = $(this).find(":selected").attr("data-locationid");
        $.ajax({
            type: "get",
            url: APP_URL + "/get_locations",
            data: { location_id: location_id },
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            dataType: "json",
            success: function (res) {
                console.log(res);
                if (res.data) {
                    $("#location_id").val(res.data.id);
                }
            },
        });
    });

    $("#selwarehouse").on("change", function () {
        $("#consignment_no").val("");
        var con_no = $("#consignment_no").val();
        var current_series = $("#selwarehouse option:selected").val();
        $("#consignment_no").val(current_series + con_no);
    });

    // Add Another Row
    $(document).on("click", ".insert-more", function () {
        $("#items_table").each(function () {
            var item_no = $("tr", this).length;
            if (item_no <= 6) {
                var tds = "<tr>";

                tds +=
                    ' <td><input type="text" class="form-control form-small orderid" name="data[' +
                    item_no +
                    '][order_id]"></td>';
                tds +=
                    '<td><input type="text" class="form-control form-small invc_no" name="data[' +
                    item_no +
                    '][invoice_no]" id="'+item_no+'" value=""></td>';
                tds +=
                    '<td><input type="date" class="form-control form-small invc_date" name="data[' +
                    item_no +
                    '][invoice_date]"></td>';
                tds +=
                    '<td><input type="number" class="form-control form-small invc_amt" name="data[' +
                    item_no +
                    '][invoice_amount]"></td>';
                tds +=
                    '<td><input type="number" class="form-control form-small ew_bill" name="data[' +
                    item_no +
                    '][e_way_bill]"></td>';
                tds +=
                    '<td><input type="date" class="form-control form-small ewb_date" name="data[' +
                    item_no +
                    '][e_way_bill_date]"></td>';
                tds +=
                    '<td><input type="number" class="form-control form-small qnt" name="data[' +
                    item_no +
                    '][quantity]"></td>';
                tds +=
                    '<td><input type="number" class="form-control form-small net" name="data[' +
                    item_no +
                    '][weight]"></td>';
                tds +=
                    '<td><input type="number" class="form-control form-small gross" name="data[' +
                    item_no +
                    '][gross_weight]"></td>';
                tds +=
                    '<td><button type="button" class="btn btn-default btn-rounded insert-more"> + </button><button type="button" class="btn btn-default btn-rounded remove-row"> - </button></td>';
                tds += "</tr>";
            }

            if ($("tbody", this).length > 0) {
                $("tbody", this).append(tds);
            } else {
                $(this).append(tds);
            }
        });
    });

    //Remove the current row
    $(document).on("click", ".remove-row", function () {
        var current_val = $(this).parent().siblings(":first").text();
        $(this).closest("tr").remove();
        reassign_ids();
        calculate_totals();
    });

    //Reassign the Ids of the row
    function reassign_ids() {
        var i = 0;
        var t = document.getElementById("items_table");
        $("#items_table tr").each(function () {
            var srno = $(t.rows[i].cells[0]).text();
            if (srno == "#" || parseInt(srno) == 1) {
                i++;
            }
            if (parseInt(srno) >= 2) {
                $(t.rows[i].cells[0]).html(i);
                $(t.rows[i])
                    .closest("tr")
                    .find(".sel1")
                    .attr("name", "data[" + i + "][description]");
                $(t.rows[i])
                    .closest("tr")
                    .find(".mode")
                    .attr("name", "data[" + i + "][packing_type]");
                $(t.rows[i])
                    .closest("tr")
                    .find(".qnt")
                    .attr("name", "data[" + i + "][quantity]");
                $(t.rows[i])
                    .closest("tr")
                    .find(".net")
                    .attr("name", "data[" + i + "][weight]");
                $(t.rows[i])
                    .closest("tr")
                    .find(".gross")
                    .attr("name", "data[" + i + "][gross_weight]");
                // $(t.rows[i]).closest('tr').find('.frei').attr('name', 'data['+i+'][freight]');
                $(t.rows[i])
                    .closest("tr")
                    .find(".term")
                    .attr("name", "data[" + i + "][payment_type]");

                $(t.rows[i])
                    .closest("tr")
                    .find(".orderid")
                    .attr("name", "data[" + i + "][order_id]");
                $(t.rows[i])
                    .closest("tr")
                    .find(".invc_no")
                    .attr("name", "data[" + i + "][invoice_no]");
                $(t.rows[i])
                    .closest("tr")
                    .find(".invc_date")
                    .attr("name", "data[" + i + "][invoice_date]");
                $(t.rows[i])
                    .closest("tr")
                    .find(".invc_amt")
                    .attr("name", "data[" + i + "][invoice_amount]");
                $(t.rows[i])
                    .closest("tr")
                    .find(".ew_bill")
                    .attr("name", "data[" + i + "][e_way_bill]");
                $(t.rows[i])
                    .closest("tr")
                    .find(".ewb_date")
                    .attr("name", "data[" + i + "][e_way_bill_date]");


                i++;
            }
        });
    }
    //Call the calculate total function
    $(document).on("keyup", ".qnt, .net, .gross, .frei", function () {
        calculate_totals();
    });

    // Calculate all totals
    function calculate_totals() {
        var rowCount = $("#items_table tr").length;
        var total_quantity = 0;
        var total_net_weight = 0;
        var total_gross_weight = 0;
        var total_freight = 0;

        for (var w = 1; w < rowCount; w++) {
            var qty = !$('[name="data[' + w + '][quantity]"]').val()
                ? 0
                : parseInt($('[name="data[' + w + '][quantity]"]').val());
            var ntweight = !$('[name="data[' + w + '][weight]"]').val()
                ? 0
                : parseInt($('[name="data[' + w + '][weight]"]').val());
            var grweight = !$('[name="data[' + w + '][gross_weight]"]').val()
                ? 0
                : parseInt($('[name="data[' + w + '][gross_weight]"]').val());

            total_quantity += qty;
            total_net_weight += ntweight;
            total_gross_weight += grweight;
        }
        $("#tot_qty").html(total_quantity);
        $("#tot_nt_wt").html(total_net_weight);
        $("#tot_gt_wt").html(total_gross_weight);

        $("#total_quantity").val(total_quantity);
        $("#total_weight").val(total_net_weight);
        $("#total_gross_weight").val(total_gross_weight);
    }

    /*===== get location on edit click =====*/
    jQuery(document).on("click", ".editlocation", function () {
        var locationid = jQuery(this).attr("data-id");
        jQuery(".locationid").val(locationid);
        var action = jQuery(this).attr("data-action");
        jQuery.ajax({
            type: "post",
            url: action,
            data: { locationid: locationid },
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            dataType: "json",
            success: function (response) {
                jQuery("#nameup").val(response.newcata.name);
                jQuery("#nick_nameup").val(response.newcata.nick_name);
                jQuery("#team_idup").val(response.newcata.team_id);
                jQuery("#consignment_noup").val(
                    response.newcata.consignment_no
                );
                jQuery("#emailup").val(response.newcata.email);
                jQuery("#phoneup").val(response.newcata.phone);
                if (response.newcata.with_vehicle_no == 1) {
                    jQuery(".radio_vehicleno_yes").attr("checked", true);
                    jQuery(".radio_vehicleno_no").attr("checked", false);
                } else {
                    jQuery(".radio_vehicleno_no").attr("checked", true);
                    jQuery(".radio_vehicleno_yes").attr("checked", false);
                }
            },
        });
    });

    $("#commonconfirm").on("hidden.bs.modal", function (e) {
        $(this)
            .find("input,textarea,select")
            .val("")
            .end()
            .find("input[type=checkbox], input[type=radio]")
            .prop("checked", "")
            .end();
    });

    // consignment status change onchange
    jQuery(document).on(
        "click",
        ".activestatus,.inactivestatus",
        function (event) {
            event.stopPropagation();

            let user_id = jQuery(this).attr("data-id");

            var dataaction = jQuery(this).attr("data-action");
            var datastatus = jQuery(this).attr("data-status");
            var updatestatus = "updatestatus";

            if (datastatus == 0) {
                statustext = "disable";
            } else {
                statustext = "enable";
            }
            jQuery("#commonconfirm").modal("show");
            jQuery(".commonconfirmclick").one("click", function () {
                var reason_to_cancel = jQuery("#reason_to_cancel").val();

                var data = {
                    id: user_id,
                    status: datastatus,
                    updatestatus: updatestatus,
                    reason_to_cancel: reason_to_cancel,
                };

                jQuery.ajax({
                    url: "consignments",
                    type: "get",
                    cache: false,
                    data: data,
                    dataType: "json",
                    headers: {
                        "X-CSRF-TOKEN": jQuery('meta[name="_token"]').attr(
                            "content"
                        ),
                    },
                    processData: true,
                    beforeSend: function () {
                        // jQuery("input[type=submit]").attr("disabled", "disabled");
                    },
                    complete: function () {
                        //jQuery("#loader-section").css('display','none');
                    },

                    success: function (response) {
                        if (response.success) {
                            jQuery("#commonconfirm").modal("hide");
                            if (response.page == "consignment-updateupdate") {
                                setTimeout(() => {
                                    window.location.href =
                                        response.redirect_url;
                                }, 10);
                            }
                        }
                    },
                });
            });
        }
    );
    //////////////////////// Active Cancel Status in drs/////////////////////////////////
    // consignment status change onchange
    jQuery(document).on("click", ".active_drs", function (event) {
        event.stopPropagation();
        let drs_id = jQuery(this).attr("drs-no");
        var updatestatus = "updatestatus";

        jQuery("#drs_commonconfirm").modal("show");
        jQuery(".commonconfirmclick").one("click", function () {
            var data = { drs_id: drs_id, updatestatus: updatestatus };

            jQuery.ajax({
                url: "drs-status",
                type: "get",
                cache: false,
                data: data,
                dataType: "json",
                headers: {
                    "X-CSRF-TOKEN": jQuery('meta[name="_token"]').attr(
                        "content"
                    ),
                },
                processData: true,
                beforeSend: function () {
                    // jQuery("input[type=submit]").attr("disabled", "disabled");
                },
                complete: function () {
                    //jQuery("#loader-section").css('display','none');
                },

                success: function (response) {
                    if (response.success) {
                        jQuery("#commonconfirm").modal("hide");
                        if (response.page == "dsr-cancel-update") {
                            setTimeout(() => {
                                window.location.href = response.redirect_url;
                            }, 10);
                        }
                    }
                },
            });
        });
    });
    ///////////////////////get data successful model++++++++++++++++++++++++++++

    jQuery(document).on("click", ".drs_cancel", function (event) {
        event.stopPropagation();

        let drs_no = jQuery(this).attr("drs-no");
        var data = { drs_no: drs_no };
        var base_url = window.location.origin;
        jQuery.ajax({
            url: "get-delivery-datamodel",
            type: "get",
            cache: false,
            data: data,
            dataType: "json",
            headers: {
                "X-CSRF-TOKEN": jQuery('meta[name="_token"]').attr("content"),
            },
            processData: true,
            beforeSend: function () {
                $("#get-delvery-date").dataTable().fnClearTable();
                $("#get-delvery-date").dataTable().fnDestroy();
            },
            complete: function () { },

            success: function (data) {

                var consignmentID = [];
                var i = 1;
                $.each(data.fetch, function (index, value) {
                    var drs_sign = value.signed_drs;
                    var storage_img = base_url + "/drs/Image/" + drs_sign;
                    if (value.signed_drs == null) {
                        var field =
                            "<input type='file' name='data[" +
                            i +
                            "][img]' data-id='" +
                            value.consignment_no +
                            "' placeholder='Choose image' class='drs_image value='" +
                            value.signed_drs +
                            "'>";
                    } else {
                        var field =
                            "<a href='" +
                            storage_img +
                            "' target='_blank' class='btn btn-warning'>view</a>";
                    }
                    // delivery date//
                    if (value.dd == null) {
                        var deliverydate =
                            "<input type='date' name='data[" +
                            i +
                            "][delivery_date]' data-id=" +
                            value.consignment_no +
                            " class='delivery_d' value='" +
                            value.dd +
                            "'>";
                    } else {
                        var deliverydate = value.dd;
                    }

                    if (value.edd == null || value.edd == "") {
                        var edd_date = "-";
                    } else {
                        var edd_date = value.edd;
                    }

                    var alldata = value;
                    consignmentID.push(alldata.consignment_no);

                    $("#get-delvery-date tbody").append(
                        "<tr><td><input type='hidden' name='data[" +
                        i +
                        "][lrno]' class='delivery_d' value='" +
                        value.consignment_no +
                        "'>" +
                        value.consignment_no +
                        "</td><td><input type='hidden' name='data[" +
                        i +
                        "][lr_date]' class='c_date' value='" +
                        value.consignment_date +
                        "'>" +
                        value.consignee_id +
                        " </td><td>" +
                        value.city +
                        "</td><td>" +
                        edd_date +
                        "</td><td>" +
                        deliverydate +
                        "</td><td>" +
                        field +
                        "</td><td><button type='button'  data-id=" +
                        value.consignment_no +
                        " class='btn btn-primary remover_lr'>remove</button></td></tr>"
                    );
                    i++;
                });
            },
        });
    });
    ///// Drs Cncel status update ////////
    jQuery(document).on("click", ".drs_cancel", function (event) {
        event.stopPropagation();

        let drs_no = jQuery(this).attr("drs-no");
        var dataaction = jQuery(this).attr("data-action");
        var updatestatus = "updatestatus";

        jQuery("#commonconfirm").modal("show");
        jQuery(".commonconfirmclick").one("click", function () {
            var status_value = jQuery("#drs_status").val();

            if (status_value == "Successful") {
                var consignmentID = [];
                $('input[name="delivery_date[]"]').each(function () {
                    if (this.value == "") {
                        alert("Please filled all delevery date");
                        exit;
                    }
                    consignmentID.push(this.value);
                });
            }
            var drs_status = jQuery("#drs_status").val();
            var data = {
                drs_no: drs_no,
                drs_status: drs_status,
                updatestatus: updatestatus,
            };

            jQuery.ajax({
                url: dataaction,
                type: "get",
                cache: false,
                data: data,
                dataType: "json",
                headers: {
                    "X-CSRF-TOKEN": jQuery('meta[name="_token"]').attr(
                        "content"
                    ),
                },
                processData: true,
                beforeSend: function () {
                    // jQuery("input[type=submit]").attr("disabled", "disabled");
                },
                complete: function () {
                    //jQuery("#loader-section").css('display','none');
                },

                success: function (response) {
                    if (response.success) {
                        jQuery("#commonconfirm").modal("hide");
                        if (response.page == "dsr-cancel-update") {
                            setTimeout(() => {
                                window.location.href = response.redirect_url;
                            }, 10);
                        }
                    }
                },
            });
        });


    });
    //    Manual LR status update+++++++++++++++++++++++++++++++++++++
    jQuery(document).on("click", ".manual_updateLR", function (event) {
        event.stopPropagation();

        let lr_no = jQuery(this).attr("lr-no");
        var updatestatus = "updatestatus";
        jQuery("#manualLR").modal("show");
        $(".commonconfirmclick")
            .unbind()
            .click(function () {
                var lr_status = jQuery("#lr_status").val();
                var data = {
                    lr_no: lr_no,
                    lr_status: lr_status,
                    updatestatus: updatestatus,
                };

                jQuery.ajax({
                    url: "update-lrstatus",
                    type: "get",
                    cache: false,
                    data: data,
                    dataType: "json",
                    headers: {
                        "X-CSRF-TOKEN": jQuery('meta[name="_token"]').attr(
                            "content"
                        ),
                    },
                    processData: true,
                    beforeSend: function () {
                        // jQuery("input[type=submit]").attr("disabled", "disabled");
                    },
                    complete: function () {
                        //jQuery("#loader-section").css('display','none');
                    },

                    success: function (response) {
                        if (response.success) {
                            jQuery("#commonconfirm").modal("hide");
                            if (response.page == "dsr-cancel-update") {
                                setTimeout(() => {
                                    window.location.href =
                                        response.redirect_url;
                                }, 10);
                            }
                        }
                    },
                });
            });
    });
    ///////////////////////get deleverydata LR successful model++++++++++++++++++++++++++++

    jQuery(document).on("click", ".manual_updateLR", function (event) {
        event.stopPropagation();

        let lr_no = jQuery(this).attr("lr-no");
        var data = { lr_no: lr_no };
        var base_url = window.location.origin;
        jQuery.ajax({
            url: "get-delivery-dateLR",
            type: "get",
            cache: false,
            data: data,
            dataType: "json",
            headers: {
                "X-CSRF-TOKEN": jQuery('meta[name="_token"]').attr("content"),
            },
            processData: true,
            beforeSend: function () {
                $("#get-delvery-dateLR").dataTable().fnClearTable();
                $("#get-delvery-dateLR").dataTable().fnDestroy();
                $("#lr_status").empty();
            },
            complete: function () { },

            success: function (data) {
                var consignmentID = [];

                $.each(data.fetch, function (index, value) {
                   
                   var trail_history = jQuery.parseJSON(value.trail);

                   if(value.job_id != null){
                    var img_api = [];
                   
                    $.each(trail_history.task_history, function (index, history) {
                        if(history.type == "image_added"){
                            img_api.push(history.description)
                        }
                    });
                }
            //   console.log(img_api); return false;
                    var alldata = value;
                    consignmentID.push(alldata.consignment_no);
                    var drs_sign = value.signed_drs;
                    /////pod img
                    var storage_img = base_url + "/drs/Image/" + drs_sign;

                    if(value.job_id == null || value.job_id == ''){
                    if (value.signed_drs == null) {
                        if(data.role_id == 7){
                            var field = '-';
                        }else{
                        var field =
                            "<input type='file' name='img' data-id='" +
                            value.id +
                            "' placeholder='Choose image' class='drs_image'>";
                        }
                    } else {
                         var field = 
                        "<a href='" +
                        storage_img +
                        "' target='_blank' class='btn btn-warning'>view</a>";
                    }
                }else{
                    if(img_api == null || img_api == ''){
                        var field =
                        "<input type='file' name='img' data-id='" +
                        value.id +
                        "' placeholder='Choose image' class='drs_image'>";
                    }else{
                         var field1 = [];
                        var img_length = img_api.length;
                        var i = 0;
                        $.each(img_api, function (index, img) {
                            i++
                            img_group  = "<a href='"+img+
                             "' target='_blank' class='btn btn-warning mt-3'>Image "+i+"</a> "; 
                             field1.push(img_group);
                        });
                       var field = (field1.join(' '));
                    }
                }
                    // delivery date check
                    if (value.delivery_date == null) {
                        if(data.role_id == 7){
                            var deliverydat = '-';
                        }else{
                        var deliverydat =
                            "<input type='date' name='delivery_date[]' data-id=" +
                            value.id +
                            " class='delivery_d' value='" +
                            value.delivery_date +
                            "' Required>";
                        }
                    } else {
                        var deliverydat = value.delivery_date;
                    }
                    // save button check
                    if (
                        value.delivery_date != null &&
                        value.signed_drs != null
                    ) {
                        var buton = "Successful";
                    } else {
                        var buton =
                            "<button type='button'  data-id=" +
                            value.consignment_no +
                            " class='btn btn-primary onelrupdate'>Save</button>";
                    }

                     row =   "<tr><td>" +
                    value.id +
                    " <input type='hidden' name='delivery_date' value='"+value.consignment_date+"'</td><td>" +
                    value.consignee_nick +
                    "</td><td>" +
                    value.conee_city +
                    "</td><td>" +
                    deliverydat +
                    "</td><td>" +
                    field +
                    "</td>";
                    if(data.role_id != 7){
                    row += "<td>" + buton +"</td>";
                    }
                    row += "</tr>";

                    $("#get-delvery-dateLR tbody").append(row);

                });
            },
        });
    });

    //for setting branch address edit
    jQuery(document).on("click", ".editBranchadd", function () {
        jQuery(".submitBtn").css("display", "block");
        $("input").prop("disabled", false);
        $("#address").prop("disabled", false);
        jQuery(".editBranchadd").css("display", "none");
    });

    /* For create/update consigner/consignee page  */
    $(document).on("keyup", "#gst_number", function () {
        var gstno = $(this).val().toUpperCase();
        var gstno = gstno.replace(/[^A-Z0-9]/g, "");
        $(this).val(gstno);

        const gst_numberlen = gstno.length;
        if (gst_numberlen > 0) {
            $(".gstno_error").hide();
        } else {
            $(".gstno_error").show();
        }
    });

    $("#dealer_type").change(function (e) {
        e.preventDefault();
        var valueSelected = this.value;
        var gstno = $("#gst_number").val();
        if (valueSelected == 1 && gstno == "") {
            $("#gst_number").attr("disabled", false);
            $(".gstno_error").show();
            return false;
        } else {
            $("#gst_number").val("");
            $("#gst_number").attr("disabled", true);
            $(".gstno_error").hide();
        }
    });

    ////////////////////
    $("#vehicle_no").change(function (e) {
        e.preventDefault();
        var valueSelected = this.value;
        var edd = $("#edd").val();
        if (valueSelected != "" && edd != null) {
            $("#edd").attr("disabled", false);
            $(".edd_error").css("display", "block");
            return false;
        } else {
            $(".edd_error").css("display", "none");
        }
    });

    $(document).on("blur", "#edd", function () {
        var edd = $(this).val();

        const edd_len = edd.length;
        if (edd_len > 0) {
            $(".edd_error").css("display", "none");
        } else {
            $(".edd_error").css("display", "block");
        }
    });

    // for vehicle tonnage capacity calculation
    $("#gross_vehicle_weight").keyup(function () {
        var gross_vehicle_weight = $("#gross_vehicle_weight").val();
        if (gross_vehicle_weight != "") {
            $("#unladen_weight").prop("readonly", false);
        } else {
            $("#unladen_weight").prop("readonly", true);
        }
    });

    $("#unladen_weight, #gross_vehicle_weight").keyup(function () {
        var gross_vehicle_weight = $("#gross_vehicle_weight").val();
        if (gross_vehicle_weight != "") {
            $("#unladen_weight").prop("readonly", false);
        } else {
            $("#unladen_weight").prop("readonly", true);
        }
        var unladen_weight = $("#unladen_weight").val();
        var total_weight =
            parseInt(gross_vehicle_weight) - parseInt(unladen_weight);
        if (parseInt(gross_vehicle_weight) > parseInt(unladen_weight)) {
            $("#tonnage_capacity").val(total_weight);
        } else {
            $("#unladen_weight").val("");
            $("#tonnage_capacity").val("");
        }
    });

    //fetch address on postcode keyup
    $(document).on("keyup", "#postal_code", function () {
        var postcode = $(this).val();
        var postcode_len = postcode.length;
        if (postcode_len > 0) {
            $.ajax({
                url: "/get-address-by-postcode",
                type: "get",
                cache: false,
                data: { postcode: postcode },
                dataType: "json",
                headers: {
                    "X-CSRF-TOKEN": jQuery('meta[name="_token"]').attr(
                        "content"
                    ),
                },
                success: function (data) {
                    if (data.success) {
                        $("#city").val(data.data.city);
                        $("#district").val(data.zone.district);
                        $("#state").val(data.zone.state);

                        if (data.zone == null || data.zone == "") {
                            $("#zone_name").val("No Zone Assigned");
                            $("#zone_id").val("0");
                        } else {
                            $("#zone_name").val(data.zone.primary_zone);
                            $("#zone_id").val(data.zone.id);
                        }
                    } else {
                        $("#city").val("");
                        $("#district").val("");
                        $("#state").val("");
                        $("#zone_name").val("");
                        $("#zone_id").val("");
                    }
                },
            });
        } else {
            $("#city").val("");
            $("#state").val("");
            $("#district").val("");
            $("#zone").val("");
        }
    });
});
/* End document ready function */

function get_delivery_date() {
    $(".delivery_d").blur(function () {
        var consignment_id = $(this).attr("data-id");
        var delivery_date = $(this).val();

        var _token = $('input[name="_token"]').val();
        $.ajax({
            url: "update-delivery-date",
            method: "POST",
            data: {
                delivery_date: delivery_date,
                consignment_id: consignment_id,
                _token: _token,
            },
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            dataType: "json",
            success: function (result) { },
        });
    });
}
/*======upload drs delevery img============================== */
$(document).on("click", ".onelrupdate", function () {
    var lr_no = $(this).closest("tr").find("td").eq(0).text();
    var consignment_date = $(this).closest("tr").find("td:eq(0) input[type='hidden']").val();
    var ddd = $(this).closest("tr").find("td:eq(3) input[type='date']").val();
    
    if (ddd == undefined) { 
        var delivery_date = $(this).closest("tr").find("td").eq(3).text();
    } else {
        var delivery_date = $(this)
            .closest("tr")
            .find("td:eq(3) input[type='date']")
            .val();
    } 

    if (delivery_date == null || delivery_date == "") {
        alert("Please select a delivery date");
        return false;
    }

    var c_date = new Date(consignment_date); //Year, Month, Date    
    var d_date = new Date(ddd); //Year, Month, Date   
    if (c_date > d_date) {     
        swal("Error", "delivery date can't be less than lr date", "error");
        return false ; 
    }

    var files = $(this)
        .closest("tr")
        .find("td")
        .eq(4)
        .children(".drs_image")[0].files;

        // if (files.length == 0) {
        //     alert("Please choose a file");
        //     return false;
        // }

    var form_data = new FormData();
    if (files.length != 0) {
        var ext = files[0]["name"].split(".").pop().toLowerCase();
        if (jQuery.inArray(ext, ["png", "jpg", "jpeg", "pdf"]) == -1) {
            swal("error", "Invalid img file", "error");
            return false;
        }
    }

    form_data.append("file", files[0]);
    form_data.append("lr", lr_no);
    form_data.append("delivery_date", delivery_date);
    $.ajax({
        url: "upload-delivery-img",
        method: "POST",
        data: form_data,
        contentType: false,
        cache: false,
        processData: false,
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        beforeSend: function () { },
        success: function (data) {
            // alert(data.success);
            if (data.success == true) {
                swal("success", "Status Updated successfully", "success");
                location.reload();
            } else {
                swal("error", "Something went wrong", "error");
            }
        },
    });
});
//////////////////////////
$("#allsave").submit(function (e) {
    e.preventDefault();

    var formData = new FormData(this);

    $.ajax({
        url: "all-save-deliverydate",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        type: "POST",
        data: new FormData(this),
        processData: false,
        contentType: false,
        beforeSend: function () {
            $(".indicator-progress").show();
            $(".indicator-label").hide();
        },
        success: (data) => {
            $(".indicator-progress").hide();
            $(".indicator-label").show();
            if (data.success == true) {
                swal("success", "Status Updated successfully", "success");
                location.reload();
            }else if(data.error == 'date_less'){
                swal("error", data.messages, "error");
            } else {
                swal("error", data.messages, "error");
            }
        },
    });
});
//////////////////////////////////
$("#all_inv_save").submit(function (e) {
    e.preventDefault();

    var formData = new FormData(this);

    $.ajax({
        url: "all-invoice-save",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        type: "POST",
        data: new FormData(this),
        processData: false,
        contentType: false,
        beforeSend: function () {
            $("#view_invoices").dataTable().fnClearTable();
            $("#view_invoices").dataTable().fnDestroy();
            $(".indicator-progress").show();
            $(".indicator-label").hide();
        },
        success: (data) => {
            $(".indicator-progress").hide();
            $(".indicator-label").show();
            if (data.success == true) {
                swal("success", "Data Updated successfully", "success");

                var i = 1;
                $.each(data.fetch, function (index, value) {
                    if (value.e_way_bill == null || value.e_way_bill == "") {
                        var billno =
                            "<input type='text' name='data[" +
                            i +
                            "][e_way_bill]' >";
                    } else {
                        var billno = value.e_way_bill;
                    }
                    if (
                        value.e_way_bill_date == null ||
                        value.e_way_bill_date == ""
                    ) {
                        var billdate =
                            "<input type='date' name='data[" +
                            i +
                            "][e_way_bill_date]' >";
                    } else {
                        var billdate = value.e_way_bill_date;
                    }
                    $("#view_invoices tbody").append(
                        "<tr><input type='hidden' name='data[" +
                        i +
                        "][id]' value=" +
                        value.id +
                        " ><td>" +
                        value.consignment_id +
                        "</td><td>" +
                        value.invoice_no +
                        "</td><td>" +
                        billno +
                        "</td><td>" +
                        billdate +
                        "</td></tr>"
                    );

                    i++;
                });
                // location.reload();
            } else {
                swal("error", "Something went wrong", "error");
            }
        },
    });
});


// search function on keypress
$.fn.searchtyping = function (callback) {
    var _this = $(this);
    var x_timer;
    _this.keyup(function () {
        clearTimeout(x_timer);
        x_timer = setTimeout(clear_timer, 300);
    });

    function clear_timer() {
        clearTimeout(x_timer);
        callback.call(_this);
    }
};

// common search function feature for all
jQuery("#search").searchtyping(function (callback) {
    let search = $.trim(jQuery(this).val());
    let url = jQuery(this).attr("data-action");
    jQuery.ajax({
        url: url,
        type: "get",
        data: { search: search },
        headers: {
            "X-CSRF-TOKEN": jQuery('meta[name="csrf-token"]').attr("content"),
        },
        dataType: "json",
        beforeSend: function () {
            jQuery(".load-main").show();

        },
        complete: function () {
            jQuery(".load-main").hide();
        },
        success: function (response) {
            if (response.html) {
                if (
                    (response.page && response.page == "proposal") ||
                    response.page == "order"
                ) {
                    jQuery(".wines_stock").html(response.html);
                } else {
                    jQuery(".main-table").html(response.html);
                }
            }
        },
    });
});

// common reset filter function
jQuery(document).on("click", ".reset_filter", function () {
    var url = jQuery(this).attr("data-action");
    var resetfilter = "resetfilter";
    jQuery.ajax({
        type: "get",
        url: url,
        data: { resetfilter: resetfilter },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        dataType: "json",
        success: function (response) {
            if (response.success) {
                setTimeout(() => {
                    window.location.href = response.redirect_url;
                }, 10);
            }
        },
    });
    return false;
});

// common search for listing page
jQuery("body").on("click", ".pagination a", function () {
    jQuery(".pagination li.active").removeClass("active");
    jQuery(this).parent("li").addClass("active");
    var page = jQuery(this).attr("href").split("page=")[1];
    var pageUrl = jQuery(this).attr("href");
    history.pushState({ page: page }, "title " + page, "?page=" + page);

    $.ajax({
        type: "GET",
        cache: false,
        url: pageUrl,
        data: { page: page },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        dataType: "json",
        success: function (response) {
            if (response.html) {
                jQuery(".main-table").html(response.html);
            }
        },
    });
    return false;
});

// common function for change per page 50
jQuery(document).on("change", ".perpage", function () {
    var url = jQuery(this).attr("data-action");
    var peritem = jQuery(this).val();
    var search = jQuery("#search").val();
    jQuery.ajax({
        type: "get",
        url: url,
        data: { peritem: peritem, search: search },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        dataType: "json",
        success: function (response) {
            if (response.html) {
                jQuery(".main-table").html(response.html);
            }
        },
    });
    return false;
});

///////////////////// vendor //////////
$('#vendor-master').submit(function (e) {
    e.preventDefault();
    var formData = new FormData(this);
    var v_name = $('#vendor_name').val();
    var trans_name = $('#transporter_name').val();
    var vendor_type = $('#vendor_type').val();
    var acc_holder_name = $('#acc_holder_name').val();
    var acc_no = $('#account_no').val();
    var ifsc = $('#ifsc').val();
    var pan_no = $('#pan_no').val();

    if (!v_name) {
        swal("Error!", "Please Enter Vendor Name", "error");
        return false;
    }
    if (!vendor_type) {
        swal("Error!", "Please Select Vendor Type", "error");
        return false;
    }
    if (!acc_holder_name) {
        swal("Error!", "Please Enter Account holder Number", "error");
        return false;
    }
    if (!acc_no) {
        swal("Error!", "Please Enter Account Number", "error");
        return false;
    }
    if (!ifsc) {
        swal("Error!", "Please Enter Ifsc Code", "error");
        return false;
    }
    if (!pan_no) {
        swal("Error!", "Please Enter Pan No", "error");
        return false;
    }
    $.ajax({
        url: "add-vendor",
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        type: 'POST',
        data: new FormData(this),
        processData: false,
        contentType: false,
        beforeSend: function () {

        },
        success: (data) => {
            if (data.success === true) {
                swal("success", data.success_message, "success");
                $('#vendor-master')[0].reset();
                window.location.href = data.redirect_url;
            }else if(data.validation === false){
                swal('error', data.error_message.name[0], 'error');
            }else if(data.pan_check === true){
                swal('error', data.errors, 'error');
            }else if(data.decl_check === true){
                swal('error', data.errors, 'error');
            } else {
                swal('error', data.error_message, 'error');
            }

        }
    });
});
//////////////////Drs Payment Transaction////////////
$('#payment_form').submit(function (e) {
    e.preventDefault();
    var formData = new FormData(this);
    var tds_rate = $('#tds_rate').val();

    if(!tds_rate){
        swal('Error','please add tds rate in vendor','error');
        return false;
    }

    $.ajax({
        url: "create-payment",
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        type: 'POST',
        data: new FormData(this),
        processData: false,
        contentType: false,
        beforeSend: function () {
            $(".indicator-progress").show();
            $(".indicator-label").hide();
        },
        success: (data) => {
            $(".indicator-progress").hide();
            $(".indicator-label").show();
            if(data.success == true){

            swal('success', data.message, 'success')
            $('#payment_form')[0].reset();
            }else{
                swal('error', data.message ,'error');
            }

        }
    });
});
//////////////////Add Purchase Price////////////
$('#purchase_amt_form').submit(function (e) {
    e.preventDefault();
    var formData = new FormData(this);

    $.ajax({
        url: "update-purchas-price",
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        type: 'POST',
        data: new FormData(this),
        processData: false,
        contentType: false,
        beforeSend: function () {

        },
        success: (data) => {
            if (data.success == true) {
                swal('success', data.success_message, 'success');
                window.location.reload();
            } else {
                swal('error', data.error_message, 'error');
            }

        }
    });
});
////////////////// Import Vendor File   ////////////
$('#vendor_import').submit(function (e) {
    e.preventDefault();
    var formData = new FormData(this);
    var file = $('#vendor_file').val();
    if (!file) {
        swal("Error!", "Please Select File", "error");
        return false;
    }

    $.ajax({
        url: "import-vendor",
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        type: 'POST',
        data: new FormData(this),
        processData: false,
        contentType: false,
        beforeSend: function () {

        },
        success: (data) => {
            if (data.success == true) {
                if (data.ignorecount > 0) {
                    $(".ignored").show();
                    $.each(data.ignore_vendor, function (key, value) {
                        $(".ignored").append('<li>' + value.vendor + '</li>');
                    });
                swal('success', data.ignorecount + " ignored, These Vendor Ifsc code is less than 11 digit", 'success');
                } else {
                    swal("success!", data.success_message, "success");
                }
            } else {
                swal('error', data.error_message, 'error');
            }
        }
    });
});

//////////////////update vendor////////////////

$('#update_vendor').validate({
    rules: {
        name: {
            required: true
        },

    },
    messages: {
        name: {
            required: "Enter Vendor name",
        },
    },
    submitHandler: function (form) {
        jQuery.ajax({
            url: form.action,
            type: form.method,
            data: new FormData(form),
            contentType: false,
            cache: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            processData: false,
            dataType: "json",

            success: function (response) {
                if (response.success === true) {
                    swal("success", response.success_message, "success");

                } else {
                    swal('error', data.error_message, 'error');
                }
            }
        });
    }
});

    /*======get LR's on regional client in client report =====*/
    $('.searchclientreport').click(function(e){
        var regclient_id = $("#select_regclient").val();
        var from_date = $("#select_regclient").val();
        var to_date = $("#select_regclient").val();

        $.ajax({
            url         : '/consignment-regclient',
            type        : 'get',
            cache       : false,
            data        :  {regclient_id:regclient_id},
            dataType    :  'json',
            headers     : {
                'X-CSRF-TOKEN': jQuery('meta[name="_token"]').attr('content')
            },
            beforeSend : function(){
                $('#select_consigner').empty();
            },
            success:function(res){
                // console.log(res.data_regclient.is_multiple_invoice);
                $('#consigner_address').empty();
                $('#consignee_address').empty();
                $('#ship_to_address').empty();

                $('#select_consigner').append('<option value="">select consigner</option>');
                $('#select_consignee').append('<option value="">Select Consignee</option>');
                $('#select_ship_to').append('<option value="">Select Ship To</option>');

                $.each(res.data, function (index, value) {

                    $('#select_consigner').append('<option value="' + value.id + '">' + value.nick_name + '</option>');

                });

                if(res.data_regclient == null){
                    var multiple_invoice = '';
                }else{
                    if(res.data_regclient.is_multiple_invoice == null || res.data_regclient.is_multiple_invoice == ''){
                    var multiple_invoice = '';
                    }else{
                        var multiple_invoice = res.data_regclient.is_multiple_invoice;
                    }
                }

                if(multiple_invoice == 1 ){
                    $('.insert-more').attr('disabled',false);
                }else{
                    $('.insert-more').attr('disabled',true);
                }
            }
        });
    });

    //////////////////////////////////
    $('#all_inv_save').submit(function(e) {
        e.preventDefault();

        var formData = new FormData(this);

            $.ajax({
                url: "all-invoice-save",
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                type: 'POST',
                data:new FormData(this),
                processData: false,
                contentType: false,
                beforeSend: function(){
                    $('#view_invoices').dataTable().fnClearTable();
                    $('#view_invoices').dataTable().fnDestroy();
                    $(".indicator-progress").show();
                    $(".indicator-label").hide();
                },
                success: (data) => {
                    $(".indicator-progress").hide();
                    $(".indicator-label").show();
                    if(data.success == true){
                        swal("success","Data Updated successfully", 'success')

                        var i = 1;
                     $.each(data.fetch, function(index, value) {

                         if(value.e_way_bill == null || value.e_way_bill == ''){
                            var billno = "<input type='text' name='data["+i+"][e_way_bill]' >";
                         } else {
                            var billno = value.e_way_bill;
                         }

                         if(value.e_way_bill_date == null || value.e_way_bill_date == ''){
                            var billdate = "<input type='date' name='data["+i+"][e_way_bill_date]' >";
                         }else{
                            var billdate = value.e_way_bill_date;
                         }

                        $('#view_invoices tbody').append("<tr><input type='hidden' name='data["+i+"][id]' value="+value.id+" ><td>" + value.consignment_id + "</td><td>" + value.invoice_no + "</td><td>" + billno + "</td><td>"+ billdate + "</td></tr>");

                        i++ ;
                    });
                        // location.reload();

                    }else{
                        swal("error","Something went wrong", 'error')
                    }
                }
            });
        });

        ////////////////// reate Drs Payment Request ////////////
$('#create_request_form').submit(function (e) {
    e.preventDefault();
    var formData = new FormData(this);
    var vendor = $('#vendor_id_1').val();
    if(!vendor)
    {
        swal('Error!', 'Please select a vendor','error');
        return false;
    }
    var base_url = window.location.origin;
    $.ajax({
        url: "create-payment_request",
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        type: 'POST',
        data: new FormData(this),
        processData: false,
        contentType: false,
        beforeSend: function () {
            $(".indicator-progress").show();
            $(".indicator-label").hide();
        },
        success: (data) => {
            $(".indicator-progress").hide();
            $(".indicator-label").show();
            if(data.success == true){

            swal('success', data.message, 'success')
            window.location.href = data.redirect_url;

            }else{
                swal('error', data.message ,'error');
            }

        }
    });
});
//////////////////Update Purchase Price////////////
$('#update_purchase_amt_form').submit(function (e) {
    e.preventDefault();
    var formData = new FormData(this);

    $.ajax({
        url: "update-purchas-price-vehicle-type",
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        type: 'POST',
        data: new FormData(this),
        processData: false,
        contentType: false,
        beforeSend: function () {

        },
        success: (data) => {
            if (data.success == true) {
                swal('success', data.success_message, 'success');
                window.location.reload();
            } else {
                swal('error', data.error_message, 'error');
            }

        }
    });
});

function closeGetDeliveryDateLR(){
    $('#close_get_delivery_dateLR').click();
};
