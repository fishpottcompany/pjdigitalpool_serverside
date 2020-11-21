<?php
//phpinfo(); exit;
$active_page = "Audios";
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
<div class="main-content"> 
    <!-- Basic Form area Start -->
    <div class="container-fluid">
        <!-- Form row -->
        <div class="row">
            <div class="col-xl-12 box-margin height-card">
                <div class="card card-body">
                    <h4 class="card-title">Add Audio</h4>
                    <div class="row">
                        <div class="col-sm-12 col-xs-12">
                            <div class="d-flex justify-content-center">
                                <div id="loader" class="customloader" style="display: none;"></div>
                            </div> 
                            <form id="form_nope" method="POST" action="{{route('add_audio')}}"  enctype="multipart/form-data">

                                @csrf
                                <div class="form-group">
                                    <label for="audio_name">Audio Title</label>
                                    <input type="text" required id="audio_name" name="audio_name" class="form-control" placeholder="Enter Audio Title">
                                </div>
                                <div class="form-group">
                                    <label for="audio_description">Audio Description</label>
                                    <input type="text" required id="audio_description" name="audio_description" class="form-control" placeholder="Enter Audio Description">
                                </div>
                                <div class="form-group">
                                    <label for="audio_image">Audio Cover Art Image</label>
                                    <input type="file" required id="audio_image" name="audio_image"  accept="image/jpeg" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="audio_mp3">Audio MP3</label>
                                    <input type="file" required id="audio_mp3" name="audio_mp3"   accept="audio/mpeg3" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="user_phone_number">Your Phone Number</label>
                                    <input type="text" required id="user_phone_number" name="user_phone_number"  class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="password">Your Password</label>
                                    <input type="password" required id="password" name="password" class="form-control">
                                </div>
                                <button type="submit" class="btn btn-primary mr-2">Submit</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- end row -->
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
                    <div class="fotter-icon text-center">
                        <a href="#" class="action-item mr-2" data-toggle="tooltip" title="Facebook">
                            <i class="fa fa-facebook" aria-hidden="true"></i>
                        </a>
                        <a href="#" class="action-item mr-2" data-toggle="tooltip" title="Twitter">
                            <i class="fa fa-twitter" aria-hidden="true"></i>
                        </a>
                        <a href="#" class="action-item mr-2" data-toggle="tooltip" title="Pinterest">
                            <i class="fa fa-pinterest-p" aria-hidden="true"></i>
                        </a>
                        <a href="#" class="action-item mr-2" data-toggle="tooltip" title="Instagram">
                            <i class="fa fa-instagram" aria-hidden="true"></i>
                        </a>
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
<script src="/js/default-assets/basic-form.js"></script>
<script src="/js/default-assets/file-upload.js"></script>

<!-- CUSTOMJS -->
<script src="/js/custom/web/audios/audios.js"></script>

@if ($message = Session::get('success'))
<script>
    show_notification("msg_holder", "success", "Success:", "{{$message}}");
</script>
@endif
@if ($message = Session::get('fail'))
<script>
    show_notification("msg_holder", "danger", "Error:", "{{$message}}");
</script>
@endif
@endsection