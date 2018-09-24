//自分なりにわかりにくいところはコメントアウトをつけました
<!DOCTYPE html>
<html lang = "ja"> 
<head>
<meta http-equiv="content-type" charset="utf-8">
</head>
<body>



//～～テーブル作成～～
<?php
header("Content-Type: text/html; charset=UTF-8");
$dsn='mysql:dbname=データベース名;host=ホスト';
$user='ユーザー名';
$password='パスワード';
$pdo=new PDO($dsn,$user,$password);
//mysqlに接続
$sql="CREATE TABLE post(id INT AUTO_INCREMENT PRIMARY KEY,name char(20),comment TEXT,time char(40),num int);";
//投稿用のテーブルを作成する。　id（投稿番号：自動で取得）,name（名前）,comment（コメント）,time（投稿された時間）,num（論理削除用の番号）
$stml=$pdo->query($sql);
//$sqlをmysqlで実行
?>

<?php
header("Content-Type: text/html; charset=UTF-8");
$dsn='mysql:dbname=データベース名;host=ホスト';
$user='ユーザー名';
$password='パスワード';
$pdo=new PDO($dsn,$user,$password);
$sql2="CREATE TABLE pass(id INT AUTO_INCREMENT PRIMARY KEY,pass char(25));";
//パスワード用のテーブルを作成する。　
$stml2=$pdo->query($sql2);
?>



//～～編集機能～～
<?php
if ((!empty($_POST['edit']))&&!empty($_POST['pass3'])){//edit&pass3が送信されていれば
		$dsn='mysql:dbname=データベース名;host=ホスト';
		$user='ユーザー名';
		$password='パスワード';
		$pdo=new PDO($dsn,$user,$password);
		$sql2='SELECT*FROM pass';
		//passの情報取得
		$results2=$pdo->query($sql2);
		foreach($results2 as $row2){
		//passテーブルの中身を行の文繰り返しそれぞれの要素を$row2に格納
			if($row2['id']==$_POST['edit']&&$row2['pass']==$_POST['pass3']){
			//idとeditが一致かつpassとpass3が一致していれば
				$sql='SELECT*FROM post';
				$results=$pdo->query($sql);
				foreach($results as $row){
					if($row['id']==$_POST['edit']){
						$data1=$row['id'];
						$data2=$row['name'];
						$data3=$row['comment'];
					}
				}
			}
		}
		
}
?>



//～～フォームの作成～～
<form action = "missionGITver.php" method="post">
名前：
<input type = "text" name="name" value="<?php echo $data2; ?>"><br/>
コメント：
<input type ="text" name="comment" value="<?php echo $data3; ?>">
<input type ="hidden" name="editnum" value="<?php echo $data1; ?>" ><br/>
<input type ="text" name="pass1" placeholder="パスワード">
<input type="submit" value="送信" name="button1"><br/>

<br/>
<input type ="text" name="delete" placeholder="削除対象番号""><br/>
<input type ="text" name="pass2" placeholder="パスワード">
<input type="submit" value="削除" name="button2"><br/>
<br/>
<input type ="text" name="edit" placeholder="編集対象番号"><br/>
<input type ="text" name="pass3" placeholder="パスワード">
<input type="submit" value="編集" name="button3">
</form>




<?php
header("Content-Type: text/html; charset=UTF-8");



//～～投稿機能～～
if (isset($_POST['button1'])){
	if (!empty($_POST['name'])&&!empty($_POST['comment'])&&empty($_POST['editnum'])&&!empty($_POST['pass1'])){
		$dsn='mysql:dbname=データベース名;host=ホスト';
		$user='ユーザー名';
		$password='パスワード';
		$pdo=new PDO($dsn,$user,$password);
		$sql = $pdo->prepare("INSERT INTO post(name,comment,time,num)VALUES(:name,:comment,:time,'0')");
		//postテーブルの中身の要素をvalues()の中身に変換
		$sql->bindParam(':name',$name,PDO::PARAM_STR);
		$sql->bindParam(':comment',$comment,PDO::PARAM_STR);
		$sql->bindParam(':time',$time,PDO::PARAM_STR);
		//bindParamで変換したものを変数に
		$name=$_POST['name'];
		$comment=$_POST['comment'];
		$time=date("Y/m/d H:i:s");
		$sql->execute();
		//bindParam()はexecute()された時点で変数を評価する。
		$sql2 = $pdo->prepare("INSERT INTO pass(pass)VALUES(:pass)");
		$sql2->bindParam(':pass',$pass,PDO::PARAM_STR);
		$pass=$_POST['pass1'];
		$sql2->execute();
	}

}



//～～削除機能～～
if (isset($_POST['button2'])){
	if (!empty($_POST['delete'])&&!empty($_POST['pass2'])){
		$dsn='mysql:dbname=データベース名;host=ホスト';
		$user='ユーザー名';
		$password='パスワード';
		$pdo=new PDO($dsn,$user,$password);
		$sql2='SELECT*FROM pass';
		$results2=$pdo->query($sql2);
		foreach($results2 as $row2){
			if($row2['id']==$_POST['delete']&&$row2['pass']==$_POST['pass2']){
				$id=$_POST['delete'];
				$num='1';
				$del = "update post set num='$num' where id = $id";
				//whereで編集したい投稿番号のみを指定その投稿番号のnumを$num(=1)に編集
				$resultdel=$pdo->query($del);
			}
		}
	}
}




//～～編集機能～～
//※editnumはフォーム作成時にtypeをhiddenにしているので見えなくしている
if (isset($_POST['button1'])){
	if (!empty($_POST['name'])&&!empty($_POST['comment'])&&!empty($_POST['editnum'])&&!empty($_POST['pass1'])){
		$dsn='mysql:dbname=データベース名;host=ホスト';
		$user='ユーザー名';
		$password='パスワード';
		$pdo=new PDO($dsn,$user,$password);
		$sql2='SELECT*FROM pass';
		$results2=$pdo->query($sql2);
		foreach($results2 as $row2){
			if($row2['id']==$_POST['editnum']){
				$id=$_POST['editnum'];
				$name=$_POST['name'];
				$comment=$_POST['comment'];
				$time=date("Y/m/d H:i:s");
				$edit = "update post set name='$name',comment='$comment',time='$time' where id = $id";
				$resultedit=$pdo->query($edit);
				$newpass=$_POST['pass1'];
				$id=$_POST['editnum'];
				$editpass="update pass set pass='$newpass' where id = $id";
				$resulteditpass=$pdo->query($editpass);
			}
		}
	}
}
?>



//～～WEB表示～～
<?php
header("Content-Type: text/html; charset=UTF-8");
$dsn='mysql:dbname=データベース名;host=ホスト';
$user='ユーザー名';
$password='パスワード';
$pdo=new PDO($dsn,$user,$password);
$sql='SELECT*FROM post';
$results=$pdo->query($sql);
foreach($results as $row){
	if($row['num']=='0'){
	//numが０のときのみ表示するようにする。削除機能のところでは削除したい投稿のnumを1にかえているのでweb上では表示されず削除したように見える
		echo $row['id'].',';
		echo $row['name'].',';
		echo $row['comment'].',';
		echo $row['time'].'<br>';
	}
}
?>
</body>
</html>

