<!DOCTYPE html>
  <html lang="ja">
  <head>
    <meta charset="UTF-8">
    <title>検索結果</title>
    <style>
       #map {
        height: 400px;
        width: 100%;
       }
    </style>
  </head>
  <body>
    <h1>検索結果</h1><div id="map"></div>
    <?php
    $uri   = "https://api.gnavi.co.jp/RestSearchAPI/20150630/";
    $acckey= "c97bc4330d3d296c5c678662a73f265d";
    //返却値のフォーマットを指定
    $format= "json";
    //緯度・経度、範囲を受け取る
    $lat = $_POST['lat'];
    $lon = $_POST['lon'];
    $range = $_POST['range'];
    $hit_per_page = 100;
    //リクエストパラメータ
$url  = sprintf("%s%s%s%s%s%s%s%s%s%s%s%s%s", $uri, "?format=", $format, "&keyid=", $acckey, "&latitude=", $lat,"&longitude=",$lon,"&range=",$range, "&hit_per_page=",$hit_per_page);
//var_dump($url);
//exit;
    //APIを実行
    $json = file_get_contents($url);
    //取得した結果をオブジェクト化
    $obj  = json_decode($json);
    $markerData = array();

    //結果をパース
    //トータルヒット件数、店舗番号、店舗名、最寄の路線、最寄の駅、最寄駅から店までの時間を出力

    foreach((array)$obj as $key => $val){
        if(strcmp($key, "total_hit_count" ) == 0 ){
            echo "トータルヒット件数:".$val;
            echo nl2br("\n").nl2br("\n");
        }
        if(strcmp($key, "rest") == 0){
            foreach((array)$val as $restArray){
                $markerData += array((string)$restArray->{'name'} => array('lat' => $restArray->{'latitude'},'lon' => $restArray->{'longitude'},
                'url' => $restArray->{'url'}, 'category' => $restArray->{'category'}, 'walk' => $restArray->{'access'}->{'walk'}));
                if (checkString($restArray->{'id'})) {
            		    echo $restArray->{'id'};
            		}
                if (checkString($restArray->{'name'})) {
            		    echo $restArray->{'name'};
            		}
                if (checkString($restArray->{'access'}->{'line'})){
            		    echo (string)$restArray->{'access'}->{'line'};
            		}
                if (checkString($restArray->{'access'}->{'station'})) {
            		    echo (string)$restArray->{'access'}->{'station'};
            		}
                if (checkString($restArray->{'access'}->{'walk'})) {
            		    echo (string)$restArray->{'access'}->{'walk'}."分";
            		}
                echo nl2br("\n");
            }
        }

    }

    //文字列であるかをチェック
    function checkString($input){
        if(isset($input) && is_string($input)) {
        return true;
        } else {
        return false;
        }
    }
?>
  <br>
  <br>
  <input type="button" onclick="location.href='index.php'"value="Topへ">
  <br>
  <br>
  <center>
      <a href="http://api.gnavi.co.jp/api/scope/" target="_blank">
      <img src="http://api.gnavi.co.jp/api/img/credit/api_265_65.gif" width="265" height="65" border="0" alt="グルメ情報検索サイト　ぐるなび">
      </a>
  </center>
  <script>
    var map;
    var marker = [];
    var infoWindow = [];
    function initMap() {
      var uluru = {lat: <?php echo $lat ?>, lng: <?php echo $lon ?>};
      var map = new google.maps.Map(document.getElementById('map'), {
        zoom: 16,
        center: uluru
      });
      var i = 0;
      <?php foreach ($markerData as $key => $value): ?>
        markerLatLng = new google.maps.LatLng({lat: <?php echo $value['lat']; ?>, lng: <?php echo $value['lon']; ?>}); // 緯度経度のデータ作成
        marker[i] = new google.maps.Marker({ // マーカーの追加
         position: markerLatLng, // マーカーを立てる位置を指定
            map: map // マーカーを立てる地図を指定
          });

     infoWindow[i] = new google.maps.InfoWindow({ // 吹き出しの追加
         content: '<div class="sample">' + '<?php echo $key; ?>' + '<br>' +
         '<?php echo $value["walk"]; ?>' + '分' + '<br>'
         + 'カテゴリー：' + '<?php echo (string)$value["category"]; ?>' + '<br>' +
         '<a href=\"' + '<?php echo $value["url"]; ?>' + '\" target="blank"><?php echo $value["url"]; ?></a>' +
        '</div>' // 吹き出しに表示する内容
       });
     markerEvent(i); // マーカーにクリックイベントを追加
     i++;
    <?php endforeach; ?>
  }
  function markerEvent(i) {
    marker[i].addListener('click', function() { // マーカーをクリックしたとき
      infoWindow[i].open(map, marker[i]); // 吹き出しの表示
    });
  }
  </script>
  <script async defer
  src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD5eKBrtbRY8ohoiYMkM9zyKI38VzO5C3o&callback=initMap">
  </script>

  </body>
</html>
