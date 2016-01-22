<?php
$body = @file_get_contents('php://input');
$payload = json_decode($body, true);
if ($payload['ref'] == 'refs/heads/master') {

    $root = '/var/www/bioware/bioware.ru/www';
    $admin_mail = 'sonicgd@gmail.com';
    $send_to_admin = true;

    $commands = [
        'git update main'      =>
            "cd $root && git init && git stash && git fetch --all && git reset --hard origin/master",
        /*'git update cgweb'     =>
            "cd $root && git merge -s subtree --no-commit cg2_web/master",*/
        'composer self-update' => "cd $root && curl -sS https://getcomposer.org/installer | php",
        'composer update'      => "cd $root && php composer.phar update --no-dev --prefer-dist",
        'npm'                  => "cd $root/frontend/static && npm install",
        'bower'                => "cd $root/frontend/static && bower install",
        'grunt copy'           => "cd $root/frontend/static && grunt copy",
        'grunt'                => "cd $root/frontend/static && grunt publish"
    ];

    $date_start = time();

    //Execute commands
    $commands_output = [];

    executeCommands($commands, $commands_output);
    chmod("$root/yii", 0755);

    $commands = [

    ];
    executeCommands($commands, $commands_output);
    //Execute other functions

    //log
    $path = 'git-push.log';
    $f = fopen($path, 'a+');
    fwrite($f, date("Y-m-d H:i:s") . ': ' . implode("\n", $_POST) . "\n");
    fclose($f);
    chmod($path, 0775);

    $date_finish = time();

    $commands_log = implode("\n", $commands_output);

    $logs = 'Старт в: ' . date("Y-m-d H:i:s", $date_start) . "\n";
    $logs .= 'Общее время выполнения: ' . ($date_finish - $date_start) . " сек. \n";
    $logs .= $commands_log;

    if ($send_to_admin) {
        mail($admin_mail, 'BW deploy logs', $logs);
    }

    echo $logs;
}
function log_copy($dst, $src)
{
    if (copy($dst, $src)) {
        $result = "$src copy to $dst";
    } else {
        $result = "error: $src copy to $dst";
    }

    return $result;
}

/**
 * @param $commands
 * @param $commands_output
 *
 * @return array
 */
function executeCommands($commands, &$commands_output)
{
    if ($commands) {
        foreach ($commands as $name => $command) {
            $commands_output[] = $name . ' :';
            exec($command, $commands_output);
        }
    }
}
