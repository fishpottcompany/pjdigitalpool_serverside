/*
|--------------------------------------------------------------------------
| GETTING THE LIST OF ADMINS
|--------------------------------------------------------------------------
|--------------------------------------------------------------------------
|
*/
function get_messages_for_page_success_response_function(response)
{
    fade_out_loader_and_fade_in_form("loader", "dataTableExample"); 
    if(response.data.data.length > 0){
        $('#user_count').html('  ( Total: ' + response.users_count + '  ) ' );
        for (let index = 0; index < response.data.data.length; index++) {
            const element = response.data.data[index];
            $('#table_body_list').append(
                '<tr style="cursor: ;" class="administrator">'
                + '<td>' + element.user_id + '</td>'
                + '<td>' + element.user_firstname + ' ' +  element.user_surname + '</td>'
                + '<td>' + element.user_country + '</td>'
                + '<td>' + element.user_phone_number + '</td>'
                + '<td>' + element.user_email + '</td>'
                + '<td>' + element.created_at + '</td>'
                + '</tr>'
            );
        }
        document.getElementById("dataTableExample").style.display = "";
    } else {
        show_notification("msg_holder", "danger", "", "No messages found");
    }
}

function get_messages_for_page_error_response_function(errorThrown)
{
    show_notification("msg_holder", "danger", "Error", errorThrown);
}


/*
|--------------------------------------------------------------------------
| GETTING THE LIST OF ADMINS FUNCTIONS
|--------------------------------------------------------------------------
|--------------------------------------------------------------------------
|
*/
function get_messages_for_page(page_number)
{
    fade_in_loader_and_fade_out_form("loader", "dataTableExample");   
    var bearer = "Bearer " + localStorage.getItem("admin_access_token"); 
    send_restapi_request_to_server_from_form("get", api_users_list_url, bearer, "", "json", get_messages_for_page_success_response_function, get_messages_for_page_error_response_function);
}
