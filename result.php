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
    //返却値のフォーマットを変数に入れる
    $format= "json";
    //緯度・経度、範囲を変数に入れる。
    //範囲はrange=1で300m以内を指定。(1:300m、2:500m、3:1000m、4:2000m、5:3000m)
    $lat = $_POST['lat'];
    $lon = $_POST['lon'];
    $range = $_POST['range'];
    //URL組み立て
    $url  = sprintf("%s%s%s%s%s%s%s%s%s%s%s", $uri, "?format=", $format, "&keyid=", $acckey, "&latitude=", $lat,"&longitude=",$lon,"&range=",$range);
    //API実行
    $json = file_get_contents($url);
    //取得した結果をオブジェクト化
    $obj  = json_decode($json);

    $restlat = array();
    $restlon = array();
    //結果をパース
    //トータルヒット件数、店舗番号、店舗名、最寄の路線、最寄の駅、最寄駅から店までの時間、店舗の小業態を出力

    foreach((array)$obj as $key => $val){
        if(strcmp($key, "total_hit_count" ) == 0 ){
            echo "total:".$val."\n";
        }

        if(strcmp($key, "rest") == 0){
            foreach((array)$val as $restArray){
              $restlat += array($restArray->{'latitude'});
              $restlon += array($restArray->{'longitude'});
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


                foreach((array)$restArray->{'code'}->{'category_name_s'} as $v){
                    if (checkString($v)) echo $v;
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
  <input type="button" onclick="location.href='top.php'"value="Topへ">
  <br>
  <br>
  <center>
      <a href="http://api.gnavi.co.jp/api/scope/" target="_blank">
      <img src="http://api.gnavi.co.jp/api/img/credit/api_265_65.gif" width="265" height="65" border="0" alt="グルメ情報検索サイト　ぐるなび">
      </a>
  </center>
  <script>
    function initMap() {
      var uluru = {lat: <?php echo $lat ?>, lng: <?php echo $lon ?>};
      var map = new google.maps.Map(document.getElementById('map'), {
        zoom: 16,
        center: uluru
      });
      <?php foreach ($restlat as $key => $value): ?>
      var marker = new google.maps.Marker({
        position: {lat: <?php echo $value[$key]; ?>, lng: <?php echo $restlon[$key]; ?>},
        map: map
      });
      <?php endforeach; ?>
    }
  </script>
  <script async defer
  src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD5eKBrtbRY8ohoiYMkM9zyKI38VzO5C3o&callback=initMap">
  </script>

  </body>
</html>
