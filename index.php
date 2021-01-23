
<!DOCTYPE html>
<html lang="fr" class="h-100">
<head>
  <title>ADD YOUR YOUTUBE MUSIC</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="/css/style.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <script src="/script/createWebSocket.js"></script>
</head>
<body style="background-color:#454342">
<?php 
        $API_key    = "Your API Key";
        
        $titleVideo = str_replace(" ","%",htmlentities($_POST["title"]));
        if($titleVideo !== ""){
            $videoList = json_decode(file_get_contents("https://youtube.googleapis.com/youtube/v3/search?part=snippet&q=".$titleVideo."&key=".$API_key));
            foreach($videoList->items as $item){
                $img = ($item->snippet->thumbnails->medium->url);
                $title = $item->snippet->title;

                if(isset($item->id->videoId)){

                    $videoDuration = json_decode(file_get_contents("https://youtube.googleapis.com/youtube/v3/videos?part=contentDetails&id=".$item->id->videoId."&key=".$API_key));
                    foreach($videoDuration->items as $val){
                        $array = array("PT","M", "S");
                        $array2 = array("",",", "");
                        $array2_repace = str_replace($array, $array2,$val->contentDetails->duration);
                        $array2_explode = explode(",",$array2_repace);
                        $durationMulli = ($array2_explode[0] * 60 + $array2_explode[1]) * 1000 - 500;
                        addMusic($item->id->videoId, $durationMulli, $title, $img);
                    }
                    break;
                }
            }
        }
        
        function showPlaylist(){
            $file = "./txtFiles/readMusic.txt";
            $contents = file_get_contents($file);
            $lines = explode("\n", $contents);

            foreach($lines as $word) {
                if($word){
                    $word = explode(",", $word);
                    echo '<ul class="list-group liste" style="margin-bottom: 5px">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <p style="font-size: 12px">'.$word[2].'</p>
                                <div class="image-parent">
                                    <img src="'.$word[3].'" class="img-fluid" alt="quixote">
                                </div>
                            </li>
                        </ul>';
                }
            }                        
        }

        function addMusic($videoId, $time, $title, $img){
            $fileRead = './txtFiles/readMusic.txt';
            $fileMem = './txtFiles/memvarMusic.txt';
            $content = $videoId.",".$time.",".$title.",".$img."\n";
            file_put_contents($fileRead, $content, FILE_APPEND | LOCK_EX);
            file_put_contents($fileMem, $content, FILE_APPEND | LOCK_EX);
            echo '<script>
                    socket.onopen = () => socket.send("test");
                </script>';
        }

        
?>
    <div class="container h-100">
        <div class="row align-items-center h-100 ">
            <div class="col-sm-12">
                <button onclick="transmitMessage('NEXT')" class="btn btn-outline-info col-sm-3"><span>NEXT</span></button>
                <button onclick="transmitMessage('RESTART')" class="btn btn-outline-info col-sm-3"><span>RESTART</span></button>
                <button onclick="transmitMessage('CLEAR')" class="btn btn-outline-info col-sm-3"><span>CLEAR</span></button>
                
                <br><br><br>

                <form action="/index.php" method="post" id="addMusic">
                    <div class="form-group">
                        <input type="text" name="title" class="form-control" placeholder="Tittre ou URL Youtube">
                    </div>
                    <button type="submit" class="btn btn-outline-info col-sm-12"><span>SEND</span></button>
                </form>
                <br><br><br>
                <?php
                    showPlaylist();
                ?>
            </div>
        </div>
    </div>
 </body>
 
<script>
     function transmitMessage(action) {
         socket.send(action);
     }
 
     socket.onmessage = function(e){
         setTimeout(function(){
             window.location.replace("/");    
         }, 200);
         
     }
 </script>
</html>