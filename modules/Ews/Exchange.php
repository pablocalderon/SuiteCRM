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

class Exchange extends SugarBean
{
    public $tracker_visibility = false;
    public $object_name = 'Exchange';
    public $field_defs = array();

    public function createMeeting(SugarBean $bean, User $user)
    {
        if (empty($bean->date_start)) {
            $start = new DateTime($bean->date_start);
        } else {
            $start = date("Y-m-d H:i:s");
        }

        if (empty($bean->date_end)) {
            $end = new DateTime($bean->date_end);
        } else {
            $end = date("Y-m-d H:i:s", strtotime('1 hour'));
        }

        $guests = [];

        $eventAttendees = $this->getAttendees();

        if (is_array($eventAttendees)) {
            foreach ($eventAttendees as $attendee) {
                $guests = [
                    [
                        'name' => $attendee->name,
                        'email' => $attendee->email1
                    ],
                ];
            }
        }

// Set connection information.
        $username = $user->email1;
        $password = '';
        $host = '';
        $version = Client::VERSION_2016;

        $client = new Client($host, $username, $password, $version);

// Build the request,
        $request = new CreateItemType();
        $request->SendMeetingInvitations = CalendarItemCreateOrDeleteOperationType::SEND_ONLY_TO_ALL;
        $request->Items = new NonEmptyArrayOfAllItemsType();

// Build the event to be added.
        $event = new CalendarItemType();
        $event->RequiredAttendees = new NonEmptyArrayOfAttendeesType();
        $event->Start = $start->format('c');
        $event->End = $end->format('c');
        $event->Subject = $bean->name;

// Set the event body.
        $event->Body = new BodyType();
        $event->Body->_ = 'This is the event body';
        $event->Body->BodyType = BodyTypeType::TEXT;

// Iterate over the guests, adding each as an attendee to the request.
        foreach ($guests as $guest) {
            $attendee = new AttendeeType();
            $attendee->Mailbox = new EmailAddressType();
            $attendee->Mailbox->EmailAddress = $guest['email'];
            $attendee->Mailbox->Name = $guest['name'];
            $attendee->Mailbox->RoutingType = RoutingType::SMTP;
            $event->RequiredAttendees->Attendee[] = $attendee;
        }

// Add the event to the request. You could add multiple events to create more
// than one in a single request.
        $request->Items->CalendarItem[] = $event;

        $response = $client->CreateItem($request);

// Iterate over the results, printing any error messages or event ids.
        $response_messages = $response->ResponseMessages->CreateItemResponseMessage;
        foreach ($response_messages as $response_message) {
            // Make sure the request succeeded.
            if ($response_message->ResponseClass != ResponseClassType::SUCCESS) {
                $code = $response_message->ResponseCode;
                $message = $response_message->MessageText;
                fwrite(STDERR, "Event failed to create with \"$code: $message\"\n");
                continue;
            }

            // Iterate over the created events, printing the id for each.
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

        foreach ($contactInvitees as $contact) {
            $contacts = new Contact;
            $contactList[] = $contacts->retrieve($contact);
        }

        foreach ($userInvitees as $user) {
            $users = new User;
            if ($user !== $current_user->id && $user !== ' ') {
                $userList[] = $users->retrieve($user);
            }
        }

        foreach ($leadInvitees as $lead) {
            $leads = new Lead;
            $leadList[] = $leads->retrieve($lead);
        }

        $attendees = array_merge($contactList, $userList, $leadList);

        return $attendees;
    }
}