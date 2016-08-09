<?php
namespace Volley\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Authentication\AuthenticationService;
use Zend\Mvc\MvcEvent;
use Zend\Console\Request as ConsoleRequest;
use Zend\Console\Console as Console;
use Zend\Console\ColorInterface as Color;
use Volley\Entity\Guest;
use Volley\Entity\Notification;
use Volley\Entity\Device;
use Volley\Services\Mail;

class ConsoleController extends AbstractActionController
{
    public function reminderAction()
    {
        $guestMapper = $this->_getMapper('guest');
        $userMapper  = $this->_getMapper('user');
        $eventMapper = $this->_getMapper('event');
        $placeMapper = $this->_getMapper('place');
        $config      = $this->getServiceLocator()->get('volley-config');
        $from        = @date('Y-m-d', strtotime('+1day')) . ' 00:00:00';
        $to          = @date('Y-m-d', strtotime('+1day')) . ' 23:59:59';
        $guests = $guestMapper->fetchAll(array(
            'date > ?' => $from,
            'date < ?' => $to,
            'response  IN (?, ?)' => array(Guest::RESP_NO_ANSWER, Guest::RESP_INCERTAIN)
        ));
        $data = array();
        date_default_timezone_set('Europe/Paris');

        foreach ($guests as $guest) {
            if (isset($users[$guest->userId])) {
                $user = $users[$guest->userId];
            } else {
                if (!$notifMapper->isAllowed(Notification::EVENT_REMINDER, $guest->userId)) continue;

                $user = $userMapper->getById($guest->userId);
                $users[$guest->userId] = $user;
            }

            if (isset($events[$guest->eventId])) {
                $event = $events[$guest->eventId];
            } else {
                $event = $eventMapper->getById($guest->eventId);
                $events[$guest->eventId] = $event;
            }

            if (isset($places[$event->placeId])) {
                $place = $places[$event->placeId];
            } else {
                $place = $placeMapper->getById($event->placeId);
                $places[$event->placeId] = $place;
            }

            $data[$guest->eventId]['bcc'][] = $user->email;
            $event = $eventMapper->getById($guest->eventId);
            $data[$guest->eventId]['event'] = $event;
            $data[$guest->eventId]['comment']  = (empty($data['comment'])) ? 'Aucun commentaire sur l\'évènement' : nl2br($event->comment);
            $data[$guest->eventId]['place'] = $placeMapper->getById($event->placeId);
            $group = $this->_getMapper('group')->getById($event->groupId);
            $data[$guest->eventId]['subject'] = $group->name;
        }

        foreach ($data as $params) {
            $mail = new Mail($this->getServiceLocator()->get('volley_transport_mail'));
            $mail->addBcc($params['bcc']);
            $mail->setSubject('[Volley-ball.eu] ' . $params['subject'] . ' ' . $params['event']->name . ' - En attente de réponse!');
            $mail->setTemplate(Mail::TEMPLATE_REMINDER, array(
                'pitch'     => 'Hey n\'oublie pas!',
                'title'     => $params['event']->name . '<br />' . $params['event']->getDate()->format('l d F \à H\hi'),
                'address'   => $params['place']->address,
                'name'      => $params['place']->name,
                'comment'   => $params['comment'],
                'zip'       => $params['place']->zipCode,
                'city'      => $params['place']->city,
                'eventId'   => $params['event']->id,
                'date'      => $event->getDate()->format('l d F \à H\hi'),
                'day'       => $event->getDate()->format('d'),
                'month'     => $event->getDate()->format('F'),
                'ok'        => Guest::RESP_OK,
                'no'        => Guest::RESP_NO,
                'perhaps'   => Guest::RESP_INCERTAIN,
                'baseUrl'   => $config['baseUrl'],
                'subtitle'  => $params['subject'],
            ));
            $mail->send();
        }
    }

