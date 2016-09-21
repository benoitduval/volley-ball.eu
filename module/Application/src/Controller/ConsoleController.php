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
use Zend\Console\Console;
use Zend\Console\Exception\RuntimeException as ConsoleException;
use Zend\Console\ColorInterface as Color;

class ConsoleController extends AbstractController
{
    protected $_users = [];

    public function init()
    {
        $this->groupTable = $this->getContainer()->get(TableGateway\Group::class);
        $this->eventTable = $this->getContainer()->get(TableGateway\Event::class);
        $this->guestTable = $this->getContainer()->get(TableGateway\Guest::class);
        $this->userTable  = $this->getContainer()->get(TableGateway\User::class);

        $this->adapter = new Adapter([
            'driver'   => 'Pdo_Mysql',
            'database' => '',
            'username' => '',
            'password' => '',
            'driver_options' => [
                \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
            ],
        ]);

        $this->newAdapter = new Adapter([
            'driver'   => 'Pdo_Mysql',
            'database' => '',
            'username' => '',
            'password' => '',
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
        $console->writeLine('DONE', Color::BLUE);
        $console->writeLine('Working on groups ...', Color::BLUE);
        $this->groups();
        $console->writeLine('DONE', Color::BLUE);
        $console->writeLine('Working on places ...', Color::BLUE);
        $this->places();
        $console->writeLine('DONE', Color::BLUE);
        $console->writeLine('Working on events ...', Color::BLUE);
        $this->events();
        $console->writeLine('DONE', Color::BLUE);
        $console->writeLine('Working on guests ...', Color::BLUE);
        $this->guests();
        $console->writeLine('DONE', Color::BLUE);
        $console->writeLine('Working on comments ...', Color::BLUE);
        $this->comments();
        $console->writeLine('DONE', Color::BLUE);
    }

    public function events()
    {
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
        // Groups
        $data = $this->adapter->query('SELECT * FROM `group`')->execute();
        $values = '';
        $userValues = '';
        foreach ($data as $group) {
            $userIds = json_decode($group['userIds']);
            foreach ($userIds as $userId) {
                $isAdmin = (in_array(json_decode($group['adminIds']), $userIds) || ($userId == $group['userId'])) ? 1 : 0;
                $userValues .= '("' . $userId . '", "' . $group['id'] . '", "' . $isAdmin . '"),';
            }
            $values .= '("' . $group['id'] . '", "' . $group['name'] . '",  "' . $group['brand'] . '",  "' . $group['description'] . '",  "' . $group['info'] . '"),';
        }
        $values = substr($values, 0, -1);
        $userValues = substr($userValues, 0, -1);
        
        $values .= ';';

        $this->newAdapter->query('INSERT INTO `group` VALUES ' . $values)->execute();
        $this->newAdapter->query('INSERT INTO `userGroup` (`userId`, `groupId`, `admin`) VALUES ' . $userValues . ' ON DUPLICATE KEY UPDATE userId=userId')->execute();

    }

    public function users()
    {
        $users = $this->adapter->query('SELECT * FROM `user`')->execute();
        foreach ($users as $data) {
            $bCrypt = new Bcrypt();
            $this->_users[$data['id']] = $data;
            $this->_users[$data['id']]['status']   = Model\User::CONFIRMED;
            $this->_users[$data['id']]['password'] = $bCrypt->create($data['password']);
        }

        // insert users
        $values = '';
        foreach ($this->_users as $key => $data) {
            $values .= '("' . implode('","', $data) . '"),';
        }
        $values = substr($values, 0, -1);
        $values .= ';';

        $this->newAdapter->query('INSERT INTO `user` VALUES ' . $values)->execute();
    }

    public function places()
    {
        $data = $this->adapter->query('SELECT * FROM `place`')->execute();
        $this->places = [];
        foreach ($data as $place) {
            $this->places[$place['id']] = $place;
        }
    }

    public function guests()
    {
        $guests = $this->adapter->query('SELECT * FROM `guest`')->execute();

        // insert users
        $values = '';
        foreach ($guests as $guest) {
            unset($guest['date']);
            $values .= '("' . implode('","', $guest) . '"),';
        }
        $values = substr($values, 0, -1);
        $values .= ';';

        $this->newAdapter->query('INSERT INTO `guest` VALUES ' . $values)->execute();
    }

    public function comments()
    {
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
}