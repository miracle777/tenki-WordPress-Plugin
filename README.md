# tenki-WordPress-Plugin
# 天気をWordPressの記事の中で表示するプラグイン

このプラグインは、下記の機能があります。

* WordPressの投稿記事や固定記事の中で、天気を表示できる
* ２４時間単位で、5日分の天気を表示できる
* ５か所まで天気を表示する座標の登録ができる
* 表示名を登録できる
* 好きなショートコードを登録できる
* 登録した地点の修正や削除ができる

# 表示内容
![スクリーンショット_20230108_195321](https://user-images.githubusercontent.com/13194391/211198003-575a81a2-3a47-463a-a93c-1b266ea0f224.png)
WordPressの管理画面で、動作確認の意味で、一つ目の地点の表示ができます。

![スクリーンショット_20230108_195342](https://user-images.githubusercontent.com/13194391/211198055-b3010ea9-1411-426d-8414-b3968d6fb886.png)
API KEYの登録も、できます。

![スクリーンショット_20230108_195405](https://user-images.githubusercontent.com/13194391/211198063-b9df7beb-f1b4-44fd-8f99-ba8bf910c67c.png)
最大５か所まで、登録できます。

![スクリーンショット_20230108_202855](https://user-images.githubusercontent.com/13194391/211198076-af0a86fb-d1c2-49f4-bbdc-b8c2a2168c00.png)
ショートコードを削除して保存すると、その地点を削除できます。
値を修正して保存すると、上書きされます。

# このプラグインは日本語表示です。
このプラグインは、日本国内での利用を想定しています。
多言語化は、行っていません。

# 表示名について
openweathermapの都市表示ですが、日本語で表示される地点とローマ字で表示される地点があるため、本来の都市の名称を利用していません。

必ず都市名は、入力してください。
一応入力が漏れている場合は、エラーになるようにしてあります。

表示名の文字数は、制限していません。
表示が崩れないように、適宜調整してください。

# 地点登録について
## ショートコード
ショートコードは、英数文字とアンダーバーのみ利用できます。
また、キーボードから入力する際に、余分な見えない文字が入力されてしまうことが有るようです。

もし正常に天気を表示できず、ショートコードがそのまま表示される場合は、入力したショートコードの先頭をDELキーを使って削除してみてください。
一文字か二文字消すことができる場合がありますので、ショートコードを入力した際は、気を付けてください。

## 座標について
座標も、キーボードから入力する際に、コピー＆ペーストを行うと見えない文字が先頭に入ってしまう場合があります。
入力した文字の先頭に隙間があるようでしたら、DELキーを使って削除してみてください。

# 作者
Masaru Kumazaki

## Twitter
@masaru21

## web
https://www.itsupporter-mk.com/

# ライセンス
商用利用も、無料で自由に使ってください。

このプログラムを利用して損害が生じても、私は責任を追うことはできません。

