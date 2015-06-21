<?php
/*
 * This file is part of the RBot.
 *
 * (c) Francois Lajoie <o_o@francoislajoie.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RBot;

use RBot\RBot;
use RBot\BaseDataObject;
use RBot\Exception;

class ConsolePreset extends BaseDataObject
{
    /**
     * Default preset
     * @var array
     */
    protected $_data = [
        'error' => [
            'color' => '#FF9999'
        ],
        'warning' => [
            'color' => '#FFFF99'
        ],
        'important' => [
            'color'       => '#F9F9F9',
            'text-shadow' => '1px 2px 3px #000'
        ],
        'success' => [
            'color' => '#9EFF99'
        ],
        'notice' => [
            'color' => '#3498DB'
        ]
    ];

    /**
     * Check app config console.presets and merge
     */
    public function __construct()
    {
        $conf = RBot::conf('console.presets');
        if(is_array($conf) && !empty($conf)) {
            parent::__construct($conf);
        }
    }
}