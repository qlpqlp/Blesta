<?php
// Errors
$lang['SupportManagerproDepartments.!error.company_id.exists'] = "Invalid company ID.";
$lang['SupportManagerproDepartments.!error.name.empty'] = "Please enter a name for this department.";
$lang['SupportManagerproDepartments.!error.description.empty'] = "Please enter a description.";
$lang['SupportManagerproDepartments.!error.email.format'] = "Please enter a valid email address.";
$lang['SupportManagerproDepartments.!error.method.format'] = "Invalid method type.";
$lang['SupportManagerproDepartments.!error.method.imap'] = "The PHP IMAP extension is required for downloading messages via POP3 or IMAP.";
$lang['SupportManagerproDepartments.!error.method.mailparse'] = "The PHP Mailparse PECL extension is required for parsing email tickets.";
$lang['SupportManagerproDepartments.!error.default_priority.format'] = "Invalid default priority type.";
$lang['SupportManagerproDepartments.!error.host.format'] = "Please enter a valid host name.";
$lang['SupportManagerproDepartments.!error.host.length'] = "The host name may not exceed 128 characters in length.";
$lang['SupportManagerproDepartments.!error.user.format'] = "Please enter a user name.";
$lang['SupportManagerproDepartments.!error.user.length'] = "The user name may not exceed 64 characters in length.";
$lang['SupportManagerproDepartments.!error.password.format'] = "Please enter a password.";
$lang['SupportManagerproDepartments.!error.password.length'] = "The password may not exceed 64 characters in length.";
$lang['SupportManagerproDepartments.!error.port.format'] = "Please enter a port number.";
$lang['SupportManagerproDepartments.!error.port.length'] = "The port may not exceed 6 digits in length.";
$lang['SupportManagerproDepartments.!error.service.format'] = "Please select a valid service type.";
$lang['SupportManagerproDepartments.!error.security.format'] = "Please select a valid security type.";
$lang['SupportManagerproDepartments.!error.mark_messages.format'] = "Please select a valid message type to mark messages.";
$lang['SupportManagerproDepartments.!error.mark_messages.valid'] = "Messages using POP3 may only be marked as deleted.";
$lang['SupportManagerproDepartments.!error.clients_only.format'] = "Whether to allow clients to open or reply to tickets must be set to 0 or 1.";
$lang['SupportManagerproDepartments.!error.override_from_email.format'] = "Whether to allow this department's email address to be used as the from address in email templates must be set to 0 or 1.";
$lang['SupportManagerproDepartments.!error.status.format'] = "Invalid status type.";
$lang['SupportManagerproDepartments.!error.department_id.exists'] = "Invalid department ID.";

$lang['SupportManagerproDepartments.!error.department_id.has_tickets'] = "The department could not be deleted because it currently has tickets assigned to it.";


// Methods
$lang['SupportManagerproDepartments.methods.none'] = "None";
$lang['SupportManagerproDepartments.methods.pipe'] = "Piping";
$lang['SupportManagerproDepartments.methods.pop3'] = "POP3";
$lang['SupportManagerproDepartments.methods.imap'] = "IMAP";


// Statuses
$lang['SupportManagerproDepartments.statuses.hidden'] = "Hidden";
$lang['SupportManagerproDepartments.statuses.visible'] = "Visible";


// Priorities
$lang['SupportManagerproDepartments.priorities.emergency'] = "Emergency";
$lang['SupportManagerproDepartments.priorities.critical'] = "Critical";
$lang['SupportManagerproDepartments.priorities.high'] = "High";
$lang['SupportManagerproDepartments.priorities.medium'] = "Medium";
$lang['SupportManagerproDepartments.priorities.low'] = "Low";


// Security types
$lang['SupportManagerproDepartments.security_types.none'] = "None";
$lang['SupportManagerproDepartments.security_types.ssl'] = "SSL";
$lang['SupportManagerproDepartments.security_types.tls'] = "TLS";


// Message types
$lang['SupportManagerproDepartments.message_types.read'] = "Read";
$lang['SupportManagerproDepartments.message_types.deleted'] = "Deleted";
?>