    public function recurentAction()
    {
        date_default_timezone_set('Europe/Paris');
        $recurentMapper = $this->_getMapper('recurent');
        $groupMapper    = $this->_getMapper('group');
        $eventMapper    = $this->_getMapper('event');
        $guestMapper    = $this->_getMapper('guest');
        $notifMapper    = $this->_getMapper('notif');
        $userMapper     = $this->_getMapper('user');
        $recurents      = $recurentMapper->fetchAll(['sendDay' => date('l'), 'status' => \Volley\Entity\Recurent::ACTIVE]);
        $mail           = new Mail($this->getServiceLocator()->get('volley_transport_mail'));
        $config         = $this->getServiceLocator()->get('volley-config');

        foreach ($recurents as $recurent) {
            $place = $this->_getMapper('place')->getById($recurent->placeId);
            $group = $groupMapper->getById($recurent->groupId);
            $users = json_decode($group->userIds, true);
            if (!is_array($users)) $users = array($users);
            $userIds = array();
            foreach ($users as $id) {
                if (in_array($id, $userIds)) continue;
                $userIds[] = $id;
            }

            $date = new \DateTime('now');
            $date = $date->modify('next ' . strtolower($recurent->day));
            $date = $date->modify('+ ' . $recurent->time . ' hours');
            $date = $date->format('Y-m-d H:i:s');

            // Create Event
            $eventData = array(
                'userId'  => $recurent->userId,
                'placeId' => $recurent->placeId,
                'date'    => $date,
                'name'    => $recurent->name,
                'comment' => '',
                'groupId' => $recurent->groupId,
            );
            $event = $eventMapper->fromArray($eventData)->save();

            if ($event) {
                $deviceMapper = $this->_getMapper('device');
                $email = false;
                foreach ($userIds as $id) {

                    if (!$notifMapper->isAllowed(Notification::EVENT_RECURENT, $id)) continue;

                    if ($deviceMapper->fetchOne(['userId' =>  $id, 'status' => Device::ACTIVE])) {
                        $pbUsers[] = $id;
                    } else {
                        $email = true;
                        $user = $userMapper->getById($id);
                        $mail->addBcc($user->email);
                    }
                    $params = array(
                        'eventId'  => $event->id,
                        'userId'   => $id,
                        'response' => Guest::RESP_NO_ANSWER,
                        'date'     => $date,
                        'groupId'  => $group->id,
                    );
                    $guest = $guestMapper->fromArray($params)->getEntity();
                    // make sure id is empty to avoid update. Create only here
                    $guest->id = null;
                    $guestMapper->setEntity($guest)->save();
                }

                $comment  = 'Aucun commentaire sur l\'évènement';
                if (!empty($pbUsers)) {
                    foreach ($pbUsers as $userId) {
                        $devices = $deviceMapper->fetchAll(['userId' => $userId, 'status' => Device::ACTIVE]);
                        $url = $config['baseUrl'] . '/event/detail/' . $event->id;
                        foreach ($devices as $device) {
                            $pb = new \Pushbullet\Pushbullet($device->token);
                            $pb->device($device->iden)->pushLink(
                                'Évènement' . "\n" . $event->name . "\n" . $event->getDate()->format(),
                                $url,
                                $comment
                            );
                        }
                    }
                }

                if ($email) {
                    // Send Email
                    $mail->setSubject('[' . $group->name . '] ' . $event->name . ' - ' . $event->getDate()->format('d-m-Y'));
                    $mail->setTemplate(Mail::TEMPLATE_EVENT, array(
                        'pitch'     => 'Nouvel Évènement!',
                        'subtitle'  => $group->name,
                        'title'     => $event->name . ' <br /> ' . $event->getDate()->format('l d F \à H\hi'),
                        'name'      => $place->name,
                        'address'   => $place->address,
                        'zip'       => $place->zipCode,
                        'city'      => $place->city,
                        'eventId'   => $event->id,
                        'date'      => $event->getDate()->format('l d F \à H\hi'),
                        'day'       => $event->getDate()->format('d'),
                        'month'     => $event->getDate()->format('F'),
                        'ok'        => Guest::RESP_OK,
                        'no'        => Guest::RESP_NO,
                        'perhaps'   => Guest::RESP_INCERTAIN,
                        'comment'   => $comment,
                        'baseUrl'   => $config['baseUrl']
                    ));
                    $mail->send();
                }
            } else {
                error_log('Recurent event failed');
            }
        }
    }

    public function notificationAction()
    {
        $console = Console::getInstance();

        $userMapper  = $this->_getMapper('user');
        $users       = $userMapper->fetchAll();

        foreach ($users as $user) {
            $console->writeLine('User : ' . $user->getFullname(), Color::BLUE);
            $notifMapper = $this->_getMapper('notif');
            $result = $notifMapper->addNotifications($user->id);
            foreach ($result as $label => $value) {
                $console->writeLine($label);
                if ($value) {
                    $console->writeLine('OK', Color::GREEN);
                } else {
                    $console->writeLine('ADDED', Color::RED);
                }
            }
        }
    }

    private function _getMapper($name)
    {
        return $this->getServiceLocator()->get('volley-' . $name . '-mapper');
    }
}
