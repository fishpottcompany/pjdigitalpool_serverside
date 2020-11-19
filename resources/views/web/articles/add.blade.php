<?php
$active_page = "Articles";
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
                    <h4 class="card-title">Add Article</h4>
                    <div class="row">
                        <div class="col-sm-12 col-xs-12">
                            <div class="d-flex justify-content-center">
                                <div id="loader" class="customloader" style="display: none;"></div>
                            </div> 
                            <form id="form"  enctype="multipart/form-data">
                                <div class="form-group">
                                    <label for="article_type">Article Type</label>
                                    <select type="text" required id="article_type" name="article_type" class="form-control">
                                        <option value="HERALD OF GLORY">HERALD OF GLORY</option>
                                        <option value="SPECIAL ARTICLE">SPECIAL ARTICLE</option>
                                        <option value="GLORY NEWS">GLORY NEWS</option>
                                        <option value="BIBLE READING PLAN">BIBLE READING PLAN</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="article_title">Article Title</label>
                                    <input type="text" maxlength="30" required id="article_title" name="article_title" class="form-control" placeholder="Enter Article Title">
                                </div>
                                <div class="form-group">
                                    <label for="article_body">Type The Article Description</label>
                                    <textarea type="text" required id="article_body" name="article_body" class="form-control" placeholder="Type Article"></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="article_image">Article Cover Art Image</label>
                                    <input type="file" required id="article_image" name="article_image"  accept="image/jpeg" class="form-control">
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
<script src="/js/custom/web/articles/articles.js"></script>
@endsection