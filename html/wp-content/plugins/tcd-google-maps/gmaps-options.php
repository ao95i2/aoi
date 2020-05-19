  <script type="text/javascript">
    var currentApiKey;
    var app = angular.module('GMapsEasyApp', ['ui.slider'])
    app.controller('GMapsEasyController', function($scope) {
      $scope.minWidth = 40;
      $scope.maxWidth = 2048;
      $scope.fullWidth = false;
      $scope.width = 300;

      $scope.minHeight = 40;
      $scope.maxHeight = 800;
      $scope.height = 300;

      $scope.address = "大阪府大阪市 北区中之島1丁目3-20";
      $scope.apiKey = "";
      $scope.theme = "";
      $scope.borders = false;
      $scope.centered = false;
      $scope.draggable = true;
      $scope.controls = true;

      $scope.zoom = 18;

      var settings = localStorage.getItem('settings')
      if( settings ){
        settings = JSON.parse(settings)

        $scope.address = settings.address,
        $scope.apiKey = settings.apiKey,
        $scope.fullWidth = settings.fullWidth,
        $scope.width = settings.width
        $scope.height = settings.height
        $scope.theme = settings.theme
        $scope.borders = settings.borders
        $scope.centered = settings.centered
        $scope.zoom = settings.zoom
        $scope.draggable = settings.draggable
        $scope.controls = settings.controls
      }

      $scope.$watchGroup(['zoom', 'draggable', 'width', 'fullWidth', 'height', 'apiKey', 'address', 'theme', 'borders', 'centered', 'controls'], function() {

        localStorage.setItem('settings', JSON.stringify({
          address: $scope.address,
          apiKey: $scope.apiKey,
          fullWidth: $scope.fullWidth,
          width: $scope.width,
          height: $scope.height,
          theme: $scope.theme,
          borders: $scope.borders,
          centered: $scope.centered,
          zoom: $scope.zoom,
          draggable: $scope.draggable,
          controls: $scope.controls,
        }))

        $scope.result = [
          '[map ',
          'address="',
          $scope.address,
          '" width="',
          $scope.fullWidth ? '100%' : $scope.width + 'px',
          '" height="',
          $scope.height + 'px',
          '" api="',
          $scope.apiKey,
          '" theme="',
          $scope.theme,
          '" class="',
          $scope.borders ? 'dp-light-border-map' : '',
          ' ',
          $scope.centered ? 'dp-map-centered' : '',
          '" zoom="',
          $scope.zoom,
          '" draggable="',
          $scope.draggable ? "true" : "false",
          '" controls="',
          $scope.controls,
          '"]'
        ].join('');

        var width = $scope.fullWidth ? '100%' : $scope.width + 'px';
        var extraClasses = [
          $scope.borders ? 'dp-light-border-map' : '',
          ' ',
          $scope.centered ? 'dp-map-centered' : '',
        ].join('')

        if( $scope.theme == "black" ){
          var theme = [{"featureType":"all","elementType":"labels.text.fill","stylers":[{"saturation":36},{"color":"#000000"},{"lightness":40}]},{"featureType":"all","elementType":"labels.text.stroke","stylers":[{"visibility":"on"},{"color":"#000000"},{"lightness":16}]},{"featureType":"all","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"administrative","elementType":"geometry.fill","stylers":[{"color":"#000000"},{"lightness":20}]},{"featureType":"administrative","elementType":"geometry.stroke","stylers":[{"color":"#000000"},{"lightness":17},{"weight":1.2}]},{"featureType":"landscape","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":20}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":21}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#000000"},{"lightness":17}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#000000"},{"lightness":29},{"weight":0.2}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":18}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":16}]},{"featureType":"transit","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":19}]},{"featureType":"water","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":17}]}];
        } else if( $scope.theme == "light" ){
          var theme = [{"featureType":"water","elementType":"geometry","stylers":[{"color":"#e9e9e9"},{"lightness":17}]},{"featureType":"landscape","elementType":"geometry","stylers":[{"color":"#f5f5f5"},{"lightness":20}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#ffffff"},{"lightness":17}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#ffffff"},{"lightness":29},{"weight":0.2}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"color":"#ffffff"},{"lightness":18}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"color":"#ffffff"},{"lightness":16}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#f5f5f5"},{"lightness":21}]},{"featureType":"poi.park","elementType":"geometry","stylers":[{"color":"#dedede"},{"lightness":21}]},{"elementType":"labels.text.stroke","stylers":[{"visibility":"on"},{"color":"#ffffff"},{"lightness":16}]},{"elementType":"labels.text.fill","stylers":[{"saturation":36},{"color":"#333333"},{"lightness":40}]},{"elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"geometry","stylers":[{"color":"#f2f2f2"},{"lightness":19}]},{"featureType":"administrative","elementType":"geometry.fill","stylers":[{"color":"#fefefe"},{"lightness":20}]},{"featureType":"administrative","elementType":"geometry.stroke","stylers":[{"color":"#fefefe"},{"lightness":17},{"weight":1.2}]}];
        } else if( $scope.theme == "paper" ){
          var theme = [{"featureType":"administrative","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"landscape","elementType":"all","stylers":[{"visibility":"simplified"},{"hue":"#0066ff"},{"saturation":74},{"lightness":100}]},{"featureType":"poi","elementType":"all","stylers":[{"visibility":"simplified"}]},{"featureType":"road","elementType":"all","stylers":[{"visibility":"simplified"}]},{"featureType":"road.highway","elementType":"all","stylers":[{"visibility":"off"},{"weight":0.6},{"saturation":-85},{"lightness":61}]},{"featureType":"road.highway","elementType":"geometry","stylers":[{"visibility":"on"}]},{"featureType":"road.arterial","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"road.local","elementType":"all","stylers":[{"visibility":"on"}]},{"featureType":"transit","elementType":"all","stylers":[{"visibility":"simplified"}]},{"featureType":"water","elementType":"all","stylers":[{"visibility":"simplified"},{"color":"#5f94ff"},{"lightness":26},{"gamma":5.86}]}];
        } else if( $scope.theme == "discreet" ){
          var theme = [{"featureType":"administrative","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"administrative","elementType":"geometry.stroke","stylers":[{"visibility":"on"}]},{"featureType":"administrative","elementType":"labels","stylers":[{"visibility":"on"},{"color":"#716464"},{"weight":"0.01"}]},{"featureType":"administrative.country","elementType":"labels","stylers":[{"visibility":"on"}]},{"featureType":"landscape","elementType":"all","stylers":[{"visibility":"simplified"}]},{"featureType":"landscape.natural","elementType":"geometry","stylers":[{"visibility":"simplified"}]},{"featureType":"landscape.natural.landcover","elementType":"geometry","stylers":[{"visibility":"simplified"}]},{"featureType":"poi","elementType":"all","stylers":[{"visibility":"simplified"}]},{"featureType":"poi","elementType":"geometry.fill","stylers":[{"visibility":"simplified"}]},{"featureType":"poi","elementType":"geometry.stroke","stylers":[{"visibility":"simplified"}]},{"featureType":"poi","elementType":"labels.text","stylers":[{"visibility":"simplified"}]},{"featureType":"poi","elementType":"labels.text.fill","stylers":[{"visibility":"simplified"}]},{"featureType":"poi","elementType":"labels.text.stroke","stylers":[{"visibility":"simplified"}]},{"featureType":"poi.attraction","elementType":"geometry","stylers":[{"visibility":"on"}]},{"featureType":"road","elementType":"all","stylers":[{"visibility":"on"}]},{"featureType":"road.highway","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"road.highway","elementType":"geometry","stylers":[{"visibility":"on"}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"visibility":"on"}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"visibility":"simplified"},{"color":"#a05519"},{"saturation":"-13"}]},{"featureType":"road.local","elementType":"all","stylers":[{"visibility":"on"}]},{"featureType":"transit","elementType":"all","stylers":[{"visibility":"simplified"}]},{"featureType":"transit","elementType":"geometry","stylers":[{"visibility":"simplified"}]},{"featureType":"transit.station","elementType":"geometry","stylers":[{"visibility":"on"}]},{"featureType":"water","elementType":"all","stylers":[{"visibility":"simplified"},{"color":"#84afa3"},{"lightness":52}]},{"featureType":"water","elementType":"geometry","stylers":[{"visibility":"on"}]},{"featureType":"water","elementType":"geometry.fill","stylers":[{"visibility":"on"}]}];
        } else if( $scope.theme == "flatpale" ){
          var theme = [{"featureType":"administrative","elementType":"labels.text.fill","stylers":[{"color":"#6195a0"}]},{"featureType":"administrative.province","elementType":"geometry.stroke","stylers":[{"visibility":"off"}]},{"featureType":"landscape","elementType":"geometry","stylers":[{"lightness":"0"},{"saturation":"0"},{"color":"#f5f5f2"},{"gamma":"1"}]},{"featureType":"landscape.man_made","elementType":"all","stylers":[{"lightness":"-3"},{"gamma":"1.00"}]},{"featureType":"landscape.natural.terrain","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"poi","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"poi.park","elementType":"geometry.fill","stylers":[{"color":"#bae5ce"},{"visibility":"on"}]},{"featureType":"road","elementType":"all","stylers":[{"saturation":-100},{"lightness":45},{"visibility":"simplified"}]},{"featureType":"road.highway","elementType":"all","stylers":[{"visibility":"simplified"}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#fac9a9"},{"visibility":"simplified"}]},{"featureType":"road.highway","elementType":"labels.text","stylers":[{"color":"#4e4e4e"}]},{"featureType":"road.arterial","elementType":"labels.text.fill","stylers":[{"color":"#787878"}]},{"featureType":"road.arterial","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"all","stylers":[{"visibility":"simplified"}]},{"featureType":"transit.station.airport","elementType":"labels.icon","stylers":[{"hue":"#0a00ff"},{"saturation":"-77"},{"gamma":"0.57"},{"lightness":"0"}]},{"featureType":"transit.station.rail","elementType":"labels.text.fill","stylers":[{"color":"#43321e"}]},{"featureType":"transit.station.rail","elementType":"labels.icon","stylers":[{"hue":"#ff6c00"},{"lightness":"4"},{"gamma":"0.75"},{"saturation":"-68"}]},{"featureType":"water","elementType":"all","stylers":[{"color":"#eaf6f8"},{"visibility":"on"}]},{"featureType":"water","elementType":"geometry.fill","stylers":[{"color":"#c7eced"}]},{"featureType":"water","elementType":"labels.text.fill","stylers":[{"lightness":"-49"},{"saturation":"-53"},{"gamma":"0.79"}]}];
        } else if( $scope.theme == "justplaces" ){
          var theme = [{"featureType":"road","elementType":"geometry","stylers":[{"visibility":"off"}]},{"featureType":"poi","elementType":"geometry","stylers":[{"visibility":"off"}]},{"featureType":"landscape","elementType":"geometry","stylers":[{"color":"#fffffa"}]},{"featureType":"water","stylers":[{"lightness":50}]},{"featureType":"road","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"transit","stylers":[{"visibility":"off"}]},{"featureType":"administrative","elementType":"geometry","stylers":[{"lightness":40}]}];
        } else if( $scope.theme == "cleancut" ){
          var theme = [{"featureType":"road","elementType":"geometry","stylers":[{"lightness":100},{"visibility":"simplified"}]},{"featureType":"water","elementType":"geometry","stylers":[{"visibility":"on"},{"color":"#C6E2FF"}]},{"featureType":"poi","elementType":"geometry.fill","stylers":[{"color":"#C5E3BF"}]},{"featureType":"road","elementType":"geometry.fill","stylers":[{"color":"#D1D1B8"}]}];
        } else if( $scope.theme == "evenlighter" ){
          var theme = [{"featureType":"administrative","elementType":"labels.text.fill","stylers":[{"color":"#6195a0"}]},{"featureType":"landscape","elementType":"all","stylers":[{"color":"#f2f2f2"}]},{"featureType":"landscape","elementType":"geometry.fill","stylers":[{"color":"#ffffff"}]},{"featureType":"poi","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"poi.park","elementType":"geometry.fill","stylers":[{"color":"#e6f3d6"},{"visibility":"on"}]},{"featureType":"road","elementType":"all","stylers":[{"saturation":-100},{"lightness":45},{"visibility":"simplified"}]},{"featureType":"road.highway","elementType":"all","stylers":[{"visibility":"simplified"}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#f4d2c5"},{"visibility":"simplified"}]},{"featureType":"road.highway","elementType":"labels.text","stylers":[{"color":"#4e4e4e"}]},{"featureType":"road.arterial","elementType":"geometry.fill","stylers":[{"color":"#f4f4f4"}]},{"featureType":"road.arterial","elementType":"labels.text.fill","stylers":[{"color":"#787878"}]},{"featureType":"road.arterial","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"water","elementType":"all","stylers":[{"color":"#eaf6f8"},{"visibility":"on"}]},{"featureType":"water","elementType":"geometry.fill","stylers":[{"color":"#eaf6f8"}]}];
        } else if( $scope.theme == "ultralight" ){
          var theme = [{"featureType":"water","elementType":"geometry","stylers":[{"color":"#e9e9e9"},{"lightness":17}]},{"featureType":"landscape","elementType":"geometry","stylers":[{"color":"#f5f5f5"},{"lightness":20}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#ffffff"},{"lightness":17}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#ffffff"},{"lightness":29},{"weight":0.2}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"color":"#ffffff"},{"lightness":18}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"color":"#ffffff"},{"lightness":16}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#f5f5f5"},{"lightness":21}]},{"featureType":"poi.park","elementType":"geometry","stylers":[{"color":"#dedede"},{"lightness":21}]},{"elementType":"labels.text.stroke","stylers":[{"visibility":"on"},{"color":"#ffffff"},{"lightness":16}]},{"elementType":"labels.text.fill","stylers":[{"saturation":36},{"color":"#333333"},{"lightness":40}]},{"elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"geometry","stylers":[{"color":"#f2f2f2"},{"lightness":19}]},{"featureType":"administrative","elementType":"geometry.fill","stylers":[{"color":"#fefefe"},{"lightness":20}]},{"featureType":"administrative","elementType":"geometry.stroke","stylers":[{"color":"#fefefe"},{"lightness":17},{"weight":1.2}]}];
        } else if( $scope.theme == "lightmonochrome" ){
          var theme = [{"featureType":"administrative.locality","elementType":"all","stylers":[{"hue":"#2c2e33"},{"saturation":7},{"lightness":19},{"visibility":"on"}]},{"featureType":"landscape","elementType":"all","stylers":[{"hue":"#ffffff"},{"saturation":-100},{"lightness":100},{"visibility":"simplified"}]},{"featureType":"poi","elementType":"all","stylers":[{"hue":"#ffffff"},{"saturation":-100},{"lightness":100},{"visibility":"off"}]},{"featureType":"road","elementType":"geometry","stylers":[{"hue":"#bbc0c4"},{"saturation":-93},{"lightness":31},{"visibility":"simplified"}]},{"featureType":"road","elementType":"labels","stylers":[{"hue":"#bbc0c4"},{"saturation":-93},{"lightness":31},{"visibility":"on"}]},{"featureType":"road.arterial","elementType":"labels","stylers":[{"hue":"#bbc0c4"},{"saturation":-93},{"lightness":-2},{"visibility":"simplified"}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"hue":"#e9ebed"},{"saturation":-90},{"lightness":-8},{"visibility":"simplified"}]},{"featureType":"transit","elementType":"all","stylers":[{"hue":"#e9ebed"},{"saturation":10},{"lightness":69},{"visibility":"on"}]},{"featureType":"water","elementType":"all","stylers":[{"hue":"#e9ebed"},{"saturation":-78},{"lightness":67},{"visibility":"simplified"}]}];
        } else if( $scope.theme == "redhat" ){
          var theme = [{"featureType":"administrative","elementType":"labels.text.fill","stylers":[{"color":"#444444"}]},{"featureType":"landscape","elementType":"all","stylers":[{"color":"#f2f2f2"}]},{"featureType":"poi","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"poi.business","elementType":"geometry.fill","stylers":[{"visibility":"on"}]},{"featureType":"road","elementType":"all","stylers":[{"saturation":-100},{"lightness":45}]},{"featureType":"road.highway","elementType":"all","stylers":[{"visibility":"simplified"}]},{"featureType":"road.arterial","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"water","elementType":"all","stylers":[{"color":"#b4d4e1"},{"visibility":"on"}]}];
        } else if( $scope.theme == "lightgreyandblue" ){
          var theme = [{"featureType":"administrative","elementType":"labels.text.fill","stylers":[{"color":"#444444"}]},{"featureType":"landscape","elementType":"all","stylers":[{"color":"#f2f2f2"}]},{"featureType":"poi","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"road","elementType":"all","stylers":[{"saturation":-100},{"lightness":45}]},{"featureType":"road.highway","elementType":"all","stylers":[{"visibility":"simplified"}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#ffffff"}]},{"featureType":"road.arterial","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"water","elementType":"all","stylers":[{"color":"#dde6e8"},{"visibility":"on"}]}];
        } else if( $scope.theme == "elevation" ){
          var theme = [{"featureType":"administrative","elementType":"labels","stylers":[{"visibility":"on"},{"gamma":"1.82"}]},{"featureType":"administrative","elementType":"labels.text.fill","stylers":[{"visibility":"on"},{"gamma":"1.96"},{"lightness":"-9"}]},{"featureType":"administrative","elementType":"labels.text.stroke","stylers":[{"visibility":"off"}]},{"featureType":"landscape","elementType":"all","stylers":[{"visibility":"on"},{"lightness":"25"},{"gamma":"1.00"},{"saturation":"-100"}]},{"featureType":"poi.business","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"poi.park","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"road","elementType":"geometry.stroke","stylers":[{"visibility":"off"}]},{"featureType":"road","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"road.highway","elementType":"geometry","stylers":[{"hue":"#ffaa00"},{"saturation":"-43"},{"visibility":"on"}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"visibility":"off"}]},{"featureType":"road.highway","elementType":"labels","stylers":[{"visibility":"simplified"},{"hue":"#ffaa00"},{"saturation":"-70"}]},{"featureType":"road.highway.controlled_access","elementType":"labels","stylers":[{"visibility":"on"}]},{"featureType":"road.arterial","elementType":"all","stylers":[{"visibility":"on"},{"saturation":"-100"},{"lightness":"30"}]},{"featureType":"road.local","elementType":"all","stylers":[{"saturation":"-100"},{"lightness":"40"},{"visibility":"off"}]},{"featureType":"transit.station.airport","elementType":"geometry.fill","stylers":[{"visibility":"on"},{"gamma":"0.80"}]},{"featureType":"water","elementType":"all","stylers":[{"visibility":"off"}]}];
        } else if( $scope.theme == 'greyscale' ){
          var theme = [{"featureType":"all","elementType":"all","stylers":[{"saturation":-100},{"gamma":0.5}]}];
        } else if( $scope.theme == 'lightgrey' ){
          var theme = [{"featureType":"water","elementType":"geometry.fill","stylers":[{"color":"#d3d3d3"}]},{"featureType":"transit","stylers":[{"color":"#808080"},{"visibility":"off"}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"visibility":"on"},{"color":"#b3b3b3"}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#ffffff"}]},{"featureType":"road.local","elementType":"geometry.fill","stylers":[{"visibility":"on"},{"color":"#ffffff"},{"weight":1.8}]},{"featureType":"road.local","elementType":"geometry.stroke","stylers":[{"color":"#d7d7d7"}]},{"featureType":"poi","elementType":"geometry.fill","stylers":[{"visibility":"on"},{"color":"#ebebeb"}]},{"featureType":"administrative","elementType":"geometry","stylers":[{"color":"#a7a7a7"}]},{"featureType":"road.arterial","elementType":"geometry.fill","stylers":[{"color":"#ffffff"}]},{"featureType":"road.arterial","elementType":"geometry.fill","stylers":[{"color":"#ffffff"}]},{"featureType":"landscape","elementType":"geometry.fill","stylers":[{"visibility":"on"},{"color":"#efefef"}]},{"featureType":"road","elementType":"labels.text.fill","stylers":[{"color":"#696969"}]},{"featureType":"administrative","elementType":"labels.text.fill","stylers":[{"visibility":"on"},{"color":"#737373"}]},{"featureType":"poi","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"poi","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"road.arterial","elementType":"geometry.stroke","stylers":[{"color":"#d6d6d6"}]},{"featureType":"road","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{},{"featureType":"poi","elementType":"geometry.fill","stylers":[{"color":"#dadada"}]}];
        } else if( $scope.theme == 'bluessence' ){
          var theme = [{"featureType":"landscape.natural","elementType":"geometry.fill","stylers":[{"visibility":"on"},{"color":"#e0efef"}]},{"featureType":"poi","elementType":"geometry.fill","stylers":[{"visibility":"on"},{"hue":"#1900ff"},{"color":"#c0e8e8"}]},{"featureType":"road","elementType":"geometry","stylers":[{"lightness":100},{"visibility":"simplified"}]},{"featureType":"road","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"transit.line","elementType":"geometry","stylers":[{"visibility":"on"},{"lightness":700}]},{"featureType":"water","elementType":"all","stylers":[{"color":"#7dcdcd"}]}];
        } else if( $scope.theme == 'bentley' ){
          var theme = [{"featureType":"landscape","stylers":[{"hue":"#F1FF00"},{"saturation":-27.4},{"lightness":9.4},{"gamma":1}]},{"featureType":"road.highway","stylers":[{"hue":"#0099FF"},{"saturation":-20},{"lightness":36.4},{"gamma":1}]},{"featureType":"road.arterial","stylers":[{"hue":"#00FF4F"},{"saturation":0},{"lightness":0},{"gamma":1}]},{"featureType":"road.local","stylers":[{"hue":"#FFB300"},{"saturation":-38},{"lightness":11.2},{"gamma":1}]},{"featureType":"water","stylers":[{"hue":"#00B6FF"},{"saturation":4.2},{"lightness":-63.4},{"gamma":1}]},{"featureType":"poi","stylers":[{"hue":"#9FFF00"},{"saturation":0},{"lightness":0},{"gamma":1}]}];
        } else if( $scope.theme == 'mybluewater' ){
          var theme = [{"featureType":"administrative","elementType":"labels.text.fill","stylers":[{"color":"#444444"}]},{"featureType":"landscape","elementType":"all","stylers":[{"color":"#f2f2f2"}]},{"featureType":"poi","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"poi.business","elementType":"all","stylers":[{"visibility":"simplified"},{"hue":"#7e00ff"},{"saturation":"0"}]},{"featureType":"poi.place_of_worship","elementType":"labels.text","stylers":[{"visibility":"on"}]},{"featureType":"poi.place_of_worship","elementType":"labels.text.fill","stylers":[{"visibility":"on"}]},{"featureType":"poi.place_of_worship","elementType":"labels.text.stroke","stylers":[{"visibility":"on"}]},{"featureType":"poi.place_of_worship","elementType":"labels.icon","stylers":[{"visibility":"on"}]},{"featureType":"road","elementType":"all","stylers":[{"saturation":-100},{"lightness":45}]},{"featureType":"road.highway","elementType":"all","stylers":[{"visibility":"simplified"}]},{"featureType":"road.arterial","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"water","elementType":"all","stylers":[{"color":"#46bcec"},{"visibility":"on"}]}];
        } else if( $scope.theme == 'girly' ){
          var theme = [{"featureType":"administrative","elementType":"labels.text.fill","stylers":[{"color":"#444444"}]},{"featureType":"landscape","elementType":"all","stylers":[{"color":"#f2f2f2"}]},{"featureType":"poi","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"road","elementType":"all","stylers":[{"saturation":-100},{"lightness":45}]},{"featureType":"road.highway","elementType":"all","stylers":[{"visibility":"simplified"}]},{"featureType":"road.highway","elementType":"geometry","stylers":[{"visibility":"simplified"},{"color":"#ff6a6a"},{"lightness":"0"}]},{"featureType":"road.highway","elementType":"labels.text","stylers":[{"visibility":"on"}]},{"featureType":"road.highway","elementType":"labels.icon","stylers":[{"visibility":"on"}]},{"featureType":"road.arterial","elementType":"all","stylers":[{"visibility":"on"}]},{"featureType":"road.arterial","elementType":"geometry.fill","stylers":[{"color":"#ff6a6a"},{"lightness":"75"}]},{"featureType":"road.arterial","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"road.local","elementType":"geometry.fill","stylers":[{"lightness":"75"}]},{"featureType":"transit","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"transit.line","elementType":"all","stylers":[{"visibility":"on"}]},{"featureType":"transit.station.bus","elementType":"all","stylers":[{"visibility":"on"}]},{"featureType":"transit.station.rail","elementType":"all","stylers":[{"visibility":"on"}]},{"featureType":"transit.station.rail","elementType":"labels.icon","stylers":[{"weight":"0.01"},{"hue":"#ff0028"},{"lightness":"0"}]},{"featureType":"water","elementType":"all","stylers":[{"visibility":"on"},{"color":"#80e4d8"},{"lightness":"25"},{"saturation":"-23"}]}];
        } else if( $scope.theme == 'flatmapwithlabels' ){
          var theme = [{"featureType":"water","elementType":"all","stylers":[{"hue":"#7fc8ed"},{"saturation":55},{"lightness":-6},{"visibility":"on"}]},{"featureType":"water","elementType":"labels","stylers":[{"hue":"#7fc8ed"},{"saturation":55},{"lightness":-6},{"visibility":"off"}]},{"featureType":"poi.park","elementType":"geometry","stylers":[{"hue":"#83cead"},{"saturation":1},{"lightness":-15},{"visibility":"on"}]},{"featureType":"landscape","elementType":"geometry","stylers":[{"hue":"#f3f4f4"},{"saturation":-84},{"lightness":59},{"visibility":"on"}]},{"featureType":"landscape","elementType":"labels","stylers":[{"hue":"#ffffff"},{"saturation":-100},{"lightness":100},{"visibility":"off"}]},{"featureType":"road","elementType":"geometry","stylers":[{"hue":"#ffffff"},{"saturation":-100},{"lightness":100},{"visibility":"on"}]},{"featureType":"road","elementType":"labels","stylers":[{"hue":"#bbbbbb"},{"saturation":-100},{"lightness":26},{"visibility":"on"}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"hue":"#ffcc00"},{"saturation":100},{"lightness":-35},{"visibility":"simplified"}]},{"featureType":"road.highway","elementType":"geometry","stylers":[{"hue":"#ffcc00"},{"saturation":100},{"lightness":-22},{"visibility":"on"}]},{"featureType":"poi.school","elementType":"all","stylers":[{"hue":"#d7e4e4"},{"saturation":-60},{"lightness":23},{"visibility":"on"}]}];
        } else if( $scope.theme == 'fashionflat' ){
          var theme = [{"featureType":"administrative","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"administrative","elementType":"geometry.stroke","stylers":[{"visibility":"on"}]},{"featureType":"administrative","elementType":"labels","stylers":[{"visibility":"on"},{"color":"#716464"},{"weight":"0.01"}]},{"featureType":"administrative.country","elementType":"labels","stylers":[{"visibility":"on"}]},{"featureType":"landscape","elementType":"all","stylers":[{"visibility":"simplified"}]},{"featureType":"landscape.natural","elementType":"geometry","stylers":[{"visibility":"simplified"}]},{"featureType":"landscape.natural.landcover","elementType":"geometry","stylers":[{"visibility":"simplified"}]},{"featureType":"poi","elementType":"all","stylers":[{"visibility":"simplified"}]},{"featureType":"poi","elementType":"geometry.fill","stylers":[{"visibility":"simplified"}]},{"featureType":"poi","elementType":"geometry.stroke","stylers":[{"visibility":"simplified"}]},{"featureType":"poi","elementType":"labels.text","stylers":[{"visibility":"simplified"}]},{"featureType":"poi","elementType":"labels.text.fill","stylers":[{"visibility":"simplified"}]},{"featureType":"poi","elementType":"labels.text.stroke","stylers":[{"visibility":"simplified"}]},{"featureType":"poi.attraction","elementType":"geometry","stylers":[{"visibility":"on"}]},{"featureType":"road","elementType":"all","stylers":[{"visibility":"on"}]},{"featureType":"road.highway","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"road.highway","elementType":"geometry","stylers":[{"visibility":"on"}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"visibility":"on"},{"color":"#9c223b"},{"lightness":"45"}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"visibility":"simplified"},{"color":"#9c223b"},{"lightness":"23"},{"weight":"0.01"}]},{"featureType":"road.highway","elementType":"labels","stylers":[{"visibility":"on"}]},{"featureType":"road.local","elementType":"all","stylers":[{"visibility":"on"}]},{"featureType":"transit","elementType":"all","stylers":[{"visibility":"simplified"}]},{"featureType":"transit","elementType":"geometry","stylers":[{"visibility":"simplified"}]},{"featureType":"transit.station","elementType":"geometry","stylers":[{"visibility":"on"}]},{"featureType":"water","elementType":"all","stylers":[{"visibility":"simplified"},{"color":"#12586f"},{"lightness":"16"},{"gamma":"6.95"}]},{"featureType":"water","elementType":"geometry","stylers":[{"visibility":"on"}]},{"featureType":"water","elementType":"geometry.fill","stylers":[{"visibility":"on"}]}];
        } else if( $scope.theme == 'yellowgray' ){
          var theme = [{"featureType":"administrative","elementType":"labels.text.fill","stylers":[{"color":"#444444"}]},{"featureType":"landscape","elementType":"all","stylers":[{"color":"#f2f2f2"}]},{"featureType":"poi","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"road","elementType":"all","stylers":[{"saturation":-100},{"lightness":45}]},{"featureType":"road.highway","elementType":"all","stylers":[{"visibility":"simplified"}]},{"featureType":"road.arterial","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"water","elementType":"all","stylers":[{"color":"#fdeb06"},{"visibility":"on"}]}];
        } else if( $scope.theme == 'trainlinesbg' ){
          var theme = [{"featureType":"administrative","elementType":"all","stylers":[{"visibility":"simplified"}]},{"featureType":"administrative","elementType":"labels.text.fill","stylers":[{"lightness":"70"}]},{"featureType":"administrative","elementType":"labels.icon","stylers":[{"color":"#c2d760"}]},{"featureType":"landscape","elementType":"geometry.fill","stylers":[{"color":"#ffffff"}]},{"featureType":"landscape","elementType":"labels.text.fill","stylers":[{"lightness":"70"},{"visibility":"off"}]},{"featureType":"poi","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"road","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"transit.line","elementType":"geometry.fill","stylers":[{"color":"#cc0033"},{"gamma":"1"},{"weight":"1.80"}]},{"featureType":"transit.station.airport","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"transit.station.bus","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"water","elementType":"geometry.fill","stylers":[{"color":"#dbe7f3"}]},{"featureType":"water","elementType":"labels","stylers":[{"visibility":"off"}]}];
        } else {
          var theme = [];
        }

        jQuery("#map").remove()
        jQuery("#preview").append("<div id='map' class='dp-google-map " + extraClasses+ "' style='width:" + width+ "; height:" + $scope.height + "px'></div>");

        if (currentApiKey !== $scope.apiKey) {
          currentApiKey = $scope.apiKey;
          var gmapsapiurl = "https://maps.googleapis.com/maps/api/js";
          if ($scope.apiKey) {
            gmapsapiurl += "?key=" + $scope.apiKey;
          }
          jQuery("#js-dp-google-map-api").remove()
          jQuery("body").append("<script id='js-dp-google-map-api' src='" + gmapsapiurl + "' async defer></scr" + "ipt>");
        }

        var previewLoaded = false;
        var previewTimer;

        previewTimer = setInterval(function(){
          if (google && !previewLoaded){
            clearInterval(previewTimer);
            previewLoaded = true;

            var disabled = $scope.controls ? false : true
            var draggable = $scope.draggable
            var geocoder = new google.maps.Geocoder();

            geocoder.geocode({address: $scope.address}, function(results, status) {
              if (status == google.maps.GeocoderStatus.OK){
                var mapOptions = {
                  draggable: jQuery(document).width() > 480 && draggable == true ? true : false,
                  center: results[0].geometry.location,
                  zoom: $scope.zoom,
                  mapTypeId: google.maps.MapTypeId.ROADMAP,
                  scrollwheel: false,
                  disableDefaultUI: disabled,
                  disableDoubleClickZoom: disabled,
                  styles: theme,
                }

                var map = new google.maps.Map(document.getElementById('map'), mapOptions)
                var marker = new google.maps.Marker({
                    map: map,
                    position: results[0].geometry.location
                })
              }
            })
          }
        }, 500);

      })
    })
  </script>

  <div class="wrap" ng-app="GMapsEasyApp" ng-controller="GMapsEasyController" style="-webkit-box-sizing: border-box; -moz-box-sizing: border-box; box-sizing: border-box;">

    <h2 id="tcd-google-maps-h2">TCD Google Maps</h2>

    <div class="tcd-google-maps-settings">
      <h3><?php _e('Address', 'tcd-google-maps'); ?></h3>
      <p><?php _e('Write the address here. Do not include the post-code.', 'tcd-google-maps'); ?></p>
      <input type="text" ng-model="address" ng-model-options="{ debounce: 1000 }" placeholder="大阪府" style="width:50%">
    </div>

    <div class="tcd-google-maps-settings">
      <h3><?php _e('API Key', 'tcd-google-maps'); ?></h3>
      <p><?php _e('Write your API key here if you have one.', 'tcd-google-maps'); ?></p>
      <input type="text" ng-model="apiKey" ng-model-options="{ debounce: 1000 }" style="width:50%">
    </div>

    <div class="tcd-google-maps-settings">
      <h3><?php _e('Zoom', 'tcd-google-maps'); ?></h3>
      <p><?php _e('Adjust the zoom level. leftmost will show the whole world, righmost just the building. Use the image below for a preview.', 'tcd-google-maps'); ?></p>
      <div ui-slider min="1" max="21" ng-model="zoom" ng-model-options="{ debounce: 1000 }"></div>
    </div>

    <div class="tcd-google-maps-settings">
      <h3><?php _e('Width', 'tcd-google-maps'); ?></h3>
      <p><?php _e('Select the width of the map in pixels. If you are using a responsive design, please select "Full width"', 'tcd-google-maps'); ?></p>
      <input ng-disabled="fullWidth" type="number" ng-model="minWidth" style="width:10%" min="0" max="3000">
      <div style="width:68%; display:inline-block">
        <div ng-disabled="fullWidth" ui-slider min="{{minWidth}}" max="{{maxWidth}}" ng-model="width" ng-model-options="{ debounce: 1000 }"></div>
      </div>
      <input ng-disabled="fullWidth" type="number" ng-model="maxWidth" style="width:10%" min="0" max="3000">
      <input ng-disabled="fullWidth" type="number" ng-model="width" style="width:10%" ng-model-options="{ debounce: 1000 }"><br/><br/>
      <label><input type="checkbox" ng-model="fullWidth"><?php _e('Full width', 'tcd-google-maps'); ?></label>
    </div>

    <div class="tcd-google-maps-settings">
      <h3><?php _e('Height', 'tcd-google-maps'); ?></h3>
      <p><?php _e('Select the height of the map in pixels.', 'tcd-google-maps'); ?></p>
      <input type="number" ng-model="minHeight" style="width:10%" min="0" max="3000">
      <div style="width:68%; display:inline-block">
        <div ui-slider min="{{minHeight}}" max="{{maxHeight}}" ng-model="height" ng-model-options="{ debounce: 1000 }"></div>
      </div>
      <input type="number" ng-model="maxHeight" style="width:10%" min="0" max="3000">
      <input type="number" ng-model="height" style="width:10%" ng-model-options="{ debounce: 1000 }">
    </div>

    <div class="tcd-google-maps-settings">
      <h3><?php _e('Various', 'tcd-google-maps'); ?></h3>
      <ul class="tcd-google-maps-check">
        <li><label><input type="checkbox" ng-model="controls"><?php _e('Map controls - Show map controls like zoom, street view, etc.', 'tcd-google-maps'); ?></label></li>
        <li><label><input type="checkbox" ng-model="borders"><?php _e('Add border - Show a nice shadowed border outside the map', 'tcd-google-maps'); ?></label></li>
        <li><label><input type="checkbox" ng-model="centered"><?php _e('Align center - Align the map in the center of the section', 'tcd-google-maps'); ?></label></li>
        <li><label><input type="checkbox" ng-model="draggable"><?php _e('Draggable - Allow user to move the map around with the mouse', 'tcd-google-maps'); ?></label></li>
      </ul>
    </div>

    <div class="tcd-google-maps-settings">
      <h3><?php _e('Themes', 'tcd-google-maps'); ?></h3>
      <p><?php _e('Select between those themes to add a bit of your own style!', 'tcd-google-maps'); ?></p>

      <div class="tcd-google-maps-themes-default"><label><input type="radio" value="" ng-model="theme"><?php _e('Default', 'tcd-google-maps'); ?></label></div>

      <div class="tcd-google-maps-themes">
        <label><input type="radio" value="discreet" ng-model="theme">Discreet</label><br/>
        <img width="200" src="<?php echo plugins_url( 'img/discreet.png', __FILE__ ); ?>">
      </div>

      <div class="tcd-google-maps-themes">
        <label><input type="radio" value="flatpale" ng-model="theme">Flat pale</label><br/>
        <img width="200" src="<?php echo plugins_url( 'img/flat-pale.png', __FILE__ ); ?>">
      </div>

      <div class="tcd-google-maps-themes">
        <label><input type="radio" value="justplaces" ng-model="theme">Just places</label><br/>
        <img width="200" src="<?php echo plugins_url( 'img/just-places.png', __FILE__ ); ?>">
      </div>

      <div class="tcd-google-maps-themes">
        <label><input type="radio" value="cleancut" ng-model="theme">Clean cut</label><br/>
        <img width="200" src="<?php echo plugins_url( 'img/clean-cut.png', __FILE__ ); ?>">
      </div>

      <div class="tcd-google-maps-themes">
        <label><input type="radio" value="evenlighter" ng-model="theme">Even lighter</label><br/>
        <img width="200" src="<?php echo plugins_url( 'img/even-lighter.png', __FILE__ ); ?>">
      </div>

      <div class="tcd-google-maps-themes">
        <label><input type="radio" value="ultralight" ng-model="theme">Ultra light</label><br/>
        <img width="200" src="<?php echo plugins_url( 'img/ultra-light-with-labels.png', __FILE__ ); ?>">
      </div>

      <div class="tcd-google-maps-themes">
        <label><input type="radio" value="lightmonochrome" ng-model="theme">Light monochrome</label><br/>
        <img width="200" src="<?php echo plugins_url( 'img/light-monochrome.png', __FILE__ ); ?>">
      </div>

      <div class="tcd-google-maps-themes">
        <label><input type="radio" value="redhat" ng-model="theme">Red hat</label><br/>
        <img width="200" src="<?php echo plugins_url( 'img/red-hat-antwerp.png', __FILE__ ); ?>">
      </div>

      <div class="tcd-google-maps-themes">
        <label><input type="radio" value="lightgreyandblue" ng-model="theme">Light grey and blue</label><br/>
        <img width="200" src="<?php echo plugins_url( 'img/light-grey-and-blue.png', __FILE__ ); ?>">
      </div>

      <div class="tcd-google-maps-themes">
        <label><input type="radio" value="elevation" ng-model="theme">Elevation</label><br/>
        <img width="200" src="<?php echo plugins_url( 'img/elevation.png', __FILE__ ); ?>">
      </div>

      <div class="tcd-google-maps-themes">
        <label><input type="radio" value="greyscale" ng-model="theme">Grey scale</label><br/>
        <img width="200" src="<?php echo plugins_url( 'img/greyscale.png', __FILE__ ); ?>">
      </div>

      <div class="tcd-google-maps-themes">
        <label><input type="radio" value="lightgrey" ng-model="theme">Lightgrey</label><br/>
        <img width="200" src="<?php echo plugins_url( 'img/light-gray.png', __FILE__ ); ?>">
      </div>

      <div class="tcd-google-maps-themes">
        <label><input type="radio" value="bluessence" ng-model="theme">Blue essence</label><br/>
        <img width="200" src="<?php echo plugins_url( 'img/blue-essence.png', __FILE__ ); ?>">
      </div>

      <div class="tcd-google-maps-themes">
        <label><input type="radio" value="bentley" ng-model="theme">Bentley</label><br/>
        <img width="200" src="<?php echo plugins_url( 'img/bentley.png', __FILE__ ); ?>">
      </div>

      <div class="tcd-google-maps-themes">
        <label><input type="radio" value="mybluewater" ng-model="theme">My blue water</label><br/>
        <img width="200" src="<?php echo plugins_url( 'img/my-blue-water.png', __FILE__ ); ?>">
      </div>

      <div class="tcd-google-maps-themes">
        <label><input type="radio" value="girly" ng-model="theme">Girly</label><br/>
        <img width="200" src="<?php echo plugins_url( 'img/girly.png', __FILE__ ); ?>">
      </div>

      <div class="tcd-google-maps-themes">
        <label><input type="radio" value="flatmapwithlabels" ng-model="theme">Flat map with labels</label><br/>
        <img width="200" src="<?php echo plugins_url( 'img/flat-map-with-labels.png', __FILE__ ); ?>">
      </div>

      <div class="tcd-google-maps-themes">
        <label><input type="radio" value="fashionflat" ng-model="theme">Fashion flat</label><br/>
        <img width="200" src="<?php echo plugins_url( 'img/fashion-flat.png', __FILE__ ); ?>">
      </div>

      <div class="tcd-google-maps-themes">
        <label><input type="radio" value="yellowgray" ng-model="theme">Yellow gray</label><br/>
        <img width="200" src="<?php echo plugins_url( 'img/yellow-gray.png', __FILE__ ); ?>">
      </div>

      <div class="tcd-google-maps-themes">
        <label><input type="radio" value="trainlinesbg" ng-model="theme">Train lines</label><br/>
        <img width="200" src="<?php echo plugins_url( 'img/train-lines-bg.png', __FILE__ ); ?>">
      </div>

    </div>

    <div class="tcd-google-maps-settings">
      <h3><?php _e('Preview', 'tcd-google-maps'); ?></h3>
      <div id="preview"></div>
    </div>

    <div class="tcd-google-maps-settings">
      <h3><?php _e('Result', 'tcd-google-maps'); ?></h3>
      <p><?php _e('Just copy and paste this shortcode inside your blog post, page or widget!', 'tcd-google-maps'); ?></p>
      <textarea style="width:100%; height:100px" ng-model="result"></textarea>
    </div>

  </div>