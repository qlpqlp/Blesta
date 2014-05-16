<?php
// Errors
$lang['SupportManagerproTickets.!error.code.format'] = "The ticket code must contain only digits.";
$lang['SupportManagerproTickets.!error.department_id.exists'] = "Please select a valid department.";
$lang['SupportManagerproTickets.!error.staff_id.exists'] = "The staff member selected for this ticket does not exist.";
$lang['SupportManagerproTickets.!error.service_id.exists'] = "The service selected for this ticket does not exist.";
$lang['SupportManagerproTickets.!error.service_id.belongs'] = "The service selected is invalid.";
$lang['SupportManagerproTickets.!error.client_id.exists'] = "The client selected for this ticket does not exist.";
$lang['SupportManagerproTickets.!error.client_id.set'] = "The ticket belongs to another client and the assigned client may not be changed.";
$lang['SupportManagerproTickets.!error.email.format'] = "Please enter a valid email address.";
$lang['SupportManagerproTickets.!error.summary.empty'] = "Please enter a summary.";
$lang['SupportManagerproTickets.!error.summary.length'] = "The summary may not exceed 255 characters in length.";
$lang['SupportManagerproTickets.!error.priority.format'] = "Please select a valid priority.";
$lang['SupportManagerproTickets.!error.status.format'] = "Please select a valid status.";
$lang['SupportManagerproTickets.!error.date_closed.format'] = "The ticket close date is in an invalid date format.";
$lang['SupportManagerproTickets.!error.ticket_id.exists'] = "Invalid ticket ID.";

$lang['SupportManagerproTickets.!error.type.format'] = "Please select a valid reply type.";
$lang['SupportManagerproTickets.!error.type.new_valid'] = "New tickets must have a reply type of 'reply'.";
$lang['SupportManagerproTickets.!error.details.empty'] = "Please enter some details about this ticket.";
$lang['SupportManagerproTickets.!error.ticket_id.exists'] = "Invalid ticket ID.";
$lang['SupportManagerproTickets.!error.client_id.attached_to'] = "The ticket reply may not be from a different client.";

$lang['SupportManagerproTickets.!error.replies.valid'] = "At least one ticket reply ID is invalid, or all replies have been selected. You must leave at least one reply remaining.";
$lang['SupportManagerproTickets.!error.tickets.valid'] = "At least one ticket selected is invalid, closed, or does not belong to the same client as the chosen ticket.";
$lang['SupportManagerproTickets.!error.merge_into.itself'] = "The ticket may not be merged with itself.";

$lang['SupportManagerproTickets.!error.tickets.service_matches'] = "At least one of the tickets could not be assigned to the given service because it does not belong to the associated client.";
$lang['SupportManagerproTickets.!error.tickets.department_matches'] = "At least one of the tickets could not be assigned to the given department because it does not belong to the same company.";


// Replies
$lang['SupportManagerproTickets.merge.reply'] = "This ticket has been merged into ticket #%1\$s."; // %1$s is the ticket number


// Priorities
$lang['SupportManagerproTickets.priority.emergency'] = "Emergency";
$lang['SupportManagerproTickets.priority.critical'] = "Critical";
$lang['SupportManagerproTickets.priority.high'] = "High";
$lang['SupportManagerproTickets.priority.medium'] = "Medium";
$lang['SupportManagerproTickets.priority.low'] = "Low";


// Statuses
$lang['SupportManagerproTickets.status.open'] = "Open";
$lang['SupportManagerproTickets.status.awaiting_reply'] = "Awaiting Reply";
$lang['SupportManagerproTickets.status.in_progress'] = "In Progress";
$lang['SupportManagerproTickets.status.closed'] = "Closed";
$lang['SupportManagerproTickets.status.spam'] = "Spam";
$lang['SupportManagerproTickets.status.deleted'] = "Delete";
$lang['SupportManagerproTickets.status.pdelete'] = "Permanent Delete";


// Reply types
$lang['SupportManagerproTickets.type.reply'] = "Reply";
$lang['SupportManagerproTickets.type.note'] = "Note";
$lang['SupportManagerproTickets.type.log'] = "Log";


// Log text
$lang['SupportManagerproTickets.log.department_id'] = "The department has been changed to %1\$s."; // %1$s is the department name
$lang['SupportManagerproTickets.log.summary'] = "The summary has been updated.";
$lang['SupportManagerproTickets.log.priority'] = "The priority has been changed to %1\$s."; // %1$s is the priority
$lang['SupportManagerproTickets.log.status'] = "The status has been changed to %1\$s."; // %1$s is the status
$lang['SupportManagerproTickets.log.ticket_staff_id'] = "Assigned to %1\$s."; // %1$s is the name of the department staff member the ticket was assigned to
$lang['SupportManagerproTickets.log.unassigned'] = "Not Assigned";
?>