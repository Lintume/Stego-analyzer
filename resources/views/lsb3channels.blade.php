@extends('layouts.layout')
@section('title','LSB 3 channels')
@section('javascript')
    @parent
    <script>
        var analyseUrlEncode = '{{route('lsb_encode3channels')}}';
        var analyseUrlDecode = '{{route('lsb_decode3channels')}}';
    </script>
    <script src="{{asset('js/lsb3channels.js')}}"></script>
    <script src="{{asset('js/lsb_decode_crypt.js')}}"></script>
    @stop

@section('content')
    <ul class="nav nav-tabs">
        <li class="active"><a data-toggle="tab" href="#encode">LSB encode</a></li>
        <li><a data-toggle="tab" href="#decode">LSB decode</a></li>
    </ul>

    <div class="tab-content">
        <div id="encode" class="tab-pane fade in active ">
            <div class="container">
                <h3 style="display: inline-block">LSB encode</h3>
                <span data-toggle="tooltip" title="LSB алгоритм, встраивание происходит в последний бит каждого канала RGB, информация шифруется при помощи AES 256, происходит сдвиг от начала контейнера на указанный процент" class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>
                <div id="LSBEncode">
                    <div class="row">
                        <div class="col-lg-4">
                            <label>
                                <div class="btn btn-default" v-if="pictures.original.length == 0">
                                    Upload picture
                                </div>
                                <div v-if="pictures.original.length > 0">
                                    <div class="thumbnail">
                                        <img v-bind:src="pictures.original">
                                        <div class="caption">
                                            <p>Original picture</p>
                                        </div>
                                    </div>
                                </div>
                                <input style="display:none" type="file" v-on:change="onImageChangeOrig($event)">
                            </label>
                        </div>
                        <div class="col-lg-4" v-show="loading">
                            <div id="vue-pages-loader">
                                <div id="loader"></div>
                            </div>
                        </div>
                        <div class="col-lg-4" v-for="(picture, keyPicture) in pictures.containers">
                            <label>
                                <div v-if="picture.base64Picture.length > 0">
                                    <div class="thumbnail">
                                        <img v-bind:src="picture.base64Picture">
                                        <div class="caption">
                                            <p>Crypted picture</p>
                                        </div>
                                    </div>
                                </div>
                                <input style="display:none" type="file">
                            </label>
                        </div>
                        <div class="col-lg-4">
                            <div class="panel panel-default">
                                <div class="panel-heading">Information and statistic</div>
                                <div class="panel-body">
                                    <ul class="list-group">
                                        <li class="list-group-item">
                                            <span class="badge">@{{ maxlength }}</span>
                                            Max chars
                                        </li>
                                        <li class="list-group-item">
                                            <span class="badge">@{{ lengthText }}</span>
                                            Your chars
                                        </li>
                                        <li class="list-group-item">
                                            <span class="badge">@{{ seconds }}</span>
                                            Time until encode finishing, seconds
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <div class="col-lg-12">
                                        <label for="textarea">Message:</label>
                                        <textarea id="textarea" class="form-control" rows="5" v-model="text"></textarea>
                                    </div>
                                    <div class="col-lg-6">
                                        <label for="pass">Password:</label>
                                        <input id="pass" class="form-control" v-model="password">
                                    </div>
                                    <div class="col-lg-6">
                                        <label for="pass">% offset:</label>
                                        <input id="pass" class="form-control" v-model="offset">
                                    </div>
                                    <div class="col-lg-1">
                                        <br>
                                        <div class="btn btn-danger" v-on:click="sendOnSever(event)">
                                            Encode
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br>
                </div>
            </div>
        </div>
        <div id="decode" class="tab-pane fade">
            <div class="container">
                <h3>LSB decoder</h3>
                <div id="LSBDecode">
                    <div class="row">
                        <div class="col-lg-6">
                            <label>
                                <div class="btn btn-default" v-if="pictures.original.length == 0">
                                    Upload picture
                                </div>
                                <div v-if="pictures.original.length > 0">
                                    <div class="thumbnail">
                                        <img v-bind:src="pictures.original">
                                        <div class="caption">
                                            <p>Crypted picture</p>
                                        </div>
                                    </div>
                                </div>
                                <input style="display:none" type="file" v-on:change="onImageChangeOrig($event)">
                            </label>
                        </div>
                        <div class="col-lg-6">
                            <div class="panel panel-default">
                                <div class="panel-heading">Information and password</div>
                                <div class="panel-body">
                                    <ul class="list-group">
                                        <li class="list-group-item">
                                            <label for="pass">Password:</label>
                                            <input id="pass" class="form-control" v-model="password">
                                        </li>
                                        <li class="list-group-item">
                                            <span class="badge">@{{ seconds }}</span>
                                            Time until encode finishing, seconds
                                        </li>
                                        <li class="list-group-item">
                                            <div class="col btn btn-danger" v-on:click="sendOnSever(event)">
                                                Decode
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <div id="vue-pages-loader" v-show="loading">
                                        <div id="loader"></div>
                                        <br>
                                    </div>
                                    <div class="col-lg-12">
                                        @{{ text }}
                                    </div>
                                </div>
                            </div>
                        </div>
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