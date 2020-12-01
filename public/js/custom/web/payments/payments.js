/*
|--------------------------------------------------------------------------
| GETTING THE LIST OF ADMINS
|--------------------------------------------------------------------------
|--------------------------------------------------------------------------
|
*/
function get_payments_for_page_success_response_function(response)
{
    fade_out_loader_and_fade_in_form("loader", "dataTableExample"); 
    if(response.data.data.length > 0){
        for (let index = 0; index < response.data.data.length; index++) {
            const element = response.data.data[index];
            if(element.message_type == "[not set]"){
                var status = '<span class="u-label bg-warning text-white">Pending</span>';
            }

            $('#table_body_list').append(
                '<tr style="cursor: ;" class="administrator">'
                + '<td>' + element.transaction_id +' </td>'
                + '<td>' + element.transaction_ext_id +' </td>'
                + '<td>Ghc' + element.amount +' </td>'
                + '<td>' + status +' </td>'
                + '<td>' + element.reference +' </td>'
                + '<td>' + element.payer_name + '</td>'
                + '<td>' + element.payer_phone + '</td>'
                + '<td>' + element.payer_email + '</td>'
                + '<td>' + element.payer_country + '</td>'
                + '<td>' + element.payment_type + '</td>'
                + '<td>' + element.created_at + '</td>'
                + '</tr>'
            );
        }
        document.getElementById("dataTableExample").style.display = "";
    } else {
        show_notification("msg_holder", "danger", "", "No messages found");
    }
}

function get_payments_for_page_error_response_function(errorThrown)
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
function get_payments_for_page(page_number)
{
    fade_in_loader_and_fade_out_form("loader", "dataTableExample");   
    var bearer = "Bearer " + localStorage.getItem("admin_access_token"); 
    send_restapi_request_to_server_from_form("get", api_payments_list_url, bearer, "", "json", get_payments_for_page_success_response_function, get_payments_for_page_error_response_function);
}
