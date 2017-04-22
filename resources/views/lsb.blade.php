@extends('layouts.layout')
@section('title','LSB encode')
@section('javascript')
    @parent
    <script>
        var analyseUrlEncode = '{{route('lsb_encode')}}';
        var analyseUrlDecode = '{{route('lsb_decode')}}';
    </script>
    <script src="{{asset('js/encryption.js')}}"></script>
    <script src="{{asset('js/lsb_decode.js')}}"></script>
    @stop

@section('content')
    <ul class="nav nav-tabs">
        <li class="active"><a data-toggle="tab" href="#encode">LSB encode</a></li>
        <li><a data-toggle="tab" href="#decode">LSB decode</a></li>
    </ul>
    <div class="tab-content">
        <div id="encode" class="tab-pane fade in active">
            <div style="margin: 20px">
                <h3>LSB encode</h3>
                <div id="LSBEncode">
                    <div style="margin: 20px">
                        <div class="row">
                            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6" style="width: 300px">
                                <label>
                                    <div class="btn btn-default" v-if="pictures.original.length == 0">
                                        Download picture
                                    </div>
                                    <div v-if="pictures.original.length > 0">
                                        <img style="max-width: 300px" v-bind:src="pictures.original">
                                    </div>
                                    <input style="display:none" type="file" v-on:change="onImageChangeOrig($event)">
                                </label>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6" style="width: 300px" v-for="(picture, keyPicture) in pictures.containers">
                                <label>
                                    <div v-if="picture.base64Picture.length > 0">
                                        <img style="max-width: 300px" v-bind:src="picture.base64Picture">
                                    </div>
                                    <input style="display:none" type="file">
                                </label>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-8 col-xs-6" >
                    <textarea id="textarea" class="form-control" rows="5" v-model="text" style="max-width:100%;">
                    </textarea>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-8 col-xs-6" >
                                Max chars: <p style="color: red">@{{ maxlength }}</p>
                                Your chars: <p style="color: green">@{{ lengthText }}</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col btn btn-danger" v-on:click="sendOnSever(event)">
                                Decode
                            </div>
                        </div>
                    </div>
                    <div id="vue-pages-loader" v-show="loading">
                        <div id="loader"></div>
                        <br>
                    </div>
                    <br>
                </div>
            </div>
        </div>
        <div id="decode" class="tab-pane fade">
            <div style="margin: 20px">
                <h3>LSB decoder</h3>
                <div id="LSBDecode">
                    <div style="margin: 20px">
                        <div class="row">
                            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6" style="width: 300px">
                                <label>
                                    <div class="btn btn-default" v-if="pictures.original.length == 0">
                                        Download picture
                                    </div>
                                    <div v-if="pictures.original.length > 0">
                                        <img style="max-width: 300px" v-bind:src="pictures.original">
                                    </div>
                                    <input style="display:none" type="file" v-on:change="onImageChangeOrig($event)">
                                </label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col btn btn-danger" v-on:click="sendOnSever(event)">
                                Analyze
                            </div>
                        </div>
                    </div>
                    <div id="vue-pages-loader" v-show="loading">
                        <div id="loader"></div>
                        <br>
                    </div>
                    <br>
                    <div v-if="text">
                        @{{ text }}
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