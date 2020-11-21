$(document).ready(function () 
{
    // SUBMITTING 'ADD MERCHANT' FORM
    $("#form").submit(function (e) 
    { 
        e.preventDefault(); 
        fade_in_loader_and_fade_out_form("loader", "form");    
        var form_data = $("#form").serialize();
        var form = $("#form");
        var form_data = new FormData(form[0]);
        var bearer = "Bearer " + localStorage.getItem("admin_access_token"); 
        //show_log_in_console("Bearer: " + bearer);
        //show_log_in_console("url: " + api_update_notice_url);
        send_file_upload_restapi_request_to_server_from_form("post", api_update_notice_url, bearer, form_data, "", update_notice_success_response_function, update_notice_error_response_function);
    });

});

    // RESENDING THE PASSCODE
    function update_notice_success_response_function(response)
    {
        show_notification("msg_holder", "success", "Success:", "Notice image added successfully");
        fade_out_loader_and_fade_in_form("loader", "form"); 
        $('#form')[0].reset();
    }

    function update_notice_error_response_function(errorThrown)
    {
        fade_out_loader_and_fade_in_form("loader", "form"); 
        show_notification("msg_holder", "danger", "Error", errorThrown);
    }



