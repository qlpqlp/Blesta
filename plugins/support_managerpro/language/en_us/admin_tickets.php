<?php
// Success messages
$lang['AdminTickets.!success.ticket_created'] = "The ticket #%1\$s has been successfully opened."; // %1$s is the ticket number
$lang['AdminTickets.!success.ticket_updated'] = "The ticket #%1\$s has been successfully updated."; // %1$s is the ticket number
$lang['AdminTickets.!success.ticket_split'] = "The ticket #%1\$s has been successfully split into ticket #%2\$s."; // %1$s is the ticket number of the ticket being split, %2$s is the ticket number of the split ticket
$lang['AdminTickets.!success.ticket_merge'] = "The selected tickets have been successfully merged."; // %1$s is the ticket number
$lang['AdminTickets.!success.ticket_update_status'] = "The selected tickets have been successfully updated.";
$lang['AdminTickets.!success.ticket_deleted'] = "The selected tickets have been successfully deleted.";
$lang['AdminTickets.!success.reply_deleted'] = "The selected replies have been successfully deleted.";


// Notice messages
$lang['AdminTickets.!notice.no_departments_staff'] = "No staff and/or departments have yet been created. Click %1\$s above to create a department, or %2\$s to assign a staff member."; // %1$s is the language definition for the Departments navigation item, %2$s is the language definition for the Staff navigation item


// Page Titles
$lang['AdminTickets.index.page_title'] = "Support Manager Pro > Tickets";
$lang['AdminTickets.add.page_title'] = "Support Manager Pro > Open Ticket";
$lang['AdminTickets.reply.page_title'] = "Support Manager Pro > Ticket #%1\$s"; // %1$s is the ticket number
$lang['AdminTickets.search.page_title'] = "Search Results for \"%1\$s\""; // %1$s is the search keywords


$lang['AdminTickets.text.unassigned'] = "Not Assigned";



// Index
$lang['AdminTickets.index.category_open'] = "Open";
$lang['AdminTickets.index.category_awaiting_reply'] = "Awaiting Reply";
$lang['AdminTickets.index.category_in_progress'] = "In Progress";
$lang['AdminTickets.index.category_closed'] = "Closed";
$lang['AdminTickets.index.category_spam'] = "Spam";
$lang['AdminTickets.index.category_deleted'] = "Trash";

$lang['AdminTickets.index.categorylink_createticket'] = "Open Ticket";

$lang['AdminTickets.index.add_client'] = "Add Client";
$lang['AdminTickets.index.quote'] = "Quote";

$lang['AdminTickets.index.boxtitle_tickets'] = "Tickets";
$lang['AdminTickets.index.heading_code'] = "Ticket Number";
$lang['AdminTickets.index.heading_priority'] = "Priority";
$lang['AdminTickets.index.heading_department_name'] = "Department";
$lang['AdminTickets.index.heading_summary'] = "Summary";
$lang['AdminTickets.index.heading_from'] = "From/To";
$lang['AdminTickets.index.heading_assigned_staff'] = "Assigned To";
$lang['AdminTickets.index.heading_last_reply_date'] = "Last Reply";

$lang['AdminTickets.index.unassigned'] = "Unassigned";
$lang['AdminTickets.index.last_reply_by'] = "by";

$lang['AdminTickets.index.no_results'] = "There are currently no tickets with this status.";

$lang['AdminTickets.index.text_with_selected'] = "With selected tickets, perform:";
$lang['AdminTickets.index.text_into_ticket'] = "Into ticket:";
$lang['AdminTickets.index.text_to_status'] = "Change to:";
$lang['AdminTickets.index.ticket_number_placeholder'] = "Ticket Number";
$lang['AdminTickets.index.text_no_tickets'] = "No open tickets found. Try searching again.";
$lang['AdminTickets.index.field_actionsubmit'] = "Submit";

$lang['AdminTickets.index.ticket_name'] = "#%1\$s %2\$s %3\$s"; // %1$s is the ticket number, %2$s is the client's first name, %3$s is the client's last name
$lang['AdminTickets.index.ticket_email'] = "#%1\$s %2\$s"; // %1$s is the ticket number, %2$s is the client's email address


// Add Ticket
$lang['AdminTickets.add.boxtitle_add'] = "Open Ticket";

$lang['AdminTickets.add.heading_search_client'] = "Search for the Client";
$lang['AdminTickets.add.text_no_clients'] = "No clients found. Try searching again.";

