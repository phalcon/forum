<html>
<head>
    <title>{{- title -}}</title>
</head>
<body>
    {{- content() -}}

    {{- html_content -}}

    <p style="font-size:small;-webkit-text-size-adjust:none;color:#717171;">
        &mdash;<br>Reply to this email directly or view the complete thread on {{ post_url -}}.<br>
        Change your e-mail preferences <a href="{{- settings_url -}}">here</a>.
    </p>
</body>
</html>
