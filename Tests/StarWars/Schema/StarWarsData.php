<?php
/**
 * Copyright (c) 2015–2018 Alexandr Viniychuk <http://youshido.com>.
 * Copyright (c) 2015–2018 Portey Vasil <https://github.com/portey>.
 * Copyright (c) 2018 Ryan Parman <https://github.com/skyzyx>.
 * Copyright (c) 2018 Ashley Hutson <https://github.com/asheliahut>.
 * Copyright (c) 2015–2018 Contributors.
 *
 * http://opensource.org/licenses/MIT
 */

declare(strict_types=1);
/**
 * Date: 07.12.15.
 */

namespace Youshido\Tests\StarWars\Schema;

class StarWarsData
{
    public static function humans()
    {
        return [
            '1000' => self::luke(),
            '1001' => self::vader(),
            '1002' => self::han(),
            '1003' => self::leia(),
            '1004' => self::tarkin(),
        ];
    }

    /**
     * We export artoo directly because the schema returns him
     * from a root field, and hence needs to reference him.
     */
    public static function artoo()
    {
        return [

            'id'              => '2001',
            'name'            => 'R2-D2',
            'friends'         => ['1000', '1002', '1003'],
            'appearsIn'       => [4, 5, 6],
            'primaryFunction' => 'Astromech',
        ];
    }

    public static function droids()
    {
        return [
            '2000' => self::threepio(),
            '2001' => self::artoo(),
        ];
    }

    /**
     * Helper function to get a character by ID.
     *
     * @param mixed $id
     */
    public static function getCharacter($id)
    {
        $humans = self::humans();
        $droids = self::droids();

        if (isset($humans[$id])) {
            return $humans[$id];
        }

        if (isset($droids[$id])) {
            return $droids[$id];
        }
    }

    /**
     * @param $episode
     *
     * @return array
     */
    public static function getHero($episode)
    {
        if (5 === $episode) {
            // Luke is the hero of Episode V.
            return self::luke();
        }

        // Artoo is the hero otherwise.
        return self::artoo();
    }

    /**
     * Allows us to query for a character's friends.
     *
     * @param mixed $character
     */
    public static function getFriends($character)
    {
        return \array_map([__CLASS__, 'getCharacter'], $character['friends']);
    }

    private static function luke()
    {
        return [
            'id'         => '1000',
            'name'       => 'Luke Skywalker',
            'friends'    => ['1002', '1003', '2000', '2001'],
            'appearsIn'  => [4, 5, 6],
            'homePlanet' => 'Tatooine',
        ];
    }

    private static function vader()
    {
        return [
            'id'         => '1001',
            'name'       => 'Darth Vader',
            'friends'    => ['1004'],
            'appearsIn'  => [4, 5, 6],
            'homePlanet' => 'Tatooine',
        ];
    }

    private static function han()
    {
        return [
            'id'        => '1002',
            'name'      => 'Han Solo',
            'friends'   => ['1000', '1003', '2001'],
            'appearsIn' => [4, 5, 6],
        ];
    }

    private static function leia()
    {
        return [
            'id'         => '1003',
            'name'       => 'Leia Organa',
            'friends'    => ['1000', '1002', '2000', '2001'],
            'appearsIn'  => [4, 5, 6],
            'homePlanet' => 'Alderaan',
        ];
    }

    private static function tarkin()
    {
        return [
            'id'        => '1004',
            'name'      => 'Wilhuff Tarkin',
            'friends'   => ['1001'],
            'appearsIn' => [4],
        ];
    }

    private static function threepio()
    {
        return [
            'id'              => '2000',
            'name'            => 'C-3PO',
            'friends'         => ['1000', '1002', '1003', '2001'],
            'appearsIn'       => [4, 5, 6],
            'primaryFunction' => 'Protocol',
        ];
    }
}
