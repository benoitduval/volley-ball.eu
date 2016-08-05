<?php

namespace Application\Model;

class Notification extends AbstractModel
{

    const EVENT_SIMPLE       = 1;
    const EVENT_RECURENT     = 2;
    const EVENT_UPDATE       = 3;
    const COMMENTS           = 4;
    const REMINDER           = 5;
    const COMMENT_ABSENT     = 6;
    const SELF_COMMENT       = 7;

    CONST STATUS_ENABLE      = 1;
    CONST STATUS_DISABLE     = 2;

    public static $labels = [
        self::EVENT_SIMPLE   => 'Recevoir les notifications pour les évènements ponctuels.',
        self::EVENT_RECURENT => 'Recevoir les notifications pour les évènements récurrents.',
        self::EVENT_UPDATE   => 'Recevoir les notifications pour les modifications et annulation d\'évènement.',
        self::COMMENTS       => 'Recevoir les notifications pour les commentaires.',
        self::REMINDER       => 'Recevoir les notifications pour les rappels.',
        self::COMMENT_ABSENT => 'Recevoir les notifications pour les commentaires étant absent à l\'évènement.',
        self::SELF_COMMENT   => 'Recevoir les notifications pour ses propres commentaires.',
    ];

    protected $_id           = null;
    protected $_userId       = null;
    protected $_notification = null;
    protected $_status       = null;

    public function getLabel()
    {
        return self::$labels[$this->_notification];
    } 

    public function toArray()
    {
        return array(
            'id'           => (int) $this->_id,
            'userId'       => (int) $this->_userId,
            'notification' => (int) $this->_notification,
            'status'       => (int) $this->_status,
        );
    }
}
