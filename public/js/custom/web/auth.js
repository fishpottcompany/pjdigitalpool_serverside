if(!user_has_api_token()){
    redirect_to_next_page(web_login_url, false);
}