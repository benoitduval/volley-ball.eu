<?php
namespace Application\Controller;

use RuntimeException;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Db\Adapter\Adapter;
use Zend\View\Model\ViewModel;
use Zend\Console\Request as ConsoleRequest;
use Zend\Math\Rand;
use Zend\Crypt\Password\Bcrypt;
use Application\TableGateway;
use Application\Model;
use Application\Service;
use Zend\Console\Console;
use Zend\Console\Exception\RuntimeException as ConsoleException;
use Zend\Console\ColorInterface as Color;
use Application\Service\MailService;

class ConsoleController extends AbstractController
{
    protected $_users = [];

    public function init()
    {
        $this->groupTable = $this->get(TableGateway\Group::class);
        $this->eventTable = $this->get(TableGateway\Event::class);
        $this->guestTable = $this->get(TableGateway\Guest::class);
        $this->userTable  = $this->get(TableGateway\User::class);
        $config = $this->get('config');


        $this->adapter = new Adapter([
            'driver'   => 'Pdo_Mysql',
            'username' => $config['db']['username'],
            'password' => $config['db']['password'],
            'database' => $config['db']['migration']['old-db'],
            'driver_options' => [
                \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
            ],
        ]);

        $this->newAdapter = new Adapter([
            'driver'   => 'Pdo_Mysql',
            'username' => $config['db']['username'],
            'password' => $config['db']['password'],
            'database' => $config['db']['migration']['current'],
            'driver_options' => [
                \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
            ],
        ]);
    }

    public function migrationAction()
    {
        $console = Console::getInstance();

        $this->init();
        $console->writeLine('Working on users ...', Color::BLUE);
        $this->users();
        $console->writeLine('DONE', Color::GREEN);
        $console->writeLine('Working on groups ...', Color::BLUE);
        $this->groups();
        $console->writeLine('DONE', Color::GREEN);
        $console->writeLine('Working on places ...', Color::BLUE);
        $this->places();
        $console->writeLine('DONE', Color::GREEN);
        $console->writeLine('Working on events ...', Color::BLUE);
        $this->events();
        $console->writeLine('DONE', Color::GREEN);
        $console->writeLine('Working on recurent events ...', Color::BLUE);
        $this->recurents();
        $console->writeLine('DONE', Color::GREEN);
        $console->writeLine('Working on guests ...', Color::BLUE);
        $this->guests();
        $console->writeLine('DONE', Color::GREEN);
        $console->writeLine('Working on comments ...', Color::BLUE);
        $this->comments();
        $console->writeLine('DONE', Color::GREEN);
        $console->writeLine('Working on notifications ...', Color::BLUE);
        $this->notifs();
        $console->writeLine('DONE', Color::GREEN);
    }

    public function events()
    {
        $this->newAdapter->query('TRUNCATE TABLE `event`')->execute();

        // Groups
        $data = $this->adapter->query('SELECT * FROM `event`')->execute();
        $values = '';
        foreach ($data as $event) {
            $values .= '("' . $event['id'] . '", "' . $event['name'] . '",  "' . $event['date'] . '",  "' . $event['comment'] . '",  "' . $event['groupId'] . '" ,  "' . $this->places[$event['placeId']]['name'] . '" ,  "' . $this->places[$event['placeId']]['address'] . '" ,  "' . $this->places[$event['placeId']]['city'] . '" ,  "' . $this->places[$event['placeId']]['zipCode'] . '",  "' . $this->places[$event['placeId']]['lat'] . '", "' . $this->places[$event['placeId']]['long'] . '"),';
        }
        $values = substr($values, 0, -1);
        $values .= ';';

        $this->newAdapter->query('INSERT INTO `event` VALUES ' . $values)->execute();
    }

    public function groups()
    {
        $this->newAdapter->query('TRUNCATE TABLE `group`')->execute();
        $this->newAdapter->query('TRUNCATE TABLE `userGroup`')->execute();

        // Groups
        $data = $this->adapter->query('SELECT * FROM `group`')->execute();
        $values = '';
        $userValues = '';
        $this->_userGroups = [];
        foreach ($data as $group) {
            $group['brand'] = Model\Group::initBrand($group['name']);
            if (!isset($group['description'])) $group['description'] = '';
            $userIds = json_decode($group['userIds']);
            foreach ($userIds as $userId) {
                $this->_userGroups[$group['id']][] = $userId;
                $isAdmin = (in_array(json_decode($group['adminIds']), $userIds) || ($userId == $group['userId'])) ? 1 : 0;
                $userValues .= '("' . $userId . '", "' . $group['id'] . '", "' . $isAdmin . '"),';
            }
            $values .= '("' . $group['id'] . '", "' . $group['name'] . '",  "' . $group['brand'] . '",  "' . $group['description'] . '"),';
        }
        $values = substr($values, 0, -1);
        $userValues = substr($userValues, 0, -1);
        
        $values .= ';';

        $this->newAdapter->query('INSERT INTO `group` (`id`, `name`, `brand`, `description`) VALUES ' . $values)->execute();
        $this->newAdapter->query('INSERT INTO `userGroup` (`userId`, `groupId`, `admin`) VALUES ' . $userValues . ' ON DUPLICATE KEY UPDATE userId=userId')->execute();
    }

