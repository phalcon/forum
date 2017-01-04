{#
  This mail template is an experimental proof-of-concept based on the idea
  that the most common design patterns seen in email can be placed in modular
  blocks and moved around to create different designs.

  The same principle is used to build the email templates in MailChimp's
  Drag-and-Drop email editor.

  This email is optimized for mobile email clients, and even works relatively
  well in the Android Gmail App, which does not support Media Queries, but
  does have limited mobile-friendly functionality.

  While this coding method is very flexible, it can be more brittle than
  traditionally-coded emails, particularly in Microsoft Outlook 2007-2010.
  Outlook-specific conditional CSS is included to counteract the
  inconsistencies that crop up.

  For more information on HTML email design and development, visit
  http://templates.mailchimp.com

  NOTE: Some email clients strip out <head> and <style> tags from emails, so
  it's best to have your CSS written inline within your markup. See:
  https://templates.mailchimp.com/resources/inline-css/
#}<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
{{ partial('partials/mail/head', ['subject': subject]) }}
</head>
<body style="margin: 0;padding: 0;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;background-color: #F5F5F5;height: 100% !important;width: 100% !important;">
<center>
<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" id="bodyTable" style="border-collapse: collapse;mso-table-lspace: 0;mso-table-rspace: 0;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;margin: 0;padding: 0;background-color: #F5F5F5;height: 100% !important;width: 100% !important;">
<tr>
<td align="center" valign="top" id="bodyCell" style="mso-table-lspace: 0;mso-table-rspace: 0;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;margin: 0;padding: 0;padding-top: 40px;padding-bottom: 40px;height: 100% !important;width: 100% !important;">
<table border="0" cellpadding="0" cellspacing="0" width="600" id="emailBody" style="border-collapse: separate;mso-table-lspace: 0;mso-table-rspace: 0;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;background-color: #FFFFFF;border: 1px solid #DDDDDD;border-radius: 4px;">
{{ partial('partials/mail/body', ['title': title, 'html_content': html_content]) }}
{{ partial('partials/mail/footer', ['settings_url': settings_url, 'post_url': post_url]) }}
</table>
</td>
</tr>
</table>
</center>
</body>
</html>
