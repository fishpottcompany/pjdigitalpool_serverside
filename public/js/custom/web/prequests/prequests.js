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
        for (let index = 0; index < response.data.data.length; index++) {
            const element = response.data.data[index];
            if(element.message_type == "Prayer Request"){
                var status = '<span class="u-label bg-success text-white">Prayer Request</span>';
            } else if(element.message_type == "Testimonies"){
                var status = '<span class="u-label bg-info text-white">Testimonies</span>';
            } else {
                var status = '<span class="u-label bg-warning text-white">Feedback</span>';
            }
            $('#table_body_list').append(
                '<tr style="cursor: ;" class="administrator">'
                + '<td>' + element.message_id +' </td>'
                + '<td>' + status +' </td>'
                + '<td>' + element.user_full_name + ' (' +  element.user_phone + ')</td>'
                + '<td>' + element.message_text + '</td>'
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
    send_restapi_request_to_server_from_form("get", api_prequests_list_url, bearer, "", "json", get_messages_for_page_success_response_function, get_messages_for_page_error_response_function);
}
