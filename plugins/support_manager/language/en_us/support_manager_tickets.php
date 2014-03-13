<?php
// Errors
$lang['SupportManagerTickets.!error.code.format'] = "The ticket code must contain only digits.";
$lang['SupportManagerTickets.!error.department_id.exists'] = "Please select a valid department.";
$lang['SupportManagerTickets.!error.staff_id.exists'] = "The staff member selected for this ticket does not exist.";
$lang['SupportManagerTickets.!error.service_id.exists'] = "The service selected for this ticket does not exist.";
$lang['SupportManagerTickets.!error.service_id.belongs'] = "The service selected is invalid.";
$lang['SupportManagerTickets.!error.client_id.exists'] = "The client selected for this ticket does not exist.";
$lang['SupportManagerTickets.!error.client_id.set'] = "The ticket belongs to another client and the assigned client may not be changed.";
$lang['SupportManagerTickets.!error.email.format'] = "Please enter a valid email address.";
$lang['SupportManagerTickets.!error.summary.empty'] = "Please enter a summary.";
$lang['SupportManagerTickets.!error.summary.length'] = "The summary may not exceed 255 characters in length.";
$lang['SupportManagerTickets.!error.priority.format'] = "Please select a valid priority.";
$lang['SupportManagerTickets.!error.status.format'] = "Please select a valid status.";
$lang['SupportManagerTickets.!error.date_closed.format'] = "The ticket close date is in an invalid date format.";
$lang['SupportManagerTickets.!error.ticket_id.exists'] = "Invalid ticket ID.";

$lang['SupportManagerTickets.!error.type.format'] = "Please select a valid reply type.";
$lang['SupportManagerTickets.!error.type.new_valid'] = "New tickets must have a reply type of 'reply'.";
$lang['SupportManagerTickets.!error.details.empty'] = "Please enter some details about this ticket.";
$lang['SupportManagerTickets.!error.ticket_id.exists'] = "Invalid ticket ID.";
$lang['SupportManagerTickets.!error.client_id.attached_to'] = "The ticket reply may not be from a different client.";


// Priorities
$lang['SupportManagerTickets.priority.emergency'] = "Emergency";
$lang['SupportManagerTickets.priority.critical'] = "Critical";
$lang['SupportManagerTickets.priority.high'] = "High";
$lang['SupportManagerTickets.priority.medium'] = "Medium";
$lang['SupportManagerTickets.priority.low'] = "Low";


// Statuses
$lang['SupportManagerTickets.status.open'] = "Open";
$lang['SupportManagerTickets.status.awaiting_reply'] = "Awaiting Reply";
$lang['SupportManagerTickets.status.in_progress'] = "In Progress";
$lang['SupportManagerTickets.status.closed'] = "Closed";
$lang['SupportManagerTickets.status.spam'] = "Spam";
$lang['SupportManagerTickets.status.deleted'] = "Deleted";


// Reply types
$lang['SupportManagerTickets.type.reply'] = "Reply";
$lang['SupportManagerTickets.type.note'] = "Note";
$lang['SupportManagerTickets.type.log'] = "Log";


// Log text
$lang['SupportManagerTickets.log.department_id'] = "The department has been changed to %1\$s."; // %1$s is the department name
$lang['SupportManagerTickets.log.summary'] = "The summary has been updated.";
$lang['SupportManagerTickets.log.priority'] = "The priority has been changed to %1\$s."; // %1$s is the priority
$lang['SupportManagerTickets.log.status'] = "The status has been changed to %1\$s."; // %1$s is the status
$lang['SupportManagerTickets.log.ticket_staff_id'] = "Assigned to %1\$s."; // %1$s is the name of the department staff member the ticket was assigned to
$lang['SupportManagerTickets.log.unassigned'] = "Not Assigned";
?>