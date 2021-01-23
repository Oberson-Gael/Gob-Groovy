<!DOCTYPE html>
<html lang="fr" class="h-100">
<head>
  <title>LISTEN YOUR YOUTUBE MUSIC</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="/css/style.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <script src="/script/createWebSocket.js"></script>
</head>
<body style="background-color:#454342; text-align: center">
    <?php
        $musicFile = "txtFiles/readMusic.txt";

        //Restart
        if(isset($_POST['restart'])){
            file_put_contents($musicFile,file_get_contents("txtFiles/memvarMusic.txt"));
        }

        //get contents of the file
        $file = file($musicFile);

        //Next
        if(isset($_POST["next"])){
            unset($file[0]);
            file_put_contents($musicFile,implode("",$file));
            $file = file($musicFile);
        }

        //Read Music
        $music_data_explode = explode(",",$file[0]);
        if($file[0] != NULL){
            $videoId = $music_data_explode[0];
            $duration = $music_data_explode[1];
        }else{
            $videoId = "9K-T6h84F7k";
            $duration ="226000";
        }

        //clear
        if(isset($_POST["delete"])){
            file_put_contents($musicFile, "");
            file_put_contents("txtFiles/memvarMusic.txt", "");
        }


        echo "<input type='hidden' id='durationvideo' value=".$duration."></input>";

        echo    '<form action="/readMusic.php" method="post">
                    <input type="hidden" name="next" value=1>
                    <button type="submit" class="btn btn-outline-info col-sm-3" id="next"><span>NEXT</span></button>
                </form>&emsp;';
        echo    '<form action="/readMusic.php" method="post">
                    <input type="hidden" name="restart" value=1>
                    <button type="submit" class="btn btn-outline-info col-sm-3" id="restart"><span>Restart</span></button>
                </form>&emsp;';
        echo    '<form action="/readMusic.php" method="post">
                    <input type="hidden" name="delete" value="Delete Now!"/>
                    <button type="submit" class="btn btn-outline-info col-sm-3" id="clear"><span>Clear</span></button>
                </form>';
        echo '<input type="hidden" id="currentVideoId" value='.$videoId.'>';
    ?>

    <div id="ytplayer"></div>
    
    <script>
        // Load the IFrame Player API code asynchronously.
        var tag = document.createElement('script');
        tag.src = "https://www.youtube.com/player_api";
        var firstScriptTag = document.getElementsByTagName('script')[0];
        firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

        // Replace the 'ytplayer' element with an <iframe> and
        // YouTube player after the API code downloads.
        var player;
        function onYouTubePlayerAPIReady() {
            player = new YT.Player('ytplayer', {
            height: '75%',
            width: '95%',
            videoId: document.getElementById("currentVideoId").value,
            events: {
                    onReady: onPlayerReady
                }
            });
        }
        function onPlayerReady(event) {
            event.target.playVideo();
        }

        function nextMusic(){
            document.getElementById("next").click();
        }

        time = parseInt(document.getElementById("durationvideo").value);
        setTimeout(function(){
            nextMusic();
            socket.send('test');
        },time);        

        socket.onmessage = function(e) {
            console.log(e.date)
            switch(e.data){
                case 'NEXT': 
                    document.getElementById("next").click();
                    socket.send('test');
                    break;
                case 'RESTART':
                    document.getElementById("restart").click();
                    socket.send('test');
                    break;
                case 'CLEAR':
                    document.getElementById("clear").click();
                    socket.send('test');
                    break;
                default:
            }     
        }
    </script>
</body>
</html>