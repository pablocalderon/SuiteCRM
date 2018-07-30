<?php
/**
 *
 * SugarCRM Community Edition is a customer relationship management program developed by
 * SugarCRM, Inc. Copyright (C) 2004-2013 SugarCRM Inc.
 *
 * SuiteCRM is an extension to SugarCRM Community Edition developed by SalesAgility Ltd.
 * Copyright (C) 2011 - 2018 SalesAgility Ltd.
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU Affero General Public License version 3 as published by the
 * Free Software Foundation with the addition of the following permission added
 * to Section 15 as permitted in Section 7(a): FOR ANY PART OF THE COVERED WORK
 * IN WHICH THE COPYRIGHT IS OWNED BY SUGARCRM, SUGARCRM DISCLAIMS THE WARRANTY
 * OF NON INFRINGEMENT OF THIRD PARTY RIGHTS.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more
 * details.
 *
 * You should have received a copy of the GNU Affero General Public License along with
 * this program; if not, see http://www.gnu.org/licenses or write to the Free
 * Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301 USA.
 *
 * You can contact SugarCRM, Inc. headquarters at 10050 North Wolfe Road,
 * SW2-130, Cupertino, CA 95014, USA. or at email address contact@sugarcrm.com.
 *
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU Affero General Public License version 3.
 *
 * In accordance with Section 7(b) of the GNU Affero General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "Powered by
 * SugarCRM" logo and "Supercharged by SuiteCRM" logo. If the display of the logos is not
 * reasonably feasible for technical reasons, the Appropriate Legal Notices must
 * display the words "Powered by SugarCRM" and "Supercharged by SuiteCRM".
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/Exchange.php';

use jamesiarmes\PhpEws\ArrayType\NonEmptyArrayOfAllItemsType;
use jamesiarmes\PhpEws\Enumeration\MessageDispositionType;
use jamesiarmes\PhpEws\Enumeration\ResponseClassType;
use jamesiarmes\PhpEws\Request\CreateItemType;
use jamesiarmes\PhpEws\Type\CancelCalendarItemType;
use jamesiarmes\PhpEws\Type\ItemIdType;
use \jamesiarmes\PhpEws\Client;
use \jamesiarmes\PhpEws\Request\FindItemType;
use \jamesiarmes\PhpEws\ArrayType\NonEmptyArrayOfBaseFolderIdsType;
use \jamesiarmes\PhpEws\Enumeration\DefaultShapeNamesType;
use \jamesiarmes\PhpEws\Enumeration\DistinguishedFolderIdNameType;
use \jamesiarmes\PhpEws\Type\CalendarViewType;
use \jamesiarmes\PhpEws\Type\DistinguishedFolderIdType;
use \jamesiarmes\PhpEws\Type\ItemResponseShapeType;

class Cancel extends SugarBean
{
    public function cancelMeeting(User $user)
    {
        $exchange = new Exchange();
        $client = $exchange->setConnection($user);

        $event = $this->getEvent($client);


        $request = new CreateItemType();
        $request->MessageDisposition = MessageDispositionType::SEND_AND_SAVE_COPY;
        $request->Items = new NonEmptyArrayOfAllItemsType();
        $cancellation = new CancelCalendarItemType();
        $cancellation->ReferenceItemId = new ItemIdType();
        $cancellation->ReferenceItemId->Id = $event['eventID'];
        $cancellation->ReferenceItemId->ChangeKey = $event['changeKey'];
        $request->Items->CancelCalendarItem[] = $cancellation;
        $response = $client->CreateItem($request);

        $response_messages = $response->ResponseMessages->CreateItemResponseMessage;
        foreach ($response_messages as $response_message) {
            if ($response_message->ResponseClass != ResponseClassType::SUCCESS) {
                $code = $response_message->ResponseCode;
                $message = $response_message->MessageText;
                fwrite(
                    STDERR,
                    "Cancellation failed to create with \"$code: $message\"\n"
                );
                continue;
            }
        }
    }

    protected function getEvent($client)
    {

        $request = new FindItemType();
        $request->ParentFolderIds = new NonEmptyArrayOfBaseFolderIdsType();
// Return all event properties.
        $request->ItemShape = new ItemResponseShapeType();
        $request->ItemShape->BaseShape = DefaultShapeNamesType::ALL_PROPERTIES;
        $folder_id = new DistinguishedFolderIdType();
        $folder_id->Id = DistinguishedFolderIdNameType::CALENDAR;
        $request->ParentFolderIds->DistinguishedFolderId[] = $folder_id;
        $request->CalendarView = new CalendarViewType();
        $response = $client->FindItem($request);
// Iterate over the results, printing any error messages or event ids.
        $response_messages = $response->ResponseMessages->FindItemResponseMessage;
        foreach ($response_messages as $response_message) {
            // Make sure the request succeeded.
            if ($response_message->ResponseClass != ResponseClassType::SUCCESS) {
                $code = $response_message->ResponseCode;
                $message = $response_message->MessageText;
                fwrite(
                    STDERR,
                    "Failed to search for events with \"$code: $message\"\n"
                );
                continue;
            }
        }

        // Iterate over the events that were found, printing some data for each.
        $items = $response_message->RootFolder->Items->CalendarItem;
        foreach ($items as $item) {
            $id = $item->ItemId->Id;
            $start = new DateTime($item->Start);
            $end = new DateTime($item->End);
            $output = 'Found event ' . $item->ItemId->Id . "\n"
                . '  Change Key: ' . $item->ItemId->ChangeKey . "\n"
                . '  Title: ' . $item->Subject . "\n"
                . '  Start: ' . $start->format('l, F jS, Y g:ia') . "\n"
                . '  End:   ' . $end->format('l, F jS, Y g:ia') . "\n\n";
            fwrite(STDOUT, $output);
        }

        // Replace these values with those of the event you wish to cancel.
        $event = [];
        $event[] = [
            'eventID' => $id,
            'changeKey' => $item->ItemId->ChangeKey,
        ];

        return $event;
    }
}