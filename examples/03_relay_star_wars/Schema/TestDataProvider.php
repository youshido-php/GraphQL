<?php
/*
* This file is a part of GraphQL project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 5/10/16 11:07 PM
*/

namespace Examples\StarWars;


class TestDataProvider
{
    private static $ships = [
        'xwing' => ['id' => 1, 'name' => 'X-Wing'],
        'ywing' => ['id' => 2, 'name' => 'Y-Wing'],
        'awing' => ['id' => 3, 'name' => 'A-Wing'],

        'falcon'         => ['id' => 4, 'name' => 'Millenium Falcon'],
        'homeOne'        => ['id' => 5, 'name' => 'Home One'],
        'tieFighter'     => ['id' => 6, 'name' => 'TIE Fighter'],
        'tieInterceptor' => ['id' => 7, 'name' => 'TIE Interceptor'],
        'executor'       => ['id' => 8, 'name' => 'Executor'],
    ];

    private static $factions = [
        'rebels' => [
            'id'    => 1,
            'name'  => 'Alliance to Restore the Republic',
            'ships' => [1, 2, 3, 4, 5]
        ],
        'empire' => [
            'id'    => 2,
            'name'  => 'Galactic Empire',
            'ships' => [6, 7, 8]
        ],
    ];

    private static $nextShipId = 9;

    public static function createShip($name, $factionId)
    {
        $newShip = [
            'id'   => self::$nextShipId++,
            'name' => $name
        ];

        self::$ships[$newShip['id']]           = $newShip;
        self::$factions[$factionId]['ships'][] = $newShip['id'];

        return $newShip;
    }

    public static function getShip($id)
    {
        return empty(self::$ships[$id]) ? null : self::$ships[$id];
    }

    public static function getFaction($id)
    {
        return empty(self::$factions[$id]) ? null : self::$factions[$id];
    }
    /**
     * export function getFactions(names) {
    return names.map(name => {
    if (name === 'empire') {
    return empire;
    }
    if (name === 'rebels') {
    return rebels;
    }
    return null;
    });
    }
     */
}
