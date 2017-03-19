@extends('layouts.layout')
@section('javascript')
    @parent
    <script>
        var cryptoData = {!! \GuzzleHttp\json_encode($crypto) !!};
    </script>
    <script src="{{asset('js/charts.js')}}"></script>
    <script src="{{asset('js/gallery.js')}}"></script>
    @stop

@section('content')

<ul class="nav nav-tabs">
    <li class="active"><a data-toggle="tab" href="#home">LSB example</a></li>
    <li><a data-toggle="tab" href="#menu1">Analyser</a></li>
</ul>
<div class="tab-content">
    <div id="home" class="tab-pane fade in active">
        <table>
            <tr>
                <td>
                    <img src="{{$originalSrc}}">
                </td>
                @foreach($crypto['LSB']['images'] as $image)
                    <td>
                        <img src="{{$image}}">
                    </td>
                @endforeach
            </tr>
            <tr>
                @foreach($crypto['LSB']['images'] as $image => $key)
                    <td>
                        {{$key}}
                    </td>
                @endforeach
                <td>
                    original
                </td>
            </tr>
        </table>
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
    <div id="menu1" class="tab-pane fade">
        <h3>Analyzer</h3>
        <div id="gallery">
            @{{ message }}
            <div class="upload col" v-for="(keyPicture, picture) in pictures">
                <label>
                    <svg class="fitPicture" v-if="pictures['containers'][keyPicture].length == 0"  width="100" height="100">
                        <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#add-image"></use>
                    </svg>
                    <div v-if="pictures['containers'][keyPicture].length > 0">
                        <img class="fitPicture" style="object-fit: cover; width: 100%;" id="image" v-bind:src="pictures['containers'][keyPicture]">
                    </div>
                    <div v-if="pictures['containers'][keyPicture]['id']">
                        <img class="fitPicture" style="object-fit: cover; width: 100%;" id="image" v-bind:src="pictures['containers'][keyPicture]['small_picture_url']">
                    </div>
                    <input style="display:none" id="uploadPicture" class="uploadPicture" type="file" v-on:change="onImageChange($event, keyPicture)" name="image">
                </label>
                <a v-if="pictures['containers'][keyPicture].length != 0" href="#" v-on:click="deletePicture($event, keyPicture)">
                    Delete
                </a>
            </div>
        </div>
    </div>
</div>
@stop