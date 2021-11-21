<?php
$active_page = "Users";
?>
<!-- INCLUDING THE FILE THAT HOLDS THE CORE STRUCTURE OF THE PAGE -->
@extends('web.app')

<!-- INCLUDING CUSTOM SCRIPTS AND STYLES -->
@section('top_scripts_and_styles')
    <link rel="stylesheet" href="/css/custom.css">
    <script src="/js/custom/web/config.js"></script>
    <script src="/js/custom/web/auth.js"></script>
@endsection()

@section('main_content_and_footer')
            <!-- Main Content Area -->
            <div class="main-content">
                <div class="data-table-area">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12 mb-30">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title">--Users<span style="color: rgb(231, 191, 31);" id="user_count"></span></h6>
                                        <div class="d-flex justify-content-center">
                                            <div id="loader" class="customloader"></div>
                                        </div> 
                                        <div class="table-responsive" >
                                            <table id="dataTableExample"  style="display: none;" class="table">
                                                <thead>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Full Name</th>
                                                        <th>Country</th>
                                                        <th>Phone</th>
                                                        <th>Email</th>
                                                        <th>Joined-On</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="table_body_list">
                                                    
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            <!-- Footer Area -->
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <!-- Footer Area -->
                        <footer class="footer-area d-sm-flex justify-content-center align-items-center justify-content-between">
                            <!-- Copywrite Text -->
                            <div class="copywrite-text">
                                <p>Created by @<a href="#">FishPotLLC</a></p>
                            </div>
                        </footer>
                    </div>
                </div>
            </div>
        </div>

@endsection

@section('bottom_scripts')
    

<script src="/js/core.js"></script>
<script src="/js/bundle.js"></script>

<!-- Inject JS -->
<script src="/js/default-assets/setting.js"></script>
<script src="/js/default-assets/active.js"></script>

<!-- Custom js -->
<script src="js/default-assets/demo.datatable-init.js"></script>

<!-- CUSTOMJS -->
<script src="/js/custom/web/users/users.js"></script>
<script type="text/javascript">
    get_messages_for_page('<?php if(isset($_GET["page"]) && intval($_GET["page"]) > 0){echo intval($_GET["page"]);} else {echo "1"; } ?>');
  </script>

@endsection