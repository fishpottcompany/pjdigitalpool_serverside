$(document).ready(function () 
{
    // SUBMITTING THE LOGIN FORM TO GET API TOKEN
    $("#form").submit(function (e) 
    {
        e.preventDefault(); 
        fade_in_loader_and_fade_out_form("loader", "form");       
        send_request_to_server_from_form("post", api_admin_login_url, $("#form").serialize(), "json", success_response_function, error_response_function);
    });

});


// RESENDING THE PASSCODE
function success_response_function(response)
{
    localStorage.setItem("admin_firstname", response.user_surname);
    localStorage.setItem("admin_surname", response.user_firstname);
    localStorage.setItem("admin_access_token", response.access_token);
    show_notification("msg_holder", "success", "Success:", "Login successful");
    redirect_to_next_page(web_home_url, false);
}

function error_response_function(errorThrown)
{
    fade_out_loader_and_fade_in_form("loader", "form"); 
    show_notification("msg_holder", "danger", "Error", errorThrown);
}