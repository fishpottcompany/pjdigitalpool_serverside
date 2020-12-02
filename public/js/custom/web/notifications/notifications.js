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
        send_file_upload_restapi_request_to_server_from_form("post", api_add_notification_url, bearer, form_data, "", add_notification_success_response_function, add_notification_error_response_function);
    });

});

    // RESENDING THE PASSCODE
    function add_notification_success_response_function(response)
    {
        show_notification("msg_holder", "success", "Success:", "Notification sent successfully");
        fade_out_loader_and_fade_in_form("loader", "form"); 
        $('#form')[0].reset();
    }

    function add_notification_error_response_function(errorThrown)
    {
        fade_out_loader_and_fade_in_form("loader", "form"); 
        show_notification("msg_holder", "danger", "Error", errorThrown);
    }




