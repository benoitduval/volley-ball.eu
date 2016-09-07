<?php

namespace Application\TableGateway;

use RuntimeException;
use Zend\Db\TableGateway\TableGatewayInterface;

class UserGroup extends AbstractTableGateway
{

    public function isMember($userId, $groupId)
    {
        $obj = $this->fetchOne(['userId' => $userId, 'groupId' => $groupId]);
        return (bool) $obj;
    }

    public function isAdmin($userId, $groupId)
    {
        $obj = $this->fetchOne(['userId' => $userId, 'groupId' => $groupId]);
        return $obj && $obj->admin;
    }
}