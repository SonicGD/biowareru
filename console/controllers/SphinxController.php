<?php
/**
 * Created by PhpStorm.
 * User: sonic
 * Date: 13-Jun-15
 * Time: 18:54
 */

namespace biowareru\console\controllers;


use yii\console\Controller;

class SphinxController extends Controller
{
    public static $config = [
        'be_news'         => [
            'columns' => 'id, game_id, developer_id, topic_id, url, source, title, short_text, add_text',
            'table'   => 'be_news',
            'where'   => 'WHERE pub=1',
            'uint'    => ['game_id', 'developer_id', 'topic_id'],
            'string'  => ['url', 'source', 'title', 'short_text', 'add_text']
        ],
        'be_games'        => [
            'columns' => 'id, url, developer_id, title, `desc` as description',
            'table'   => 'be_games',
            'where'   => '',
            'uint'    => ['developer_id'],
            'string'  => ['url', 'title', 'description']
        ],
        'be_articles'     => [
            'columns' => 'id, url, announce, title, text',
            'table'   => 'be_articles',
            'where'   => 'WHERE pub=1',
            'uint'    => [],
            'string'  => ['url', 'text', 'title', 'announce']
        ],
        'be_articlesCats' => [
            'columns' => 'id, url, title, content',
            'table'   => 'be_articles_cats',
            'where'   => '',
            'uint'    => [],
            'string'  => ['url', 'title', 'content']
        ],
        'be_files'        => [
            'columns' => 'id, url, title, `desc` as description, announce',
            'table'   => 'be_files',
            'where'   => '',
            'uint'    => [],
            'string'  => ['url', 'description', 'title', 'announce']
        ],
        'be_filesCats'    => [
            'columns' => 'id, url, title, descr',
            'table'   => 'be_files_cats',
            'where'   => '',
            'uint'    => [],
            'string'  => ['url', 'title', 'descr']
        ],
        'be_galleryCats'  => [
            'columns' => 'id, url, title, `desc` as description',
            'table'   => 'be_gallery_cats',
            'where'   => '',
            'uint'    => [],
            'string'  => ['url', 'description', 'title']
        ]
    ];

    public function actionGenerate()
    {
        $dbUser = \Yii::$app->params['db']['username'];
        $dbPass = \Yii::$app->params['db']['password'];
        $dbName = \Yii::$app->params['db']['dbname'];
        $datapath = \Yii::$app->params['db']['sphinxPath'];
        $conf = <<<EOF
source bioware {\n
	type			= mysql\n
	sql_host		= localhost\n
	sql_user		= {$dbUser}\n
	sql_pass		= {$dbPass}\n
	sql_db			= {$dbName}\n
	sql_port		= 3306	# optional, default is 3306\n
	sql_query_pre = SET NAMES utf8\n
    sql_query_pre = SET CHARACTER SET utf8\n
}\n

EOF;
        foreach (self::$config as $key => $config) {
            $conf .= <<<EOF
source {$key}Src : bioware\n
{

	sql_query   = SELECT {$config['columns']} FROM {$config['table']} {$config['where']}\n

EOF;
            foreach ($config['uint'] as $columnName) {
                $conf .= <<<EOF
                sql_attr_uint		= {$columnName}\n
EOF;
            }
            foreach ($config['string'] as $columnName) {
                $conf .= <<<EOF
                sql_field_string		= {$columnName}\n
EOF;
            }

            $conf .= <<<EOF
	sql_ranged_throttle	= 0\n
}\n

index {$key}
{
	source			= {$key}Src
	path			= {$datapath}{$key}
	docinfo			= extern
	dict			= keywords
	mlock			= 0
	morphology		= stem_en, stem_ru
	min_word_len		= 1
	html_strip		= 0
}

EOF;

        }
        file_put_contents('sphinx.conf', $conf);
    }
}