    public function users()
    {
        $this->newAdapter->query('TRUNCATE TABLE `user`')->execute();

        $users = $this->adapter->query('SELECT * FROM `user`')->execute();
        foreach ($users as $data) {
            $bCrypt = new Bcrypt();
            $this->_users[$data['id']] = $data;
            $this->_users[$data['id']]['status']   = Model\User::CONFIRMED;
            $this->_users[$data['id']]['password'] = $bCrypt->create($data['password']);
            $this->_users[$data['id']]['display']  = Model\User::DISPLAY_LARGE;
        }

        // insert users
        $values = '';
        foreach ($this->_users as $key => $data) {
            $values .= '("' . implode('","', $data) . '"),';
        }
        $values = substr($values, 0, -1);
        $values .= ';';
        $this->newAdapter->query('INSERT INTO `user` (`id`, `firstname`, `lastname`, `email`, `password`, `status`, `display`) VALUES ' . $values)->execute();
    }

    public function places()
    {
        $this->newAdapter->query('TRUNCATE TABLE `place`')->execute();

        $data = $this->adapter->query('SELECT * FROM `place`')->execute();
        $this->places = [];
        foreach ($data as $place) {
            $this->places[$place['id']] = $place;
        }
    }

    public function guests()
    {
        $this->newAdapter->query('TRUNCATE TABLE `guest`')->execute();

        $guests = $this->adapter->query('SELECT * FROM `guest`')->execute();

        // insert users
        $values = '';
        foreach ($guests as $guest) {
            if (($guest['date'] > date('Y-m-d H:i:s', time())) && !in_array($guest['userId'], $this->_userGroups[$guest['groupId']])) {
                $console = Console::getInstance();
                $console->writeLine('User ' . $guest['userId'] . ' no more in group ' . $guest['groupId'], Color::RED);
                continue;
            }
            unset($guest['date']);
            $values .= '("' . implode('","', $guest) . '"),';
        }
        $values = substr($values, 0, -1);

        $this->newAdapter->query('INSERT INTO `guest` VALUES ' . $values . ' ON DUPLICATE KEY UPDATE eventId=eventId;')->execute();
    }

    public function comments()
    {
        $this->newAdapter->query('TRUNCATE TABLE `comment`')->execute();

        $data = $this->adapter->query('SELECT * FROM `comment`')->execute();

        // insert users
        $values = '';
        foreach ($data as $row) {
            $values .= '("' . $row['id'] . '", "' . $row['userId'] . '",  "' . $row['eventId'] . '",  "' . addslashes($row['comment']) . '",  "' . $row['date'] . '"),';
        }
        $values = substr($values, 0, -1);
        $values .= ';';

        $this->newAdapter->query('INSERT INTO `comment` VALUES ' . $values)->execute();
    }

    public function recurents()
    {
        $this->newAdapter->query('TRUNCATE TABLE `recurent`')->execute();

        // Groups
        $data = $this->adapter->query('SELECT * FROM `recurent`')->execute();
        $values = '';
        foreach ($data as $recurent) {
            $values .= '("' . $recurent['id'] . '", "' . $recurent['status'] . '",  "' . $recurent['groupId'] . '",  "' . $recurent['name'] . '",  "' . $recurent['sendDay'] . '",  "' . $recurent['day'] . '",  "' . $recurent['time'] . '" ,  "' . $this->places[$recurent['placeId']]['name'] . '" ,  "' . $this->places[$recurent['placeId']]['address'] . '" ,  "' . $this->places[$recurent['placeId']]['city'] . '" ,  "' . $this->places[$recurent['placeId']]['zipCode'] . '"),';
        }
        $values = substr($values, 0, -1);
        $values .= ';';

        $this->newAdapter->query('INSERT INTO `recurent` VALUES ' . $values)->execute();
    }

