(function ($) {

    // Validation for Role ID
    $.validator.addMethod("role_id", function (value, element) {
        return this.optional(element) || /^[a-zA-Z0-9-_]+$/.test(value);
    }, "");

    // role already 
    $.validator.addMethod("role_exist", function (value, element) {
        var solvease_wp_roles_obj = jQuery.parseJSON(solvease_wp_roles.solvease_wp_roles);
        if (value.trim() == 'administrator' || typeof solvease_wp_roles_obj[value.trim()] !== 'undefined') {
            return false;
        }
        return true;
    }, "This role ID already exist.");

    $.validator.addMethod("role_name_exist", function (value, element) {
        if (value.trim() === 'Administrator') {
            return false;
        }
        var status = true;
        $.each(jQuery.parseJSON(solvease_wp_roles.solvease_wp_roles), function (index, existing_value) {
            if (existing_value.name === value.trim()) {
                status = false;
            }
        });
        return status;
    }, "This Display name already exist.");


    $.validator.addMethod("cap_exist", function (value, element) {
        var status = true;
        $.each(jQuery.parseJSON(solvease_wp_roles.solvease_wp_roles), function (index, existing_value) {
            if (typeof existing_value.capabilities[value] !== 'undefined') {
                status = false;
            }
        });
        return status;
    }, "This Capability already exist.");


    // final version is here
    $(document).ready(function () {

        if ($("table.solvease-rnc-table-head").length > 0 ) {
            $("table.solvease-rnc-table-head").stickyTableHeaders();
        }
        var $uniformed;
        $uniformed = $("#solvease_capability_form tbody").find("input").not(".skipThese");
        if ($uniformed.length) {
            $uniformed.uniform();
        }

        // On enter key made it default
        $('input#filter-capability').keydown(function (event) {
            var keypressed = event.keyCode || event.which;
            if (keypressed == 13) {
                return false;
            }

        });
        /* Filter Capability Function */
        $('input#filter-capability').keyup(function (event) {
            var keypressed = event.keyCode || event.which;

            // do nothing on enter press
            if (keypressed == 13) {
                return false;
            }
            // length should be greater than 0
            if ($('input#filter-capability').val().trim().length < 2) {
                $(".cap-name-to-filter").removeClass('green');
                return;
            }
            // add class green when there is match
            var regxp = new RegExp($('input#filter-capability').val().trim());
            $(".cap-name-to-filter").each(function () {
                if (regxp.test($(this).text())) {
                    $(this).addClass('green');
                    //console.log($(this).closest('tr').prev());
                    //console.log($(this).closest('tr').next());
                } else {
                    $(this).removeClass('green');
                }
            });
        });


        $('#solvease_add_role_form').validate({
            rules: {
                'role-id': {
                    minlength: 3,
                    maxlength: 15,
                    required: true,
                    role_id: true,
                    role_exist: true
                },
                'role-name': {
                    minlength: 3,
                    maxlength: 15,
                    required: true,
                    role_name_exist: true
                }
            },
            highlight: function (element) {
                $(element).closest('.form-group').addClass('has-error');
            },
            unhighlight: function (element) {
                $(element).closest('.form-group').removeClass('has-error');
            }
        });


        $('#solvease_add_capability_form').validate({
            rules: {
                'cap_name': {
                    minlength: 3,
                    required: true,
                    cap_exist: true
                },
            },
            highlight: function (element) {
                $(element).closest('.form-group').addClass('has-error');
            },
            unhighlight: function (element) {
                $(element).closest('.form-group').removeClass('has-error');
            }
        });

        $(".role-opertaion .select-all").click(function () {
            $("input[name^='capability[" + $(this).parents('div.role-opertaion').attr('role-id') + "']").each(function () {
                if ($(this).prop('checked') === false) {
                    $(this).trigger('click');
                }
            });
        })

        $(".role-opertaion .un-select-all").click(function () {
            $("input[name^='capability[" + $(this).parents('div.role-opertaion').attr('role-id') + "']").each(function () {
                if ($(this).prop('checked') === true) {
                    $(this).trigger('click');
                }
            });
        })


    });
})(jQuery);


