@extends('layouts.layout')
@section('title','Steganography analyzer')
@section('javascript')
    @parent
    <script>
        var analyseUrl = '{{route('analyze')}}';
    </script>
    <script src="{{asset('js/gallery.js')}}"></script>
    @stop

@section('content')
<div class="container">
    <h3>Steganography Analyzer</h3>
    <div id="gallery">
        <div>
            <div class="row">
                <div class="col-lg-3">
                    <div class="panel panel-default">
                        <div class="panel-heading">Original picture
                            <a style="margin-left: 8px" v-if="pictures.original.length != 0" href="#" v-on:click="deletePictureOrig($event)">
                                <span class="label label-default">Delete</span>
                            </a>
                        </div>
                        <div class="panel-body">
                            <label>
                                <div class="btn btn-default" v-if="pictures.original.length == 0">
                                    Upload
                                </div>
                                <div v-if="pictures.original.length > 0">
                                    <div class="thumbnail">
                                        <img v-bind:src="pictures.original">
                                    </div>
                                </div>
                                <input style="display:none" type="file" v-on:change="onImageChangeOrig($event)">
                            </label>
                            <br><br>
                                <div class="col btn btn-success" v-on:click="copyPicture()">
                                    Add picture
                                </div>
                                <div class="col btn btn-danger" v-on:click="sendOnSever(event)">
                                    Analyze
                                </div>

                        </div>
                    </div>
                </div>
                <div class="col-lg-3" v-for="(picture, keyPicture) in pictures.containers">
                    <div class="panel panel-default">
                        <div class="panel-heading">Crypted picture
                            <a style="margin-left: 8px" v-if="picture.base64Picture.length != 0" href="#" v-on:click="deletePicture($event, keyPicture)">
                                <span class="label label-default">Delete</span>
                            </a>
                        </div>
                        <div class="panel-body">
                            <label>
                                <div class="btn btn-default" v-if="picture.base64Picture.length == 0">
                                    Download picture
                                </div>
                                <div v-if="picture.base64Picture.length > 0">
                                    <div class="thumbnail">
                                        <img v-bind:src="picture.base64Picture">
                                    </div>
                                </div>
                                <input style="display:none" type="file" v-on:change="onImageChange($event, keyPicture)">
                                <div class="form-group">
                                    <label for="bytes">Number bytes:</label>
                                    <input v-model="picture.bytes" type="text" class="form-control" id="bytes">
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="vue-pages-loader" v-show="loading">
            <div id="loader"></div>
            <br>
        </div>
        <br>
        <div v-if="errors">
            <ul>
                <li v-for="error in errors">
                    @{{ error }}
                </li>
            </ul>
        </div>
        <div v-if="methods.length != 0">
            <ul class="nav nav-tabs">
                <li class="active"><a data-toggle="tab" href="#IF">IF</a></li>
                <li><a data-toggle="tab" href="#SNR">SNR</a></li>
                <li><a data-toggle="tab" href="#NC">NC</a></li>
                <li><a data-toggle="tab" href="#NAD">NAD</a></li>
            </ul>
            <div class="tab-content">
                <div id="IF" class="tab-pane fade in active">
                    <h3>IF</h3>
                    <div id="chart_divIf" style="width: 100%"></div>
                </div>
                <div id="SNR" class="tab-pane fade">
                    <h3>SNR</h3>
                    <div id="chart_div_snr" style="width: 100%"></div>
                </div>
                <div id="NC" class="tab-pane fade">
                    <h3>NC</h3>
                    <div id="chart_div_nc" style="width: 100%"></div>
                </div>
                <div id="NAD" class="tab-pane fade">
                    <h3>NAD</h3>
                    <div id="chart_div_nad" style="width: 100%"></div>
                </div>
            </div>
        </div>
    </div>
</div>

    <style>
        #vue-pages-loader {
            clear: both;
           }
        #vue-pages-loader > div {
            width: 40px;
            height: 40px;
            margin: 0 auto;
            background: url("/assets/img/loader.svg") no-repeat center; }
    </style>
@stop