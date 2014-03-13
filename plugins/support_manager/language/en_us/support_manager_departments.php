<?php
// Errors
$lang['SupportManagerDepartments.!error.company_id.exists'] = "Invalid company ID.";
$lang['SupportManagerDepartments.!error.name.empty'] = "Please enter a name for this department.";
$lang['SupportManagerDepartments.!error.description.empty'] = "Please enter a description.";
$lang['SupportManagerDepartments.!error.email.format'] = "Please enter a valid email address.";
$lang['SupportManagerDepartments.!error.method.format'] = "Invalid method type.";
$lang['SupportManagerDepartments.!error.method.imap'] = "The PHP IMAP extension is required for downloading messages via POP3 or IMAP.";
$lang['SupportManagerDepartments.!error.method.mailparse'] = "The PHP Mailparse PECL extension is required for parsing email tickets.";
$lang['SupportManagerDepartments.!error.default_priority.format'] = "Invalid default priority type.";
$lang['SupportManagerDepartments.!error.host.format'] = "Please enter a valid host name.";
$lang['SupportManagerDepartments.!error.host.length'] = "The host name may not exceed 128 characters in length.";
$lang['SupportManagerDepartments.!error.user.format'] = "Please enter a user name.";
$lang['SupportManagerDepartments.!error.user.length'] = "The user name may not exceed 64 characters in length.";
$lang['SupportManagerDepartments.!error.password.format'] = "Please enter a password.";
$lang['SupportManagerDepartments.!error.password.length'] = "The password may not exceed 64 characters in length.";
$lang['SupportManagerDepartments.!error.port.format'] = "Please enter a port number.";
$lang['SupportManagerDepartments.!error.port.length'] = "The port may not exceed 6 digits in length.";
$lang['SupportManagerDepartments.!error.service.format'] = "Please select a valid service type.";
$lang['SupportManagerDepartments.!error.security.format'] = "Please select a valid security type.";
$lang['SupportManagerDepartments.!error.mark_messages.format'] = "Please select a valid message type to mark messages.";
$lang['SupportManagerDepartments.!error.mark_messages.valid'] = "Messages using POP3 may only be marked as deleted.";
$lang['SupportManagerDepartments.!error.clients_only.format'] = "Whether to allow clients to open or reply to tickets must be set to 0 or 1.";
$lang['SupportManagerDepartments.!error.override_from_email.format'] = "Whether to allow this department's email address to be used as the from address in email templates must be set to 0 or 1.";
$lang['SupportManagerDepartments.!error.status.format'] = "Invalid status type.";
$lang['SupportManagerDepartments.!error.department_id.exists'] = "Invalid department ID.";

$lang['SupportManagerDepartments.!error.department_id.has_tickets'] = "The department could not be deleted because it currently has tickets assigned to it.";


// Methods
$lang['SupportManagerDepartments.methods.none'] = "None";
$lang['SupportManagerDepartments.methods.pipe'] = "Piping";
$lang['SupportManagerDepartments.methods.pop3'] = "POP3";
$lang['SupportManagerDepartments.methods.imap'] = "IMAP";


// Statuses
$lang['SupportManagerDepartments.statuses.hidden'] = "Hidden";
$lang['SupportManagerDepartments.statuses.visible'] = "Visible";


// Priorities
$lang['SupportManagerDepartments.priorities.emergency'] = "Emergency";
$lang['SupportManagerDepartments.priorities.critical'] = "Critical";
$lang['SupportManagerDepartments.priorities.high'] = "High";
$lang['SupportManagerDepartments.priorities.medium'] = "Medium";
$lang['SupportManagerDepartments.priorities.low'] = "Low";


// Security types
$lang['SupportManagerDepartments.security_types.none'] = "None";
$lang['SupportManagerDepartments.security_types.ssl'] = "SSL";
$lang['SupportManagerDepartments.security_types.tls'] = "TLS";


// Message types
$lang['SupportManagerDepartments.message_types.read'] = "Read";
$lang['SupportManagerDepartments.message_types.deleted'] = "Deleted";
?>