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
        send_file_upload_restapi_request_to_server_from_form("post", api_add_article_url, bearer, form_data, "", add_article_success_response_function, add_article_error_response_function);
    });

    // SUBMITTING 'SEARCH MERCHANT' FORM
    $("#delete_form").submit(function (e) 
    { 
        e.preventDefault(); 
        fade_in_loader_and_fade_out_form("loader", "delete_form");       
        var form_data = $("#delete_form").serialize();
        var bearer = "Bearer " + localStorage.getItem("admin_access_token"); 
        show_log_in_console("url: " + api_delete_article_url);
        send_restapi_request_to_server_from_form("post", api_delete_article_url, bearer, form_data, "json", delete_article_success_response_function, delete_article_error_response_function);
    });

});

    // RESENDING THE PASSCODE
    function add_article_success_response_function(response)
    {
        show_notification("msg_holder", "success", "Success:", "Article added successfully");
        fade_out_loader_and_fade_in_form("loader", "form"); 
        $('#form')[0].reset();
    }

    function add_article_error_response_function(errorThrown)
    {
        fade_out_loader_and_fade_in_form("loader", "form"); 
        show_notification("msg_holder", "danger", "Error", errorThrown);
    }


/*
|--------------------------------------------------------------------------
| GETTING THE A SINGLE BUREAU TO BE EDITED AND IT'S RESPONSE FUNCTIONS
|--------------------------------------------------------------------------
|--------------------------------------------------------------------------
|
*/
function delete_article_success_response_function(response)
{
    show_notification("msg_holder", "success", "Success", "Article deleted successfully");
    fade_out_loader_and_fade_in_form("loader", "delete_form"); 
    $('#delete_form')[0].reset();
}

function delete_article_error_response_function(errorThrown)
{
    fade_out_loader_and_fade_in_form("loader", "delete_form"); 
    show_notification("msg_holder", "danger", "Error", errorThrown);
}


/*
|--------------------------------------------------------------------------
| FETCHING A SINGLE BUEAU FUNCTION
|--------------------------------------------------------------------------
|--------------------------------------------------------------------------
|
*/

function get_this_bureau(bureau_id)
{
    fade_in_loader_and_fade_out_form("loader", "edit_bureau_form");   
    var bearer = "Bearer " + localStorage.getItem("admin_access_token"); 
    url = admin_api_bureaus_get_one_bureau_url + bureau_id;
    show_log_in_console("url: " + url);
    send_restapi_request_to_server_from_form("get", url, bearer, "", "json", get_this_bureau_success_response_function, get_this_bureau_error_response_function);
}




