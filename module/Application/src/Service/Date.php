<?php

namespace Volley\Services;

/**
*
*/
class Date
{
    protected $_date;

    public static $translate = array(
        '/Monday/i'    => 'Lundi',
        '/Tuesday/i'   => 'Mardi',
        '/Wednesday/i' => 'Mercredi',
        '/Thursday/i'  => 'Jeudi',
        '/Friday/i'    => 'Vendredi',
        '/Saturday/i'  => 'Samedi',
        '/Sunday/i'    => 'Dimanche',
        '/january/i'   => 'Janvier',
        '/february/i'  => 'Février',
        '/march/i'     => 'Mars',
        '/april/i'     => 'Avril',
        '/may/i'       => 'Mai',
        '/june/i'      => 'Juin',
        '/july/i'      => 'Juillet',
        '/august/i'    => 'Août',
        '/september/i' => 'Septembre',
        '/october/i'   => 'Octobre',
        '/november/i'  => 'Novembre',
        '/december/i'  => 'Décembre',
        '/Mon/i'       => 'Lun',
        '/Tue/i'       => 'Mar',
        '/Wed/i'       => 'Mer',
        '/Thu/i'       => 'Jeu',
        '/Fri/i'       => 'Ven',
        '/Sat/i'       => 'Sam',
        '/Sun/i'       => 'Dim',
        '/jan/i'       => 'Jan',
        '/feb/i'       => 'Fev',
        '/mar/i'       => 'Mar',
        '/apr/i'       => 'Avr',
        '/may/i'       => 'Mai',
        '/jun/i'       => 'Juin',
        '/jul/i'       => 'Juil',
        '/aug/i'       => 'Aout',
        '/sep/i'       => 'Sept',
        '/oct/i'       => 'Oct',
        '/nov/i'       => 'Nov',
        '/dec/i'       => 'Dec',
    );

    public function __construct($date)
    {
        $this->_date = \DateTime::createFromFormat('Y-m-d H:i:s', $date);
    }

    public function format($format = 'D d M \- H\hi')
    {
        $date = $this->_date->format($format);
        return preg_replace(array_keys(static::$translate), array_values(static::$translate), $date);
    }
}
