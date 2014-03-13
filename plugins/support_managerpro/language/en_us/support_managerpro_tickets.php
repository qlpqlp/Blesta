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
$lang['SupportManagerproTickets.status.deleted'] = "Deleted";


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