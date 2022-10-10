jQuery(document).ready(function(){
    /*========== multi select drop down ========*/
    $(".tagging").select2({
        tags: true
    });
    /*========== valid email check ========*/
    jQuery.validator.addMethod("regex", function(value, element, param) {
        return this.optional(element) || /^\b[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b$/i.test(value);
    }, "Please enter a valid email address.");
    /*========== Enter only number ========*/
    jQuery(document).on('keyup blur', '.mbCheckNm', function(e){
        e.preventDefault();
        var key  = e.charCode || e.keyCode || 0;
        if (key >= 65 && key <= 90){
          this.value = this.value.replace(/[^\d]/g,'');
          return false;
        }
    });
    /*========== Number ========*/
    $.validator.addMethod("Numbers", function(value, element) {
        return this.optional(element) || /^[0-9]*$/.test(value);
    }, "Please enter numeric values only.");
    /*========== Alphabets and Numbers only ========*/
    $.validator.addMethod("AlphabetandNumbers", function(value, element) {
        return this.optional(element) || /^[A-Za-z0-9]+$/i.test(value);
    }, "Only Alphabets and Numbers allowed.");

    /*========== create user in users ========*/
    // $(document).on('submit','.general_form',function(e){
    //     e.preventDefault();
    //     $("input[type=submit]").attr("disabled", "disabled");
    //     $("button[type=submit]").attr("disabled", "disabled");
    //     let form = $(this)[0];
    //     formSubmitRedirect(form);
    // });

    $(document).on('click focus','.is-invalid',function(){
        $(this).removeClass('is-invalid');
        let name = $(this).attr('name');
        $('#'+name+'-error').hide();
    });

    // user login
    jQuery('#loginform').validate({
        rules:
        {
            login_id: {
                required: true,
            },
            email: {
                // required: true,
                regex: "",
                email: true,
            },
            password: {
                required: true,
            },
        },
        messages:
        {
            login_id: {
                required: "Please enter login id",
            },
            email: {
              required: "Email address is required",
             },
            password: {
              required: "Password is required",
             }, 
        },
        submitHandler : function(form)
        {
            formSubmitRedirect(form);
        }
    });

  /*===== create role =====*/
    jQuery('#createrole').validate({
        rules:
        {
            name: {
              required: true,
            },
        },
        messages:
        {
            name: {
              required: "Enter role"
            },
        },
        submitHandler : function(form)
        {
            formSubmitRedirect(form);
        }
    });
  /*===== End create role =====*/

    jQuery('#add_role').click(function(){
        jQuery('#role_savebtn').text('Add');
        $("#createrole").trigger("reset");
    });

  /*=== get role on edit click in role listing page ===*/
    jQuery(document).on('click','.editrole',function(){
        var id = jQuery(this).attr('data-id');
        jQuery('.roleid').val(id);
        var action = jQuery(this).attr('data-action');
        jQuery.ajax({
            type      : 'post',
            url       : action,
            data      : {id:id},
            headers   : {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType  : 'json',
            success:function(response){
                if(response.success){
                    var res = response.data;
                    jQuery('#name').val(res.name); 
                    jQuery('#role_savebtn').text('Update');
                }
            }
        });
    });

/*===== Create user =====*/
    $('#createuser').validate({ 
        rules: {
            name: {
                required: true
            },
            login_id: {
                required: true
            },
            email: {
                required: true,
                // email: true,
                regex: "",
            },
            password : {
                required: true,
                minlength : 5
            },
            phone : {
                Numbers: true,
                minlength:10,
            },
            "branch_id[]" : {
                required: true,
            },
            "regionalclient_id[]" : {
                required: true,
            },      
        },
        messages: {
            name: {
                required: "Enter name",
            },
            login_id: {
                required: "Enter login id",
            },
            email: {
                required: "Enter email",
                email: "Enter correct email address",
            },
            password : {
                required: "Enter password",
                minlength : "Password must be at least 5 characters long" 
            },
            phone: {
                Numbers: "Enter only numbers",
                minlength: "Enter at least 10 digits",
                // maxlength: "Maximum length sholud not more than 10 digits"
            },
            "branch_id[]" : {
                required: "Please select location",
            },
            "regionalclient_id[]" : {
                required: "Please select regional client",
            },
        },
        submitHandler : function(form)
        {
            formSubmitRedirect(form);
        }
    });


    /*===== update user =====*/
    $('#updateuser').validate({ 
        rules: {
            name: {
                required: true
            },
            email: {
                required: true,
                email: true,
                regex: "",
            },
            password : {
                required: true,
                minlength : 5
            },
            phone : {
                Numbers: true,
                minlength:10,
            },
            "branch_id[]" : {
                required: true,
            },          
        },
        messages: {
            name: {
                required: "Enter name",
            },
            email: {
                required: "Enter Email",
                email: "Enter correct email address",
            },
            password : {
                required: "Enter password",
                minlength : "Password must be at least 5 characters long" 
            },
            phone: {
                Numbers: "Enter only numbers",
                minlength: "Enter at least 10 digits",
                // maxlength: "Maximum length sholud not more than 10 digits"
            },
            "branch_id[]" : {
                required: "Please select location",
            },
        },
        submitHandler : function(form)
        {
            formSubmitRedirect(form);
        }
    });

/*===== create branch =====*/
    $('#createbranch').validate({ 
        rules: {
            name: {
                required: true
            },
            email: {
                required: true,
                email: true,
                regex: "",
            },
            address_line1 : {
              required: true,
            },
            phone : {
                Numbers: true,
                minlength:10,
                // maxlength:10,
            },
            "files[]":{
                extension: "jpg|jpeg|png"
            },          
        },
        messages: {
            name: {
                required: "Enter name",
            },
            email: {
                required: "Enter Email",
                email: "Enter correct email address",
            },
            address_line1: {
                required: "Enter address1",
            },
            phone: {
                Numbers: "Enter only numbers",
                minlength: "Enter at least 10 digits",
                // maxlength: "Maximum length sholud not more than 10 digits"
            },
            "files[]":{
                extension: "Please choose a valid file"
            },
        },
        submitHandler : function(form)
        {
            formSubmitRedirect(form);
        }
    });

    /*===== update branch =====*/
    $('#updatebranch').validate({ 
        rules: {
            name: {
                required: true
            },
            email: {
                required: true,
                email: true,
                regex: "",
            },
            address_line1 : {
              required: true,
            },
            phone : {
                Numbers: true,
                minlength:10,
            },
            "files[]":{
                extension: "jpg|jpeg|png"
            },           
        },
        messages: {
            name: {
                required: "Enter name",
            },
            email: {
                required: "Enter Email",
                email: "Enter correct email address",
            },
            address_line1: {
                required: "Enter address1",
            },
            phone: {
                Numbers: "Enter only numbers",
                minlength: "Enter at least 10 digits",
            },
            "files[]":{
                extension: "Please choose a valid file"
            },
        },
        submitHandler : function(form)
        {
            formSubmitRedirect(form);
        }
    });

    /*===== create consigner =====*/
    $('#createconsigner').validate({ 
        rules: {
            nick_name: {
                required: true
            },
            // email: {
            //     required: true,
            //     email: true,
            //     regex: "",
            // },
            address_line1 : {
                // required: true,
            },
            phone : {
                required: true,
                Numbers: true,
                minlength: 10,
            },
            gst_number : {
                // required: true,
                AlphabetandNumbers: true,
                minlength: 15,
            },
            branch_id : {
                required: true,
            },
            postal_code : {
                required: true,
                Numbers: true,
                minlength: 6,
            },
        },
        messages: {
            nick_name: {
                required: "Enter name",
            },
            email: {
                required: "Enter Email",
                email: "Enter correct email address",
            },
            address_line1: {
                required: "Enter address1",
            },
            phone: {
                required: "Enter phone number",
                Numbers: "Enter only numbers",
                minlength: "Enter at least 10 digits",
            },
            gst_number: {
                required: "Enter gst number",
                AlphabetandNumbers: "Enter only alphabets and numbers",
                minlength: "Enter at least 15 digits",
            },
            branch_id: {
                required: "Please select location",
            },
            postal_code: {
                required: "Enter pin code",
                Numbers: "Enter only numbers",
                minlength: "Enter at least 6 digits",
            },
        },
        submitHandler : function(form)
        {
            formSubmitRedirect(form);
        }
    });

    /*===== update consigner =====*/
    $('#updateconsigner').validate({ 
        rules: {
            nick_name: {
                required: true
            },
            // email: {
            //     required: true,
            //     email: true,
            //     regex: "",
            // },
            address_line1 : {
                // required: true,
            },
            phone : {
                required: true,
                Numbers: true,
                minlength: 10,
            },
            gst_number : {
                // required: true,
                AlphabetandNumbers: true,
                minlength: 15,
            },
            branch_id : {
                required: true,
            },
            postal_code : {
                required: true,
                Numbers: true,
                minlength: 6,
            },
        },
        messages: {
            nick_name: {
                required: "Enter name",
            },
            email: {
                required: "Enter Email",
                email: "Enter correct email address",
            },
            address_line1: {
                required: "Enter address1",
            },
            phone: {
                required: "Enter phone number",
                Numbers: "Enter only numbers",
                minlength: "Enter at least 10 digits",
            },
            gst_number: {
                required: "Enter gst number",
                AlphabetandNumbers: "Enter only alphabets and numbers",
                minlength: "Enter at least 15 digits",
            },
            branch_id: {
                required: "Please select location",
            },
            postal_code: {
                required: "Enter pin code",
                Numbers: "Enter only numbers",
                minlength: "Enter at least 6 digits",
            },
        },
        submitHandler : function(form)
        {
            formSubmitRedirect(form);
        }
    });

    /*===== create consignee =====*/
    $('#createconsignee').validate({ 
        rules: {
            nick_name: {
                required: true
            },
            // email: {
            //     required: true,
            //     email: true,
            //     regex: "",
            // },
            phone : {
                required: true,
                Numbers: true,
                minlength: 10,
            },
            gst_number : {
                // required: true,
                AlphabetandNumbers: true,
                minlength: 15,
            },
            consigner_id : {
                required: true,
            },
            postal_code : {
                required: true,
                Numbers: true,
                minlength: 6,
            }
        },
        messages: {
            nick_name: {
                required: "Enter name",
            },
            email: {
                required: "Enter Email",
                email: "Enter correct email address",
            },
            phone: {
                required: "Enter phone number",
                Numbers: "Enter only numbers",
                minlength: "Enter at least 10 digits",
            },
            gst_number: {
                required: "Enter gst number",
                AlphabetandNumbers: "Enter only alphabets and numbers",
                minlength: "Enter at least 15 digits",
            },
            consigner_id: {
                required: "Please select consigner",
            },
            postal_code: {
                required: "Enter pin code",
                Numbers: "Enter only numbers",
                minlength: "Enter at least 6 digits",
            },
        },
        submitHandler : function(form)
        {
            formSubmitRedirect(form);
        }
    });

    /*===== update consignee =====*/
    $('#updateconsignee').validate({ 
        rules: {
            nick_name: {
                required: true
            },
            // email: {
            //     required: true,
            //     email: true,
            //     regex: "",
            // },
            phone : {
                required: true,
                Numbers: true,
                minlength: 10,
            },
            gst_number : {
                AlphabetandNumbers: true,
                minlength: 15,
            },
            consigner_id : {
                required: true,
            },
            postal_code : {
                required: true,
                Numbers: true,
                minlength: 6,
            },
        },
        messages: {
            nick_name: {
                required: "Enter name",
            },
            email: {
                required: "Enter Email",
                email: "Enter correct email address",
            },
            phone: {
                required: "Enter phone number",
                Numbers: "Enter only numbers",
                minlength: "Enter at least 10 digits",
            },
            gst_number: {
                AlphabetandNumbers: "Enter only alphabets and numbers",
                minlength: "Enter at least 15 digits",
            },
            consigner_id: {
                required: "Please select consigner",
            },
            postal_code: {
                required: "Enter pin code",
                Numbers: "Enter only numbers",
                minlength: "Enter at least 6 digits",
            },
        },
        submitHandler : function(form)
        {
            formSubmitRedirect(form);
        }
    });

    /*===== create broker =====*/
    $('#createbroker').validate({ 
        rules: {
            name: {
                required: true
            },
            email: {
                required: true,
                email: true,
                regex: "",
            },
            address : {
                // required: true,
            },
            phone : {
                Numbers: true,
                minlength: 10,
            },
            broker_type : {
                required: true,
            }, 
            is_lane_approved : {
                required: true,
            },          
        },
        messages: {
            name: {
                required: "Enter name",
            },
            email: {
                required: "Enter Email",
                email: "Enter correct email address",
            },
            address: {
                required: "Enter address1",
            },
            phone: {
                Numbers: "Enter only numbers",
                minlength: "Enter at least 10 digits",
            },
            broker_type : {
                required: "Select broker type",
            }, 
            is_lane_approved : {
                required: "Select lane approval",
            },
        },
        submitHandler : function(form)
        {
            formSubmitRedirect(form);
        }
    });

    /*===== update broker =====*/
    $('#updatebroker').validate({ 
        rules: {
            name: {
                required: true
            },
            email: {
                required: true,
                email: true,
                regex: "",
            },
            address : {
                // required: true,
            },
            phone : {
                Numbers: true,
                minlength: 10,
            },
            broker_type : {
                required: true,
            }, 
            is_lane_approved : {
                required: true,
            },           
        },
        messages: {
            name: {
                required: "Enter name",
            },
            email: {
                required: "Enter Email",
                email: "Enter correct email address",
            },
            address: {
                required: "Enter address1",
            },
            phone: {
                Numbers: "Enter only numbers",
                minlength: "Enter at least 10 digits",
            },
            broker_type : {
                required: "Select broker type",
            }, 
            is_lane_approved : {
                required: "Select lane approval",
            },
        },
        submitHandler : function(form)
        {
            formSubmitRedirect(form);
        }
    });

    /*===== create driver =====*/
    $('#createdriver').validate({ 
        rules: {
            name: {
                required: true
            },
            phone : {
                required: true,
                Numbers: true,
                minlength: 10,
            },
            license_number : {
                required: true,
            },         
        },
        messages: {
            name: {
                required: "Enter name",
            },
            phone: {
                required: "Enter phone number",
                Numbers : "Enter only numbers",
                minlength: "Enter at least 10 digits",
            },
            license_number : {
                required: "Enter license number",
            }, 
        },
        submitHandler : function(form)
        {
            formSubmitRedirect(form);
        }
    });

    /*===== update driver =====*/
    $('#updatedriver').validate({ 
        rules: {
            name: {
                required: true
            },
            phone : {
                required: true,
                Numbers: true,
                minlength: 10,
            },
            license_number : {
                required: true,
            },         
        },
        messages: {
            name: {
                required: "Enter name",
            },
            phone: {
                required: "Enter phone number",
                Numbers : "Enter only numbers",
                minlength: "Enter at least 10 digits",
            },
            license_number : {
                required: "Enter license number",
            }, 
        },
        submitHandler : function(form)
        {
            formSubmitRedirect(form);
        }
    });

    /*===== create vehicle =====*/
    $('#createvehicle').validate({ 
        rules: {
            regn_no: {
                required: true,
                // AlphabetandNumbers: true,
                minlength: 8,
            },
            mfg: {
                required: true
            },
            make: {
                required: true
            },
            engine_no: {
                required: true,
                // AlphabetandNumbers: true,
            },
        },
        messages: {
            regn_no: {
                required: "Enter number",
                AlphabetandNumbers: "Enter only alphabets and numbers",
                minlength: "Enter at least 8 digits",
            },
            mfg: {
                required: "Enter manufacturer",
            },
            make: {
                required: "Enter maker",
            },
            engine_no: {
                required: "Enter engin no.",
                AlphabetandNumbers: "Enter only alphabets and numbers",
            }, 
        },
        submitHandler : function(form)
        {
            formSubmitRedirect(form);
        }
    });

     /*===== update vehicle =====*/
     $('#updatevehicle').validate({ 
        rules: {
            regn_no: {
                required: true,
                // AlphabetandNumbers: true,
                minlength: 8,
            },
            mfg: {
                required: true
            },
            make: {
                required: true
            },
            engine_no: {
                required: true,
                // AlphabetandNumbers: true,
            },
        },
        messages: {
            regn_no: {
                required: "Enter number",
                AlphabetandNumbers: "Enter only alphabets and numbers",
                minlength: "Enter at least 8 digits",
            },
            mfg: {
                required: "Enter manufacturer",
            },
            make: {
                required: "Enter maker",
            },
            engine_no: {
                required: "Enter engin no.",
                AlphabetandNumbers: "Enter only alphabets and numbers",
            },
        },
        submitHandler : function(form)
        {
            formSubmitRedirect(form);
        }
    });
    
    /*===== create consignment =====*/
    $('#createconsignment').validate({ 
        rules: {
            consigner_id: {
                required: true
            },
            consignee_id: {
                required: true
            },
            ship_to_id: {
                required: true
            },
           
            'quantity[]': {
                required: true
                //   lettersonly:true
                },
           'weight[]': {
                  required: true
                },
           'gross_weight[]': {
                  required: true
                },
           
        },
        messages: {
            consigner_id: {
                required: "Select consigner address",
            },
            consignee_id: {
                required: "Select consignee address",
            },
            ship_to_id: {
                required: "Select ship to address",
            },
            driver_id : {
                required: "Select driver",
            },
            transporter_name : {
                required: "Enter transporter name",
            },
            vehicle_type : {
                required: "Select vehicle type",
            },
            vehicle_id : {
                required: "Select vehicle no.",
            },
            'quantity[]': {
                required: "Enter quantity",
            },
            'weight[]': {
                required: "Enter weight",
            },
            'gross_weight[]': {
                required: "Enter gross weight",
            },
            'freight[]': {
                required: "Enter freight",
            },
            'payment_type[]': {
                required: "Select payment type",
            },
        },
        submitHandler : function(form)
        {
            formSubmitRedirect(form);
        }
    });
    //consignment multiple rows field validation

    /*===== create order =====*/
    $('#createorder').validate({ 
        rules: {
            consigner_id: {
                required: true,
            },
            consignee_id: {
                required: true
            },
            ship_to_id: {
                required: true
            },
            invoice_no: {
                // required: true,
            },
            invoice_amount: {
                // required: true,
            },
            driver_id : {
                // required: true,
            },
            transporter_name : {
                // required: true,
            },
            vehicle_type : {
                // required: true,
            },
            vehicle_id : {
                // required: true,
            },
            e_way_bill: {
                // required: true,
            },
            e_way_bill_date: {
                // required: true,
            },
            'quantity[]': {
                required: true,
                //   lettersonly:true
                },
           'weight[]': {
                  required: true
                },
           'gross_weight[]': {
                  required: true
                },
           'freight[]': {
                  required: true
                },
            'payment_type[]': {
                required: true
                },
        },
        messages: {
            consigner_id: {
                required: "Select consigner address",
            },
            consignee_id: {
                required: "Select consignee address",
            },
            ship_to_id: {
                required: "Select ship to address",
            },
            invoice_no: {
                required: "Enter invoice no.",
                AlphabetandNumbers: "Enter only alphabets and numbers",
            },
            invoice_amount: {
                required: "Enter invoice amount",
                AlphabetandNumbers: "Enter only alphabets and numbers",
            },
            driver_id : {
                required: "Select driver",
            },
            transporter_name : {
                required: "Enter transporter name",
            },
            vehicle_type : {
                required: "Select vehicle type",
            },
            vehicle_id : {
                required: "Select vehicle no.",
            },
            e_way_bill : {
                required: "Enter E Way Bill Number",
            },
            e_way_bill_date : {
                required: "Enter E Way Bill Date",
            },
            'quantity[]': {
                required: "Enter quantity",
            },
            'weight[]': {
                required: "Enter weight",
            },
            'gross_weight[]': {
                required: "Enter gross weight",
            },
            'freight[]': {
                required: "Enter freight",
            },
            'payment_type[]': {
                required: "Select payment type",
            },
        },
        submitHandler : function(form)
        {
            formSubmitRedirect(form);
        }
    });
    
    $('#updateorder').validate({ 
        rules: {
            consigner_id: {
                required: true
            },
            consignee_id: {
                required: true
            },
            ship_to_id: {
                required: true
            },
            'quantity[]': {
                required: true
                //   lettersonly:true
                },
           'weight[]': {
                  required: true
                },
           'gross_weight[]': {
                  required: true
                }
            
        },
        messages: {
            consigner_id: {
                required: "Select consigner address"
            },
            consignee_id: {
                required: "Select consignee address"
            },
            ship_to_id: {
                required: "Select ship to address"
            },
            driver_id : {
                required: "Select driver"
            },
            transporter_name : {
                required: "Enter transporter name"
            },
            vehicle_type : {
                required: "Select vehicle type"
            },
            vehicle_id : {
                required: "Select vehicle no."
            },
            'quantity[]': {
                required: "Enter quantity"
            },
            'weight[]': {
                required: "Enter weight"
            },
            'gross_weight[]': {
                required: "Enter gross weight"
            },
            'freight[]': {
                required: "Enter freight"
            },
            'payment_type[]': {
                required: "Select payment type"
            }
        },
        submitHandler : function(form)
        {
            formSubmitRedirect(form);
        }
    });
    $(".qnt").each(function()
    {
        $(this).rules('add', {
                required: true,
                messages: {
                    required: "Enter quantity."
                },
            });
    });
    $(".net").each(function()
    {
        $(this).rules('add', {
                required: true,
                messages: {
                    required: "Enter net weight."
                },
            });
    });
    $(".gross").each(function()
    {
        $(this).rules('add', {
                required: true,
                messages: {
                    required: "Enter gross weight."
                },
            });
    });
  
    /*===== Create payment =====*/
    $('#createpayment').validate({ 
        rules: {
            origin: {
                required: true
            },
            destination: {
                required: true,
            },
            purchase_price: {
                number: true,
            },
            advance_payment: {
                number: true,
            },
                
        },
        messages: {
            origin: {
                required: "Enter origin",
            },
            destination: {
                required: "Enter destination",
            },
            purchase_price: {
                number: "Enter numeric value only",
            },
            advance_payment: {
                number: "Enter numeric value only",
            },
        },
        submitHandler : function(form)
        {
            formSubmitRedirect(form);
        }
    });

    /*===== Create location =====*/
    jQuery('#createlocation').validate({
        rules:
        {
            name: {
                required: true,
            },
            nick_name: {
                required: true,
            },
            consignment_no: {
                required: true,
            },
            email: {
                required: true,
                email: true,
            },
            phone: {
                required: true,
                Numbers: true,
                minlength: 10,
            },

        },
        messages:
        {
            name: {
                required: "Location name is required"
            },
            nick_name: {
                required: "Nick name is required"
            },
            team_id: {
                required: "Team id is required"
            },
            consignment_no: {
                required: "Consignment no is required"
            },
            email: {
                required: "Email is required",
                email: "Enter valid email",
            },
            phone: {
                required: "Phone is required",
                Numbers: "Enter numeric value only",
                minlength: "Enter at least 10 digits",
            },
        },
        submitHandler : function(form)
        {
            formSubmitRedirect(form);
            jQuery('#location-modal').modal('hide');
        }
    });

    /*===== Update location =====*/
    jQuery('#updatelocation').validate({
        rules:
        {
            name: {
                required: true,
            },
            nick_name: {
                required: true,
            },
            team_id: {
                required: true,
            },
            consignment_no: {
                required: true,
            },
            email: {
                required: true,
                email: true,
            },
            phone: {
                required: true,
                Numbers: true,
                minlength: 10,
            },
        },
        messages:
        {
            name: {
                required: "Location name is required"
            },
            nick_name: {
                required: "Nick name is required"
            },
            team_id: {
                required: "Team id is required"
            },
            consignment_no: {
                required: "Consignment no is required"
            },
            email: {
                required: "Email is required",
                email: "Enter valid email",
            },
            phone: {
                required: "Phone is required",
                Numbers: "Enter numeric value only",
                minlength: "Enter at least 10 digits",
            },
        },
        submitHandler : function(form)
        {
            formSubmitRedirect(form);
            jQuery('#location-modal').modal('hide');
        }
    });

    // reset location create form not working check yet
    jQuery('#add_location').click(function(){
        $("#createlocation").trigger( "reset" );
    });

    // Import consignee csv files
    jQuery('#importfiles').validate({
        rules:
        {
            consigneesfile: {
                // required: true,
                // extension: "csv",
            },
            vehiclesfile: {
                // required: true,
                //  extension: "csv",
            },
            consignersfile: {
                // required: true,
                //  extension: "csv",
            },
        },
        messages:
        {
            consigneesfile: {
                required: "Please select file",
                extension: "Please upload .csv file format only"
            },
            vehiclesfile: {
                required: "Please select file",
                extension: "Please upload .csv file format only"
            },
            consignersfile: {
                required: "Please select file",
                extension: "Please upload .csv file format only"
            }
        },
        submitHandler : function(form)
        {
            formSubmitRedirect(form);
        }
    });

    // create branch address
    jQuery('#createbranchadd').validate({
        rules:
        {
            name: {
                required: true,
            },
            gst_number: {
                required: true,
            },
            phone: {
                required: true,
            },
            address: {
                required: true,
            },
            email: {
                required: true,
            },
            state: {
                required: true,
            },
            postal_code: {
                required: true,
            },
        },
        messages:
        {
            name: {
                required: "Enter branch name",
            },
            gst_number: {
                required: "Enter GST number",
            },
            phone: {
                required: "Enter phone number",
            },
            address: {
                required: "Enter address",
            },
            email: {
                required: "Enter email",
            },
            state: {
                required: "Enter state",
            },
            postal_code: {
                required: "Enter pin code",
            },
        },
        submitHandler : function(form)
        {
            formSubmitRedirect(form);
        }
    });

    /*===== Create Client =====*/
    $('#createclient').validate({ 
        rules: {
            client_name: {
                required: true
            },
            name: {
                required: true
            },
            'location_id[]' : {
                required: true,
            },
        },
        messages: {
            client_name: {
                required: "Enter client name",
            },
            name: {
                required: "Enter name",
            },
            'location_id[]' : {
                required: "Please select location",
            },
        },
        submitHandler : function(form)
        {
            formSubmitRedirect(form);
        }
    });
    $('#updateclient').validate({ 
        rules: {
            client_name: {
                required: true
            },
            name: {
                required: true
            },
            'location_id[]' : {
                required: true,
            },      
        },
        messages: {
            client_name: {
                required: "Enter client name",
            },
            name: {
                required: "Enter name",
            },
            'location_id[]' : {
                required: "Please select location",
            },
        },
        submitHandler : function(form)
        {
            formSubmitRedirect(form);
        }
    });

    $(".location_id").each(function()
    {

        $(this).rules('add', {
                required: true,
                // lettersonly:true,
                messages: {
                required: "Please select location",
                },
            });
    });

    // create reg client detail
    jQuery('#createregclientdetail').validate({
        rules:
        {
            from_state_id: {
                // required: true,
            },
            to_state_id: {
                // required: true,
            },
        },
        messages:
        {
            from_state_id: {
                required: "Please enter login id",
            },
            to_state_id: {
                required: "Email address is required",
             },
        },
        submitHandler : function(form)
        {
            formSubmitRedirect(form);
        }
    });
    
    // create reg client detail
    jQuery('#updateregclientdetail').validate({
        rules:
        {
            from_state_id: {
                // required: true,
            },
            to_state_id: {
                // required: true,
            },
        },
        messages:
        {
            from_state_id: {
                required: "Please enter login id",
            },
            to_state_id: {
                required: "Email address is required",
            },
        },
        submitHandler : function(form)
        {
            formSubmitRedirect(form);
        }
    });
    




});
/*======= End document ready fuction =======*/

/*======= form submit fuction =======*/

function formSubmit(form)
{
    jQuery.ajax({
        url         : form.action,
        type        : form.method,
        data        : new FormData(form),
        contentType : false,
        cache       : false,
        headers     : {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        processData : false,
        dataType    : "json",
        beforeSend  : function () {
            $(".loader").show();
        },
        complete: function () {
            $(".loader").hide();
        },
        success: function (response) {
            if(response.success){
                if(response.page == 'role'){
                    if(response.rolepage == 'rolepage'){
                        setTimeout(() => {window.location.href = response.redirect_url},1000);
                    }else{
                        // var allcountries = response.alldata;
                        // jQuery("#countrymodal").modal("hide");
                        // $(".country option").remove();
                        // $.each(allcountries, function(key, value) {
                        //     $('.country')
                        //     .append($("<option></option>")
                        //     .attr("value",value)
                        //     .text(key));
                        // });
                        // $('#proposal_regions').empty().append('<option value="">Please choose region</option> ');
                        // let country_id = $("#winery_country").val();
                        // getRegions(country_id);

                        // jQuery("#name-error").hide();
                        // jQuery("#country_id-error").hide();
                        // jQuery('.country').val(response.data.id);
                        // jQuery("#createcountry").trigger("reset");
                    }
                }
            }

            if(response.formErrors)
            {
                var i = 0;
                $.each(response.errors, function(index,value)
                {
                    if (i == 0) {
                        $("input[name='"+index+"']").focus();
                    }
                    $("input[name='"+index+"']").parents('.form-group').addClass('has-error');
                    $("input[name='"+index+"']").after('<label id="'+index+'-error" class="error" for="'+index+'">'+value+'</label>');
                    $("select[name='"+index+"']").parents('.form-group').addClass('has-error');
                    $("select[name='"+index+"']").after('<label id="'+index+'-error" class="has-error" for="'+index+'">'+value+'</label>');
                      i++;
                });
            }
        },
        error:function(response){
            console.error(response);
        }
    });
}
/*======= End form submit fuction =======*/


/*======= submit redirect fuction =======*/
function formSubmitRedirect(form)
{
    jQuery.ajax({
        url         : form.action,
        type        : form.method,
        data        : new FormData(form),
        contentType : false,
        cache       : false,
        headers     : {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        processData : false,
        dataType    : "json",
        beforeSend  : function () {
            $(".loader").show();
            
            if ($('#dealer_type').val() == 1 && $("#gst_number").val() == '') {
                $('.gstno_error').show();
                return false;
            }else{
                $('.gstno_error').hide();
            }
           $('.disableme').prop('disabled', true);
        },
        complete: function (response) {
            $('.disableme').prop('disabled', true);

             $("input[type=submit]").attr("enabled", "enabled");
        	 $("button[type=submit]").attr("enabled", "enabled");
            $(".loader").hide();
        },
        success: function (response)
        {
            //alert(response);
          	$.toast().reset('all');
      		var delayTime = 3000;
	        if(response.success){
	            $.toast({
		          heading             : 'Success',
		          text                : response.success_message,
		          loader              : true,
		          loaderBg            : '#fff',
		          showHideTransition  : 'fade',
		          icon                : 'success',
		          hideAfter           : delayTime,
		          position            : 'top-right'
		    	});
	        }
	        if(response.resetform)
            {
                $('#'+form.id).trigger('reset');
            }else if(response.page == 'login'){
                setTimeout(() => {window.location.href = response.redirect_url},1000);
            }else if(response.page == 'user-create' || response.page == 'user-update'){
                setTimeout(() => {window.location.href = response.redirect_url},2000);
            }else if(response.page == 'role'){
                setTimeout(() => {window.location.href = response.redirect_url},1000);
            }else if(response.page == 'branch-create' || response.page == 'branch-update'){
                setTimeout(() => {window.location.href = response.redirect_url},2000);
            }else if(response.page == 'consigner-create' || response.page == 'consigner-update'){
                setTimeout(() => {window.location.href = response.redirect_url},2000);
            }else if(response.page == 'consignee-create' || response.page == 'consignee-update'){
                setTimeout(() => {window.location.href = response.redirect_url},2000);
            }else if(response.page == 'broker-update'){
                setTimeout(() => {window.location.href = response.redirect_url},2000);
            }else if(response.page == 'driver-create' || response.page == 'driver-update'){
                setTimeout(() => {window.location.href = response.redirect_url},2000);
            }else if(response.page == 'vehicle-create' || response.page == 'vehicle-update'){
                setTimeout(() => {window.location.href = response.redirect_url},2000);
            }else if(response.page == 'save-locations'|| response.page == 'update-locations'){
                setTimeout(function(){location.reload();}, 50);
            }else if(response.page == 'bulk-imports'){
                setTimeout(() => {window.location.href = response.redirect_url},2000);
            }else if(response.page == 'create-consignment'){
                setTimeout(() => {window.location.href = response.redirect_url},2000);
            }else if(response.page == 'create-order' || response.page == 'update-order'){
                setTimeout(() => {window.location.href = response.redirect_url},2000);
            }else if(response.page == 'settings-branch-address'){
                setTimeout(function(){ location.reload(); }, 50);
            }else if(response.page == 'clientdetail-create' || response.page == 'clientdetail-update'){
                setTimeout(() => {window.location.href = response.redirect_url},2000);
            }
            
            if(response.formErrors)
            {
                var i = 0;
              $('.error').remove();
              
              $.each(response.errors, function(index,value)
              {
                  if (i == 0) {
                   $("input[name='"+index+"']").focus();
                  }
                  $("input[name='"+index+"']").parents('.form-group').addClass('has-error');
                  $("input[name='"+index+"']").after('<label id="'+index+'-error" class="error" for="'+index+'">'+value+'</label>');

                  $("select[name='"+index+"']").parents('.form-group').addClass('has-error');
                  $("select[name='"+index+"']").after('<label id="'+index+'-error" class="has-error" for="'+index+'">'+value+'</label>');
                  i++;
              });
	        }

            if(response.cnr_nickname_duplicate_error){
                jQuery("input[name='nick_name']").focus();
                jQuery("input[name='nick_name']").parents('.form-group').addClass('has-error');
                jQuery("input[name='nick_name']").after('<label id="nick_name-error" class="error" for="nick_name">'+response.error_message+'</label>');
                $("select[name='nick_name']").after('<label id="nick_name-error" class="has-error" for="nick_name">'+response.error_message+'</label>');
            }

            if(response.cnee_nickname_duplicate_error){
                jQuery("input[name='nick_name']").focus();
                jQuery("input[name='nick_name']").parents('.form-group').addClass('has-error');
                jQuery("input[name='nick_name']").after('<label id="nick_name-error" class="error" for="nick_name">'+response.error_message+'</label>');
                $("select[name='nick_name']").after('<label id="nick_name-error" class="has-error" for="nick_name">'+response.error_message+'</label>');
            }
            if(response.invoiceno_duplicate_error){
                jQuery("input[class='invc_no']").focus(); //.invc_no
                jQuery("input[class='invc_no']").parents().parents('#items_table').addClass('has-error');
                jQuery("input[class='invc_no']").after('<label class="invoice_no-error" class="error" for="invoice_no">'+response.error_message+'</label>');
                $("select[class='invc_no']").after('<label class="invoice_no-error" class="has-error" for="invoice_no">'+response.error_message+'</label>');
            }

            if(response.email_error){
                jQuery("#login_id-error").remove();
                jQuery("input[name='login_id']").focus();
                jQuery("input[name='login_id']").parents('.form-group').addClass('has-error');
                jQuery("input[name='login_id']").after('<label id="login_id-error" class="error" for="login_id">'+response.error_message+'</label>');
                $("select[name='login_id']").after('<label id="login_id-error" class="has-error" for="login_id">'+response.error_message+'</label>');
            }
		    var i = 0;
            $.each(response.errors, function( index, value )
            {
                if (i == 0) {
                    $("input[name='"+index+"']").focus();
                }
                var str=value.toString();
                if (str.indexOf('edit') != -1) {
                    // will not be triggered because str has _..
                    value=str.toString().replace('edit', '');
                }


                // $("input[name='"+index+"']").parents('.form-group').addClass('has-error');
                $("input[name='"+index+"']").after('<label id="'+index+'-error" class="has-error" for="'+index+'">'+value+'</label>');

                // $("textarea[name='"+index+"']").parents('.form-group').addClass('has-error');
                $("textarea[name='"+index+"']").after('<label id="'+index+'-error" class="has-error" for="'+index+'">'+value+'</label>');

                // $("select[name='"+index+"']").parents('.form-group').addClass('has-error');
                $("select[name='"+index+"']").after('<label id="'+index+'-error" class="has-error" for="'+index+'">'+value+'</label>');
                $("input[name='"+index+"']").addClass('is-invalid');
                $("select[name='"+index+"']").addClass('is-invalid');
                $("textarea[name='"+index+"']").addClass('is-invalid');
                i++;

            });
            $("input[type=submit]").removeAttr("disabled");
            $("button[type=submit]").removeAttr("disabled");
		},
        error:function(response){
            $.toast({
                heading             : 'Error',
                text                : "Server Error",
                loader              : true,
                loaderBg            : '#fff',
                showHideTransition  : 'fade',
                icon                : 'error',
                hideAfter           : 4000,
                position            : 'top-right'
            });
        }
    });
}
/*======= End submit redirect fuction =======*/