    public function notifs()
    {
        $this->newAdapter->query('TRUNCATE TABLE `notification`')->execute();

        $data = $this->adapter->query('SELECT * FROM `notification`')->execute();

        // insert users
        $values = '';
        foreach ($data as $row) {
            $values .= '("' . implode('","', $row) . '"),';
        }
        $values = substr($values, 0, -1);
        $values .= ';';

        $this->newAdapter->query('INSERT INTO `notification` VALUES ' . $values)->execute();
    }

    public function recurentAction()
    {
        date_default_timezone_set('Europe/Paris');
        $console        = Console::getInstance();
        $recurentTable  = $this->get(TableGateway\Recurent::class);
        $groupTable     = $this->get(TableGateway\Group::class);
        $eventTable     = $this->get(TableGateway\Event::class);
        $guestTable     = $this->get(TableGateway\Guest::class);
        $userGroupTable = $this->get(TableGateway\UserGroup::class);
        $userTable      = $this->get(TableGateway\User::class);
        $absentTable    = $this->get(TableGateway\Absent::class);

        $recurents      = $recurentTable->fetchAll([
            'emailDay' => date('l'), 
            'status'   => \Application\Model\Recurent::ACTIVE
        ]);

        $groups = [];
        foreach ($recurents as $recurent) {
            if (!isset($groups[$recurent->groupId])) {
                $group = $groupTable->find($recurent->groupId);
                $groups[$recurent->groupId] = $group;
            } else {
                $group = $groups[$recurent->groupId];
            }

            $userIds = $userGroupTable->getUserIds($group->id);
            $users = $userTable->fetchAll([
                'id' => $userIds
            ]);

            $date = new \DateTime('now');
            $date = $date->modify('next ' . strtolower($recurent->eventDay) . ' ' . $recurent->time);
            $date = $date->format('Y-m-d H:i:s');

            // Create Event
            $params = [
                'date'    => $date,
                'comment' => '',
                'name'    => $recurent->name,
                'groupId' => $recurent->groupId,
                'place'   => $recurent->place,
                'address' => $recurent->address,
                'zipCode' => $recurent->zipCode,
                'city'    => $recurent->city,
            ];

            try {
                $mapService = $this->get(Service\Map::class);
                $address = $recurent->address . ', ' . $recurent->zipCode . ' ' . $recurent->city . ' France';

                if ($coords = $mapService->getCoordinates($address)) {
                    $params = array_merge($params, $coords);
                }
            } catch (RuntimeException $e) {
                $console->writeLine($e->getMessage(), Color::RED);
            }

            $event = $eventTable->save($params);

            foreach ($userIds as $id) {
                $absent = $absentTable->fetchOne([
                    '`from` < ?' => $date,
                    '`to` > ?'   => $date,
                    'userId = ?' => $id
                ]);

                $response = ($absent) ? Model\Guest::RESP_NO : Model\Guest::RESP_NO_ANSWER;

                $guest = $guestTable->save([
                    'eventId'  => $event->id,
                    'userId'   => $id,
                    'response' => $response,
                    'groupId'  => $group->id,
                ]);
            }

            $date = \DateTime::createFromFormat('Y-m-d H:i:s', $event->date);
            $config = $this->get('config');
            if ($config['mail']['allowed']) {
                $mail   = $this->get(MailService::class);
                foreach ($users as $user) {
                    $mail->addBcc($user->email);
                }
                $mail->setSubject('[' . $group->name . '] ' . $event->name . ' - ' . \Application\Service\Date::toFr($date->format('l d F \à H\hi')));

                // $mail->addIcalEvent($event);
                $mail->setTemplate(MailService::TEMPLATE_EVENT, [
                    'title'     => $event->name . ' <br /> ' . \Application\Service\Date::toFr($date->format('l d F \à H\hi')),
                    'subtitle'  => $group->name,
                    'name'      => $event->place,
                    'zip'       => $event->zipCode,
                    'address'   => $event->address,
                    'city'      => $event->city,
                    'eventId'   => $event->id,
                    'date'      => \Application\Service\Date::toFr($date->format('l d F \à H\hi')),
                    'day'       => \Application\Service\Date::toFr($date->format('d')),
                    'month'     => \Application\Service\Date::toFr($date->format('F')),
                    'ok'        => Model\Guest::RESP_OK,
                    'no'        => Model\Guest::RESP_NO,
                    'perhaps'   => Model\Guest::RESP_INCERTAIN,
                    'comment'   => 'Aucun commentaire sur l\'évènement',
                    'baseUrl'   => $config['baseUrl']
                ]);
                try {
                    $mail->send();
                } catch (RuntimeException $e) {
                    $console->writeLine($e->getMessage(), Color::RED);
                }
            }
        }
    }
}