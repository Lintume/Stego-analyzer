@extends('layouts.layout')
@section('title','LSB crypt decoder')
@section('javascript')
    @parent
    <script>
        var analyseUrl = '{{route('lsb_decode_crypt')}}';
    </script>
    <script src="{{asset('js/lsb_decode_crypt.js')}}"></script>
    @stop

@section('content')
<div style="margin: 20px">
    <h3>LSB decoder</h3>
    <div id="gallery">
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
                <div class="col-lg-4 col-md-4 col-sm-8 col-xs-6" >
                    <label for="pass">Password:</label>
                    <input id="pass" class="form-control" v-model="password">
                </div>
            </div>
            <br>
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
        <div v-if="text">
            @{{ text }}
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