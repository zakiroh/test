<!DOCTYPE html>
<html lang = "ja">
    <head>
        <meta chaesret = "UTF-8">
        <title>mission5-1-2.php</title>
    </head>
    
    <body>
        
    <?php
    $dsn = 'データベース名';
    $user = 'ユーザー名';
    $password = 'パスワード';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

	
    $sql = "CREATE TABLE IF NOT EXISTS KEIJIBAN"//テーブルの作成
	." ("
	. "id INT AUTO_INCREMENT PRIMARY KEY,"//自動で登録されていうナンバリング
	. "name char(32),"//名前を入れる。文字列、半角英数で32文字。
	. "comment TEXT,"//コメントを入れる。文字列、長めの文章も入る。
	. "datetime timestamp"//日時の表示
	.");";
	$stmt = $pdo->query($sql);//SQL文の実行

    /////////投稿フォーム////////////    
    if (!empty($_POST["name"]) && !empty($_POST["comment"]) && !empty($_POST["password1"])) {
        //送信されたものがあり、中身が空でないときに以下の処理を行う
        $pass = $_POST["password1"];
        if($pass == "pass") {//passが一致したとき、以下の処理を行う
            $name = $_POST["name"];
            $comment = $_POST["comment"];
            $datetime = date("Y/m/d G:i:s");
            
            if (!empty($_POST["edit2"])) {//送信されたものがあり、中身が空でないときに以下の処理を行う
                $id = $_POST["edit2"]; //変更する投稿番号
	            $sql = 'UPDATE KEIJIBAN SET name=:name,comment=:comment,datetime=:datetime WHERE id=:id';
	            $stmt = $pdo -> prepare($sql);
	            $stmt -> bindParam(':name', $name, PDO::PARAM_STR);
	            $stmt -> bindParam(':comment', $comment, PDO::PARAM_STR);
	            $stmt -> bindParam(':id', $id, PDO::PARAM_INT);
	            $stmt -> bindParam(':datetime', $datetime, PDO::PARAM_STR);
	            $stmt -> execute();
            }else{//新規投稿
                $sql = $pdo -> prepare("INSERT INTO KEIJIBAN (name, comment, datetime) VALUES (:name, :comment, :datetime)");
	            $sql -> bindParam(':name', $name, PDO::PARAM_STR);
	            $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
                $sql -> bindParam(':datetime', $datetime, PDO::PARAM_STR);
                $sql -> execute();
            }
        }
    }
    
        
        
    ////////削除フォーム/////////
    if (!empty($_POST["delete"]) && !empty($_POST["password2"])) {//送信されたものがあり、中身が空でないときに以下の処理を行う
        $pass = $_POST["password2"];
        if ($pass == "pass") {    
            $id = $_POST["delete"];
	        $sql = 'delete from KEIJIBAN where id=:id';
	        $stmt = $pdo -> prepare($sql);
	        $stmt -> bindParam(':id', $id, PDO::PARAM_INT);
	        $stmt -> execute();
        }
    }
    
    ////////編集フォーム//////////
    if (!empty($_POST["edit1"]) && !empty($_POST["password3"])) {//送信されたものがあり、中身が空でないときに以下の処理を行う。
        $pass = $_POST["password3"];
        if($pass == "pass"){
            $edit1 = $_POST["edit1"];                                          
        }        
    }
    ?>
    
    <form action = "mission5-1-2.php" method = "post">
    【投稿フォーム】<br>
    <input type = "text" name = "name" placeholder = "名前" 
    value = "<?php 
    if(isset($edit1)){$id = $edit1;
        $sql = 'SELECT * FROM KEIJIBAN WHERE id=:id ';
        $stmt = $pdo -> prepare($sql);                  // ←差し替えるパラメータを含めて記述したSQLを準備し、
        $stmt -> bindParam(':id', $id, PDO::PARAM_INT); // ←その差し替えるパラメータの値を指定してから、
        $stmt -> execute();                             // ←SQLを実行する。
        $results = $stmt -> fetchAll(); 
	    foreach ($results as $row){//$rowの中にはテーブルのカラム名が入る
		    echo $row['name'];
	    }
    }?>"><br>
        
    <input type = "text" name = "comment" placeholder ="コメント" 
    value ="<?php 
    if(isset($edit1)){$id = $edit1;
        $sql = 'SELECT * FROM KEIJIBAN WHERE id=:id ';
        $stmt = $pdo -> prepare($sql);                  // ←差し替えるパラメータを含めて記述したSQLを準備し、
        $stmt -> bindParam(':id', $id, PDO::PARAM_INT); // ←その差し替えるパラメータの値を指定してから、
        $stmt -> execute();                             // ←SQLを実行する。
        $results = $stmt -> fetchAll(); 
	    foreach ($results as $row){//$rowの中にはテーブルのカラム名が入る
		    echo $row['comment'];
        }
    }?>">
    <!--編集用のテキストボックス（非表示)-->
    <input type = "hidden" name = "edit2" value = "<?php if(isset($edit1)){echo $edit1;}?>"><br>
    <input type = "text" name = "password1" placeholder = "パスワード" value = "">
    <input type = "submit" name = "submit"><br>
    </form>
        
    <!--削除フォーム-->    
    <form action = "mission5-1-2.php" method = "post">
    【削除フォーム】<br>
    <input type = "text" name = "delete" placeholder = "削除番号"><br>
    <input type = "text" name ="password2" placeholder = "パスワード" value = "">
    <input type = "submit" value = "削除">
    </form>
        
    <!--編集フォーム-->    
    <form action="mission5-1-2.php" method="post">
    【 編集フォーム 】<br>
    <input type = "text" name = "edit1" placeholder = "編集番号"><br>
    <input type = "text" name = "password3" placeholder = "パスワード" value = "">
    <input type = "submit" name = "編集する"> <br>
    </form>

    【投稿一覧】<br>

    <?php
    //////データベースに書き込まれた内容をブラウザに表示させる//////
    $sql = 'SELECT * FROM KEIJIBAN';
	$stmt = $pdo -> query($sql);
	$results = $stmt -> fetchAll();
	foreach ($results as $row){
		echo $row['id'].',';
		echo $row['name'].',';
        echo $row['comment'].',';
        echo $row['datetime'];
	    echo "<hr>";
	}
    ?>  
        
    </body>
    </html>