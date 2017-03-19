@extends('layouts.layout')
@section('javascript')
    @parent
    <script>
        {{--var cryptoData = {!! \GuzzleHttp\json_encode($crypto) !!};--}}
        var analyseUrl = '{{route('analyze')}}';
    </script>
    {{--<script src="{{asset('js/charts.js')}}"></script>--}}
    <script src="{{asset('js/gallery.js')}}"></script>
    @stop

@section('content')

<ul class="nav nav-tabs">
    <li><a data-toggle="tab" href="#home">LSB example</a></li>
    <li class="active"><a data-toggle="tab" href="#menu1">Analyser</a></li>
</ul>
<div class="tab-content">
    <div id="home" class="tab-pane fade">
        {{--<table>--}}
            {{--<tr>--}}
                {{--<td>--}}
                    {{--<img src="{{$originalSrc}}">--}}
                {{--</td>--}}
                {{--@foreach($crypto['LSB']['images'] as $image)--}}
                    {{--<td>--}}
                        {{--<img src="{{$image}}">--}}
                    {{--</td>--}}
                {{--@endforeach--}}
            {{--</tr>--}}
            {{--<tr>--}}
                {{--@foreach($crypto['LSB']['images'] as $image => $key)--}}
                    {{--<td>--}}
                        {{--{{$key}}--}}
                    {{--</td>--}}
                {{--@endforeach--}}
                {{--<td>--}}
                    {{--original--}}
                {{--</td>--}}
            {{--</tr>--}}
        {{--</table>--}}
        {{--<ul class="nav nav-tabs">--}}
            {{--<li class="active"><a data-toggle="tab" href="#IF">IF</a></li>--}}
            {{--<li><a data-toggle="tab" href="#SNR">SNR</a></li>--}}
            {{--<li><a data-toggle="tab" href="#NC">NC</a></li>--}}
            {{--<li><a data-toggle="tab" href="#NAD">NAD</a></li>--}}
        {{--</ul>--}}
        {{--<div class="tab-content">--}}
            {{--<div id="IF" class="tab-pane fade in active">--}}
                {{--<h3>IF</h3>--}}
                {{--<div id="chart_divIf" style="width: 100%"></div>--}}
            {{--</div>--}}
            {{--<div id="SNR" class="tab-pane fade">--}}
                {{--<h3>SNR</h3>--}}
                {{--<div id="chart_div_snr" style="width: 100%"></div>--}}
            {{--</div>--}}
            {{--<div id="NC" class="tab-pane fade">--}}
                {{--<h3>NC</h3>--}}
                {{--<div id="chart_div_nc" style="width: 100%"></div>--}}
            {{--</div>--}}
            {{--<div id="NAD" class="tab-pane fade">--}}
                {{--<h3>NAD</h3>--}}
                {{--<div id="chart_div_nad" style="width: 100%"></div>--}}
            {{--</div>--}}
        {{--</div>--}}
    </div>
    <div id="menu1" class="tab-pane fade in active">
        <h3>Analyzer</h3>
        <div id="gallery">
            {{--@{{ pictures|json }}--}}
            <div class="container">
                <div class="row">
                    <div class="col col-sm-2" style="width: 300px">
                        <label>
                            <div class="btn btn-default" v-if="pictures.original.length == 0">
                                Download original picture
                            </div>
                            <div v-if="pictures.original.length > 0">
                                <img style="max-width: 300px" v-bind:src="pictures.original">
                            </div>
                            <input style="display:none" type="file" v-on:change="onImageChangeOrig($event)">
                            <label>Original picture</label>
                        </label>
                        <a v-if="pictures.original.length != 0" href="#" v-on:click="deletePictureOrig($event)">
                            Delete
                        </a>
                    </div>
                    <div class="col col-sm-2" style="width: 300px" v-for="(picture, keyPicture) in pictures.containers">
                        <label>
                            <div class="btn btn-default" v-if="picture.base64Picture.length == 0">
                                Download picture
                            </div>
                            <div v-if="picture.base64Picture.length > 0">
                                <img style="max-width: 300px" v-bind:src="picture.base64Picture">
                            </div>
                            <input style="display:none" type="file" v-on:change="onImageChange($event, keyPicture)">
                            <div class="form-group">
                                <label for="bytes">Number bytes:</label>
                                <input v-model="picture.bytes" type="text" class="form-control" id="bytes">
                            </div>
                        </label>
                        <a v-if="picture.base64Picture.length != 0" href="#" v-on:click="deletePicture($event, keyPicture)">
                            Delete
                        </a>
                    </div>
                </div>
                <div class="row">
                    <div class="col btn btn-success" v-on:click="copyPicture()">
                        Add picture
                    </div>
                    <div class="col btn btn-danger" v-on:click="sendOnSever(event)">
                        Analyze
                    </div>
                </div>
            </div>
            <div id="vue-pages-loader" v-show="loading">
                <div id="loader"></div>
                <br>
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