$lang['AdminTickets.add.heading_summary'] = "Summary";
$lang['AdminTickets.add.heading_client'] = "Client";
$lang['AdminTickets.add.heading_department'] = "Department";
$lang['AdminTickets.add.heading_staff_id'] = "Assigned To";
$lang['AdminTickets.add.heading_priority'] = "Priority";
$lang['AdminTickets.add.heading_status'] = "Status";
$lang['AdminTickets.add.field_attachments'] = "Attachments";
$lang['AdminTickets.add.field_details'] = "Details";
$lang['AdminTickets.add.text_add_attachment'] = "Add Attachment";
$lang['AdminTickets.add.field_addsubmit'] = "Open Ticket";
$lang['Admintickets.add.client_placeholder'] = "Client ID or Name";

$lang['AdminTickets.add.text_add_response'] = "Insert a Predefined Response";


// Reply
$lang['AdminTickets.reply.boxtitle_reply'] = "Ticket #%1\$s"; // %1$s is the ticket number

$lang['AdminTickets.reply.services'] = "Services";
$lang['AdminTickets.reply.heading_summary'] = "Summary";

$lang['AdminTickets.reply.heading_client'] = "Client";
$lang['AdminTickets.reply.heading_department'] = "Department";
$lang['AdminTickets.reply.heading_staff_id'] = "Assigned To";
$lang['AdminTickets.reply.heading_priority'] = "Priority";
$lang['AdminTickets.reply.heading_status'] = "Status";
$lang['AdminTickets.reply.heading_date_added'] = "Date Opened";

$lang['AdminTickets.reply.text_add_response'] = "Insert a Predefined Response";
$lang['AdminTickets.reply.text_with_selected'] = "With selected replies, perform:";

$lang['AdminTickets.reply.heading_reply'] = "Add Reply";
$lang['AdminTickets.reply.field_reply'] = "Reply";
$lang['AdminTickets.reply.field_note'] = "Note";
$lang['AdminTickets.reply.field_attachments'] = "Attachments";
$lang['AdminTickets.reply.text_add_attachment'] = "Add Attachment";
$lang['AdminTickets.reply.field_replysubmit'] = "Update Ticket";
$lang['AdminTickets.reply.field_actionsubmit'] = "Go";

$lang['AdminTickets.reply.reply_date'] = "On %1\$s %2\$s %3\$s replied"; // %1$s is the ticket reply date, %2$s is the first name of the reply author, %3$s is the last name of the reply author
$lang['AdminTickets.reply.log_date'] = "%1\$s by %2\$s %3\$s"; // %1$s is the ticket reply date, %2$s is the first name of the reply author, %3$s is the last name of the reply author
$lang['AdminTickets.reply.system'] = "System";
$lang['AdminTickets.reply.staff_title'] = "Support Staff";

$lang['AdminTickets.reply.services'] = "List Services";
$lang['AdminTickets.reply.no_services'] = "No services found.";


// Client widget
// Index
$lang['AdminTickets.client.category_open'] = "Open";
$lang['AdminTickets.client.category_awaiting_reply'] = "Awaiting Reply";
$lang['AdminTickets.client.category_in_progress'] = "In Progress";
$lang['AdminTickets.client.category_closed'] = "Closed";

$lang['AdminTickets.client.categorylink_createticket'] = "Open Ticket";

$lang['AdminTickets.client.boxtitle_tickets'] = "Tickets";
$lang['AdminTickets.client.heading_code'] = "Ticket Number";
$lang['AdminTickets.client.heading_priority'] = "Priority";
$lang['AdminTickets.client.heading_department_name'] = "Department";
$lang['AdminTickets.client.heading_summary'] = "Summary";
$lang['AdminTickets.client.heading_last_reply_date'] = "Last Reply";

$lang['AdminTickets.client.no_results'] = "There are currently no tickets with this status.";


// Search
$lang['AdminTickets.search.boxtitle_tickets'] = "Search Tickets for \"%1\$s\""; // %1$s is the search criteria
$lang['AdminTickets.search.heading_code'] = "Ticket Number";
$lang['AdminTickets.search.heading_priority'] = "Priority";
$lang['AdminTickets.search.heading_status'] = "Status";
$lang['AdminTickets.search.heading_department_name'] = "Department";
$lang['AdminTickets.search.heading_summary'] = "Summary";
$lang['AdminTickets.search.heading_last_reply_date'] = "Last Reply";

$lang['AdminTickets.search.no_results'] = "There are no tickets that match the search criteria.";
?>