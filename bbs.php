<?php
  // ここにDBに登録する処理を記述する
//1 DBへ接続
// $dsn= 'mysql:dbname=;host=LAA0778973-onelinebbs;host=mysql114.phy.lolipop.lan';
//   $user= 'LAA0778973';
//   $password= '19900608jS';
$dsn= 'mysql:dbname=oneline_bbs;host=localhost';
$user= 'root';
$password='';
  $dbh= new PDO($dsn, $user, $password);
  $dbh->query('SET NAMES utf8');

/////////////////////////////////
//歯車アイコンクリック時
  $editName= '';
  $editComment= '';
  $id='';
  if (!empty($_GET['action'])&& $_GET['action']=='edit'){
    $sql= 'SELECT * FROM `posts` WHERE `id` = ?';
    $data[] = $_GET['id'];
//SQL実行

    $stmt = $dbh->prepare($sql);
    $stmt->execute($data);
//データを取得
    $rec= $stmt->fetch(PDO::FETCH_ASSOC);
//値を変数に格納
    $editName = $rec['nickname'];
    $editComment = $rec['comment'];
    $id= $rec['id'];
  }
  



//POST送信された時のみ登録処理実行
  if (!empty($_POST)) {
    if (!empty($_POST['$id'])){
//データ更新
      $sql='UPDATE `posts` SET `nickname`=?,`comment`=? WHERE `id`=?';
$data[]= $_POST['nickname'];
$data[]= $_POST['comment'];
$data[]= $POST['id'];

}else{
//データ登録
$sql = 'INSERT INTO `posts`(`nickname`, `comment`, `created`, `delete_flag`) VALUES (?,?,now(),0)';
$data[]= $_POST['nickname'];
$data[]= $_POST['comment'];
   }


//SQLを実行
$stmt= $dbh->prepare($sql);
  $stmt->execute($data);
}

//////////////////////////////////
//データの削除処理
if (!empty($_GET['action'])&& $_GET['action']== 'delete'){
// $sql='DELETE FROM `posts` WHERE `id`=?';
$sql= 'UPDATE `posts` SET `delete_flag`= 1 WHERE `id`=?';
$data[]= $_GET['id'];

//SQLを実行
$stmt= $dbh->prepare($sql);
  $stmt->execute($data);


//bbs.phpに画面を移行!!!よく使う!!!
header('Location: bbs.php');
exit();
 }


////////////////////////////////////
//データの一覧表示
$sql= 'SELECT * FROM `posts` WHERE `delete_flag`= 0 ORDER BY `created` DESC';
//SQLを実行
$stmt= $dbh->prepare($sql);
  $stmt->execute();

  $data= array();

//データを取得
  while (1) {
    $rec= $stmt->fetch(PDO::FETCH_ASSOC);
    if ($rec==false){
      break;
    }
//格納用変数にレコードのでアーを入れる
    $data[]= $rec;
 }


//DBを切断
$dbh= null;
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>セブ掲示版</title>

  <!-- CSS -->
  <link rel="stylesheet" href="assets/css/bootstrap.css">
  <link rel="stylesheet" href="assets/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="assets/css/form.css">
  <link rel="stylesheet" href="assets/css/timeline.css">
  <link rel="stylesheet" href="assets/css/main.css">
</head>
<body>
  <!-- ナビゲーションバー -->
  <nav class="navbar navbar-default navbar-fixed-top">
      <div class="container">
          <!-- Brand and toggle get grouped for better mobile display -->
          <div class="navbar-header page-scroll">
              <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                  <span class="sr-only">Toggle navigation</span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
              </button>
              <a class="navbar-brand" href="#page-top"><span class="strong-title"><i class="fa fa-linux"></i> Oneline bbs</span></a>
          </div>
          <!-- Collect the nav links, forms, and other content for toggling -->
          <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
              <ul class="nav navbar-nav navbar-right">
              </ul>
          </div>
          <!-- /.navbar-collapse -->
      </div>
      <!-- /.container-fluid -->
  </nav>

  <!-- Bootstrapのcontainer -->
  <div class="container">
    <!-- Bootstrapのrow -->
    <div class="row">

      <!-- 画面左側 -->
      <div class="col-md-4 content-margin-top">
        <!-- form部分 -->
        <form action="bbs.php" method="post">
          <!-- nickname -->
          <div class="form-group">
            <div class="input-group">
              <input type="text" name="nickname" class="form-control" id="validate-text" placeholder="nickname" required value="<?php echo $editName; ?>">

              <span class="input-group-addon danger"><span class="glyphicon glyphicon-remove"></span></span>
            </div>
          </div>
          <!-- comment -->
          <div class="form-group">
            <div class="input-group" data-validate="length" data-length="4">
              <textarea type="text" class="form-control" name="comment" id="validate-length" placeholder="comment" required ><?php echo $editComment; ?></textarea>
              <span class="input-group-addon danger"><span class="glyphicon glyphicon-remove"></span></span>
            </div>
          </div>

          <!-- つぶやくボタン -->
          <?php if (!empty($_GET['action']) && $_GET['action']=='edit'):
          ?>
          <button type="submit" class="btn btn-primary col-xs-12" >更新する</button>
          <input type="hidden" name="id" value="<?php echo $id;?>">

          <?php else: ?>
            <button type="submit" class="btn btn-primary col-xs-12" >つぶやく</button>
            <?php endif;?>
          
          
        </form>
      </div>

      <!-- 画面右側 -->
      <div class="col-md-8 content-margin-top">
        <div class="timeline-centered">
        <?php foreach ($data as $d): ?>
          <article class="timeline-entry">
              <div class="timeline-entry-inner">

              <a href="bbs.php?action=edit&id=<?php echo $d['id']; ?>">
                  <div class="timeline-icon bg-success">
                      <i class="entypo-feather"></i>
                      <i class="fa fa-cogs"></i>
                  </div>
              </a>
                  <div class="timeline-label">
                  <!-- 1 文字列型から日付け型へ -->
                  　<?php
                     $created= strtotime($d['created']);
                   
                   //2 フォーマットを措定
                   $created= date('Y/m/d', $created);

                   ?>

                      <h2><a href="#"><?php echo $d['nickname']; ?></a> <span><?php echo $created;?></span></h2>
                      <p><?php echo $d['comment'];?></p>
                      <a href="bbs.php?action=delete&id=<?php echo $d['id']; ?>" onclick="return confirm('本当に削除しますか？');"><i class="fa fa-trash trash" aria-hidden="true"></i></a>
                  </div>
              </div>
          </article>
        <?php endforeach; ?>

          <article class="timeline-entry begin">
              <div class="timeline-entry-inner">
                  <div class="timeline-icon" style="-webkit-transform: rotate(-90deg); -moz-transform: rotate(-90deg);">
                      <i class="entypo-flight"></i> +
                  </div>
              </div>
          </article>
        </div>
      </div>

    </div>
  </div>

  <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
  <!-- Include all compiled plugins (below), or include individual files as needed -->
  <script src="assets/js/bootstrap.js"></script>
  <!-- <script src="assets/js/form.js"></script> -->
</body>
</html>



