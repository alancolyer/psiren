<?php

        // Report all PHP errors
        error_reporting(-1);

        // Same as error_reporting(E_ALL);
        ini_set('error_reporting', E_ALL);

    /**
    * Psiren is an AJAX / PHP interface for playing audio on a Linux box over a network.
    *
    * The server project is written in PHP and uses the mpg123 audio player to play the songs
    * on the machine it's installed on. Configurable options such as player commands etc are
    * at the top of this file.
    *
    * This is the index, where all requests go.
    * essentially it is a bunch of functions that are
    * called by the client using AJAX, generally things
    * are returned using JSON.
    *
    * @param action String the action to perform
    * @return mixed boolean for 'doing' actions, JSON arrays for 'listing' actions.
    * @author Alan Colyer
    * @since 1.0
    */

    define("MUSIC_DIRECTORY", "/home/pi/music/");
    define("PLAY_COMMAND", "/var/www/psiren/server/play.sh");
    define("FILE_TYPES", "mp3,mpg,wav,ogg,flac");


    if (isset($_REQUEST['action']))
    {
        if ($_REQUEST['action']=='list_audio')
        {
                return list_audio();
        }
        if ($_REQUEST['action']=='play' && isset($_REQUEST['file']))
        {
            return play_file(urldecode($_REQUEST['file']));
        }
    }

    function list_audio($json=false)
    {
        //lists all currently playable audio
        $files=array();
        //have a wee look on the hard drive
        $it = new RecursiveDirectoryIterator(MUSIC_DIRECTORY);
                foreach(new RecursiveIteratorIterator($it) as $file)
                {
                    if (end(explode( '.', $file->getFilename())) == 'mp3')
                    {
                        $files[]=array(
                                        'name' => $file->getFilename(),
                                        'path' => $file->getPathname()
                                );

                    }
                }

                if ($json)
                {
                    return json_encode($files);
                }
                else
                {
                        return $files;
                }
    }

    function play_file($file)
    {
        echo 'attempting to play file: '.$file.'</br>';
        //$file = str_replace(' ', '\ ', $file);
        //$file = str_replace("'","\'", $file);

        //stops whatever track is playing now, and starts the specified track.
        $command = PLAY_COMMAND." \"".$file."\"";

        echo 'command: '.$command;

        //echo shell_exec($command);
        //exec('killall mpg123');
        echo shell_exec($command);
        return true;
    }

?>
<html>
  <head><title>Psiren PHP MP3 Server</title></head>
  <body>
        <h1>Welcome!</h1>
    <div id='audio'>
        <h2>Current Audio Files:</h2>
        <table>
                    <?php
                        $files = list_audio();
                            foreach ($files as $file)
                            {
                                ?>
                                <tr>
                      <td>
                        <a href='index.php?action=play&file=<?php echo urlencode($file['path']);?>'><?php echo $file['name'];?></a>
                      </td>
                                </tr>
                                <?php
                            }
                    ?>
            </table>
        </div>
  </body>
</html>