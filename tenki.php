<?php

/*
Plugin Name: 福岡市の天気
Plugin URI: https://www.itsupporter-mk.com/
Description: 福岡市の天気を表示します。
Version: 1.0
Author: Masaru kumazaki
Author URI:https://www.itsupporter-mk.com/
Licence: GPL v2
Licence URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

//直接phpファイルを読み込まれた場合は終了
if (!defined('ABSPATH')) exit;


//===============================================
// プラグインのフルパスを取得
//===============================================
$file = __FILE__;
if (isset($plugin)) {
	$file = $plugin;
} elseif (isset($mu_plugin)) {
	$file = $mu_plugin;
} elseif (isset($network_plugin)) {
	$file = $network_plugin;
}

//=================================================
//天気を表示するプラグイン
//5日間の天気を表示する
//https://openweathermap.org/forecast5#builtin
//=================================================


//================================================
//設定で使う変数　JavaScriptで使う
// APIキー  	= API_KEY
// 座標　loc_lat　loc_lon 配列5個
//=================================================

//ショートコード、Lat Lonを記録する
$loc_sc1 = array();
$loc_sc1[0] = ""; //ショートコード
$loc_sc1[1] = ""; //Lat
$loc_sc1[2] = ""; //Lon
$loc_sc1[3] = ""; //名前

$loc_sc2 = array();
$loc_sc2[0] = ""; //ショートコード
$loc_sc2[1] = ""; //Lat
$loc_sc2[2] = ""; //Lon
$loc_sc2[3] = ""; //名前


$loc_sc3 = array();
$loc_sc3[0] = ""; //ショートコード
$loc_sc3[1] = ""; //Lat
$loc_sc3[2] = ""; //Lon
$loc_sc3[3] = ""; //名前

$loc_sc4 = array();
$loc_sc4[0] = ""; //ショートコード
$loc_sc4[1] = ""; //Lat
$loc_sc4[2] = ""; //Lon
$loc_sc4[3] = ""; //名前

$loc_sc5 = array();
$loc_sc5[0] = ""; //ショートコード
$loc_sc5[1] = ""; //Lat
$loc_sc5[2] = ""; //Lon
$loc_sc5[3] = ""; //名前


$message_html = "";

// 天気を表示するHTML
$tenki_base_html =<<<EOF
<script src="https://code.jquery.com/jquery-3.6.3.min.js" integrity="sha256-pvPw+upLPUjgMXY0G+8O0xUf+/Im1MZjXxxgOcBQBXU=" crossorigin="anonymous"></script>
	<script>

window.onload = function() {
	
		//var item_id = 'lat=35.2932718&lon=139.97671';
		
		var url = 'https://api.openweathermap.org/data/2.5/forecast?lat=' + loc_lat + '&lon=' + loc_lon + '&lang=ja&units=metric&appid=' + API_KEY;

		//var url = 'https://api.openweathermap.org/data/2.5/forecast?' + item_id + '&lang=ja&units=metric&appid=' + API_KEY;

		$.ajax({ 
		url: url,
		dataType: "json",
		type: 'GET',
		})
		.done(function(data) {
		var insertHTML = "";
		var cityName = '<h2>' + data.city.name + '</h2>';
		$('#city-name').html(cityName);
		for (var i = 0; i <= 39; i = i + 8) {
			insertHTML += buildHTML(data, i);
		}
		$('#weather').html(insertHTML);
		})
		.fail(function(data) {
		console.log("失敗しました");
		});
	};

	function buildHTML(data, i) {
		var Week = new Array("（日）","（月）","（火）","（水）","（木）","（金）","（土）");
		var date = new Date (data.list[i].dt_txt);
		date.setHours(date.getHours() + 9);
		var month = date.getMonth()+1;
		var day = month + "月" + date.getDate() + "日" + Week[date.getDay()];
		var loc_time = date.getHours() + "：00";
		var icon = data.list[i].weather[0].icon;
		var html =
		'<div class="weather-report">' +
		'<!-- ' + 
		'<div id="cityname">' + data.city.name + '</div>'  +
		' -->' +
		'<div id="cityname">' + loc_name + '</div>'  +
		'<img src="https://openweathermap.org/img/w/' + icon + '.png">' +
		'<div class="weather-date">' + day + '</div>' +
		'<div class="weather-time">' + loc_time + '</div>' +
		'<div class="weather-main">'+ data.list[i].weather[0].description + '</div>' +
		'<div class="weather-temp">' + Math.round(data.list[i].main.temp) + '℃</div>' +
		'</div>';
		return html
	}

	</script>
	<style>
		.fukuoka_content {
			text-align: center;
			width: 100%;
			margin-left: auto;
			margin-right: auto;
		}

		.weather-report {
			margin-right: 20px;
			margin-left: 20px;
			float: left;
		}

	</style>  

		<div class="fukuoka_content">
		<div id="weather"></div>

		</div>	
EOF;

//POSTで受信したデータ保管用
$otp_loc1 = array();
$otp_loc2 = array();
$otp_loc3 = array();
$otp_loc4 = array();
$otp_loc5 = array();



// 変数を初期化
//APIKEY
$fukuoka_tenki_apikey = '';


// APIキーを取得
$fukuoka_tenki_apikey = get_option('fukuoka_tenki_apikey');
$opt_val_old = $fukuoka_tenki_apikey;


// APIキーが取得できた場合は、JavaScript変数へ代入
if ($fukuoka_tenki_apikey) {
	echo '<script>var API_KEY = "' . $fukuoka_tenki_apikey . '";</script>';
	$opt_val_old = $fukuoka_tenki_apikey;
} 

//---------------------------------
//データがデータベースにあるか確認
//5回繰り返す
//---------------------------------


	
	global $loc_sc1;
	global $loc_sc2;
	global $loc_sc3;
	global $loc_sc4;
	global $loc_sc5;

	global $sc_tenki1;

	$temp_get = array();

	for ($i =1; $i <= 5; $i++) {
		//データベースからデータを取得
		${"loc_sc".$i} = get_option('fukuoka_tenki_loc_sc'.$i);
		//データがデータベースにあるか確認
		if ( empty( ${"loc_sc".$i} ) ) {
			// キーが存在しない場合の処理
			${"loc_sc".$i} = array(
				"",
				"",
				"",
				""

			);
		} else {

			// キーが存在する場合の処理
			//ショートコード名を取得
			//改行コードを削除
			preg_match('/[0-9a-zA-Z_]+/', ${"loc_sc".$i}[0], $temp_get[0]);
			${"loc_sc".$i}[0] = $temp_get[0][0];
			//[1]と[2]の値を数値に変換
			${"loc_sc".$i}[1] = floatval(${"loc_sc".$i}[1]);
			${"loc_sc".$i}[2] = floatval(${"loc_sc".$i}[2]);

		}
	} //ここまでがfor文


//=================================================
// 管理画面に「福岡の天気」を追加登録する
//=================================================
add_action('admin_menu', function () {

	//---------------------------------
	// メインメニュー① ※実際のページ表示はサブメニュー①
	//---------------------------------	
	add_menu_page(
		'福岡の天気' // ページのタイトルタグ<title>に表示されるテキスト
		,
		'天気設定'   // 左メニューとして表示されるテキスト
		,
		'manage_options'       // 必要な権限 manage_options は通常 administrator のみに与えられた権限
		,
		'fukuoka_tenki_menu'        // 左メニューのスラッグ名 →URLのパラメータに使われる /wp-admin/admin.php?page=toriaezu_menu
		,
		'fukuoka_tenki_page_contents' // メニューページを表示する際に実行される関数(サブメニュー①の処理をする時はこの値は空にする)
		,
		'dashicons-admin-users'       // メニューのアイコンを指定 https://developer.wordpress.org/resource/dashicons/#awards
		,
		0                             // メニューが表示される位置のインデックス(0が先頭) 5=投稿,10=メディア,20=固定ページ,25=コメント,60=テーマ,65=プラグイン,70=ユーザー,75=ツール,80=設定
	);



	//---------------------------------
	// サブメニュー① ※事実上の親メニュー
	//---------------------------------
	add_submenu_page(
		'fukuoka_tenki_menu'    // 親メニューのスラッグ
		,
		'福岡の天気の設定' // ページのタイトルタグ<title>に表示されるテキスト
		,
		'API設定' // サブメニューとして表示されるテキスト
		,
		'manage_options' // 必要な権限 manage_options は通常 administrator のみに与えられた権限
		,
		'fukuoka_tenki_menu1'  // サブメニューのスラッグ名。この名前を親メニューのスラッグと同じにすると親メニューを押したときにこのサブメニューを表示します。一般的にはこの形式を採用していることが多い。
		,
		'fukuoka_tenki_1_page_contents' //（任意）このページのコンテンツを出力するために呼び出される関数
		,
		1
	);


	//---------------------------------
	// サブメニュー2 
	//---------------------------------
	add_submenu_page(
		'fukuoka_tenki_menu'    // 親メニューのスラッグ
		,
		'ショートコードと座標入力' // ページのタイトルタグ<title>に表示されるテキスト
		,
		'座標設定' // サブメニューとして表示されるテキスト
		,
		'manage_options' // 必要な権限 manage_options は通常 administrator のみに与えられた権限
		,
		'fukuoka_tenki_menu2'  // サブメニューのスラッグ名。この名前を親メニューのスラッグと同じにすると親メニューを押したときにこのサブメニューを表示します。一般的にはこの形式を採用していることが多い。
		,
		'fukuoka_tenki_2_page_contents' //（任意）このページのコンテンツを出力するために呼び出される関数
		,
		2
	);
	
});




//=================================================
// メインメニューページ内容の表示・更新処理
//=================================================


function fukuoka_tenki_page_contents()
{


	

	//---------------------------------
	// HTML表示
	//---------------------------------
	global $tenki_base_html;
	global $loc_sc1;
	global $fukuoka_tenki_apikey;


	

	//天気の表示1つ目の座標

	echo <<<EOF
	<div class="wrap">
		<h2>メインメニュー</h2>
		<p>
			福岡の天気の設定のページです。
		</p>
	<script>var API_KEY = "{$fukuoka_tenki_apikey}"</script>
	<script>var loc_lat = "{$loc_sc1[1]}"</script>
	<script>var loc_lon = "{$loc_sc1[2]}"</script>
	<script>var loc_name = "{$loc_sc1[3]}"</script>
	

	EOF;

	echo $tenki_base_html;
	echo <<<EOF
	</div>
	EOF;
}


//=================================================
// サブメニュー①ページ内容の表示・更新処理
//=================================================
function fukuoka_tenki_1_page_contents()
{

	global $fukuoka_tenki_apikey;





	//---------------------------------
	// 更新されたときの処理
	//---------------------------------
    if( isset($_POST[ 'fukuoka_tenki_apikey' ])) {

        // POST されたデータを取得
        $opt_val = $_POST[ 'fukuoka_tenki_apikey' ];

        // POST された値をデータベースに保存(wp_options テーブル内に保存)
        update_option('fukuoka_Tenki_apikey ', $opt_val);
		global $opt_val_old;

        // 画面にメッセージを表示
		$message_html =<<<EOF
			
		<div class="notice notice-success is-dismissible">
			<p>
				メッセージを保存しました
				({$opt_val_old}→{$opt_val})
			</p>
		</div>
					
		EOF;

	} else {
		$message_html = '';
	}

	//
	//---------------------------------
	// HTML表示
	//---------------------------------
	echo <<<EOF

	{$message_html}

	<div class="wrap">
		<h2>API KEY設定</h2>
		<p>API KEYは、下記のウェブサイトから取得してください。</p>
		<p><a href="https://openweathermap.org/api" target="_blank">https://openweathermap.org/api</a></p>

		<form method="post" action="">
			<table class="form-table">
				<tr>
					<th scope="row"><label for="fukuoka_tenki_apikey">APIキー</label></th>
					<td><input type="text" name="fukuoka_tenki_apikey" id="fukuoka_tenki_apikey" value="$fukuoka_tenki_apikey" class="regular-text"></td>
				</tr>
			</table>
			<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="保存"></p>
		</form>
	</div>
	EOF;
}



//=================================================
// サブメニュー②ページ内容の表示・更新処理
//=================================================
function fukuoka_tenki_2_page_contents()
{

	$i = 0;
	$j = 0;
	$z = 0;

	global $loc_sc1;
	global $loc_sc2;
	global $loc_sc3;
	global $loc_sc4;
	global $loc_sc5;



	$loc_sc_f_ok = 0;
	$loc_sc_f_err = 0;
	$loc_sc_f_de = 0;
	

	global $message_html;
	$opt_val = '';

	$get_loc_sc = array();

	
	

	//送信成功時のメッセージ 
	//最後のメッセージは、</div>で閉じる
	$ok_message_html =<<<EOF
	<div class="notice notice-success is-dismissible">
		<p>
			メッセージを保存しました
		</p>
	
	EOF;
	
	//送信失敗時のメッセージ
	//最後のメッセージは、</div>で閉じる
	$err_message_html =<<<EOF
	<div class="notice notice-error notice-alt is-dismissible">
	<p>ショートコード、lat、lonの3つのデータを入力してください。</p>
	<p><strong>入力データが不足しています。</strong></p>
	EOF;

	//データ削除時のメッセージ
	//最後のメッセージは、</div>で閉じる
	$dele_message_html =<<<EOF
	<div class="notice notice-warning notice-alt is-dismissible">
	<p><strong>下記のデータを削除しました。</strong></p>
	EOF;

	//3つの表示の変数
	$tenki_message_html = '';		

	//デバックのため臨時
	//POSTで受け取ったデータの構造を確認
	//$ok_message_html = var_dump($_POST);
	
	
	
	//POSTでloc_sc1から5の中のどれかが送信されているかチェック
	if(isset($_POST['loc_sc1']) || isset($_POST['loc_sc2']) || isset($_POST['loc_sc3']) || isset($_POST['loc_sc4']) || isset($_POST['loc_sc5'])){
		//POSTで送信されているか5回チェック
		for($i = 1; $i <= 5; $i++){
			//POSTで送信されているかチェック

			if(isset($_POST['loc_sc'.$i])){

				$get_loc_sc[$i] = $_POST['loc_sc'.$i];
				//nullチェック 連想配列 3つのデータがあるか
				//POSTの状態　データがないときは、文字数が0になる。空白と文字列の０と数字の０は、データ無しと判断する。
				if(strlen($get_loc_sc[$i][0]) == 0 && strlen($get_loc_sc[$i][1]) == 0 && strlen($get_loc_sc[$i][2]) == 0 && strlen($get_loc_sc[$i][3]) == 0){
					//変数に値がない場合の処理
					//3つすべて未入力の場合は、エラーとしない
					$ok_message_html .= <<<EOF
					<p>{$i}目は、すべて空白なので登録しません。</p>
					EOF;
					$loc_sc_f_ok = 1;
				} else {
						//[1][2]は、数字の０の場合は、エラーとする
						//ここには、配列に値がある状態で来る
						$temp_lat = (float)$get_loc_sc[$i][1];
						$temp_lon = (float)$get_loc_sc[$i][2];
						if (!empty($get_loc_sc[$i][0]) && ($temp_lat >0) && ($temp_lon >0) && !empty($get_loc_sc[$i][3])){
							//ショートコードがあり、latとlonが数字の０でない場合
							//正常に記録
							//変数に3つとも値がある場合の処理
								${"opt_loc".$i} = array(
									$get_loc_sc[$i][0],
									$get_loc_sc[$i][1],
									$get_loc_sc[$i][2],
									$get_loc_sc[$i][3]
								);
								//データ保存
								$ok_message_html .= <<<EOF
								<p>今までの内容</p>
								<p>ショートコード：{${"loc_sc".$i}[0]}&nbsp;&nbsp;lat:{${"loc_sc".$i}[1]}&nbsp;&nbsp;lon:{${"loc_sc".$i}[2]}&nbsp;&nbsp;表示名:{${"loc_sc".$i}[3]}</p>
								<p>新しい内容</p>
								<p>ショートコード：{${"opt_loc".$i}[0]}&nbsp;&nbsp;lat:{${"opt_loc".$i}[1]}&nbsp;&nbsp;lon:{${"opt_loc".$i}[2]}&nbsp;&nbsp;表示名:{${"opt_loc".$i}[3]}</p>
								EOF;
								update_option('fukuoka_tenki_loc_sc'.$i, ${"opt_loc".$i});
								${"loc_sc" .$i} = get_option('fukuoka_tenki_loc_sc'.$i);
								$loc_sc_f_ok = 1;

						} elseif ((!empty($get_loc_sc[$i][0])) && ((empty($get_loc_sc[$i][1])) || (empty($get_loc_sc[$i][2])))) {
								//latまたはlonが数値でない場合の処理
								$err_message_html .= <<<EOF
								<p>{$i}目です。</p>
								<p>latとlonは、数値を入力してください。</p>
								<p>ショートコード：{$get_loc_sc[$i][0]}&nbsp;&nbsp;lat:{$get_loc_sc[$i][1]}&nbsp;&nbsp;lon:{$get_loc_sc[$i][2]}&nbsp;&nbsp;表示名:{$get_loc[$i][3]}</p>
								EOF;
								$loc_sc_f_err = 1;
						} elseif ((!empty($get_loc_sc[$i][0])) && (($temp_lat >0) || ($temp_lon >0)) && ((empty($get_loc_sc[$i][3])))) {
								//表示名がない場合の処理
								$err_message_html .= <<<EOF
								<p>{$i}目です。</p>
								<p>表示名がありません。</p>
								<p>ショートコード：{$get_loc_sc[$i][0]}&nbsp;&nbsp;lat:{$get_loc_sc[$i][1]}&nbsp;&nbsp;lon:{$get_loc_sc[$i][2]}&nbsp;&nbsp;表示名:{$get_loc_sc[$i][3]}</p>
								EOF;
								$loc_sc_f_err = 1;								

							} elseif ((empty($get_loc_sc[$i][0])) && (($temp_lat >0) && ($temp_lon >0)) && (!empty($get_loc_sc[$i][3]))) {
								//ショートコードがない場合の処理
								//データのキーを削除
								delete_option('fukuoka_tenki_loc_sc'.$i);
								$dele_message_html .= <<<EOF
								<p>{$i}目です。</p>
								<p>ショートコードの入力が無いので、削除しました。</p>
								<p>ショートコード：{$get_loc_sc[$i][0]}&nbsp;&nbsp;lat:{$get_loc_sc[$i][1]}&nbsp;&nbsp;lon:{$get_loc_sc[$i][2]}&nbsp;&nbsp;表示名:{$get_loc_sc[$i][3]}</p>
								EOF;	
								$loc_sc_f_de = 1;
							}

					} //ここまでが変数に値があるかチェック
					
			} //ここまでがPOSTで送信されたデータ

		}  //ここまでがfor文
			//データベース再読込
			
			$get_loc_sc = array();
			
		
	} //ここまでがPOSTで送信されているかチェック
	
	


	//表示の制御
	if ($loc_sc_f_ok > 0) {
		$total_ok_message_html = $ok_message_html;
		$total_ok_message_html .= <<<EOF
		</div>
		EOF;
		
	} else {
		$total_ok_message_html = "";
	}

	if ($loc_sc_f_err > 0) {
		$total_err_message_html = $err_message_html;
		$total_err_message_html .= <<<EOF
		</div>
		EOF;
	
	} 

	if ($loc_sc_f_de > 0) {
		$total_dele_message_html = $dele_message_html;
		$total_dele_message_html .= <<<EOF
		</div>
		EOF;
		
	} 

	if ($loc_sc_f_ok == 0) {
		$total_ok_message_html = "";

	}
	if ($loc_sc_f_err == 0) {
		$total_err_message_html = "";

	}

	if ($loc_sc_f_de == 0) {
		$total_dele_message_html = "";

	}

	//////////////////////////////////////////////
	//HTML表示 統一
	//////////////////////////////////////////////

	$tenki_message_html = $total_ok_message_html . $total_err_message_html . $total_dele_message_html;

	//---------------------------------
	// HTML表示
	//---------------------------------
	echo <<<EOF


	<div class="wrap">

	{$tenki_message_html}
	<h2>地域設定</h2>
	<p>ショートコードを削除して保存すると、データを削除できます。</p>
	<p>データを削除した地域は、空白で表示されます。</p>
	 <form method="post" action="">
		<table class="form-table">
			<tr>
				<th scope="row"><label for="loc_sc1">地域1</label></th>
				<tr>
					<td class="fome_name">表示名</td><td><input type="text" name="loc_sc1[3]" id="loc_sc1[3]" value="$loc_sc1[3]" class="regular-text"></td>
				</tr>
				<tr>
					<td>ショート<br />コード</td>
					<td><input type="text" name="loc_sc1[0]" id="loc_sc1[0]" value="$loc_sc1[0]" class="regular-text"></td>
					<td>lat</td>
					<td><input type="text" name="loc_sc1[1]" id="loc_sc1[1]" value="$loc_sc1[1]" class="regular-text"></td>
					<td>lon</td>
					<td><input type="text" name="loc_sc1[2]" id="loc_sc1[2]" value="$loc_sc1[2]" class="regular-text"></td>
				</tr>
			</tr>
			<tr>
				<th scope="row"><label for="loc_sc2">地域2</label></th>
				<tr>
				<tr>
					<td class="fome_name">表示名</td><td><input type="text" name="loc_sc2[3]" id="loc_sc2[3]" value="$loc_sc2[3]" class="regular-text"></td>
				</tr>
					<td>ショート<br />コード</td>
					<td><input type="text" name="loc_sc2[0]" id="loc_sc2[0]" value="$loc_sc2[0]" class="regular-text"></td>
					<td>lat</td>
					<td><input type="text" name="loc_sc2[1]" id="loc_sc2[1]" value="$loc_sc2[1]" class="regular-text"></td>
					<td>lon</td>
					<td><input type="text" name="loc_sc2[2]" id="loc_sc2[2]" value="$loc_sc2[2]" class="regular-text"></td>
				</tr>

			</tr>
			<tr>
				<th scope="row"><label for="loc_sc3">地域3</label></th>
				<tr>
					<td class="fome_name">表示名</td><td><input type="text" name="loc_sc3[3]" id="loc_sc3[3]" value="$loc_sc3[3]" class="regular-text"></td>
				</tr>
				<tr>
					<td>ショート<br />コード</td>
					<td><input type="text" name="loc_sc3[0]" id="loc_sc3[0]" value="$loc_sc3[0]" class="regular-text"></td>
					<td>lat</td>
					<td><input type="text" name="loc_sc3[1]" id="loc_sc3[1]" value="$loc_sc3[1]" class="regular-text"></td>
					<td>lon</td>
					<td><input type="text" name="loc_sc3[2]" id="loc_sc3[2]" value="$loc_sc3[2]" class="regular-text"></td>
				</tr>
			</tr>
			<tr>
				<th scope="row"><label for="loc_sc4">地域4</label></th>
				<tr>
				<tr>
					<td class="fome_name">表示名</td><td><input type="text" name="loc_sc4[3]" id="loc_sc4[3]" value="$loc_sc4[3]" class="regular-text"></td>
				</tr>
					<td>ショート<br />コード</td>
					<td><input type="text" name="loc_sc4[0]" id="loc_sc4[0]" value="$loc_sc4[0]" class="regular-text"></td>
					<td>lat</td>
					<td><input type="text" name="loc_sc4[1]" id="loc_sc4[1]" value="$loc_sc4[1]" class="regular-text"></td>
					<td>lon</td>
					<td><input type="text" name="loc_sc4[2]" id="loc_sc4[2]" value="$loc_sc4[2]" class="regular-text"></td>
				</tr>
			</tr>
			<tr>
				<th scope="row"><label for="loc_sc5">地域5</label></th>
				<tr>
				<tr>
					<td class="fome_name">表示名</td><td><input type="text" name="loc_sc5[3]" id="loc_sc5[3]" value="$loc_sc5[3]" class="regular-text"></td>
				</tr>
					<td>ショート<br />コード</td>
					<td><input type="text" name="loc_sc5[0]" id="loc_sc5[0]" value="$loc_sc5[0]" class="regular-text"></td>
					<td>lat</td>
					<td><input type="text" name="loc_sc5[1]" id="loc_sc5[1]" value="$loc_sc5[1]" class="regular-text"></td>
					<td>lon</td>
					<td><input type="text" name="loc_sc5[2]" id="loc_sc5[2]" value="$loc_sc5[2]" class="regular-text"></td>
				</tr>
			</tr>
		</table>
	 <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="保存"></p>
	 </form>
	</div>

	EOF;
}





//=================================================
// 記事に天気を追加できるように、ショートコードを登録
//=================================================	

function fukuoka_tenki1() {

	global $tenki_base_html;
	global $fukuoka_tenki_apikey;
	global $loc_sc1;


	
	
	$output =<<<EOF
	<script>var API_KEY = "{$fukuoka_tenki_apikey}"</script>
	<script>var loc_lat = "{$loc_sc1[1]}"</script>
	<script>var loc_lon = "{$loc_sc1[2]}"</script>
	<script>var loc_name = "{$loc_sc1[3]}"</script>
	<style>
		.fukuoka_content {
			font-size: 70%;
		}
	</style>

	EOF;

	$output .= $tenki_base_html;
    return ($output);
}

add_shortcode($loc_sc1[0], 'fukuoka_tenki1');

function fukuoka_tenki2() {

	global $tenki_base_html;
	global $fukuoka_tenki_apikey;
	global $loc_sc2;
	
	
	$output =<<<EOF
	<script>var API_KEY = "{$fukuoka_tenki_apikey}"</script>
	<script>var loc_lat = "{$loc_sc2[1]}"</script>
	<script>var loc_lon = "{$loc_sc2[2]}"</script>
	<script>var loc_name = "{$loc_sc2[3]}"</script>
	<style>
		.fukuoka_content {
			font-size: 70%;
		}
	</style>

	EOF;

	$output .= $tenki_base_html;
	return ($output);
}

add_shortcode($loc_sc2[0], 'fukuoka_tenki2');

function fukuoka_tenki3() {

	global $tenki_base_html;
	global $fukuoka_tenki_apikey;
	global $loc_sc3;
	
	
	$output =<<<EOF
	<script>var API_KEY = "{$fukuoka_tenki_apikey}"</script>
	<script>var loc_lat = "{$loc_sc3[1]}"</script>
	<script>var loc_lon = "{$loc_sc3[2]}"</script>
	<script>var loc_name = "{$loc_sc3[3]}"</script>
	<style>
		.fukuoka_content {
			font-size: 70%;
		}
	</style>

	EOF;

	$output .= $tenki_base_html;
	return ($output);
}

add_shortcode($loc_sc3[0], 'fukuoka_tenki3');

function fukuoka_tenki4() {

	global $tenki_base_html;
	global $fukuoka_tenki_apikey;	
	global $loc_sc4;
	
	
	$output =<<<EOF
	<script>var API_KEY = "{$fukuoka_tenki_apikey}"</script>
	<script>var loc_lat = "{$loc_sc4[1]}"</script>
	<script>var loc_lon = "{$loc_sc4[2]}"</script>
	<script>var loc_name = "{$loc_sc4[3]}"</script>
	<style>
		.fukuoka_content {
			font-size: 70%;
		}
	</style>

	EOF;

	$output .= $tenki_base_html;
	return ($output);
}

add_shortcode($loc_sc4[0], 'fukuoka_tenki4');

function fukuoka_tenki5() {

	global $tenki_base_html;
	global $fukuoka_tenki_apikey;
	global $loc_sc5;
	
	
	$output =<<<EOF
	<script>var API_KEY = "{$fukuoka_tenki_apikey}"</script>
	<script>var loc_lat = "{$loc_sc5[1]}"</script>
	<script>var loc_lon = "{$loc_sc5[2]}"</script>
	<script>var loc_name = "{$loc_sc5[3]}"</script>
	<style>
		.fukuoka_content {
			font-size: 70%;
		}
	</style>

	EOF;

	$output .= $tenki_base_html;
	return ($output);
}

add_shortcode($loc_sc5[0], 'fukuoka_tenki5');



//=================================================
// プラグインアクティベーション時の処理
//=================================================
function fukuoka_tenki_activate()
{
	// APIキーのデフォルト値を設定
	add_option('fukuoka_tenki_apikey', '', '', 'yes');
	add_option('fukuoka_tenki_loc_sc1', '', '', 'yes');
	add_option('fukuoka_tenki_loc_sc2', '', '', 'yes');
	add_option('fukuoka_tenki_loc_sc3', '', '', 'yes');
	add_option('fukuoka_tenki_loc_sc4', '', '', 'yes');
	add_option('fukuoka_tenki_loc_sc5', '', '', 'yes');


}
register_activation_hook($file, 'fukuoka_tenki_activate');

//=================================================
// プラグインデアクティベーション時の処理
//=================================================
function fukuoka_tenki_deactivate()
{
	// APIキーの設定を削除
	delete_option('fukuoka_tenki_apikey');
	delete_option('fukuoka_tenki_loc_sc1');
	delete_option('fukuoka_tenki_loc_sc2');
	delete_option('fukuoka_tenki_loc_sc3');
	delete_option('fukuoka_tenki_loc_sc4');
	delete_option('fukuoka_tenki_loc_sc5');
}
register_deactivation_hook($file, 'fukuoka_tenki_deactivate');

//=================================================
// プラグインアンインストール時の処理
//=================================================
function fukuoka_tenki_uninstall()
{
	// APIキーの設定を削除
	delete_option('fukuoka_tenki_apikey');
	delete_option('fukuoka_tenki_loc_sc1');
	delete_option('fukuoka_tenki_loc_sc2');
	delete_option('fukuoka_tenki_loc_sc3');
	delete_option('fukuoka_tenki_loc_sc4');
	delete_option('fukuoka_tenki_loc_sc5');
}
register_uninstall_hook($file, 'fukuoka_tenki_uninstall');

