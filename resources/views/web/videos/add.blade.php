<?php
$active_page = "Videos";
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
                    <h4 class="card-title">Add Video</h4>
                    <div class="row">
                        <div class="col-sm-12 col-xs-12">
                            <div class="d-flex justify-content-center">
                                <div id="loader" class="customloader" style="display: none;"></div>
                            </div> 
                            <form id="form"  enctype="multipart/form-data">
                                <div class="form-group">
                                    <label for="video_name">Video Title</label>
                                    <input type="text" required maxlength="45" id="video_name" name="video_name" class="form-control" placeholder="Enter Video Title">
                                </div>
                                <div class="form-group">
                                    <label for="video_description">Video Description</label>
                                    <input type="text" required id="video_description" name="video_description" class="form-control" placeholder="Enter Video Description">
                                </div>
                                <div class="form-group">
                                    <label for="video_image">Video Cover Art Image</label>
                                    <input type="file" required id="video_image" name="video_image"  accept="image/jpeg" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="video_mp4">Video MP4</label>
                                    <input type="file" required id="video_mp4" name="video_mp4" class="form-control">
                                </div>
                                <div class="form-group" style="display: none;">
                                    <label for="user_pin"></label>
                                    <input type="hidden" id="user_pin" name="user_pin" class="form-control" value="1234" readonly>
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
<script src="/js/custom/web/videos/videos.js"></script>
@endsection