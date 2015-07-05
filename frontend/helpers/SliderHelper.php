<?php
namespace biowareru\frontend\helpers;

class SliderHelper
{
    public static function getGames()
    {
        return [
            'sonic' => [
                'title' => 'Sonic Chronicles',
                'url'   => 'sonic'
            ],
            'bg'    => [
                'title' => 'Baldur\'s Gate',
                'url'   => 'baldurs_gate',
            ],
            'da'    => [
                'title' => 'Dragon Age: Начало',
                'url'   => 'dragon_age'
            ],
            'tor'   => [
                'title' => 'Star Wars: The Old Republic',
                'url'   => 'the_old_republic'
            ],
            'da2'   => [
                'title' => 'Dragon Age II',
                'url'   => 'dragon_age_2'
            ],
            'dai'   => [
                'title' => 'Dragon Age: Инквизиция',
                'url'   => 'dragon_age_inquisition'
            ],
            'me4'   => [
                'title' => 'Mass Effect: Andromeda',
                'url'   => 'mass_effect_andromeda'
            ],
            'me3'   => [
                'title' => 'Mass Effect 3',
                'url'   => 'mass_effect_3'
            ],
            'kotor' => [
                'title' => 'Stat Wars: Knights of the Old Republic',
                'url'   => 'kotor'
            ],
            'me'    => [
                'title' => 'Mass Effect',
                'url'   => 'mass_effect'
            ],
            'nwn'   => [
                'title' => 'NeverWinter Nights',
                'url'   => 'neverwinter_nights'
            ],
            'me2'   => [
                'title' => 'Mass Effect 2',
                'url'   => 'mass_effect_2'
            ],
            'nno'   => [
                'title' => 'NeverWinter Online',
                'url'   => 'neverwinter'
            ],
            'je'    => [
                'title' => 'Jade Empire',
                'url'   => 'jade_empire'
            ]
        ];
    }
}