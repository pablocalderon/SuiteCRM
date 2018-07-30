<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use jamesiarmes\PhpEws\ArrayType\NonEmptyArrayOfAllItemsType;
use jamesiarmes\PhpEws\ArrayType\NonEmptyArrayOfAttendeesType;
use jamesiarmes\PhpEws\Client;
use jamesiarmes\PhpEws\Enumeration\BodyTypeType;
use jamesiarmes\PhpEws\Enumeration\CalendarItemCreateOrDeleteOperationType;
use jamesiarmes\PhpEws\Enumeration\ResponseClassType;
use jamesiarmes\PhpEws\Enumeration\RoutingType;
use jamesiarmes\PhpEws\Request\CreateItemType;
use jamesiarmes\PhpEws\Type\AttendeeType;
use jamesiarmes\PhpEws\Type\BodyType;
use jamesiarmes\PhpEws\Type\CalendarItemType;
use jamesiarmes\PhpEws\Type\EmailAddressType;

class Create extends SugarBean
{
    public $tracker_visibility = false;
    public $object_name = 'Exchange';
    public $field_defs = array();

    public function createMeeting(SugarBean $bean, User $user)
    {
        $guests = $this->getAttendees();
        $client = $this->createClient($user);
        $request = $this->buildRequest();
        $event = $this->buildEvent($bean);
        $this->setBody($event);
        $this->addAttendees($guests, $event);

        // Add the event to the request
        $request->Items->CalendarItem[] = $event;
        $response = $client->CreateItem($request);

        $response_messages = $response->ResponseMessages->CreateItemResponseMessage;
        foreach ($response_messages as $response_message) {
            if ($response_message->ResponseClass != ResponseClassType::SUCCESS) {
                $code = $response_message->ResponseCode;
                $message = $response_message->MessageText;
                fwrite(STDERR, "Event failed to create with \"$code: $message\"\n");
                continue;
            }

            foreach ($response_message->Items->CalendarItem as $item) {
                $id = $item->ItemId->Id;
                fwrite(STDOUT, "Created event $id\n");
            }
        }
    }

    protected function getAttendees()
    {
        global $current_user;

        $contactList = [];
        $userList = [];
        $leadList = [];
        $contactGuests = [];
        $userGuests = [];
        $leadGuests = [];
        $guests = [];

        if (!empty($_POST['contact_invitees'])) {
            $contactInvitees = explode(',', trim($_POST['contact_invitees'], ','));
        } else {
            $contactInvitees = array();
        }

        if (!empty($_POST['user_invitees'])) {
            $userInvitees = explode(',', trim($_POST['user_invitees'], ','));
        } else {
            $userInvitees = array();
        }

        if (!empty($_POST['lead_invitees'])) {
            $leadInvitees = explode(',', trim($_POST['lead_invitees'], ','));
        } else {
            $leadInvitees = array();
        }

        foreach ($contactInvitees as $contactCounter => $contact) {
            $contacts = new Contact;
            $contactList[] = $contacts->retrieve($contact);
            $contactGuests[] = [
                $contactList[$contactCounter]->name,
                $contactList[$contactCounter]->email1,
            ];
        }

        foreach ($userInvitees as $user) {
            $users = new User;
            if ($user !== $current_user->id && $user !== ' ') {
                $userList[] = $users->retrieve($user);
            }
        }

        foreach ($userList as $userCount => $userGuest) {
            $userGuests[] = [
                $userList[$userCount]->name,
                $userList[$userCount]->email1,
            ];
        }

        foreach ($leadInvitees as $leadCount => $lead) {
            $leads = new Lead;
            $leadList[] = $leads->retrieve($lead);
            $leadGuests[] = [
                $leadList[$leadCount]->name,
                $leadList[$leadCount]->email1,
            ];
        }

        $attendees = array_merge($contactGuests, $userGuests, $leadGuests);

        if (is_array($attendees)) {
            foreach ($attendees as $attendee) {
                $guests[] = [
                    'name' => $attendee[0],
                    'email' => $attendee[1]
                ];
            }
        }
        return $guests;
    }

    protected function createClient(User $user)
    {
        $username = $user->email1;
        $password = 'salesagilitypassword1';
        $host = 'outlook.com';
        $version = Client::VERSION_2016;

        $client = new Client($host, $username, $password, $version);

        return $client;
    }

    protected function buildRequest()
    {
        $request = new CreateItemType();
        $request->SendMeetingInvitations = CalendarItemCreateOrDeleteOperationType::SEND_ONLY_TO_ALL;
        $request->Items = new NonEmptyArrayOfAllItemsType();

        return $request;
    }

    protected function buildEvent(SugarBean $bean)
    {
        if (!empty($bean->date_start)) {
            $start = new DateTime($bean->date_start);
        } else {
            $start = date("Y-m-d H:i:s");
        }

        if (!empty($bean->date_end)) {
            $end = new DateTime($bean->date_end);
        } else {
            $end = date("Y-m-d H:i:s", strtotime('1 hour'));
        }

        $event = new CalendarItemType();
        $event->RequiredAttendees = new NonEmptyArrayOfAttendeesType();
        $event->Start = $start->format('c');
        $event->End = $end->format('c');
        $event->Subject = $bean->name;

        return $event;
    }

    protected function setBody($event)
    {
        // Set the event body.
        $event->Body = new BodyType();
        $event->Body->_ = 'This is the event body';
        $event->Body->BodyType = BodyTypeType::TEXT;
    }

    protected function addAttendees($guests, $event) {
        foreach ($guests as $guest) {
            $attendee = new AttendeeType();
            $attendee->Mailbox = new EmailAddressType();
            $attendee->Mailbox->EmailAddress = $guest['email'];
            $attendee->Mailbox->Name = $guest['name'];
            $attendee->Mailbox->RoutingType = RoutingType::SMTP;
            $event->RequiredAttendees->Attendee[] = $attendee;
        }

        return $event;
    }
}